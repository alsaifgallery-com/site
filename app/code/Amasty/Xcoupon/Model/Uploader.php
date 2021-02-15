<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xcoupon
 */

namespace Amasty\Xcoupon\Model;

/**
 * Class Uploader
 * @package Amasty\Xcoupon\Model
 * @author Artem Brunevski
 */
class Uploader extends \Magento\Framework\DataObject
{
    const FILE_PATH_UPLOAD = 'amasty/xcoupon/import/';

    /** @var \Magento\MediaStorage\Model\File\UploaderFactory  */
    protected $uploaderFactory;

    /** @var \Magento\Framework\Filesystem\Directory\WriteInterface  */
    protected $mediaDirectory;

    /** @var \Magento\Framework\Filesystem  */
    protected $filesystem;

    /** @var string */
    protected $resultPath;

    /**
     * Constructor
     *
     * By default is looking for first argument as array and assigns it as object attributes
     * This behavior may change in child classes
     *
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param array $data
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        array $data = []
    ) {
        $this->filesystem = $filesystem;
        $this->uploaderFactory = $uploaderFactory;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(
            \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
        );

        parent::__construct($data);
    }

    /**
     * @param $fileId
     * @return \Magento\Framework\Filesystem\File\WriteInterface
     * @throws \Exception
     */
    public function upload($fileId)
    {
        /** @var \Magento\MediaStorage\Model\File\Uploader $uploader */
        $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);

        $path = $this->filesystem->getDirectoryRead(
            \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
        )->getAbsolutePath(
            self::FILE_PATH_UPLOAD
        );

        /** @var $uploader \Magento\MediaStorage\Model\File\Uploader */

        $uploader->setAllowedExtensions(['csv', 'txt']);
        $uploader->setAllowRenameFiles(true);
        $result = $uploader->save($path);

        $this->resultPath = self::FILE_PATH_UPLOAD . $result['file'];

        return $this->mediaDirectory->openFile($this->resultPath, 'r');
    }

    /**
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function delete()
    {
        $this->mediaDirectory->delete($this->resultPath);
    }
}
