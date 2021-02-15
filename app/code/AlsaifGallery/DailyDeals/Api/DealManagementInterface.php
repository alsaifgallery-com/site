<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AlsaifGallery\DailyDeals\Api;

/**
 *
 * @author nada
 */
interface DealManagementInterface {
      /**
     * GET for DailyDeals api
     * @param int $productId
     * @return Mageplaza\DailyDeal\Model\Deal
     */
 public function getProductDeal($productId);
}
