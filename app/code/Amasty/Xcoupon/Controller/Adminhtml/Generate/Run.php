<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xcoupon
 */

namespace Amasty\Xcoupon\Controller\Adminhtml\Generate;
use Magento\Framework\App\ResponseInterface;

/**
 * Class Run
 * @package Amasty\Xcoupon\Controller\Adminhtml\Generate
 * @author Artem Brunevski
 */
class Run extends \Magento\SalesRule\Controller\Adminhtml\Promo\Quote
{
    /**
     * Generate Coupons action
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->getRequest()->isAjax()) {
            $this->_forward('noroute');
            return;
        }
        $result = [];
        $this->_initRule();

        $clean = $this->getRequest()->getParam('clean', 0);

        /** @var $rule \Magento\SalesRule\Model\Rule */
        $rule = $this->_coreRegistry->registry(\Magento\SalesRule\Model\RegistryConstants::CURRENT_SALES_RULE);

        if (!$rule->getId()) {
            $result['error'] = __('Rule is not defined');
        } else {
            try {
                $data = $this->getRequest()->getParams();
                $data['uses_per_coupon'] = $rule->getUsesPerCoupon();
                $data['usage_per_customer'] = $rule->getUsesPerCustomer();

                if (!empty($data['to_date'])) {
                    $inputFilter = new \Zend_Filter_Input(['to_date' => $this->_dateFilter], [], $data);
                    $data = $inputFilter->getUnescaped();
                }

                /** @var $generator \Amasty\Xcoupon\Model\Massgenerator */
                $generator = $this->_objectManager->get('Amasty\Xcoupon\Model\Massgenerator');
                if (!$generator->validateData($data)) {
                    $result['error'] = __('Invalid data provided');
                } else {
                    $generator->setData($data);
                    if ($clean){
                        $generator->clean();
                    }
                    $generator->generatePool();
                    $generated = $generator->getGeneratedCount();
                    $this->_view->getLayout()->initMessages();
                    $result['messages'] = __('%1 coupon(s) have been generated.', $generated);
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $result['error'] = $e->getMessage();
            } catch (\Exception $e) {
                $result['error'] = __(
                    'Something went wrong while generating coupons. Please review the log and try again.'
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            }
        }
        $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result)
        );
    }
}