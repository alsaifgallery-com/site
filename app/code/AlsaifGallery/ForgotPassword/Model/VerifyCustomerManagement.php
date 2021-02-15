<?php


namespace AlsaifGallery\ForgotPassword\Model;

class VerifyCustomerManagement implements \AlsaifGallery\ForgotPassword\Api\VerifyCustomerManagementInterface
{

    protected $smsHelper;
    protected $customerFactory;
    protected $customerRepo;
    protected $forgotPassRepo;
    protected $forgotPassRecordFactory;
    protected $searchBuilder;
    protected $dateFactory;


        /**
* Recipient email config path
*/
const XML_PATH_EMAIL_RECIPIENT = 'forgotpassword/email/send_email';
/**
* @var \Magento\Framework\Mail\Template\TransportBuilder
*/
protected $_transportBuilder;

/**
* @var \Magento\Framework\Translate\Inline\StateInterface
*/
protected $inlineTranslation;

/**
* @var \Magento\Framework\App\Config\ScopeConfigInterface
*/
protected $scopeConfig;

/**
* @var \Magento\Store\Model\StoreManagerInterface
*/
protected $storeManager;
/**
* @var \Magento\Framework\Escaper
*/
protected $_escaper;


    /**
     *
     * @param \AlsaifGallery\ForgotPassword\Api\ForgotPasswordRepositoryInterface $forgotPassRepo
     * @param \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterfaceFactory $forgotPassRecordFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepo
     * @param \AlsaifGallery\ForgotPassword\Helper\Data $smsHelper
     */

    public function __construct(
        \AlsaifGallery\ForgotPassword\Api\ForgotPasswordRepositoryInterface $forgotPassRepo,
        \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterfaceFactory $forgotPassRecordFactory,

        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepo,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchBuilder,

        \AlsaifGallery\ForgotPassword\Helper\Data $smsHelper,
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory  $dateFactory,

        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Escaper $escaper


    ){
       $this->smsHelper = $smsHelper;
       $this->customerFactory = $customerFactory;
       $this->customerRepo = $customerRepo;
       $this->forgotPassRepo = $forgotPassRepo;
       $this->forgotPassRecordFactory = $forgotPassRecordFactory;
       $this->searchBuilder = $searchBuilder;
       $this->dateFactory = $dateFactory;

        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->_escaper = $escaper;


    }
    /**
     * {@inheritdoc}
     */
//    public function postVerifyCustomer($customerData)
    public function postVerifyCustomer($identity)
    {   // return [$type, $identity] ;

        $customer = null;
        $validator = new \Zend\Validator\EmailAddress();
        if ($validator->isValid(  $identity  )) {
            $email = $identity ;
            try{
                $customer = $this->customerRepo->get($email);
            }catch( \Magento\Framework\Exception\NoSuchEntityException $e ){
               throw new \Magento\Framework\Exception\NoSuchEntityException (
                    __("customer with the specified email does not exist.")
               );
            }
            // return "email";
        }else{ // phone or not good data
            $this->searchBuilder->setCurrentPage(1);
            $this->searchBuilder->setPageSize(99999);
            $value = $identity;
            $this->searchBuilder->addFilter('phone',$value);
            $searchCriteria = $this->searchBuilder->create();
            $resault = $this->customerRepo->getList($searchCriteria);
            if( $resault->getTotalCount() > 0 ){
                $arrCustomers = $resault->getItems();
                $customer =  $arrCustomers [0];
            }
            // return $resault->getTotalCount();
            // return "no";

        }
        if(is_null( $customer )){
            throw new \Magento\Framework\Exception\LocalizedException(
                    __("identity must be correct Phone or Email")
                    );
        }else{
            if( $customer->getId()){
               $returnData[] = [
                                'message' => __("Send code via email"),
                                'value'   => $customer->getEmail(),
                                'mask'    => $this->mask($customer->getEmail(),"email"),
                                'code'    =>'email',
                               ];

               $phoneAttr = $customer->getCustomAttribute("phone");
               $phoneCodeAttr = $customer->getCustomAttribute("phone_code");
               if ( !is_null($phoneAttr)){
                    $phone = $phoneAttr ->getValue();
                    $phoneCode = "";
                    if ( !is_null($phoneCodeAttr)){
                        $phoneCode = $phoneCodeAttr ->getValue();
                    }
                    if( isset($phone) && !is_null($phone) && !empty($phone)){
                        $returnData[] = [
                                            'message' => __("Send code via phone"),
                                            'value'   => $phone,
                                            'phone_code'   => $phoneCode,
//                                            'mask'    => $this->mask( $phoneCode.$phone , "phone") ,
                                            'mask'    => $this->mask( $phone , "phone") ,
                                            'code'    => 'phone',
                                         ];
                    }
               }

               return $returnData;
            }

        }


    }

