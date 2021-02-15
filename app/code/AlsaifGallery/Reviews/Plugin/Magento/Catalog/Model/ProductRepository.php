<?php

namespace AlsaifGallery\Reviews\Plugin\Magento\Catalog\Model;

class ProductRepository {

    public $productExtensionFactory;
    public $productFactory;
    public $reviewManagement;

    public function __construct(
            \Ipragmatech\Ipreview\Model\ReviewManagement $reviewManagement,
            \Magento\Catalog\Api\Data\ProductExtensionFactory $productExtensionFactory,
            \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->productFactory = $productFactory;
        $this->productExtensionFactory = $productExtensionFactory;
        $this->reviewManagement = $reviewManagement;
    }

    public function afterGet(
            \Magento\Catalog\Api\ProductRepositoryInterface $object,
            $result, $sku, $editMode = false,
            $storeId = null, $forceReload = false
    ) {
        $productExtension = $result->getExtensionAttributes();
        if (null === $productExtension) {
            $productExtension = $this->productExtensionFactory->create();
        }
        $reviews = $this->reviewManagement->getReviewsList($result->getId());

        $productExtension->setReviews($reviews);
        $result->setExtensionAttributes($productExtension);

        return $result;
    }

}
