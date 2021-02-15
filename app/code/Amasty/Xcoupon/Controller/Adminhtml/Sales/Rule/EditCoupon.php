<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xcoupon
 */


namespace Amasty\Xcoupon\Controller\Adminhtml\Sales\Rule;

class EditCoupon extends \Magento\SalesRule\Controller\Adminhtml\Promo\Quote
{
    const CURRENT_COUPON_MODEL = 'amasty_xcoupon_current_coupon';
    /**
     * @var \Magento\SalesRule\Model\CouponRepository
     */
    protected $couponRepository;
    /**
     * @var \Magento\SalesRule\Model\Spi\CouponResourceInterface
     */
    protected $resourceModel;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Magento\SalesRule\Model\CouponRepository $couponRepository,
        \Magento\SalesRule\Model\Spi\CouponResourceInterface $resourceModel

    ) {
        parent::__construct($context, $coreRegistry, $fileFactory, $dateFilter);
        $this->couponRepository = $couponRepository;
        $this->resourceModel = $resourceModel;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $model = $this->couponRepository->getById($id);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('This coupon no longer exists.'));
                $this->redirectDependOnModel();
                return;
            }
        } else {
            $this->messageManager->addErrorMessage(__('This coupon no longer exists.'));
            $this->redirectDependOnModel();
            return;
        }

        $this->_coreRegistry->register(self::CURRENT_COUPON_MODEL, $model);

        $this->_initAction();

        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            __('Edit Coupon #') . $model->getId()
        );
        $this->_view->renderLayout();
    }

    private function redirectDependOnModel()
    {
        $model = $this->_coreRegistry->registry(
            \Magento\SalesRule\Model\RegistryConstants::CURRENT_SALES_RULE
        );
        if ($model && $model->getId()) {
            $this->_redirect('sales_rule/promo_quote/edit', ['id' =>  $model->getId()]);
        } else {
            $this->_redirect('sales_rule/promo_quote/index');
        }
    }
}