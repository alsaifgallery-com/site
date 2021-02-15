<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_SeoDashboard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\SeoDashboard\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Mediate
 * @package Mageplaza\SeoDashboard\Model
 */
class Mediate extends AbstractModel implements IdentityInterface
{
    /**
     * Cache
     */
    const CACHE_TAG = 'mp_db_mediate_code';

    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Mageplaza\SeoDashboard\Model\ResourceModel\Mediate');
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @param array $where
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteData($where = [])
    {
        if (!sizeof($where)) {
            $this->getResource()->getConnection()->truncateTable($this->getResource()->getMainTable());
        }

        return $this->getResource()->getConnection()->delete($this->getResource()->getMainTable(), $where);
    }

    /**
     * Insert multiple data
     * @param $data
     * @return mixed
     */
    public function insertMultipleData($data)
    {
        return $this->getResource()->insertMultipleData($data);
    }

    /**
     * @param $field
     * @param $entity
     * @return mixed
     */
    public function getDuplicateCollection($field, $entity)
    {
        return $this->getResource()->getDuplicateCollection($field, $entity);
    }
}