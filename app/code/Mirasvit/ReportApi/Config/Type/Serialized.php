<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-report-api
 * @version   1.0.26
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\ReportApi\Config\Type;

use Mirasvit\ReportApi\Api\Config\AggregatorInterface;

class Serialized extends Str
{
    public function getType()
    {
        return 'serialized';
    }

    public function getFormattedValue($actualValue, AggregatorInterface $aggregator)
    {
        if ($actualValue === null) {
            return __('N/A');
        }

        try {
            $data        = \Zend_Json::decode($actualValue);
            $actualValue = $data[min(array_keys($data))];
        } catch (\Exception $e) {

        }

        return $actualValue;
    }
}
