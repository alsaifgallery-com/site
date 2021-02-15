<?php


namespace AlsaifGallery\ForgotPassword\Model\ResourceModel;

class ForgotPassword extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('alsaifgallery_forgotpassword_forgotpassword', 'forgotpassword_id');
    }
}
