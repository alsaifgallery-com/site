<?php

namespace AlsaifGallery\Product\Adapters;


use Mageplaza\Shopbybrand\Helper\Data as Helper;
use Mageplaza\Shopbybrand\Model\BrandFactory;


/**
 * Brand Adapter
 *
 * @author helal
 */
class BrandAdapter implements BrandAdapterInterface {
   
    
    protected $brandList;
    protected $brandFeatured;
    
    /** @var \Mageplaza\Shopbybrand\Helper\Data */
    protected $helper;

    /** @var \Mageplaza\Shopbybrand\Model\BrandFactory */
    protected $brandFactory;

    /** @var \Mageplaza\Shopbybrand\Model\Brand */
    protected $brand;
    
    protected $productFactory;

    public function __construct(
        \Mageplaza\Shopbybrand\Block\Brand\BrandList $brandList,
        \Mageplaza\Shopbybrand\Block\Brand\Featured $brandFeatured,
        
        \Magento\Catalog\Model\ProductFactory $productFactory,
            
        Helper $helper,
        BrandFactory $brandFactory
    )
    {
        $this->brandList = $brandList;
        $this->brandFeatured = $brandFeatured;
        
       	$this->helper        = $helper;
	$this->brandFactory  = $brandFactory;
        
        $this->productFactory = $productFactory;
                
    }
  
	/**
	 * Get product brand
	 *
	 * @return null | Brand
	 */
	public function getProductBrand($productId)
	{
            $product = $this->productFactory->create()->load($productId);
            if ( $product->getId()) {
                    $attCode = $this->helper->getAttributeCode();
                    $optionId = $product->getData($attCode);
                    // var_dump($optionId);die;
                    if ($optionId) {
                            return $this->brandFactory->create()->loadByOption($optionId);
                    }
            }
		return null;
	}

	/**
	 * @return \Mageplaza\Shopbybrand\Helper\Data
	 */
	public function helper()
	{
		return $this->helper;
	}

	public function getBrandAttributeCode()
	{
		return  $this->helper->getAttributeCode();
	}
}
