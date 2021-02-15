<?php


namespace AlsaifGallery\StoreCredit\Api;
use Amasty\StoreCredit\Api\Data\StoreCreditInterface;
interface EWalletManagementInterface
{

    /**
     * GET for EWallet api
     * @param int $customerId
     * @return \Amasty\StoreCredit\Api\Data\StoreCreditInterface
     * 
     */
    public function getEWallet($customerId);
    
    
    /**
     * GET for EWallet api
     * @param int $customerId
     * @return string[]
     */
    public function getEWalletHistory($customerId);
}
