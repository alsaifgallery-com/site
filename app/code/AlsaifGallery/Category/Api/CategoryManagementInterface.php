<?php
namespace AlsaifGallery\Category\Api;


/**
 * @api
 * @since 100.0.2
 */
interface CategoryManagementInterface
{
    /**
     * Retrieve list of categories
     *
     * @param int $rootCategoryId
     * @param int $depth
     * @throws \Magento\Framework\Exception\NoSuchEntityException If ID is not found
     * @return \AlsaifGallery\Category\Api\Data\CategoryTreeInterface containing Tree objects
     */
    public function getTree($rootCategoryId = null, $depth = null);

}
