<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace AlsaifGallery\MSCE\Model;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\Stdlib\StringUtils as StringHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\Customer\CredentialsValidator;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Encryption\EncryptorInterface as Encryptor;
use Magento\Framework\Exception\InputException;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Math\Random;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\ExpiredException;
use Magento\Framework\Exception\State\InputMismatchException;
use Magento\Framework\Exception\State\InvalidTransitionException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\EmailNotificationInterface;


class CreateCustomerManagement implements \AlsaifGallery\MSCE\Api\CreateCustomerManagementInterface
{

  /**
   * Configuration path to customer password minimum length
   */
  const XML_PATH_MINIMUM_PASSWORD_LENGTH = 'customer/password/minimum_password_length';

  /**
   * Configuration path to customer password required character classes number
   */
  const XML_PATH_REQUIRED_CHARACTER_CLASSES_NUMBER = 'customer/password/required_character_classes_number';

  /**
   * Constants for the type of new account email to be sent
   *
   * @deprecated
   */
  const NEW_ACCOUNT_EMAIL_REGISTERED = 'registered';

  /**
   * @var EmailNotificationInterface
   */
  private $emailNotification;


  public function __construct(
      StringHelper $stringHelper,
      ScopeConfigInterface $scopeConfig,
      Encryptor $encryptor,
      \Magento\Customer\Model\CustomerFactory $customerFactory,
      \Magento\Store\Model\StoreManagerInterface $storeManager,
      \Magento\Customer\Model\AccountManagement $accountManagement,
      CustomerRepositoryInterface $customerRepository,
      CustomerRegistry $customerRegistry,
      AddressRepositoryInterface $addressRepository,
      Random $mathRandom,
      CredentialsValidator $credentialsValidator = null
  ) {
      $this->stringHelper = $stringHelper;
      $this->scopeConfig = $scopeConfig;
      $this->encryptor = $encryptor;
      $this->customerFactory  = $customerFactory;
      $this->storeManager     = $storeManager;
      $this->accountManagement= $accountManagement;
      $this->customerRepository = $customerRepository;
      $this->customerRegistry = $customerRegistry;
      $this->mathRandom = $mathRandom;
      $this->addressRepository = $addressRepository;
      $objectManager = ObjectManager::getInstance();
      $this->credentialsValidator =
          $credentialsValidator ?: $objectManager->get(CredentialsValidator::class);
  }

    /**
     * {@inheritdoc}
     */
    public function postCreateCustomer(CustomerInterface $customer, $password = null, $redirectUrl = '')
    {
      if ($password !== null) {
          $this->checkPasswordStrength($password);
          $customerEmail = $customer->getEmail();
          try {
              $this->credentialsValidator->checkPasswordDifferentFromEmail($customerEmail, $password);
          } catch (InputException $e) {
              throw new LocalizedException(
                  __("The password can't be the same as the email address. Create a new password and try again.")
              );
          }
          $hash = $this->createPasswordHash($password);
      } else {
          $hash = null;
      }

      $customerAddresses = $customer->getAddresses() ?: [];
      $customer->setAddresses(null);

      try {
          // If customer exists existing hash will be used by Repository
          $customer = $this->customerRepository->save($customer, $hash);
      } catch (AlreadyExistsException $e) {
          throw new InputMismatchException(
              __('A customer with the same email address already exists in an associated website.')
          );
          // phpcs:ignore Magento2.Exceptions.ThrowCatch
      } catch (LocalizedException $e) {
          throw $e;
      }

      try {
          foreach ($customerAddresses as $address) {
              if ($address->getId()) {
                  $newAddress = clone $address;
                  $newAddress->setId(null);
                  $newAddress->setCustomerId($customer->getId());
                  $this->addressRepository->save($newAddress);
              } else {
                  $address->setCustomerId($customer->getId());
                  $this->addressRepository->save($address);
              }
          }
          $this->customerRegistry->remove($customer->getId());
          // phpcs:ignore Magento2.Exceptions.ThrowCatch
      } catch (InputException $e) {
          $this->customerRepository->delete($customer);
          throw $e;
      }

      $customer = $this->customerRepository->getById($customer->getId());
      $newLinkToken = $this->mathRandom->getUniqueHash();
      $this->accountManagement->changeResetPasswordLinkToken($customer, $newLinkToken);
      $this->sendEmailConfirmation($customer, $redirectUrl);

      return $customer;
    }

