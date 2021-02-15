<?php


namespace AlsaifGallery\ForgotPassword\Model;

class VerifyCodeManagement implements \AlsaifGallery\ForgotPassword\Api\VerifyCodeManagementInterface
{
    protected $forgotPassRepo;
    protected $forgotPassRecordFactory;
    protected $smsHelper;

    /**
     * 
     * @param \AlsaifGallery\ForgotPassword\Api\ForgotPasswordRepositoryInterface $forgotPassRepo
     * @param \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterfaceFactory $forgotPassRecordFactory
     */
     public function __construct(
        \AlsaifGallery\ForgotPassword\Api\ForgotPasswordRepositoryInterface $forgotPassRepo,
        \AlsaifGallery\ForgotPassword\Helper\Data $smsHelper,
        \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterfaceFactory $forgotPassRecordFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepo
            
              
    ){
       $this->forgotPassRepo = $forgotPassRepo;
       $this->forgotPassRecordFactory = $forgotPassRecordFactory;
       $this->smsHelper =$smsHelper;
       $this->customerRepo =  $customerRepo;
   
    }
    
    

    /**
     * {@inheritdoc}
     */
    public function postVerifyCode($code)
    {
        $forgotpasswordVerifyCode =  $code ;
        try{
            $forgotpasswordRecord = $this->forgotPassRepo->getByVerifyCode($forgotpasswordVerifyCode);
        } catch (\Exception $e){
            throw new \Magento\Framework\Exception\LocalizedException(
                 __( "Error recod data  did not exist.")
            );
        }
        
        if($forgotpasswordRecord->getForgotpasswordId()){
            if( $forgotpasswordRecord->getSendStatus() != "1"){
                // return "Error recode data  did not sent";
                throw new \Magento\Framework\Exception\LocalizedException(
                 __( "Error recode data  did not sent.")
                );
            }
            /* Deprecated
            if( $forgotpasswordRecord->getType() == "phone"){
//                return "Error recode data  did not sent";
                $customer = $forgotpasswordRecord->getCustomer();
                $mobile = $customer->getCustomAttribute('phone')->getValue();
                $response = $this->smsHelper->sentVerifyNumber($mobile,$code);
                $result = json_decode($response,true);
                if( isset($result['success']) && ( strtolower( $response['success'] )  == "true" ) ){
                    $forgotpasswordRecord ->setVerifyCodeStatus(true);
                    // customer set mobile is valid.
                    //$customer->getCustomAttribute('phone_verified')->setValue(true);
                    
                }else{
                   throw new \Magento\Framework\Exception\LocalizedException(
                        __( "Error : sms verfication faild.")
                    );
                }
                
            }else{
                //email 
                $forgotpasswordRecord ->setVerifyCodeStatus(true);
            }
             */
            
            if( $forgotpasswordRecord->getType() == "phone"){
                //phone_verified   
                $customer = $forgotpasswordRecord->getCustomer();
                $customer->setCustomAttribute('phone_verified',1);
                $this->customerRepo->save( $customer );
            }
            
            $forgotpasswordRecord ->setVerifyCodeStatus(true);
            $returnObj2 = new \Magento\Framework\DataObject([
                'forgotpassword_request_token'=>$forgotpasswordRecord->getRequestToken(),
            ]);
            // $returnObj2->addData($return);
                    
            
            $forgotpasswordRecord = $this->forgotPassRepo->save($forgotpasswordRecord);
            // return [$return];
            return  [$returnObj2->toArray()];
        }
        // return 'Record With $code did not exist.' ;
       return [
           "status"  => false,
           "message" => __("Record With this code did not exist.")->jsonSerialize(),
       ];
    }
}
