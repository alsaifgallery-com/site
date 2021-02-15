<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xcoupon
 */


namespace Amasty\Xcoupon\Model\Coupon;

use Amasty\Xcoupon\Controller\Adminhtml\Sales\Rule\EditCoupon;

/**
 * Class DataProvider
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\SalesRule\Model\ResourceModel\Coupon\CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $model = $this->coreRegistry->registry(EditCoupon::CURRENT_COUPON_MODEL);
        if ($model) {
            $this->loadedData[ $model->getCouponId()] = $model->getData();
        }

        return $this->loadedData;
    }
}
