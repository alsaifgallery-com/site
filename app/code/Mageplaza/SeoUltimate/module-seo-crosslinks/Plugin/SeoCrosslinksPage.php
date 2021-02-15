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
 * @package     Mageplaza_SeoCrosslinks
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\SeoCrosslinks\Plugin;

use Magento\Framework\Message\ManagerInterface;
use Mageplaza\SeoCrosslinks\Helper\Data;
use Mageplaza\SeoCrosslinks\Model\Term\Source\ApplyFor;

/**
 * Class SeoCrosslinksPage
 * @package Mageplaza\SeoCrosslinks\Plugin
 */
class SeoCrosslinksPage
{
    /**
     * @var \Mageplaza\SeoCrosslinks\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * SeoCrosslinksPage constructor.
     * @param \Mageplaza\SeoCrosslinks\Helper\Data $helperData
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    function __construct(
        Data $helperData,
        ManagerInterface $messageManager
    )
    {
        $this->helperData     = $helperData;
        $this->messageManager = $messageManager;
    }

    /**
     * @param \Magento\Cms\Block\Page $subject
     * @param $result
     * @return mixed
     */
    public function afterToHtml(\Magento\Cms\Block\Page $subject, $result)
    {
        if ($this->helperData->isEnableSeoCrossLinks()) {
            try {
                $termCollection = $this->helperData->getTermCollection(ApplyFor::PAGE);
                if ($termCollection->getSize()) {
                    foreach ($termCollection as $term) {
                        $this->helperData->replaceKeyword($term, $result);
                    }
                }
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Can\'t apply seo cross links'));
            }
        }

        return $result;
    }
}