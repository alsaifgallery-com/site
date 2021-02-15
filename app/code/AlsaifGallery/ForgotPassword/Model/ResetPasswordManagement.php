<?php


namespace AlsaifGallery\ForgotPassword\Model;



use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\ValidationResultsInterfaceFactory;
use Magento\Customer\Helper\View as CustomerViewHelper;
use Magento\Customer\Model\Config\Share as ConfigShare;
use Magento\Customer\Model\Customer as CustomerModel;
use Magento\Customer\Model\Customer\CredentialsValidator;
use Magento\Customer\Model\Metadata\Validator;
use Magento\Customer\Model\ResourceModel\Visitor\CollectionFactory;
use Magento\Eav\Model\Validator\Attribute\Backend;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObjectFactory as ObjectFactory;
use Magento\Framework\Encryption\EncryptorInterface as Encryptor;
use Magento\Framework\Encryption\Helper\Security;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\ExpiredException;
use Magento\Framework\Exception\State\InputMismatchException;
use Magento\Framework\Exception\State\InvalidTransitionException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Framework\Intl\DateTimeFactory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Math\Random;
use Magento\Framework\Phrase;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Registry;
use Magento\Framework\Session\SaveHandlerInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\StringUtils as StringHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface as PsrLogger;


class ResetPasswordManagement 
                              implements \AlsaifGallery\ForgotPassword\Api\ResetPasswordManagementInterface
{
    
        /**#@+
     * Constant for confirmation status
     */
    const ACCOUNT_CONFIRMED = 'account_confirmed';
    const ACCOUNT_CONFIRMATION_REQUIRED = 'account_confirmation_required';
    const ACCOUNT_CONFIRMATION_NOT_REQUIRED = 'account_confirmation_not_required';
    const MAX_PASSWORD_LENGTH = 256;
    /**#@-*/
    
    
        /**
     * Configuration paths for email templates and identities
     *
     * @deprecated
     */
    const XML_PATH_REGISTER_EMAIL_TEMPLATE = 'customer/create_account/email_template';

    /**
     * @deprecated
     */
    const XML_PATH_REGISTER_NO_PASSWORD_EMAIL_TEMPLATE = 'customer/create_account/email_no_password_template';

    /**
     * @deprecated
     */
    const XML_PATH_REGISTER_EMAIL_IDENTITY = 'customer/create_account/email_identity';

    /**
     * @deprecated
     */
    const XML_PATH_REMIND_EMAIL_TEMPLATE = 'customer/password/remind_email_template';

    /**
     * @deprecated
     */
    const XML_PATH_FORGOT_EMAIL_TEMPLATE = 'customer/password/forgot_email_template';

    /**
     * @deprecated
     */
    const XML_PATH_FORGOT_EMAIL_IDENTITY = 'customer/password/forgot_email_identity';

    /**
     * @deprecated
     * @see AccountConfirmation::XML_PATH_IS_CONFIRM
     */
    const XML_PATH_IS_CONFIRM = 'customer/create_account/confirm';

    /**
     * @deprecated
     */
    const XML_PATH_CONFIRM_EMAIL_TEMPLATE = 'customer/create_account/email_confirmation_template';

    /**
     * @deprecated
     */
    const XML_PATH_CONFIRMED_EMAIL_TEMPLATE = 'customer/create_account/email_confirmed_template';

    /**
     * Constants for the type of new account email to be sent
     *
     * @deprecated
     */
    const NEW_ACCOUNT_EMAIL_REGISTERED = 'registered';

    /**
     * Welcome email, when password setting is required
     *
     * @deprecated
     */
    const NEW_ACCOUNT_EMAIL_REGISTERED_NO_PASSWORD = 'registered_no_password';

    /**
     * Welcome email, when confirmation is enabled
     *
     * @deprecated
     */
    const NEW_ACCOUNT_EMAIL_CONFIRMATION = 'confirmation';

    /**
     * Confirmation email, when account is confirmed
     *
     * @deprecated
     */
    const NEW_ACCOUNT_EMAIL_CONFIRMED = 'confirmed';

    /**
     * Constants for types of emails to send out.
     * pdl:
     * forgot, remind, reset email templates
     */
    const EMAIL_REMINDER = 'email_reminder';

    const EMAIL_RESET = 'email_reset';

    /**
     * Configuration path to customer password minimum length
     */
    const XML_PATH_MINIMUM_PASSWORD_LENGTH = 'customer/password/minimum_password_length';

    /**
     * Configuration path to customer password required character classes number
     */
    const XML_PATH_REQUIRED_CHARACTER_CLASSES_NUMBER = 'customer/password/required_character_classes_number';

    /**
     * @deprecated
     */
    const XML_PATH_RESET_PASSWORD_TEMPLATE = 'customer/password/reset_password_template';

    /**
     * @deprecated
     */
    const MIN_PASSWORD_LENGTH = 6;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    private $customerFactory;

    /**
     * @var \Magento\Customer\Api\Data\ValidationResultsInterfaceFactory
     */
    private $validationResultsDataFactory;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Random
     */
    private $mathRandom;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var CustomerMetadataInterface
     */
    private $customerMetadataService;

    /**
     * @var PsrLogger
     */
    protected $logger;

    /**
     * @var Encryptor
     */
    private $encryptor;

    /**
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var ConfigShare
     */
    private $configShare;

    /**
     * @var StringHelper
     */
    protected $stringHelper;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;

    /**
     * @var SaveHandlerInterface
     */
    private $saveHandler;

    /**
     * @var CollectionFactory
     */
    private $visitorCollectionFactory;

    /**
     * @var DataObjectProcessor
     */
    protected $dataProcessor;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var CustomerViewHelper
     */
    protected $customerViewHelper;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var ObjectFactory
     */
    protected $objectFactory;

    /**
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @var CustomerModel
     */
    protected $customerModel;

    /**
     * @var AuthenticationInterface
     */
    protected $authentication;

    /**
     * @var EmailNotificationInterface
     */
    private $emailNotification;

    /**
     * @var \Magento\Eav\Model\Validator\Attribute\Backend
     */
    private $eavValidator;

    /**
     * @var CredentialsValidator
     */
    private $credentialsValidator;

    /**
     * @var DateTimeFactory
     */
    private $dateTimeFactory;

    /**
     * @var  \Magento\Customer\Model\AccountConfirmation 
     */
    private $accountConfirmation;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var AddressRegistry
     */
    private $addressRegistry;
    
    
    protected $forgotPassRepo;
    protected $forgotPassRecordFactory;
    
    
/**
 * 
 * @param \Magento\Customer\Model\CustomerFactory $customerFactory
 * @param ManagerInterface $eventManager
 * @param StoreManagerInterface $storeManager
 * @param Random $mathRandom
 * @param Validator $validator
 * @param ValidationResultsInterfaceFactory $validationResultsDataFactory
 * @param AddressRepositoryInterface $addressRepository
 * @param CustomerMetadataInterface $customerMetadataService
 * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
 * @param PsrLogger $logger
 * @param Encryptor $encryptor
 * @param ConfigShare $configShare
 * @param StringHelper $stringHelper
 * @param CustomerRepositoryInterface $customerRepository
 * @param ScopeConfigInterface $scopeConfig
 * @param TransportBuilder $transportBuilder
 * @param DataObjectProcessor $dataProcessor
 * @param Registry $registry
 * @param CustomerViewHelper $customerViewHelper
 * @param DateTime $dateTime
 * @param CustomerModel $customerModel
 * @param ObjectFactory $objectFactory
 * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
 * @param CredentialsValidator $credentialsValidator
 * @param DateTimeFactory $dateTimeFactory
 * @param \Magento\Customer\Model\AccountConfirmation $accountConfirmation
 * @param SessionManagerInterface $sessionManager
 * @param SaveHandlerInterface $saveHandler
 * @param CollectionFactory $visitorCollectionFactory
 * @param SearchCriteriaBuilder $searchCriteriaBuilder
 * @param \Magento\Customer\Model\AddressRegistry $addressRegistry
 * @param \AlsaifGallery\ForgotPassword\Api\ForgotPasswordRepositoryInterface $forgotPassRepo
 * @param \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterfaceFactory $forgotPassRecordFactory
 */
    
     public function __construct(
             
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        Random $mathRandom,
        Validator $validator,
        ValidationResultsInterfaceFactory $validationResultsDataFactory,
        AddressRepositoryInterface $addressRepository,
        CustomerMetadataInterface $customerMetadataService,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        PsrLogger $logger,
        Encryptor $encryptor,
        ConfigShare $configShare,
        StringHelper $stringHelper,
        CustomerRepositoryInterface $customerRepository,
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        DataObjectProcessor $dataProcessor,
        Registry $registry,
        CustomerViewHelper $customerViewHelper,
        DateTime $dateTime,
        CustomerModel $customerModel,
        ObjectFactory $objectFactory,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        CredentialsValidator $credentialsValidator = null,
        DateTimeFactory $dateTimeFactory = null,
        \Magento\Customer\Model\AccountConfirmation $accountConfirmation = null,
        SessionManagerInterface $sessionManager = null,
        SaveHandlerInterface $saveHandler = null,
        CollectionFactory $visitorCollectionFactory = null,
        SearchCriteriaBuilder $searchCriteriaBuilder = null,
        \Magento\Customer\Model\AddressRegistry $addressRegistry = null,
             
        \AlsaifGallery\ForgotPassword\Api\ForgotPasswordRepositoryInterface $forgotPassRepo,
        \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterfaceFactory $forgotPassRecordFactory
             
            
              
    ){
         
        $this->customerFactory = $customerFactory;
        $this->eventManager = $eventManager;
        $this->storeManager = $storeManager;
        $this->mathRandom = $mathRandom;
        $this->validator = $validator;
        $this->validationResultsDataFactory = $validationResultsDataFactory;
        $this->addressRepository = $addressRepository;
        $this->customerMetadataService = $customerMetadataService;
        $this->customerRegistry = $customerRegistry;
        $this->logger = $logger;
        $this->encryptor = $encryptor;
        $this->configShare = $configShare;
        $this->stringHelper = $stringHelper;
        $this->customerRepository = $customerRepository;
        $this->scopeConfig = $scopeConfig;
        $this->transportBuilder = $transportBuilder;
        $this->dataProcessor = $dataProcessor;
        $this->registry = $registry;
        $this->customerViewHelper = $customerViewHelper;
        $this->dateTime = $dateTime;
        $this->customerModel = $customerModel;
        $this->objectFactory = $objectFactory;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->credentialsValidator =
            $credentialsValidator ?: ObjectManager::getInstance()->get(CredentialsValidator::class);
        $this->dateTimeFactory = $dateTimeFactory ?: ObjectManager::getInstance()->get(DateTimeFactory::class);
        $this->accountConfirmation = $accountConfirmation ?: ObjectManager::getInstance()
            ->get( \Magento\Customer\Model\AccountConfirmation::class );
        $this->sessionManager = $sessionManager
            ?: ObjectManager::getInstance()->get(SessionManagerInterface::class);
        $this->saveHandler = $saveHandler
            ?: ObjectManager::getInstance()->get(SaveHandlerInterface::class);
        $this->visitorCollectionFactory = $visitorCollectionFactory
            ?: ObjectManager::getInstance()->get(CollectionFactory::class);
        $this->searchCriteriaBuilder = $searchCriteriaBuilder
            ?: ObjectManager::getInstance()->get(SearchCriteriaBuilder::class);
        $this->addressRegistry = $addressRegistry
            ?: ObjectManager::getInstance()->get(\Magento\Customer\Model\AddressRegistry::class);
        
       $this->forgotPassRepo = $forgotPassRepo;
       $this->forgotPassRecordFactory = $forgotPassRecordFactory;
   
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
     * Retrieve minimum password length
     *
     * @return int
     */
    protected function getMinPasswordLength()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_MINIMUM_PASSWORD_LENGTH);
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
     * Destroy all active customer sessions by customer id (current session will not be destroyed).
     *
     * Customer sessions which should be deleted are collecting from the "customer_visitor" table considering
     * configured session lifetime.
     *
     * @param string|int $customerId
     * @return void
     */
    private function destroyCustomerSessions($customerId)
    {
        $sessionLifetime = $this->scopeConfig->getValue(
            \Magento\Framework\Session\Config::XML_PATH_COOKIE_LIFETIME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $dateTime = $this->dateTimeFactory->create();
        $activeSessionsTime = $dateTime->setTimestamp($dateTime->getTimestamp() - $sessionLifetime)
            ->format(DateTime::DATETIME_PHP_FORMAT);
        /** @var \Magento\Customer\Model\ResourceModel\Visitor\Collection $visitorCollection */
        $visitorCollection = $this->visitorCollectionFactory->create();
        $visitorCollection->addFieldToFilter('customer_id', $customerId);
        $visitorCollection->addFieldToFilter('last_visit_at', ['from' => $activeSessionsTime]);
        $visitorCollection->addFieldToFilter('session_id', ['neq' => $this->sessionManager->getSessionId()]);
        /** @var \Magento\Customer\Model\Visitor $visitor */
        foreach ($visitorCollection->getItems() as $visitor) {
            $sessionId = $visitor->getSessionId();
            $this->saveHandler->destroy($sessionId);
        }
    }
    
        /**
     * Disable Customer Address Validation
     *
     * @param CustomerInterface $customer
     * @throws NoSuchEntityException
     */
    private function disableAddressValidation($customer)
    {
        foreach ($customer->getAddresses() as $address) {
            $addressModel = $this->addressRegistry->retrieve($address->getId());
            $addressModel->setShouldIgnoreValidation(true);
        }
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
     * {@inheritdoc}
     */
    public function postResetPassword ($token,$password)
    {
        $forgotpasswordToken =  $token ;
        try{
            $forgotpasswordRecord = $this->forgotPassRepo->getByToken($forgotpasswordToken);
        } catch (\Exception $e){
            throw new \Magento\Framework\Exception\LocalizedException(
                 __( "Error recod data  did not exist.")
            );
        }
        
        if($forgotpasswordRecord->getForgotpasswordId()){
            if( $forgotpasswordRecord->getSendStatus() != "1"){
                // return "Error recode data  did not sent";
               throw new \Magento\Framework\Exception\LocalizedException(
                    __( "Error recod data  did not sent")
                );
            }
            // var_dump( $forgotpasswordRecord->getRequestTokenStatus() );die;
            if( $forgotpasswordRecord->getRequestTokenStatus() == "1" ){
                // return "Error recode data  did not sent";
                throw new \Magento\Framework\Exception\LocalizedException(
                    __( "The password already reseted")
                );
            }
            
            $requestNumber = $forgotpasswordRecord->getRequestNumber();
            if( $requestNumber > 2 ){
                // return "Error recode data  did not sent";
                throw new \Magento\Framework\Exception\LocalizedException(
                    __( "This request was call too many time.")
                );
            }
            
            $returnObj2 = new \Magento\Framework\DataObject([
                'forgotpassword_request_token'=>$forgotpasswordRecord->getRequestToken(),
            ]);
                   
            $forgotpasswordRecord ->setRequestTokenStatus(true);
            $forgotpasswordRecord = $this->forgotPassRepo->save($forgotpasswordRecord);
            // return [$return];
            $newPassword = $password;
            $customer = $forgotpasswordRecord->getCustomer();
            $customerEmail = $customer->getEmail();
            $this->credentialsValidator->checkPasswordDifferentFromEmail($customerEmail, $newPassword);
            $customerSecure = $this->customerRegistry->retrieveSecureData($customer->getId());
            $customerSecure->setRpToken(null);
            $customerSecure->setRpTokenCreatedAt(null);
            $this->checkPasswordStrength($newPassword);
            $customerSecure->setPasswordHash($this->createPasswordHash($newPassword));
            $this->destroyCustomerSessions($customer->getId());
            $this->disableAddressValidation($customer);
            $this->customerRepository->save($customer);
            
            $forgotpasswordRecord->SetRequestNumber( $requestNumber+1 );
            $this->forgotPassRepo->save($forgotpasswordRecord);
                        
            // return true;
            $ret=[
                "status"  => true,
                "message" => __("Password was reseted successfully")->jsonSerialize(),
            ];
            return [$ret];
        
            
            // return  $returnObj2->toJson();
        }
//        return 'Record With $code did not exist.' ;
        return[
                "status"  => false ,
                "message" => __("Record With this token did not exist.")->jsonSerialize(),
        ];
    }
}
