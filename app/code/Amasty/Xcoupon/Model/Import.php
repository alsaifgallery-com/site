<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xcoupon
 */

namespace Amasty\Xcoupon\Model;

class Import extends \Magento\Framework\DataObject
{
    /** @var \Magento\Framework\App\ResourceConnection  */
    protected $resource;

    /** @var \Magento\Framework\DB\Adapter\AdapterInterface  */
    protected $connection;

    protected $columns = [
        'code' => 'Coupon Code',
        'created_at' => 'Created',
        'times_used' => 'Times Used',
        'usage_per_customer' => 'Uses per Customer',
        'usage_limit' => 'Uses per Coupon',
        'expiration_date' => 'Expiration date'
    ];

    protected $required = [
        'code'
    ];

    protected $positions = [
        'code' => null,
        'created_at' => null,
        'times_used' => null,
        'usage_per_customer' => null,
        'usage_limit' => null,
        'expiration_date' => null
    ];

    /**
     * Constructor
     *
     * By default is looking for first argument as array and assigns it as object attributes
     * This behavior may change in child classes
     *
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
    ) {
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        parent::__construct($data);
    }

    /**
     * @param $ruleId
     */
    public function clean($ruleId)
    {
        $tableName = $this->resource->getTableName('salesrule_coupon');
        $this->connection->delete($tableName, ['rule_id = ?' => $ruleId]);
    }

    /**
     * @param \Magento\Framework\Filesystem\File\WriteInterface $file
     * @param int $ruleId
     */
    public function run(\Magento\Framework\Filesystem\File\WriteInterface $file, $ruleId)
    {
        $tableName = $this->resource->getTableName('salesrule_coupon');

        $rowNum = 0;
        $rows = [];
        while (($csvLine = $file->readCsv()) !== false) {
            if ($rowNum === 0) {
                $this->init($csvLine);
            } else {
                $rows[] = $this->buildRow($csvLine, $ruleId);

                if (count($rows) % 5 === 0) {
                    $this->connection->insertOnDuplicate(
                        $tableName,
                        $rows,
                        array_keys($this->columns)
                    );
                    $rows = [];
                }
            }

            $rowNum++;
        }

        if (count($rows) > 0) {
            $this->connection->insertOnDuplicate(
                $tableName,
                $rows,
                array_keys($this->columns)
            );
        }
    }

    /**
     * @param $csvLine
     * @param $ruleId
     * @return array
     */
    protected function buildRow($csvLine, $ruleId)
    {
        $row = [
            'rule_id' => $ruleId,
            'type' => 1
        ];

        foreach ($this->positions as $column => $position) {
            if ($position !== null) {
                $value = array_key_exists($position, $csvLine) ? $csvLine[$position] : null;

                switch ($column) {
                    case "created_at":
                    case "expiration_date":
                        $value = $value ? date('Y-m-d H:i:s', strtotime($value)) : $value;
                        break;
                }

                $row[$column] = $value;
            }
        }

        if (!array_key_exists('created_at', $row)
            || $row['created_at'] === null
            || $row['created_at'] === ''
        ) {
            $row['created_at'] = date('Y-m-d H:i:s', time());
        }

        if (!array_key_exists('times_used', $row)
            || $row['times_used'] === null
            || $row['times_used'] === ''
        ) {
            $row['times_used'] = 0;
        }

        return $row;
    }

    /**
     * @param $firstRow
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function init($firstRow)
    {
        foreach ($firstRow as $position => $column) {
            $column = trim($column);
            $code = array_search($column, $this->columns);
            if ($code != false) {
                $this->positions[$code] = $position;
            }
        }

        foreach ($this->required as $column) {
            if ($this->positions[$column] === null) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The "%1" must be completed', $this->columns[$column])
                );
            }
        }

        return $this->positions;
    }
}
