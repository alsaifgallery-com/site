<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace AlsaifGallery\Images360\Model;

use Magento\Framework\App\Filesystem\DirectoryList;

class AllImagesManagement implements \AlsaifGallery\Images360\Api\AllImagesManagementInterface
{

  public function __construct(
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
		$this->_filesystem = $filesystem;
		$this->_storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllImages($id)
    {
      $productId = $id;
      $dir = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('wysiwyg/360/'.$productId);

      $result = [];
      $files = [];
      if(is_dir($dir)) {
              if ($dh = opendir($dir)) {
                  while ($files[] = readdir($dh));
          sort($files);
          foreach ($files as $file){
            $file_parts = pathinfo($dir . $file);
            if (isset($file_parts['extension']) && (($file_parts['extension'] == 'jpg') || ($file_parts['extension'] == 'png'))) {
                          $result[] = $this->getMediaUrl().'wysiwyg/360/'.$productId.'/'.$file;
                      }
          }
                  closedir($dh);
              }
          }
      return $result;
        // return 'hello api GET return the $id ' . $id;
    }

    public function getMediaUrl(){
  		return $this ->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
  	}
}
