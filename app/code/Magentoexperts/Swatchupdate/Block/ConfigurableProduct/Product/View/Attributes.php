<?php
namespace Magentoexperts\Swatchupdate\Block\ConfigurableProduct\Product\View;

use Magento\Catalog\Model\Product;
use Magento\Framework\Phrase;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * @api
 * @since 100.0.2
 */
class Attributes extends \Magento\Framework\View\Element\Template
{

    public function getAdditionalData()
    {

        $data = [];

        // Get product object
        $product = $this->getData('product');

        $attributes = $product->getAttributes();

        // Get parent product
        $parentProduct = $this->getData('parent_product');

        foreach ($attributes as $attribute) {
            if ($attribute->getIsVisibleOnFront()) {
                $value = $attribute->getFrontend()->getValue($product);
                if (is_string($value) && strlen($value)) {
                    $data[$attribute->getAttributeCode()] = [
                        'label' => __($attribute->getStoreLabel()),
                        'value' => $value,
                        'code' => $attribute->getAttributeCode(),
                    ];
                } else {

                    $value = $attribute->getFrontend()->getValue($parentProduct);

                    if (is_string($value) && strlen($value)) {
                        $data[$attribute->getAttributeCode()] = [
                            'label' => __($attribute->getStoreLabel()),
                            'value' => $value,
                            'code' => $attribute->getAttributeCode(),
                        ];
                    }
                }
            }
        }
        return $data;
    }

}
