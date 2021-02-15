<?php


namespace AlsaifGallery\ForgotPassword\Model\ResourceModel\ForgotPassword;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \AlsaifGallery\ForgotPassword\Model\ForgotPassword::class,
            \AlsaifGallery\ForgotPassword\Model\ResourceModel\ForgotPassword::class
        );
    }
}
