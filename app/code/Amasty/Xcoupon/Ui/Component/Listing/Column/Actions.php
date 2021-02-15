<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xcoupon
 */

namespace Amasty\Xcoupon\Ui\Component\Listing\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

/**
 * Class Actions
 * @package Amasty\Xcoupon\Ui\Component\Listing\Column
 * @author Artem Brunevski
 */
class Actions extends \Magento\Ui\Component\Listing\Columns\Column
{
    protected $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    )
    {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }


    /**
     * @param array $dSource
     * @return array
     */
    public function prepareDataSource(array $dSource)
    {
        if (isset($dSource['data']['items'])) {
            foreach ($dSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['view'] = [
                    'hidden' => false,
                    'label' => __('View'),
                    'href' => $this->urlBuilder->getUrl(
                        'sales/order/view',
                        [
                            'order_id' => $item['entity_id']
                        ]
                    )
                ];
            }
        }

        return $dSource;
    }
}