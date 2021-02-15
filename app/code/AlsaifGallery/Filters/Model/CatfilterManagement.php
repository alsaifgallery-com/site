<?php

namespace AlsaifGallery\Filters\Model;

class CatfilterManagement implements \AlsaifGallery\Filters\Api\CatfilterManagementInterface {

    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_catalogLayer;

    /**
     * @var \Magento\Catalog\Model\Layer\FilterList
     */
    protected $filterList;

/**
 * Escaper
 *
 * @var \Magento\Framework\Escaper
 */
protected $_escaper;


    protected $_request;

    /**
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Catalog\Model\Layer\FilterList $filterList
     * @param \Magento\Framework\Webapi\Rest\Request $request
     * @param \Magento\Framework\Escaper $_escaper
     * @param array $data
     */
    public function __construct(
            \Magento\Catalog\Model\Layer\Resolver $layerResolver,
            \Magento\Catalog\Model\Layer\FilterList $filterList,
            \Magento\Framework\Webapi\Rest\Request $request,
            \Magento\Framework\Escaper $_escaper,
            array $data = []
    ) {
        $this->_catalogLayer = $layerResolver->get();
        $this->filterList = $filterList;
        $this->_request = $request;
        $this->_escaper = $_escaper;
    }

    /**
     * {@inheritdoc}
     */
    public function getCatfilter( ) {
        $resuklt = $this->retrieve();
        return $resuklt;
       
    }
    /**
     * {@inheritdoc}
     */
//    public function getCatfilterById( $catId ) {
//
//        $filters = $this->filterList->getFilters($this->_catalogLayer);
//        
//        $finalFilters = [];
//        $data = array();
//        $i = 0;
//        
//        
//        if(!empty($catId)){
//            $this->getLayer()->setCurrentCategory( $catId );
//        }else{
//            // get the default
//            // $this->getLayer()->setCurrentCategory( 15 );
//        }
//   
//        $this->_prepareLayoutById();
//
//        foreach ($this->getFilters() as $filter) {
//    
//            try{
//                var_dump($filter->getRequestVar());
//            if ($filter->getItemsCount()) {
//                $name = $filter->getRequestVar();
////                $finalFilters['component']=$name;
//                foreach ($filter->getItems() as $item) {    
//                    if ($item->getFilter()->hasAttributeModel() && $item->getFilter()->getAttributeModel()->getAttributeCode() == 'price') {
//
//                        $finalFilters['data'][$i]['name'] = strip_tags($item->getLabel());
//                        $finalFilters['data'][$i]['value'] = $item->getValue();
//                        $finalFilters['data'][$i]['count'] = $item->getCount();
//                        $finalFilters['data'][$i]['code'] = $item->getFilter()->getAttributeModel()->getAttributeCode();
//                    } else {
//
//                        $finalFilters['data'][$i]['name'] = $item->getLabel();
//                        $finalFilters['data'][$i]['value'] = $item->getValue();
//                        $finalFilters['data'][$i]['code'] = $item->getFilter()->getRequestVar();
//                        $finalFilters['data'][$i]['count'] = $item->getCount();
//                    }
//                    $i++;
//                }
//                
//         
//            }
//            }catch(\Exception $e){
//                continue;
//            }
//        }
//        
////               if (isset($finalFilters['price']) && is_array($finalFilters['price'])){
////                   $finalFilters['price']['max'] = $this->getLayer()->getProductCollection()->getMaxPrice();
////                   $finalFilters['price']['min'] = $this->getLayer()->getProductCollection()->getMinPrice();
////                }
//        return [$finalFilters];
//    }

    /**
     * Apply layer
     *
     * @return $this
     */
    protected function _prepareLayout() {
        foreach ($this->filterList->getFilters($this->_catalogLayer) as $filter) {
            $filter->apply($this->getRequest());
        }
        $this->getLayer()->apply();
        return $this;
    }
    /**
     * Apply layer
     *
     * @return $this
     */
    protected function _prepareLayoutById() {
//        foreach ($this->filterList->getFilters($this->_catalogLayer) as $filter) {
//            $filter->apply($this->getRequest());
//        }
        $this->getLayer()->apply();
        return $this;
    }

    /**
     * Get layer object
     *
     * @return \Magento\Catalog\Model\Layer
     */
    public function getLayer() {
        return $this->_catalogLayer;
    }
    
   
    /**
     * Get all layer filters
     *
     * @return array
     */
    public function getFilters() {
        return $this->filterList->getFilters($this->_catalogLayer);
    }

    public function getRequest() {
        return $this->_request;
    }
    
    

/**
     * Retrieve filterlist
     *
     * @api
     * @return array
     */
    public function retrieve(){     
        $category = $this->_request->getParam('cat');
        if( $category == null){
            $category = 2; //default cat
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $filterableAttributes = $objectManager->getInstance()->get(\Magento\Catalog\Model\Layer\Category\FilterableAttributeList::class);

        $appState = $objectManager->getInstance()->get(\Magento\Framework\App\State::class);
        $layerResolver = $objectManager->getInstance()->get(\Magento\Catalog\Model\Layer\Resolver::class);
        $filterList = $objectManager->getInstance()->create(
            \Magento\Catalog\Model\Layer\FilterList::class,
                [
                    'filterableAttributes' => $filterableAttributes
                ]
            );      

            $layer = $layerResolver->get();
            $layer->setCurrentCategory($category);
            $this->_prepareLayout();
            $filters = $filterList->getFilters($layer);
            $maxPrice = $layer->getProductCollection()->getMaxPrice();
            $minPrice = $layer->getProductCollection()->getMinPrice();  

        $i = 0;
        $filterArray=[];
//        var_dump($filters);
//        die;
       foreach($filters as $filter)
       {
           //$availablefilter = $filter->getRequestVar(); //Gives the request param name such as 'cat' for Category, 'price' for Price
//           $availablefilter = (string)$filter->getName(); //Gives Display Name of the filter such as Category,Price etc.
           $filterValues = array();
           $j = 0;
           $filterValues['component']=(string)$filter->getName();           
           $items = $filter->getItems(); //Gives all available filter options in that particular filter

           
           foreach($items as $item)
           {
            

               $filterValues['data'][$j]['display'] = strip_tags($item->getLabel());
               $filterValues['data'][$j]['value']   = $item->getValue();
               $filterValues['data'][$j]['count']   = $item->getCount(); //Gives no. of products in each filter options
               $filterValues['data'][$j]['code']   = $item->getFilter()->hasAttributeModel()?$item->getFilter()->getAttributeModel()->getAttributeCode():$item->getFilter()->getRequestVar(); //Gives no. of products in each filter options
               $j++;
           }
           if(!empty($filterValues) && count($filterValues)>1)
           {

               array_push($filterArray,$filterValues);
//               $filterArray['availablefilter'][$availablefilter] =  $filterValues;
           }
           $i++;
       }  


        return $filterArray;

    }

}
