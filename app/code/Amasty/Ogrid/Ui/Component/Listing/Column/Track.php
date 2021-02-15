<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Ogrid
 */


namespace Amasty\Ogrid\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class Track extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Magento\Framework\Url\EncoderInterface
     */
    protected $urlEncoder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Track constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        $this->urlEncoder = $urlEncoder;
        $this->storeManager = $storeManager;
        parent::__construct($context, $uiComponentFactory, $components, $data); // TODO: Change the autogenerated stub
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (array_key_exists('amasty_ogrid_sales_shipment_track', $item)) {
                    $protectCode = $item['amasty_ogrid_sales_shipment_track']['protect_code'];
                    unset($item['amasty_ogrid_sales_shipment_track']['protect_code']);

                    $urlPart = "order_id:{$item['entity_id']}:{$protectCode}";
                    $params = [
                        '_direct' => 'shipping/tracking/popup',
                        '_query' => ['hash' => $this->urlEncoder->encode($urlPart)]
                    ];

                    $storeId = $item['amasty_ogrid_sales_shipment_track']['store_id'];
                    unset($item['amasty_ogrid_sales_shipment_track']['store_id']);

                    $storeModel = $this->storeManager->getStore($storeId);

                    $item['amasty_ogrid_sales_shipment_track'] = implode('<br/>', $item['amasty_ogrid_sales_shipment_track']) .
                    '<br/><a href="#" onclick="popWin(\'' .
                        $storeModel->getUrl('', $params) .
                        '\',\'trackorder\',\'width=800,height=600,resizable=yes,scrollbars=yes\'); event.stopPropagation(); return false;">' .
                        __('Track Order') .
                    '</a>';
                }
            }
        }
        return $dataSource;
    }
}
