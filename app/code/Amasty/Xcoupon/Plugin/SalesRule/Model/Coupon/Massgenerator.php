<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xcoupon
 */

namespace Amasty\Xcoupon\Plugin\SalesRule\Model\Coupon;

/**
 * Class Massgenerator
 * @package Amasty\Xcoupon\Plugin\SalesRule\Model\Coupon
 * @author Artem Brunevski
 */
class Massgenerator extends \Magento\Framework\DataObject
{
    /** @var \Amasty\Xcoupon\Model\Import  */
    protected $import;

    /**
     * @param \Amasty\Xcoupon\Model\Import $import
     * @param array $data
     */
    public function __construct(
        \Amasty\Xcoupon\Model\Import $import,
        array $data = []
    ){
        $this->import = $import;
        parent::__construct($data);
    }


    public function beforeGeneratePool(
        \Magento\SalesRule\Model\Coupon\CodegeneratorInterface $subject
    ){
        $deleteExistingCoupons = $subject->getData('amasty_xcoupon_delete_existing_coupons');
        if ($deleteExistingCoupons === '1'){
            $this->import->clean($subject->getData('rule_id'));
        }
        return [];
    }
}