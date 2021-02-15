<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace AlsaifGallery\TrackingInfo\Model;

class TrackingInfoManagement implements \AlsaifGallery\TrackingInfo\Api\TrackingInfoManagementInterface
{

    /**
     * {@inheritdoc}
     */
    public function getTrackingInfo($id)
    {
      $link = "";
      $orderid = $id;
      $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
      $order = $objectManager->create('Magento\Sales\Model\Order')->load($orderid);
      $tracksCollection = $order->getTracksCollection();
      foreach ($tracksCollection->getItems() as $track) {
        if (preg_match("/{$track->getTitle()}/i", 'aramex')) {
          $link = "http://www.aramex.com/track/results?ShipmentNumber=";
        }
        $trackNumbers[] = [
            "Tracking number" => $track->getTrackNumber(),
            "Carrier name" => $track->getTitle(),
            "Tracking link" => $link
          ];
      }

      return $trackNumbers;
    }
}
