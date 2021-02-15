<?php

namespace AlsaifGallery\Orders\Model;

class OrderManagement implements \AlsaifGallery\Orders\Api\OrderManagementInterface {

    protected $orderRepository;
    protected $statusCollection;
    protected $productRepositoryFactory;
    protected $_totals;
    protected $storeManager;
    protected $media_url;
    protected $data;
    protected $image_placeholder;
    protected $searchBuilder;
    protected $orderModel;
    protected $brandAdapter;
    protected $codPaymentFeeRepository;
    protected $codConfigProvider;
    public $scopeConfig;


    public function __construct(
            \Magento\Sales\Model\OrderFactory $orderModel,
            \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
            \Magento\Sales\Model\ResourceModel\Status\CollectionFactory $statusCollection,
            \Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepositoryFactory,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \AlsaifGallery\AppConfigurations\Helper\Data $data,
             \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
            \AlsaifGallery\Product\Adapters\BrandAdapterInterface $brandAdapter,
            \Magento\Framework\Api\SearchCriteriaBuilder $searchBuilder,
            \Amasty\CashOnDelivery\Api\PaymentFeeRepositoryInterface $codPaymentFeeRepository,
            \Amasty\CashOnDelivery\Model\ConfigProvider $codConfigProvider
    ) {
        $this->orderRepository = $orderRepository;
        $this->statusCollection = $statusCollection;
        $this->productRepositoryFactory = $productRepositoryFactory;
        $this->storeManager = $storeManager;
        $this->searchBuilder = $searchBuilder;
        $this->orderModel = $orderModel;
        $this->_totals = [];
        $this->data = $data;
        $this->scopeConfig = $scopeConfig;
        $store = $this->storeManager->getStore();
        $this->brandAdapter = $brandAdapter;
        $this->codPaymentFeeRepository = $codPaymentFeeRepository;
        $this->codConfigProvider = $codConfigProvider;

        $this->image_placeholder = $this->data->getProductThumbnailImagePlaceholder();
        $this->media_url = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder($orderId) {



//        $attribute = $poductReource->getAttribute($brandAttribute);

        $data = array();

        $order = $this->orderRepository->get($orderId);

        $data['created_at'] = $order->getCreatedAt();
        $data['increment_id'] = $order->getIncrementId();
        $data['order_id'] = $order->getId();
        $data['current_status'] = $order->getStatus();
        // $data['is_cancelled_available'] = $this->getOrderCancelledAvailable($orderId);
        if (is_null($order->getReview())) {
            $data['summary'] = 0;
        } else {
            $data['summary'] = (int)$order->getReview();
        }
        // $data['available_status'] = $this->getIsBallehStatuses($order->getStatus());
        $data['shipping_address'] = $this->getShippingAddressData($order->getShippingAddress()->getData());
        $data['items'] = array();
        $data['totals'] = $this->_initTotals($order);
        foreach ($order->getItems() as $item) {
            $product = $this->productRepositoryFactory->create()->getById($item->getProductId());
            $itemData['name'] = $item->getName();
            $itemData['price'] = $item->getPrice();
            $itemData['qty'] = (int) $item->getQtyOrdered();
            $itemData['base_url'] = $this->media_url . 'catalog/product';
            $itemData['brand_name']= $this->getBrandName($product);
            if (is_null($product->getThumbnail())) {
                if (is_null($this->image_placeholder)) {
                    $itemData['thumbnail'] = '';
                } else {
                    $itemData['thumbnail'] = '/placeholder/' . $this->image_placeholder;
                }
            } else {

                $itemData['thumbnail'] = $product->getThumbnail();
            }
            array_push($data['items'], $itemData);
        }

        return array($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getListOrders($customerId) {
        $ordersArr = [];
        $searchCriteria = $this->searchBuilder->addFilter('customer_id', $customerId, 'eq')->create();
        $orders = $this->orderRepository->getList($searchCriteria);
        foreach ($orders->getItems() as $order) {
            $data['created_at'] = $order->getCreatedAt();
            $data['increment_id'] = $order->getIncrementId();
            $data['order_id'] = $order->getId();
            $data['current_status'] = $order->getStatus();
            //$data['available_status'] = $this->getIsBallehStatuses($order->getState());
            $data['subTotal'] = $order->getGrandTotal();
            array_push($ordersArr, $data);
        }
        rsort($ordersArr);
        return $ordersArr;
    }

    public function addOrderReview($orderId, $review) {
        try {
            $response = [];
            if (!is_integer($review)) {
                $response['status'] = "false";
                $response['message'] = "Please Add Number";
                return array($response);
            }

            if ((1 <= $review) && ($review <= 5)) {
                $order = $this->orderRepository->get($orderId);
                $order->setReview($review);
                $this->orderRepository->save($order);
                $response['status'] = "true";
                $response['message'] = "Review Added Successfully";
            } else {
                $response['status'] = "false";
                $response['message'] = "Sorry Can't Add Review";
            }

            return array($response);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                    __($e->getMessage())
            );
        }
    }

    public function getShippingAddressData($address) {
        $data['region'] = $address['region'];
        $data['postcode'] = $address['postcode'];
        $data['lastname'] = $address['lastname'];
        $data['street'] = $address['street'];
        $data['city'] = $address['city'];
        $data['email'] = $address['email'];
        $data['telephone'] = $address['telephone'];
        $data['country_id'] = $address['country_id'];
        $data['firstname'] = $address['firstname'];
        $data['address_type'] = $address['address_type'];
        if (is_null($address['company'])) {
            $data['company'] = "";
        } else {
            $data['company'] = $address['company'];
        }
        return $data;
    }

    public function getIsBallehStatuses($current) {
        $statuses = [];
        if ($current == 'canceled') {
            $ballehcollection = $this->statusCollection->create();
            $ballehcollection->addAttributeToFilter('state', ['in' => ['new', 'canceled']])
                    ->addAttributeToFilter('is_balleh', 1)
                    ->setOrder('sort_order', 'ASC');
            $ballehCollection = $ballehcollection->getData();
        } else {
            $ballehcollection = $this->statusCollection->create();
            $ballehcollection->addAttributeToFilter('is_balleh', 1)->setOrder('sort_order', 'ASC');
            $ballehCollection = $ballehcollection->getData();
        }
        foreach ($ballehCollection as $value) {
            $statusesData['status'] = $value['status'];
            $statusesData['label'] = $value['label'];
            $statusesData['icon'] = $this->media_url . $value['icon'];
            array_push($statuses, $statusesData);
        }
        return $statuses;
    }

    /**
     * Initialize order totals array
     *
     * @return $this
     */
    protected function _initTotals($order) {
        $source = $order;

        $this->_totals = [];
        $this->_totals['Subtotal'] = $source->getSubtotal();

        /**
         * Add shipping
         */
        if (!$source->getIsVirtual() && ((double) $source->getShippingAmount() || $source->getShippingDescription())) {
            $this->_totals['Shipping & Handling'] = $source->getShippingAmount();
        }else{
            $this->_totals['Shipping & Handling'] ="";
        }

        /**
         * Add COD fee
         */
        $paymentFee = null ;
        try{
            /** @var \Amasty\CashOnDelivery\Model\PaymentFee $paymentFee */
            $paymentFee = $this->codPaymentFeeRepository->getByQuoteId($source->getQuoteId());
        } catch ( \Exception $e ){
            // do nothing
        }
        $label=  $this->codConfigProvider->getPaymentFeeLabel();
        $label= (string)$label;
        if(!is_null( $paymentFee )){
            if ($paymentFee->getAmount()) {
                // $label=  "Cash on Delivery Fee" ;
                $amount = $paymentFee->getAmount();
                $baseAmount = $paymentFee->getBaseAmount();
                $this->_totals[ $label ]  = $amount;
            }
        }else{
           $this->_totals[ $label ]  = "";
        }

        /**
         * Add discount
         */
        if ($source->getDiscountDescription()) {
            // $discountLabel = __('Discount (%1)', $source->getDiscountDescription());
            $discountLabel = sprintf('Discount (%1)', $source->getDiscountDescription());
        } else {
//            $discountLabel = __('Discount');
            $discountLabel = 'Discount';
        }
        $discountLabel = (string) $discountLabel;
        if ((double) $source->getDiscountAmount() != 0) {
            $this->_totals[$discountLabel] = $source->getDiscountAmount();
        }else{
            $this->_totals[$discountLabel] = "";
        }

        $this->_totals['Grand Total'] = $source->getGrandTotal();

        /**
         * Base grandtotal
         */
        if ($source->isCurrencyDifferent()) {
            $this->_totals['Grand Total to be Charged'] = $source->formatBasePrice($source->getBaseGrandTotal());
        }
        return $this->_totals;
    }

    public function getOrderCancelledAvailable($orderId) {
        $orderFactory = $this->orderModel->create();
        $orderFactory->load($orderId);
        if ($orderFactory->canCancel() && $orderFactory->getState() != 'processing') {
            return true;
        }
        return false;
    }

    public function getBrandName($product){

        $brand = $this->brandAdapter->getProductBrand($product->getId());
         if (!is_null($brand)) {
        return $brand->getValue();
         }
         return '';

                    $brandAttribute = $this->scopeConfig->getValue('shopbybrand/general/attribute', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId());
                     $attribute = $product->getResource()->getAttribute($brandAttribute)->getFrontend()->getValue($product);
                     return $product->getData('name');
                         if ($attribute->usesSource()) {
            $brand['brand_name'] = $attribute->getSource()->getOptionText($brandObject['option_id']);
        }
    }

}
