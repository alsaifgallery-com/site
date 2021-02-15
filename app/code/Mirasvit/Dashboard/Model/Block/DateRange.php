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
 * @package   mirasvit/module-dashboard
 * @version   1.2.41
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Dashboard\Model\Block;

use Magento\Framework\DataObject;

class DateRange extends DataObject
{
    public function isOverride()
    {
        return $this->getData('override') ? true : false;
    }

    public function getRange()
    {
        return $this->getData('range');
    }
}
