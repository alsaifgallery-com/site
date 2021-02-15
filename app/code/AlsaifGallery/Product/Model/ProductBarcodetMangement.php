<?php

namespace AlsaifGallery\Product\Model;

class ProductBarcodetMangement implements \AlsaifGallery\Product\Api\ProductBarcodetInterface
{
    protected $productRepo;
    protected $criteriaBuilder;

    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepo,
        \Magento\Framework\Api\SearchCriteriaBuilder $criteriaBuilder
    ) {
      $this->productRepo= $productRepo;
      $this->criteriaBuilder = $criteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getProduct($barcode, $editMode = false, $storeId = null, $forceReload = false) {
        $this->criteriaBuilder->addFilter("sku", $barcode );
        $this->criteriaBuilder->setCurrentPage(1);
        $this->criteriaBuilder->setPageSize(999);
        $searchCriteria = $this->criteriaBuilder->create();

        $resault = $this->productRepo->getList($searchCriteria);

        if( $resault->getTotalCount() <= 0){
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                    __("There is no Product with provided Barcode.")
                    );
        }
        $items = $resault->getItems();
        $product = reset( $items );

        return $this->productRepo->get( $product->getSku() , $editMode = false, $storeId = null, $forceReload = false);

    }

}
