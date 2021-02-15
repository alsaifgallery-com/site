<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MGS\ThemeSettings\Controller\Menu;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class Subs extends \Magento\Framework\App\Action\Action
{
	protected $_filesystem;

	protected $_storeManager;

	public function __construct(
    \Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
    \MGS\ThemeSettings\Block\Menu\Items $menu
    ) {
        parent::__construct($context);
		$this->_filesystem = $filesystem;
    $this->_menu = $menu;
		$this->_storeManager = $storeManager;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
      $categoryId = $this->getRequest()->getParam('categoryId');
      $subCategories = $this->_menu->getCategory($categoryId);
			$categoryUrl = $this->_menu->getCategoryUrl($categoryId);
      $url = $this->getMediaUrl();
      $menuContent = '';
      $icon = '';
      foreach ($subCategories as $category) {
				$image = $this->_menu->getCategoryImage($category->getId());
        if (!empty($image)) {
            $icon = trim($url, '/') . '/catalog/category/' . $image;
        } else {
          $icon = "http://alsaifgallery.com/pub/media/catalog/product/placeholder/default/alsaif-placeholder.png";
        }
        echo '<div><a href='.$category->getUrl().' data-id='.$category->getId().'><img src='.$icon.'><span>'.$category->getName().'</span></a></div>';
      }
			$currentCategoryIcon = $url.'wysiwyg/Group_657_2x.png';
			echo '<div><a href='. $categoryUrl .'><img src='.$currentCategoryIcon.'><span>'. __('All Categories') .'</span></a></div>';
			die();
    }

  	public function getMediaUrl(){
  		return $this ->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
  	}

    public function getSubMenu($categoryId) {
        $subCategories = $this->_menu->getCategory($categoryId);
        $url = $this->getMediaUrl();
        foreach ($subCategories as $category) {
          if (!empty($category->getThumbnail())) {
              $icon = trim($url, '/') . '/catalog/category/' . $category->getThumbnail();
          }
          $menuContent .= "<div><div/>"; //"<div><a href=" . echo $category->getUrl() . " data-id=" . echo $category->getId() . "><img src=" . echo $icon . "><span>" . echo $category->getName() . "</span></a></div>";        }
        }
        return $menuContent;
    }
}
