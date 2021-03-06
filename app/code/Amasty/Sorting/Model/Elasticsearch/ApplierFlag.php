<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


declare(strict_types=1);

namespace Amasty\Sorting\Model\Elasticsearch;

class ApplierFlag
{
    /**
     * @var bool
     */
    private $flag = false;

    public function enable(): void
    {
        $this->flag = true;
    }

    public function disable(): void
    {
        $this->flag = false;
    }

    public function get(): bool
    {
        return $this->flag;
    }
}
