<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


declare(strict_types=1);

namespace Amasty\Sorting\Plugin\Catalog\Model\ResourceModel\Product\Collection\ProductLimitation;

use Amasty\Sorting\Model\Elasticsearch\ApplierFlag;
use Magento\Catalog\Model\ResourceModel\Product\Collection\ProductLimitation;

class PreventPriceSortingOnMysql
{
    /**
     * @var ApplierFlag
     */
    private $applierFlag;

    public function __construct(ApplierFlag $applierFlag)
    {
        $this->applierFlag = $applierFlag;
    }

    public function afterIsUsingPriceIndex(ProductLimitation $subject, bool $result): bool
    {
        if ($this->applierFlag->get()) {
            $result = false;
        }

        return $result;
    }
}
