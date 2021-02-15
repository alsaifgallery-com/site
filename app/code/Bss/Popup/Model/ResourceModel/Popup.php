<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_Popup
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\Popup\Model\ResourceModel;

class Popup extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Date model
     *
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $dateTime;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    ) {
        $this->dateTime = $date;
        parent::__construct($context);
    }


    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('bss_popup_popup', 'popup_id');
    }

    /**
     * Retrieves Popup from DB by position.
     *
     * @return array
     */
    public function getPopupByDate()
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable())
            ->where('status = 1');
        return $adapter->fetchAssoc($select);
    }

    /**
     * Before save callback
     *
     * @param \Magento\Framework\Model\AbstractModel|\Bss\Popup\Model\Popup $object
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $object->setUpdatedAt($this->dateTime->date());
        if ($object->isObjectNew()) {
            $object->setCreatedAt($this->dateTime->date());
        }
        return parent::_beforeSave($object);
    }
}
