<?php
namespace Alsalamah\InvUpdater\Model;
 
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;

use Alsalamah\InvUpdater\Api\InventoryUpdaterInterface as ApiInterface;
 
class InventoryUpdater implements ApiInterface {
     /**
     *  Repository
     * @var ProductRepositoryInterface $productRepository
     */
    protected $productRepository;

    /**
    * Stock Registry
    * @var StockRegistryInterface $stockRegistry
    */
    protected $stockRegistry;     
 
    /**
    * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry 
    */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        StockRegistryInterface $stockRegistry
    ) {
        $this->productRepository = $productRepository;
        $this->stockRegistry = $stockRegistry;
    }
 
    /**
    * mass update price
    * @param \Alsalamah\InvUpdater\Api\Data\MassPriceQtyUpdateInterface $massPriceUpdate
    * @return \Alsalamah\InvUpdater\Api\Data\MassItemUpdateResultInterface
    */
    public function updatePrice(\Alsalamah\InvUpdater\Api\Data\MassPriceQtyUpdateInterface $massPriceUpdate) {    
        $result = new MassItemUpdateResult();
        $resultList = array();        

		if (!empty($massPriceUpdate->getList())) {
            foreach ($massPriceUpdate->getList() as $product) {
                $singleResult = new SingleItemUpdateResult();                    
				try {
                    $sku = $product->getSku();
                    $price = $product->getPrice();            

                    $singleResult->setSku($sku);
            
                    //price updating
                    $product = $this->productRepository->get($sku);
                    $product->setPrice($price);
                    $this->productRepository->save($product);
                    
                    $singleResult->setSuccess(1);
                    $singleResult->setError('');
                } catch (\Exception $e) {
                    $singleResult->setSuccess(0);
                    $singleResult->setError($e->getMessage());
                }
                array_push($resultList, $singleResult);
            }
        }
        $result->setList($resultList);
        return $result;
    }

    /**
    * mass update qty
    * @param \Alsalamah\InvUpdater\Api\Data\MassPriceQtyUpdateInterface $massQtyUpdate
    * @return \Alsalamah\InvUpdater\Api\Data\MassItemUpdateResultInterface
    */
    public function updateQty(\Alsalamah\InvUpdater\Api\Data\MassPriceQtyUpdateInterface $massQtyUpdate) {    
        $result = new MassItemUpdateResult();
        $resultList = array();        

        if (!empty($massQtyUpdate->getList())) {
            foreach ($massQtyUpdate->getList() as $product) {
                $singleResult = new SingleItemUpdateResult();

                try {
                    $sku = $product->getSku();
                    $qty = $product->getQty();  
                    
                    $singleResult->setSku($sku);
    				
					$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Alsalamah-Update.log');
					$logger = new \Zend\Log\Logger();
					$logger->addWriter($writer);
					$logger->info($sku.": ".$qty);    			
	
                    $stockItem = $this->stockRegistry->getStockItemBySku($sku);
                    $stockItem->setQty($qty);
                    $stockItem->setIsInStock((bool)$qty);
                    $this->stockRegistry->updateStockItemBySku($sku,$stockItem);

                    $singleResult->setSuccess(1);
                    $singleResult->setError('');
                } catch (\Exception $e) {
                    $singleResult->setSuccess(0);
                    $singleResult->setError($e->getMessage());
                }	
                array_push($resultList, $singleResult);							
            }
        }
        $result->setList($resultList);
        return $result;
    }    
}
