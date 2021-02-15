<?php
namespace Magentoexperts\Swatchupdate\Block\ConfigurableProduct\Product\View\Type;

use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Json\DecoderInterface;

class Configurable
{
    protected $jsonEncoder;
    protected $jsonDecoder;
    protected $_productRepository;
    protected $_layout;

    protected $helperData;

    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\View\LayoutInterface $layout,
        EncoderInterface $jsonEncoder,
        DecoderInterface $jsonDecoder,
        \Magentoexperts\Swatchupdate\Helper\Data $helperData
    ) {
        $this->jsonDecoder = $jsonDecoder;
        $this->jsonEncoder = $jsonEncoder;
        $this->_productRepository = $productRepository;
        $this->_layout = $layout;
        $this->helperData = $helperData;
    }

    public function getProductById($id)
    {
        return $this->_productRepository->getById($id);
    }

    public function aroundGetJsonConfig(

        \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject,
        \Closure $proceed
    )
    {
        $val=$this->helperData->getModuleEnable();
        $sku=$this->helperData->getDynamicSku();
        $name=$this->helperData->getDynamicName();
        $description=$this->helperData->getDynamicDescription();
        $shortdescription=$this->helperData->getDynamicShortDescription();
        $addtionalinfo=$this->helperData->getDynamicAddtionalinfo();

        if($val==1){

            if($name==1) {
                $productName = [];
            }
            if($sku==1) {
                $productSku = [];
             }
             if($shortdescription==1) {
                $productShortDescription = [];
            }
            if($description==1) {
                $productDescription = [];
            }
            if($addtionalinfo==1) {
                $additionalData = [];
            }

        $config = $proceed();
        $config = $this->jsonDecoder->decode($config);

        // Get parent product
        $parentProduct = $subject->getProduct();

        foreach ($subject->getAllowProducts() as $prod) {
            $id = $prod->getId();
            $product = $this->getProductById($id);
            if($name==1) {
                $productName[$id] = $product->getName();
            }
            if($sku==1) {
                $productSku[$id] = $product->getSKu();
            }

            // if short description of child product is empty then take short description from parent product
            if (!empty($product->getShortDescription()) &&  $shortdescription==1) {
                $productShortDescription[$id] = $product->getShortDescription();
            } else {
                $productShortDescription[$id] = $parentProduct->getShortDescription();
            }

            // if description of child product is empty then take description from parent product
            if (!empty($product->getDescription()) && $description==1) {
                $productDescription[$id] = $product->getDescription();
            } else {
                $productDescription[$id] = $parentProduct->getDescription();
            }
            if($addtionalinfo==1) {
                $additionalData[$id] = $this->_layout
                    ->createBlock("Magentoexperts\Swatchupdate\Block\ConfigurableProduct\Product\View\Attributes")
                    ->setData('product', $product)
                    ->setData('parent_product', $parentProduct)
                    ->setTemplate('Magentoexperts_Swatchupdate::attributes.phtml')
                    ->toHtml();

            }

        }
            if($name==1) {
                $config['productname'] = $productName;
            }

            if($sku==1) {
                $config['productsku'] = $productSku;
            }
            if($shortdescription==1) {
                $config['productshortdescription'] = $productShortDescription;
            }
            if($description==1) {
                $config['productdescription'] = $productDescription;
            }
            if($addtionalinfo==1) {
                $config['additionaldata'] = $additionalData;
            }

        return $this->jsonEncoder->encode($config);
             }else{
            $config = $proceed();
            return $config;

        }
    }
}