    /**
     * Make sure that password complies with minimum security requirements.
     *
     * @param string $password
     * @return void
     * @throws InputException
     */
    protected function checkPasswordStrength($password)
    {
        $length = $this->stringHelper->strlen($password);
        if ($length > self::MAX_PASSWORD_LENGTH) {
            throw new InputException(
                __(
                    'Please enter a password with at most %1 characters.',
                    self::MAX_PASSWORD_LENGTH
                )
            );
        }
        $configMinPasswordLength = $this->getMinPasswordLength();
        if ($length < $configMinPasswordLength) {
            throw new InputException(
                __(
                    'The password needs at least %1 characters. Create a new password and try again.',
                    $configMinPasswordLength
                )
            );
        }
        if ($this->stringHelper->strlen(trim($password)) != $length) {
            throw new InputException(
                __("The password can't begin or end with a space. Verify the password and try again.")
            );
        }

        $requiredCharactersCheck = $this->makeRequiredCharactersCheck($password);
        if ($requiredCharactersCheck !== 0) {
            throw new InputException(
                __(
                    'Minimum of different classes of characters in password is %1.' .
                    ' Classes of characters: Lower Case, Upper Case, Digits, Special Characters.',
                    $requiredCharactersCheck
                )
            );
        }
    }

    /**
     * Retrieve minimum password length
     *
     * @return int
     */
    protected function getMinPasswordLength()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_MINIMUM_PASSWORD_LENGTH);
    }

    /**
     * Check password for presence of required character sets
     *
     * @param string $password
     * @return int
     */
    protected function makeRequiredCharactersCheck($password)
    {
        $counter = 0;
        $requiredNumber = $this->scopeConfig->getValue(self::XML_PATH_REQUIRED_CHARACTER_CLASSES_NUMBER);
        $return = 0;

        if (preg_match('/[0-9]+/', $password)) {
            $counter++;
        }
        if (preg_match('/[A-Z]+/', $password)) {
            $counter++;
        }
        if (preg_match('/[a-z]+/', $password)) {
            $counter++;
        }
        if (preg_match('/[^a-zA-Z0-9]+/', $password)) {
            $counter++;
        }

        if ($counter < $requiredNumber) {
            $return = $requiredNumber;
        }

        return $return;
    }

    /**
     * Create a hash for the given password
     *
     * @param string $password
     * @return string
     */
    protected function createPasswordHash($password)
    {
        return $this->encryptor->getHash($password, true);
    }

    /**
     * Send either confirmation or welcome email after an account creation
     *
     * @param CustomerInterface $customer
     * @param string $redirectUrl
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function sendEmailConfirmation(CustomerInterface $customer, $redirectUrl)
    {
        try {
            $hash = $this->customerRegistry->retrieveSecureData($customer->getId())->getPasswordHash();
            $templateType = self::NEW_ACCOUNT_EMAIL_REGISTERED;

            $this->getEmailNotification()->newAccount($customer, $templateType, $redirectUrl, $customer->getStoreId());
            $customer->setConfirmation(null);
        } catch (MailException $e) {
            // If we are not able to send a new account email, this should be ignored
            // $this->logger->critical($e);
        } catch (\UnexpectedValueException $e) {
            // $this->logger->error($e);
        }
    }

    /**
     * Get email notification
     *
     * @return EmailNotificationInterface
     * @deprecated 100.1.0
     */
    private function getEmailNotification()
    {
        if (!($this->emailNotification instanceof EmailNotificationInterface)) {
            return \Magento\Framework\App\ObjectManager::getInstance()->get(
                EmailNotificationInterface::class
            );
        } else {
            return $this->emailNotification;
        }
    }
}