    private function maskPhoneNumber($number){
        $mask_number =  substr($number,0,1). str_repeat("*", strlen($number)-3) . substr($number, -2);
        return $mask_number;
    }
    private function maskEmail($email , $fill=4 ){
            $user=strstr($email,'@',true);  // extract entire substring before @
            $len=strlen($user);  // measure length of username string
            if($len>$fill+2){$fill=$len-2;} // increase fill amount for more asterisks in str_repeat
            $starred=substr($user,0,1).str_repeat("*",$fill).substr($user,-1).strstr($email,'@');
            // 1st char-----------^^^ fill--------^^^^^^^^^ last char-----^^  ^^^^^^^^^^^^^^^^^-the rest of the original email string
            return  $starred;
    }
    private function mask($maskInput , $type = "phone" ) {
        if( $type == "email"){
            return $this->maskEmail($maskInput);
        }
        // phone case
        return  $this->maskPhoneNumber($maskInput);
    }

    /**
     * {@inheritdoc}
     */
    public function postSendCode($identity,$type)
    {   // return [$type, $identity] ;
        $typeVals = ['phone','email'];
        if(!in_array( $type, $typeVals)){
            throw new \Magento\Framework\Exception\LocalizedException(
                    __("type must be in [".implode(',', $typeVals)."].")
                    );
        }
        $customer = null;
        $validator = new \Zend\Validator\EmailAddress();
        if ($validator->isValid( $identity )) {
            $email = $identity;
            // $customer = $this->customerRepo->get($email);
            try{
                $customer = $this->customerRepo->get($email);
            }catch( \Magento\Framework\Exception\NoSuchEntityException $e ){
               throw new \Magento\Framework\Exception\NoSuchEntityException (
                    __("Customer with the specified email does not exist.")
               );
            }
            // return "email";
        }else{ // phone or not good data
            $this->searchBuilder->setCurrentPage(1);
            $this->searchBuilder->setPageSize(99999);
            $value = $identity;
            $this->searchBuilder->addFilter('phone',$value);
            $searchCriteria = $this->searchBuilder->create();
            $resault = $this->customerRepo->getList($searchCriteria);
            if( $resault->getTotalCount() > 0 ){
                $arrCustomers = $resault->getItems();
                $customer =  $arrCustomers [0];
            }
            // return $resault->getTotalCount();
            // return "no";

        }
        if(is_null( $customer )){
            throw new \Magento\Framework\Exception\LocalizedException(
                    __("identity must be Phone or Email")
                    );
        }else{
            if( $customer->getId()){
                $forgotPasswordRecord = $this->forgotPassRecordFactory->create();
                $forgotPasswordRecord->setAllData([
                    'type'=> $type,
                    'customer_id'=>$customer->getId(),
                    'created_at'=> $this->dateFactory->create()->gmtDate(),
                    'request_number'=>1,
                    'request_token'=> md5($customer->getId().$customer->getEmail()."alsaifgallery". uniqid(rand())),
                    'request_token_status'=>false,
                    // 'verify_code'=>  uniqid(rand()) ,
                    'verify_code'=>  random_int(1000,9999) ,
                    'verify_code_status'=>false,
                    'send_status'=> false,
                ]);
                // return  $forgotPasswordRecord ->getRequestToken();
                $forgotPasswordRecord = $this->forgotPassRepo->save($forgotPasswordRecord);



                // customer exists
                // what is the type of verfy ???
                // sms
                // ---- send sms
                // mail
                // ---- send mail
                $typeOfverfiication = $type;
                switch ($typeOfverfiication) {
                    case "phone":
                        $retSendSms =  $this->sendSms( $forgotPasswordRecord );
                        if( $retSendSms == true){
                            $ret=[
                                "status"  => $retSendSms,
                                "message" => __("Sms sent successfully")->render(),
                            ];
                        }else{
                            throw new \Magento\Framework\Exception\LocalizedException (
                                __( "Can not send sms." )
                            );
                            $ret["status"]= false;
                            $ret["message"]= __( "Can not send sms." )->render();
                        }

                        // $retJson = json_decode($ret);
                        // $ret["status"]=$retJson->success;
                        // $ret["message"]=$retJson->message;
                        // $ret["error_code"]=$retJson->errorCode;
                        return [$ret];
                        break;
                    case "email":
                        $ret =  $this->sendEmail( $forgotPasswordRecord );
                        if( $ret == true){
                            $ret=[
                                "status"  => $ret,
                                "message" => __("Email sent successfully")->render(),
                            ];
                        }else{
                            throw new \Magento\Framework\Exception\LocalizedException (
                                __( "Can not send email." )
                            );
                        }
                        return [$ret];
                        break;
                    default:
                        throw new \Magento\Framework\Exception\LocalizedException (
                            __( "Error: not support type." )
                        );
                        //return "Error: not support type";
                }

                // ?? what about more then one record for acustomer
                // -- select the latest added ??


            }

        }


    }

