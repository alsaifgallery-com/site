<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xcoupon
 */


namespace Amasty\Xcoupon\Controller\Adminhtml\Sales\Rule;

class SaveCoupon extends EditCoupon
{
    /**
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $id = $model = null;
        if ($this->getRequest()->getPostValue()) {
            try {
                $data = $this->getRequest()->getPostValue();
                $id = $this->getRequest()->getParam('coupon_id');
                if ($id) {
                    $model = $this->couponRepository->getById($id);
                    $model->addData(
                        [
                            'code' => $data['code'],
                            'usage_limit' => $data['usage_limit'],
                            'usage_per_customer' => $data['usage_per_customer']
                        ]
                    );
                    $this->resourceModel->save($model);

                    $this->messageManager->addSuccessMessage(__('The coupon is saved.'));

                    if ($model->getRuleId()) {
                        $this->_redirect('sales_rule/promo_quote/edit', ['id' =>  $model->getRuleId()]);
                    } else {
                        $this->_redirect('sales_rule/promo_quote/index');
                    }
                    return;
                }
                $this->messageManager->addErrorMessage(__('This coupon no longer exists.'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while saving the coupon data.') . $e->getMessage()
                );
            }
        }

        if ($this->getRequest()->getParam('back') && $model) {
            $this->_redirect('*/*/editCoupon', ['id' => $model->getCouponId()]);
            return;
        }

        if ($model && $model->getRuleId()) {
            $this->_redirect('sales_rule/promo_quote/edit', ['id' =>  $model->getRuleId()]);
        } else {
            $this->_redirect('sales_rule/promo_quote/index');
        }
    }
}