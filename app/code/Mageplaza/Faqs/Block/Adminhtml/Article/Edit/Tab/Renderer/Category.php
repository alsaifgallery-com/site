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

namespace Mageplaza\Faqs\Block\Adminhtml\Article\Edit\Tab\Renderer;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\Multiselect;
use Magento\Framework\Escaper;
use Mageplaza\Faqs\Model\ResourceModel\Category\CollectionFactory as FaqsCategoryCollectionFactory;

/**
 * Class Category
 * @package Mageplaza\Faqs\Block\Adminhtml\Article\Edit\Tab\Renderer
 */
class Category extends Multiselect
{
    /**
     * @var FaqsCategoryCollectionFactory
     */
    public $collectionFactory;

    /**
     * Authorization
     *
     * @var \Magento\Framework\AuthorizationInterface
     */
    public $authorization;

    /**
     * Category constructor.
     * @param Factory $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param FaqsCategoryCollectionFactory $collectionFactory
     * @param AuthorizationInterface $authorization
     * @param array $data
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        FaqsCategoryCollectionFactory $collectionFactory,
        AuthorizationInterface $authorization,
        array $data = []
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->authorization     = $authorization;

        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    /**
     * @inheritdoc
     */
    public function getElementHtml()
    {
        $html = '<div class="admin__field-control admin__control-grouped">';
        $html .= '<div id="faqs-category-select" class="admin__field" data-bind="scope:\'faqsCategory\'" data-index="index">';
        $html .= '<!-- ko foreach: elems() -->';
        $html .= '<input name="article[categories_ids]" data-bind="value: value" style="display: none"/>';
        $html .= '<!-- ko template: elementTmpl --><!-- /ko -->';
        $html .= '<!-- /ko -->';
        $html .= '</div></div>';

        $html .= $this->getAfterElementHtml();

        return $html;
    }

    /**
     * Get no display
     *
     * @return bool
     */
    public function getNoDisplay()
    {
        $isNotAllowed = !$this->authorization->isAllowed('Mageplaza_Faqs::category');

        return $this->getData('no_display') || $isNotAllowed;
    }

    /**
     * @return mixed
     */
    public function getCategoriesCollection()
    {
        /* @var $collection \Mageplaza\Faqs\Model\ResourceModel\Category\Collection */
        $collection   = $this->collectionFactory->create();
        $categoryById = [];
        foreach ($collection as $category) {
            $categoryById[$category->getId()]['value']     = $category->getId();
            $categoryById[$category->getId()]['is_active'] = 1;
            $categoryById[$category->getId()]['label']     = $category->getName();
        }

        return $categoryById;
    }

    /**
     * Get values for select
     *
     * @return array
     */
    public function getValues()
    {
        $values = $this->getValue();

        if (!is_array($values)) {
            $values = explode(',', $values);
        }

        if (!sizeof($values)) {
            return [];
        }

        /* @var $collection \Mageplaza\Faqs\Model\ResourceModel\Category\Collection */
        $collection = $this->collectionFactory->create()
            ->addIdFilter($values);

        $options = [];
        foreach ($collection as $category) {
            $options[] = $category->getId();
        }

        return $options;
    }

    /**
     * Attach Faqs category suggest widget initialization
     *
     * @return mixed|string
     */
    public function getAfterElementHtml()
    {
        $html = '<script type="text/x-magento-init">
            {
                "*": {
                    "Magento_Ui/js/core/app": {
                        "components": {
                            "faqsCategory": {
                                "component": "uiComponent",
                                "children": {
                                    "faqs_select_category": {
                                        "component": "Mageplaza_Faqs/js/components/category-list",
                                        "config": {
                                            "filterOptions": true,
                                            "disableLabel": true,
                                            "chipsEnabled": true,
                                            "levelsVisibility": "1",
                                            "elementTmpl": "ui/grid/filters/elements/ui-select",
                                            "options": ' . json_encode($this->getCategoriesCollection()) . ',
                                            "value": ' . json_encode($this->getValues()) . ',
                                            "listens": {
                                                "index=create_category:responseData": "setParsed",
                                                "newOption": "toggleOptionSelected"
                                            },
                                            "config": {
                                                "dataScope": "faqs_select_category",
                                                "sortOrder": 10
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        </script>';

        return $html;
    }
}