    private  function sendSms( $forgotPasswordRecord ){
        $return = false;
        $customer = $forgotPasswordRecord->getCustomer();
        $mobile = $phone = $phoneCode = "";
        $phoneAttr = $customer->getCustomAttribute('phone');
        if(!is_null($phoneAttr)){
//            $mobile= $phoneAttr->getValue();
            $mobile = $phone = $phoneAttr->getValue();
        }

        $phoneCodeAttr = $customer->getCustomAttribute('phone_code');
        if(!is_null($phoneCodeAttr)){
            $phoneCode = $phoneCodeAttr->getValue();
        }
          // var_dump ($mobile);die();
          if(empty( $mobile)){
              // return "Mobile phone not provided";
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __(
                   "Mobile phone not provided."
                )
            );
          }

          // check phone
          if(stripos((string) $phone,"+") !== false) {
              $mobile = $phone;
          }else{
              $mobile = $phoneCode.$phone;
          }


          // $verifycode = $forgotPasswordRecord->getVerifyCode();
          // 4 digits
          $verifycode = rand(1000,9999);
          $response = $this->smsHelper->sentVerifyCodeMessage($mobile,$verifycode);
            $responseDecoded  = json_decode($response,true);
             // var_dump($response);die;
            // var_dump( $response['success'] , strtolower( $response['success'] ), boolval($response['success']) , ( boolval($response['success']) == true )  );die;
            if ( isset($responseDecoded['success']) &&  strtolower( $responseDecoded['success'] )  == "true" ) {

                $forgotPasswordRecord->setSendStatus( true );
                $forgotPasswordRecord->setVerifyCode( $verifycode );
                $this->forgotPassRepo->save($forgotPasswordRecord);
                $return= true;
                // var_dump("helal", $responseDecoded);die;
                // var_dump($response);die;

//                $arr['status'] = 'success';
//                $arr['verify_id'] = $response['data']['VerifyID'];
//                $arr['message_id'] = $response['data']['MessageID'];
//                $arr['message_status'] = $response['data']['MessageStatus'];
            } else {
                // {"success":"false","message":"unsupported destination","errorCode":"ER-11","data":[]}
                //$res = json_decode($response);
                throw new \Magento\Framework\Exception\LocalizedException (
                    __( $responseDecoded->message )
               );
                // return  $response;
            }

        // var_dump( $return );die;
        return $return;

    }

    private function sendEmail( $forgotPasswordRecord ){
        $return = false;

        $this->inlineTranslation->suspend();

try {
$postObject = new \Magento\Framework\DataObject();
$post['name']="Helal";
$post['email']="a.helal@nmcit.com";
$post['verifycode'] = $forgotPasswordRecord->getVerifyCode();
// $post['verify_code'] = $forgotPasswordRecord->getVerifyCode();
$postObject->setData($post);


$error = false;

$sender = [
'name' => 'alsaifgallery.com',
'email' => 'verify@alsaifgallery.com',
];
$customer = $forgotPasswordRecord->getCustomer();
 $to = $customer->getEmail();
 $toName = $customer->getFirstName()." ".$customer->getLastName();

$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

//var_dump( $this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope) );die;
$transport = $this->_transportBuilder
->setTemplateIdentifier('send_email_email_template_forgot_password_verify') // this code we have mentioned in the email_templates.xml
->setTemplateOptions(
[
'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
]
)
->setTemplateVars(['data' => $postObject])
->setFrom($sender)
->addTo(  $to , $toName)

 ->getTransport();


$transport->sendMessage();

$this->inlineTranslation->resume();

$forgotPasswordRecord->setSendStatus( true );
$this->forgotPassRepo->save($forgotPasswordRecord);

$return = true;

} catch (\Exception $e) {
    throw  $e;

//    throw new \Magento\Framework\Exception\LocalizedException (
//        __( $res->message )
//    );
// return $e->getMessage();
//$this->inlineTranslation->resume();
// return __('We can\'t process your request right now. Sorry, that\'s all we know.'.$e->getMessage());

}

        return $return;
    }

}
