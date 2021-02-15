<?php

namespace AlsaifGallery\Productsgrid\Model\Category;

class Categorylist implements \Magento\Framework\Option\ArrayInterface
{
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionFactory
    ) {
        $this->_categoryCollectionFactory = $collectionFactory;
        
    }

    public function toOptionArray($addEmpty = true)
    {
		
        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $collection */
        $collection = $this->_categoryCollectionFactory->create();

        $collection->addAttributeToSelect('name');//->addRootLevelFilter()->load();

        $options = [];

        if ($addEmpty) {
            $options[] = ['label' => __('-- Please Select a Category --'), 'value' => ''];
        }
        foreach ($collection as $category) {
			$tab = '';
			if($category->getLevel() == 2)
				$tab = '--';
			if($category->getLevel() == 3)
				$tab = '----';
			if($category->getLevel() == 4)
				$tab = '--------';
			if($category->getLevel() == 5)
				$tab = '----------------';
            $options[] = ['label' => $tab.' '.$category->getName(), 'value' => $category->getId()];
        }

        return $options;
    }
}
