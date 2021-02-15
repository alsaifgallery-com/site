<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace AlsaifGallery\AutoUpdateCartAjax\Model;

class UpdateCartManagement implements \AlsaifGallery\AutoUpdateCartAjax\Api\UpdateCartManagementInterface
{

    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdateCart($cartId, $itemId ,$qty)
    {
        $itemQty = $qty;

        $quote = $this->quoteRepository->getActive($cartId);
        $cartitems = $cart->getQuote()->getAllItems();
        $cartitems->setquoteId($cartId);
        $cartitems->setitemId($itemId);
        $cartitems->setqty($itemQty);

        $quoteItems[] = $cartitems;
        $quote->setItems($quoteItems);
        $this->quoteRepository->save($quote);
        $quote->collectTotals();
        return $quote->getList($cartId);
    }
}
