<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace AlsaifGallery\AutoUpdateCartAjax\Api;

interface UpdateCartManagementInterface
{

  /**
   *
   * @param string $cartId
   * @param string $itemId
   * @param int $qty
   * @return \Magento\Quote\Api\Data\CartSearchResultsInterface
   */
    public function postUpdateCart(
      $cartId,
      $itemId ,
      $qty
    );
}
