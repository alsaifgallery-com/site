<?php


namespace AlsaifGallery\ForgotPassword\Model\Data;

use AlsaifGallery\ForgotPassword\Api\Data\RequestVerifyCustomerInterface;

class RequestVerifyCustomer implements RequestVerifyCustomerInterface
{

    protected $dataArray=[];

    public function getIdentity() {
        return $this->dataArray[self::IDENTITY];
    }

    public function getType() {
        return $this->dataArray[self::TYPE];
    }

    public function setIdentity($identity) {
        $this->dataArray[self::IDENTITY]=$identity;
        return $this; 
    }

    public function setType($type) {
        $this->dataArray[self::TYPE] =$type;
        return $this;
    }

}
