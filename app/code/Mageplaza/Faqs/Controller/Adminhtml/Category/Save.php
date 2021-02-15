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

use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Mageplaza\Faqs\Controller\Adminhtml\Category;
use Mageplaza\Faqs\Model\CategoryFactory;

/**
 * Class Save
 * @package Mageplaza\Faqs\Controller\Adminhtml\Category
 */
class Save extends Category
{
    /**
     * JS helper
     *
     * @var \Magento\Backend\Helper\Js
     */
    public $jsHelper;

    /**
     * @var DateTime
     */
    public $date;

    /**
     * Save constructor.
     * @param Context $context
     * @param Registry $registry
     * @param CategoryFactory $categoryFactory
     * @param Js $jsHelper
     * @param DateTime $date
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CategoryFactory $categoryFactory,
        Js $jsHelper,
        DateTime $date
    )
    {
        $this->jsHelper = $jsHelper;
        $this->date     = $date;

        parent::__construct($categoryFactory, $registry, $context);
    }

    /**
     * Save data action
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data = $this->getRequest()->getPost('category')) {

            /** @var \Mageplaza\Faqs\Model\Category $category */
            $category = $this->initCategory();

            $this->_prepareData($category, $data);

            $this->_eventManager->dispatch('mageplaza_faqs_category_prepare_save', ['post' => $category, 'request' => $this->getRequest()]);

            try {
                $category->save();

                $this->messageManager->addSuccessMessage(__('The category has been saved.'));
                $this->_getSession()->setData('mageplaza_faqs_category_data', false);

                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath('mpfaqs/*/edit', ['id' => $category->getId(), '_current' => true]);
                }
                else {
                    $resultRedirect->setPath('mpfaqs/*/');
                }

                return $resultRedirect;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Category.'));
            }

            $this->_getSession()->setData('mageplaza_faqs_category_data', $data);

            $resultRedirect->setPath('mpfaqs/*/edit', ['id' => $category->getId(), '_current' => true]);

            return $resultRedirect;
        }

        $resultRedirect->setPath('mpfaqs/*/');

        return $resultRedirect;
    }

    /**
     * Set specific data
     *
     * @param $category
     * @param array $data
     * @return $this
     */
    protected function _prepareData($category, $data = [])
    {
        if ($category->getCreatedAt() == null) {
            $data['created_at'] = $this->date->date();
        }
        $data['updated_at'] = $this->date->date();
        $category->addData($data);

        $articles = $this->getRequest()->getPost('articles');
        if (isset($articles)) {
            $category->setIsArticleGrid(true);
            $category->setArticlesIds(
                $this->jsHelper->decodeGridSerializedInput($articles)
            );
        }

        return $this;
    }
}
