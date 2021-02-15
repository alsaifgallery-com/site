<?php

namespace AlsaifGallery\Product\Model\Data;

class FilterList extends \Magento\Framework\DataObject implements \AlsaifGallery\Product\Api\Data\FilterListInterface {

    /**
     * {@inheritdoc}
     */
    public function getCurrentPage() {
        return $this->getData(self::CURRUNT_PAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterList() {
        return $this->getData(self::FILTER_LIST);
    }

    /**
     * {@inheritdoc}
     */
    public function getPageSize() {
        return $this->getData(self::PAGE_SIZE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentPage($currentPage) {
        return $this->setData(self::CURRUNT_PAGE, $currentPage);
    }

    /**
     * {@inheritdoc}
     */
    public function setFilterList($filterList) {
        return $this->setData(self::FILTER_LIST, $filterList);
    }

    /**
     * {@inheritdoc}
     */
    public function setPageSize($pageSize) {
        return $this->setData(self::PAGE_SIZE, $pageSize);
    }

}
