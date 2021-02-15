<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace AlsaifGallery\MSCE\Api;

interface CreateCustomerManagementInterface
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
   * Create customer account. Perform necessary business operations like sending email.
   *
   * @param \Magento\Customer\Api\Data\CustomerInterface $customer
   * @param string $password
   * @param string $redirectUrl
   * @return \Magento\Customer\Api\Data\CustomerInterface
   * @throws \Magento\Framework\Exception\LocalizedException
   */
    public function postCreateCustomer(
      \Magento\Customer\Api\Data\CustomerInterface $customer,
      $password = null,
      $redirectUrl = ''
    );
}
