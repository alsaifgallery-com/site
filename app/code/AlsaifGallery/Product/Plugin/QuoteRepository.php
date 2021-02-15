<?php

namespace AlsaifGallery\Product\Plugin;

use Magento\Quote\Model\Quote;

class QuoteRepository
{

     public function afterGet(
        \Magento\Quote\Api\CartRepositoryInterface $subject,
         $resault,
        $cartId
    ) {
        return $resault;
    }

}
