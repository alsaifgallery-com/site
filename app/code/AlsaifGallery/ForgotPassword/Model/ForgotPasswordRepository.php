<?php


namespace AlsaifGallery\ForgotPassword\Model;

use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordSearchResultsInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use AlsaifGallery\ForgotPassword\Model\ResourceModel\ForgotPassword as ResourceForgotPassword;
use AlsaifGallery\ForgotPassword\Api\ForgotPasswordRepositoryInterface;
use AlsaifGallery\ForgotPassword\Model\ResourceModel\ForgotPassword\CollectionFactory as ForgotPasswordCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;

class ForgotPasswordRepository implements ForgotPasswordRepositoryInterface
{

    protected $forgotPasswordFactory;

    protected $extensibleDataObjectConverter;
    private $storeManager;

    protected $searchResultsFactory;

    protected $dataObjectProcessor;

    protected $extensionAttributesJoinProcessor;

    protected $dataForgotPasswordFactory;

    protected $forgotPasswordCollectionFactory;

    protected $dataObjectHelper;

    private $collectionProcessor;

    protected $resource;


    /**
     * @param ResourceForgotPassword $resource
     * @param ForgotPasswordFactory $forgotPasswordFactory
     * @param ForgotPasswordInterfaceFactory $dataForgotPasswordFactory
     * @param ForgotPasswordCollectionFactory $forgotPasswordCollectionFactory
     * @param ForgotPasswordSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceForgotPassword $resource,
        ForgotPasswordFactory $forgotPasswordFactory,
        ForgotPasswordInterfaceFactory $dataForgotPasswordFactory,
        ForgotPasswordCollectionFactory $forgotPasswordCollectionFactory,
        ForgotPasswordSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->forgotPasswordFactory = $forgotPasswordFactory;
        $this->forgotPasswordCollectionFactory = $forgotPasswordCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataForgotPasswordFactory = $dataForgotPasswordFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface $forgotPassword
    ) {
        /* if (empty($forgotPassword->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $forgotPassword->setStoreId($storeId);
        } */
        
        $forgotPasswordData = $this->extensibleDataObjectConverter->toNestedArray(
            $forgotPassword,
            [],
            \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface::class
        );
        
        $forgotPasswordModel = $this->forgotPasswordFactory->create()->setData($forgotPasswordData);
        
        try {
            $this->resource->save($forgotPasswordModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the forgotPassword: %1',
                $exception->getMessage()
            ));
        }
        return $forgotPasswordModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getById($forgotPasswordId)
    {
        $forgotPassword = $this->forgotPasswordFactory->create();
        $this->resource->load($forgotPassword, $forgotPasswordId);
        if (!$forgotPassword->getId()) {
            throw new NoSuchEntityException(__('forgotPassword with id "%1" does not exist.', $forgotPasswordId));
        }
        return $forgotPassword->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getByVerifyCode($forgotpasswordVerifyCode)
    {
        $forgotPassword = $this->forgotPasswordFactory->create();
        $this->resource->load($forgotPassword, $forgotpasswordVerifyCode,'verify_code');
        if (!$forgotPassword->getId()) {
            throw new NoSuchEntityException(__('forgotPassword with verify code "%1" does not exist.', $forgotpasswordVerifyCode));
        }
        return $forgotPassword->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getByToken($forgotpasswordToken)
    {
        $forgotPassword = $this->forgotPasswordFactory->create();
        $this->resource->load($forgotPassword, $forgotpasswordToken,'request_token');
        if (!$forgotPassword->getId()) {
            throw new NoSuchEntityException(__('forgotPassword with Token "%1" does not exist.', $forgotpasswordToken));
        }
        return $forgotPassword->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->forgotPasswordCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface::class
        );
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface $forgotPassword
    ) {
        try {
            $forgotPasswordModel = $this->forgotPasswordFactory->create();
            $this->resource->load($forgotPasswordModel, $forgotPassword->getForgotpasswordId());
            $this->resource->delete($forgotPasswordModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the forgotPassword: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($forgotPasswordId)
    {
        return $this->delete($this->getById($forgotPasswordId));
    }
}
