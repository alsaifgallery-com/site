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

namespace Mageplaza\Faqs\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\Faqs\Model\CategoryFactory;

/**
 * Class InlineEdit
 * @package Mageplaza\Faqs\Controller\Adminhtml\Category
 */
class InlineEdit extends Action
{
    /**
     * JSON Factory
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    public $jsonFactory;

    /**
     * Category Factory
     *
     * @var \Mageplaza\Faqs\Model\CategoryFactory
     */
    public $categoryFactory;

    /**
     * InlineEdit constructor.
     *
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        CategoryFactory $categoryFactory
    )
    {
        $this->jsonFactory     = $jsonFactory;
        $this->categoryFactory = $categoryFactory;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson    = $this->jsonFactory->create();
        $error         = false;
        $messages      = [];
        $categoryItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && !empty($categoryItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error'    => true,
            ]);
        }

        $key        = array_keys($categoryItems);
        $categoryId = !empty($key) ? (int) $key[0] : '';
        /** @var \Mageplaza\Faqs\Model\Category $category */
        $category = $this->categoryFactory->create()->load($categoryId);
        try {
            $categoryData = $categoryItems[$categoryId];
            $category->addData($categoryData)
                ->save();
        } catch (LocalizedException $e) {
            $messages[] = $this->getErrorWithCategoryId($category, $e->getMessage());
            $error      = true;
        } catch (\RuntimeException $e) {
            $messages[] = $this->getErrorWithCategoryId($category, $e->getMessage());
            $error      = true;
        } catch (\Exception $e) {
            $messages[] = $this->getErrorWithCategoryId(
                $category,
                __('Something went wrong while saving the Category.')
            );
            $error      = true;
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error'    => $error
        ]);
    }

    /**
     * Add Category id to error message
     *
     * @param \Mageplaza\Faqs\Model\Category $category
     * @param string $errorText
     * @return string
     */
    public function getErrorWithCategoryId(\Mageplaza\Faqs\Model\Category $category, $errorText)
    {
        return '[Category ID: ' . $category->getId() . '] ' . $errorText;
    }
}
