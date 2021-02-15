<?php

namespace AlsaifGallery\StoreCredit\Model;

use Magento\Framework\Exception\LocalizedException;

class EWalletManagement implements \AlsaifGallery\StoreCredit\Api\EWalletManagementInterface
{
    public $storeCreditCollectionFactory;

    public $historyCollectionFactory;

    public $searchBuilder;

    public $historyRepository;

    public $storeRepository;

    public function __construct(
        \Amasty\StoreCredit\Api\StoreCreditRepositoryInterface $storeRepository,
        \Amasty\StoreCredit\Model\StoreCredit\ResourceModel\CollectionFactory $storeCreditCollectionFactory,
        \Amasty\StoreCredit\Model\History\ResourceModel\CollectionFactory $historyCollectionFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchBuilder,
        \Amasty\StoreCredit\Model\History\HistoryRepository $historyRepository
    ) {
        $this->storeCreditCollectionFactory = $storeCreditCollectionFactory;
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->searchBuilder = $searchBuilder;
        $this->historyRepository = $historyRepository;
        $this->storeRepository = $storeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getEWallet($customerId)
    {
        try {
//        $returnedData=[];
            $storeCredit = $this->storeRepository->getByCustomerId($customerId);

            $storeCreditData['store_credit_id'] = $storeCredit->getStoreCreditId();
            $storeCreditData['customer_id'] = $storeCredit->getCustomerId();
            $storeCreditData['store_credit'] = $storeCredit->getStoreCredit();
            if ($storeCredit->getStoreCredit() == 0) {
                $storeCreditData['message'] = "Sorry This Customer Does Not Have Credit Yet";
            } else {
                $storeCreditData['message'] = "";
            }
            return array($storeCreditData);
//            return $storeCredit;
            //
            //        return $storeCredit;
            //        $storeCredit=$this->storeCreditCollectionFactory->create();
            //        $storeCreditCollection=$storeCredit->getByCustomerId($customerId);
            //        if($storeCreditCollection == false){
            //             throw new LocalizedException(
            //                    __("Sorry This Customer Does Not Have Credit Yet")
            //                );
            //
            //        }
            //        return $storeCreditCollection;
        } catch (\Exception $e) {
            throw new LocalizedException(
                __($e->getMessage())
            );
        }

    }
    /**
     * {@inheritdoc}
     */
    public function getEWalletHistory($customerId)
    {
        try {
            $data = array();
            $this->searchBuilder->setCurrentPage(1);
            $searchCriteria = $this->searchBuilder->addFilter('customer_id', $customerId)->create();
            $result = $this->historyRepository->getList($searchCriteria);
            foreach ($result->getItems() as $r) {
                $history['customer_id'] = $r->getCustomerId();
                $history['is_deduct'] = $r->getIsDeduct();
                $history['difference'] = $r->getDifference();
                $history['created_at'] = date("d-m-Y", strtotime($r->getCreatedAt()));
                $history['message'] = ($r->getMessage()) ? $r->getMessage() : '';
                array_push($data, $history);
            }
//        if(count($data)== 0){
//         throw new LocalizedException(
//                    __("Sorry This Customer Does Not Have Credit Yet")
//                );
//        }
        return $data;
        }catch(\Exception $e){
            return $data;
//            throw new LocalizedException(
//                    __($e->getMessage())
//                );    
        }

    }

}
