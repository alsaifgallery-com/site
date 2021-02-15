<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xcoupon
 */

namespace Amasty\Xcoupon\Model;

/**
 * Class Massgenerator
 * @package Amasty\Xcoupon\Model
 * @author Artem Brunevski
 */
class Massgenerator extends \Magento\SalesRule\Model\Coupon\Massgenerator implements
    \Magento\SalesRule\Model\Coupon\CodegeneratorInterface
{
    protected $alphabet = array();
    protected $counts   = array();
    protected $pos      = array();

    protected $pattern = null;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\SalesRule\Helper\Coupon $salesRuleCoupon
     * @param \Magento\SalesRule\Model\CouponFactory $couponFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\SalesRule\Helper\Coupon $salesRuleCoupon,
        \Magento\SalesRule\Model\CouponFactory $couponFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        // no "0" and "1" as they are confusing
        $this->alphabet['D']  = array(2,3,4,5,6,7,8,9);
        $this->counts['D']  = count($this->alphabet['D']);
        // no I, Q and O as they are confusing
        $this->alphabet['L']  = array('A','B','C','D','E','F','G','H','J','K','L','M','N','P','R','S','T','U','V','W','X');
        $this->counts['L'] = count($this->alphabet['L']);

        $this->pos['L'] =  $this->pos['D'] = 0;
        parent::__construct($context, $registry, $salesRuleCoupon, $couponFactory, $date, $dateTime, $resource, $resourceCollection, $data); // TODO: Change the autogenerated stub
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function generateCode()
    {
        $code = '';
        if ($this->getPattern() !== null){
            // we call rand() per one code, not per one letter
            // as it is a bit faster
            shuffle($this->alphabet['D']);
            shuffle($this->alphabet['L']);

            $pattern = str_split($this->getPattern());
            foreach ($pattern as $i){
                if (empty($this->alphabet[$i])){
                    $code .= $i;
                }
                else {
                    $code .= $this->alphabet[$i][$this->pos[$i]];
                    $this->pos[$i] = ($this->pos[$i]+1) % $this->counts[$i];
//                    $code .= $this->alphabet[$i][rand(0, $this->counts[$i]-1)];
                }
            }
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(__('The pattern must be set up.'));
        }

        return $code;
    }

    public function increaseLength()
    {
        return null;
    }

    /**
     * Validate data input
     *
     * @param array $data
     * @return bool
     */
    public function validateData($data)
    {
        return !empty($data)
        && !empty($data['qty'])
        && !empty($data['rule_id'])
        && !empty($data['pattern']);
    }

    public function clean()
    {
        if ($this->getRuleId()){
            $connection = $this->getResource()->getConnection();
            $tableName = $this->getResource()->getTable('salesrule_coupon');
            $connection->delete($tableName, ['rule_id = ?' => $this->getRuleId()]);
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(__('The rule must be set up.'));
        }

    }
}