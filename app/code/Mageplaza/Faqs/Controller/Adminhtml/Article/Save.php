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

namespace Mageplaza\Faqs\Controller\Adminhtml\Article;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Mageplaza\Faqs\Controller\Adminhtml\Article;
use Mageplaza\Faqs\Model\ArticleFactory;
use Mageplaza\Faqs\Helper\Data;
use Mageplaza\Faqs\Model\Config\Source\Visibility;

/**
 * Class Save
 * @package Mageplaza\Faqs\Controller\Adminhtml\Article
 */
class Save extends Article
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
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param ArticleFactory $articleFactory
     * @param Js $jsHelper
     * @param DateTime $date
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @param Data $helperData
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ArticleFactory $articleFactory,
        Js $jsHelper,
        DateTime $date,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        Data $helperData
    )
    {
        $this->jsHelper      = $jsHelper;
        $this->date          = $date;
        $this->_storeManager = $storeManager;
        $this->_logger       = $logger;
        $this->_helperData   = $helperData;

        parent::__construct($articleFactory, $registry, $context);
    }

    /**
     * Save data action
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data = $this->getRequest()->getPost('article')) {

            /** @var \Mageplaza\Faqs\Model\Article $article */
            $article = $this->initArticle();

            $this->_prepareData($article, $data);

            $this->_eventManager->dispatch('mageplaza_faqs_article_prepare_save', ['post' => $article, 'request' => $this->getRequest()]);

            try {
                /** send email to customer when the question is answered */
                if (!empty($article->getArticleContent())) {
                    $this->_sendEmailToCustomer($article);
                }
                $article->save();

                $this->messageManager->addSuccessMessage(__('The article has been saved.'));
                $this->_getSession()->setData('mageplaza_faqs_article_data', false);

                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath('mpfaqs/*/edit', ['id' => $article->getId(), '_current' => true]);
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
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Article.'));
            }

            $this->_getSession()->setData('mageplaza_faqs_article_data', $data);

            $resultRedirect->setPath('mpfaqs/*/edit', ['id' => $article->getId(), '_current' => true]);

            return $resultRedirect;
        }

        $resultRedirect->setPath('mpfaqs/*/');

        return $resultRedirect;
    }

    /**
     * Set specific data
     *
     * @param $article
     * @param array $data
     * @return $this
     */
    protected function _prepareData($article, $data = [])
    {
        if (!$article->getCreatedAt()) {
            $data['created_at'] = $this->date->date();
        }

        if (!isset($data['email_notify'])) {
            $data['email_notify'] = 0;
        }

        $data['updated_at']     = $this->date->date();
        $data['categories_ids'] = (isset($data['categories_ids']) && $data['categories_ids']) ? explode(',', $data['categories_ids']) : [];
        $article->addData($data);

        $products = $this->getRequest()->getPost('products');
        if (isset($products)) {
            $article->setIsProductGrid(true);
            $article->setProductsIds(
                $this->jsHelper->decodeGridSerializedInput($products)
            );
        }

        return $this;
    }

    /**
     * Send email to customer when the question is answered
     *
     * @param $article
     */
    protected function _sendEmailToCustomer($article)
    {
        /** Send mail to customer when the question is answered */
        $toEmail         = $article->getAuthorEmail();
        $emailTemplate   = $this->_helperData->getEmailConfig('customer/template');
        $questionContent = strip_tags($article->getArticleContent());
        $questionContent = str_replace("&nbsp;", "", $questionContent);
        $currentStore    = $this->_storeManager->getStore();
        $sender          = $this->_helperData->getEmailConfig('customer/sender');

        if ($toEmail
            && $this->_helperData->getEmailConfig('enabled')
            && $this->_helperData->getEmailConfig('customer/enabled')
            && $article->getEmailNotify()
            && (int) $article->getVisibility() == Visibility::PUBLISH) {
            try {
                $vars = [
                    'customer_name'    => $article->getAuthorName(),
                    'question'         => $article->getName(),
                    'question_content' => $questionContent,
                    'date'             => $article->getCreatedAt(),
                    'store_name'       => $currentStore->getName()
                ];
                $this->_helperData->sendMail($currentStore, $toEmail, $emailTemplate, $vars, $sender);
            } catch (\Exception $e) {
                $this->_logger->critical($e);
            }
        }
    }
}
