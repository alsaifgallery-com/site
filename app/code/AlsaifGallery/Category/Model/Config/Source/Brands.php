<?php


namespace AlsaifGallery\Category\Model\Config\Source;


class Brands extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var CollectionFactory
     */
    protected $brandAdapter;
    
    protected $request;
    protected $state;

    /**
     * Sliders constructor.
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(  
        \AlsaifGallery\Category\Adapters\BrandAdapterInterface $brandAdapter,
        \Magento\Framework\App\RequestInterface $request ,
         \Magento\Framework\App\State $state
    )
    {
        $this->brandAdapter = $brandAdapter;
        $this->request = $request;
        $this->state = $state;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
//        $options[] = [
//                'value' => 0,
//                'label' => __('Select Slider')
//            ];
        foreach ($this->toArray() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }
        // var_dump( count( $options ));die;
        return $options;
    }

    /**
     * @return array
     */
    protected function toArray()
    {
        $options = [];
        $brands = $this->brandAdapter->getAllBrands();
        // var_dump(count( $brands));die;
        foreach ($brands as $brand ) {
             // var_dump( $brand->getData() );die;
            // $options[$brand->getBrandId()] = $brand->getValue();
            $options[$brand->getOptionId()] = $brand->getBrandId()." ".$brand->getValue();
        }

        return $options;
    }
    
        /**
     * {@inheritdoc}
     */
    public function getAllOptions()
    {
        
        if (!$this->_options) {
            $this->_options = $this->toOptionArray();
        }
//          var_dump($this->_options);die;
        return $this->_options;
        
    }
    
}
