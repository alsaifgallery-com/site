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

namespace Mageplaza\Faqs\Block\Article;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\Faqs\Helper\Data;

/**
 * Class Search
 * @package Mageplaza\Faqs\Block\Article
 */
class Search extends Template
{
    /**
     * @var Data
     */
    public $helperData;

    /**
     * @var string
     */
    protected $_template = 'Mageplaza_Faqs::article/search.phtml';

    /**
     * Search constructor.
     * @param Context $context
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helperData,
        array $data = []
    )
    {
        $this->helperData = $helperData;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getSearchFaqData()
    {
        $result   = [];
        $articles = $this->helperData->getArticleCollection();

        if (!empty($articles)) {
            foreach ($articles as $article) {
                $result[] = [
                    'value' => $article->getName(),
                    'url'   => $article->getUrl(),
                ];
            }
        }

        return Data::jsonEncode($result);
    }

    /**
     * Get full action name
     *
     * @return mixed
     */
    public function getActionName()
    {
        return $this->getRequest()->getFullActionName();
    }

	/**
	 * Get search ajax URL
	 *
	 * @return string
	 */
    public function getSearchAjaxUrl()
    {
        return $this->helperData->getUrl('mpfaqs/article/index');
    }
}
