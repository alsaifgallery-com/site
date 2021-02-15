<?php


namespace AlsaifGallery\Category\Model\Config\Source;


class Sliders extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var CollectionFactory
     */
    protected $sliderAdapter;
    
    protected $request;
    protected $state;

    /**
     * Sliders constructor.
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(  
        \AlsaifGallery\Category\Adapters\SliderAdapter $sliderAdapter,
        \Magento\Framework\App\RequestInterface $request ,
         \Magento\Framework\App\State $state
    )
    {
        $this->sliderAdapter = $sliderAdapter;
        $this->request = $request;
        $this->state = $state;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $options[] = [
                'value' => 0,
                'label' => __('Select Slider')
            ];
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
        $store = 0;
              $options = [];
        if ($this->state->getAreaCode() == \Magento\Framework\App\Area::AREA_ADMINHTML) {
            // in admin area
            /** @var \Magento\Framework\App\RequestInterface $request */
        
            $store = (int) $this->request->getParam('store', 0);
                     $rules = $this->sliderAdapter
                      ->getSliders()
                       ->addFieldToFilter('store_ids',array(['like'=> '%'.$store.'%'],['like'=> '%'.'0'.'%']));
        }else{ 
         $rules = $this->sliderAdapter
                      ->getSliders();
//                       ->addFieldToFilter('store_ids',['in',$conArr]);
         }
        
        foreach ($rules as $rule) {
            $options[$rule->getId()] = $rule->getName();
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
