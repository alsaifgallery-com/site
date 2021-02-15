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
 * @package     Mageplaza_Faqs
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Faqs\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class HelpfulRate
 * @package Mageplaza\Faqs\Ui\Component\Listing\Columns
 */
class HelpfulRate extends Column
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$this->getData('name')])) {
                    $actionNum                    = (int) $item['positives'] + (int) $item['negatives'];
                    $rate                         = ($actionNum == 0) ? 0 : round((int) $item['positives'] / $actionNum, 2) * 100;
                    $content                      = '<span>'
                                                    . $rate
                                                    . '% (<strong style="color: #24b613">'
                                                    . (int) $item['positives']
                                                    . '</strong><strong style="color:red"> /'
                                                    . (int) $item['negatives']
                                                    . '</strong><strong> /'
                                                    . $actionNum
                                                    . '</strong>)</span>';
                    $item[$this->getData('name')] = $content;
                }
            }
        }

        return $dataSource;
    }
}
