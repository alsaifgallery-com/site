<?php


namespace AlsaifGallery\Pages\Api;
use Magento\Cms\Api\Data\PageSearchResultsInterface;
interface PageManagementInterface
{

    /**
     * GET for Page api
     * @param string $identifier
     * @return \Magento\Cms\Api\Data\PageInterface
     */
    public function getPageByIdentifier($identifier);
}
