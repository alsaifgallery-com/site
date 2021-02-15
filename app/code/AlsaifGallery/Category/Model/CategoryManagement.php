<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
// use Magento\Catalog\Api\Data\CategoryTreeInterfaceFactory;
// use NovaMinds\Mobile\Api\Data\CategoryTreeInterfaceFactory;
namespace AlsaifGallery\Category\Model;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

class CategoryManagement extends \Magento\Catalog\Model\CategoryManagement implements \AlsaifGallery\Category\Api\CategoryManagementInterface
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var \AlsaifGallery\Category\Model\Category\Tree
     */
    protected $categoryTree;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    private $categoriesFactory;

    /**
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \AlsaifGallery\Category\Model\Category\Tree $categoryTree
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoriesFactory
     */
    public function __construct(
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \AlsaifGallery\Category\Model\Category\Tree $categoryTree,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoriesFactory
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->categoryTree = $categoryTree;
        $this->categoriesFactory = $categoriesFactory;
    }

    /**
     * {@inheritdoc}
     */
//    public function getTree($rootCategoryId = null, $depth = null)
    //    {
    //        $category = $this->categoryRepository->get($rootCategoryId);
    //        $sss = \Magento\Framework\App\ObjectManager::getInstance()
    //                ->get(\AlsaifGallery\Category\Adapters\SliderInterface::class);
    //        $cc = $sss->getSliders();
    //        //var_dump($cc->getItems());
    //        $itms = $cc->getItems();
    //      foreach( $itms as $k => $v){
    //          var_dump( $v->getData());
    //      }
    // $a = print_r( $itms ,1);
    //
    //
    //              $source = \Magento\Framework\App\ObjectManager::getInstance()
    //                ->get(\AlsaifGallery\Category\Model\Config\Source\Sliders::class);
    //        var_dump($source-> toOptionArray());

    //
    // die( $category ->getName() );
    // $category ->setdata('thumbnail_image','ssss');
    // $category ->setdata('asd','asd');
    // var_dump( $category ->getdata('thumbnail_image') ) ;
    // var_dump( $category ->getdata('asd') ) ;
    //        die;
    //        parent::getTree ($rootCategoryId , $depth);
    //    }
}
