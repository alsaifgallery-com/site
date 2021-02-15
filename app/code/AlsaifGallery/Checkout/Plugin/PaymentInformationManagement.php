<?php

namespace AlsaifGallery\Checkout\Plugin;

use Magento\Checkout\Model\PaymentInformationManagement as CheckoutPaymentInformationManagement;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartManagementInterface;
use Psr\Log\LoggerInterface;

/**
 * Class PaymentInformationManagement
 */
class PaymentInformationManagement
{
    /**
     * @var CartManagementInterface
     */
    private $cartManagement;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * PaymentInformationManagement constructor.
     * @param CartManagementInterface $cartManagement
     * @param LoggerInterface $logger
     */
    public function __construct(
        CartManagementInterface $cartManagement,
        LoggerInterface $logger,
        \Magento\Sales\Api\OrderRepositoryInterface $order,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
        $this->cartManagement = $cartManagement;
        $this->logger = $logger;
        $this->order = $order;
        $this->request = $request;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param CheckoutPaymentInformationManagement $subject
     * @param \Closure $proceed
     * @param $cartId
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface|null $billingAddress
     * @return string
     * @throws CouldNotSaveException
     */
    public function aroundSavePaymentInformationAndPlaceOrder(
        CheckoutPaymentInformationManagement $subject,
        \Closure $proceed,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        $subject->savePaymentInformation($cartId, $paymentMethod, $billingAddress);
        $deviceHeader = $this->request->getHeader('device');
         if($deviceHeader == null){
            $deviceHeader="Web";
          }
        try {
            $orderId = $this->cartManagement->placeOrder($cartId);
            $order = $this->orderRepository->get($orderId);
            $orderIncrementId = $order->getIncrementId();
            $orderObject = $this->order->get($orderId);
            $orderObject->setOrderOrigin($deviceHeader);
            $orderObject->addStatusToHistory($orderObject->getStatus(),'Order Placed From Origin '.$deviceHeader);
            $this->order->save($orderObject);
        } catch (LocalizedException $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            throw new CouldNotSaveException(
                __('An error occurred on the server. Please try to place the order again.'),
                $exception
            );
        }
        return $orderIncrementId;
    }
}
