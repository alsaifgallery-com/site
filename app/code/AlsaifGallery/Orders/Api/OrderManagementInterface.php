<?php


namespace AlsaifGallery\Orders\Api;

interface OrderManagementInterface
{

    /**
     * GET for Order api
     * @param int $orderId
     * @return string[]|\Magento\Framework\DataObject
     */
    public function getOrder($orderId);
    
     /**
     * GET for Order api
     * @param int $customerId
     * @return string[]
     */
    public function getListOrders($customerId);
    
    /**
     * POST for Order api
     * @param int $orderId
     * @param int $review
     * @return string[]
     */
    public function addOrderReview($orderId,$review);
}
