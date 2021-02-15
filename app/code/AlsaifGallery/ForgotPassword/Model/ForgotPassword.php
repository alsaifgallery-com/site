<?php


namespace AlsaifGallery\ForgotPassword\Model;

use AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface;

class ForgotPassword extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $_eventPrefix = 'alsaifgallery_forgotpassword_forgotpassword';
    protected $forgotpasswordDataFactory;


    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ForgotPasswordInterfaceFactory $forgotpasswordDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \AlsaifGallery\ForgotPassword\Model\ResourceModel\ForgotPassword $resource
     * @param \AlsaifGallery\ForgotPassword\Model\ResourceModel\ForgotPassword\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ForgotPasswordInterfaceFactory $forgotpasswordDataFactory,
        DataObjectHelper $dataObjectHelper,
        \AlsaifGallery\ForgotPassword\Model\ResourceModel\ForgotPassword $resource,
        \AlsaifGallery\ForgotPassword\Model\ResourceModel\ForgotPassword\Collection $resourceCollection,
        array $data = []
    ) {
        $this->forgotpasswordDataFactory = $forgotpasswordDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve forgotpassword model with forgotpassword data
     * @return ForgotPasswordInterface
     */
    public function getDataModel()
    {
        $forgotpasswordData = $this->getData();
        
        $forgotpasswordDataObject = $this->forgotpasswordDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $forgotpasswordDataObject,
            $forgotpasswordData,
            ForgotPasswordInterface::class
        );
        
        return $forgotpasswordDataObject;
    }
}
