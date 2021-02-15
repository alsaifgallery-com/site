<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_Popup
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\Popup\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Resource Model Popup 
     *
     * @var \Bss\Popup\Model\ResourceModel\Popup
     */
    protected $popup;

    /**
     * Date Time
     *
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $datetime;

    /**
     * Scope Config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Popup Cookie
     *
     * @var \Bss\Popup\Model\PopupCookie
     */
    protected $popupCookie;

    /**
     * Session Manager
     *
     * @var \Magento\Framework\Session\SessionManager
     */
    protected $sessionManager;

    /**
     * Customer Session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Constructor
     *
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Bss\Popup\Model\PopupCookie $popupCookie
     * @param \Magento\Framework\Session\SessionManager $sessionManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $datetime
     * @param \Bss\Popup\Model\ResourceModel\Popup $popup
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct (
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Bss\Popup\Model\PopupCookie $popupCookie,
        \Magento\Framework\Session\SessionManagerFactory $sessionManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $datetime,
        \Bss\Popup\Model\ResourceModel\Popup $popup,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->popupCookie = $popupCookie;
        $this->sessionManager = $sessionManager;
        $this->customerSession = $customerSession;
        $this->scopeConfig = $context->getScopeConfig();
        $this->datetime = $datetime;
        $this->popup = $popup;
        parent::__construct($context);
    }

    /**
     * Get Popup
     *
     * @param int $page
     * @return array
     */
    public function getPopup($page)
    {
        $popups = $this->popup->getPopupByDate();
        $popups = $this->removeOtherPagePopup($page, $popups);
        $popups = $this->removePopupExpired($popups);
        return $popups;
    }

    /**
     * Remove Popup Expired
     *
     * @param array $popups
     * @return array
     */
    public function removePopupExpired($popups)
    {
        $date = $this->datetime->date()->format('Y-m-d H:i:s');
        foreach ($popups as $key => $value) {
            if ((!empty($value['display_to']) && $value['display_to'] <= $date)
                || (!empty($value['display_from']) && $value['display_from'] >= $date)
            ) {
                unset($popups[$key]);
            }
        }
        return $popups;
    }

    /**
     * Remove Popup In Other Page
     *
     * @param string $page
     * @param string $popups
     * @return array
     */
    public function removeOtherPagePopup($page, $popups)
    {
        foreach ($popups as $key => $value) {
            $pages = explode(",", $value['page_display']);
            if (!in_array($page, $pages)
            ) {
                unset($popups[$key]);
            }
        }
        return $popups;
    }

    /**
     * Get Session Page Viewed By Customer
     *
     * @return array
     */
    public function getSessionPageViewedByCustomer()
    {
        if ($this->sessionManager->create()->getPagesViewedByCustomer()) {
            $this->sessionManager->create()->setPagesViewedByCustomer($this->sessionManager->create()->getPagesViewedByCustomer() +1);
        } else {
            $this->sessionManager->create()->setPagesViewedByCustomer(1);
        }

        return $this->sessionManager->create()->getPagesViewedByCustomer();
    }

    /**
     * Add Popup To Session Displayed Popup
     *
     * @param int $id
     * @return void
     */
    public function addPopupToSessionDisplayedPopup($id)
    {
        $displayedPopup = (!empty($this->sessionManager->create()->getDisplayedPopups()))?
                            $this->sessionManager->create()->getDisplayedPopups(): [0];

        if (!in_array($id, $displayedPopup)) {
            $displayedPopup[] = $id;
        }

        $this->sessionManager->create()->setDisplayedPopups($displayedPopup);
    }

    /**
     * Popup Not In Session
     *
     * @param int $id
     * @return bool|int
     */
    public function popupNotInSession($id)
    {

        $displayed = true;
        $listPopupDisplayed = (!empty($this->sessionManager->create()->getDisplayedPopups()))?
            $this->sessionManager->create()->getDisplayedPopups(): [0];
        if (in_array($id, $listPopupDisplayed)) {
            $displayed = 0;
        }

        return $displayed;
    }

    /**
     * @param $id
     * @param $duration
     * @throws \Exception
     */
    public function addPopupToCookie($id, $duration)
    {
        $cookieName = "popupCookie".$id;
        if (empty($this->popupCookie->get($cookieName))) {
            $this->popupCookie->set($cookieName, "popup{$id}", $duration);
        }
    }

    /**
     * Get Popup Cookie
     * @param int $id
     * @return string
     */
    public function getPopupCookie($id)
    {
        $cookieName = "popupCookie".$id;
        return $this->popupCookie->get($cookieName);
    }

    /**
     * Popup Is Allowed Display
     *
     * @param array $popup
     * @return bool|int
     */
    public function popupIsAllowedDisplay($popup)
    {
        switch ($popup['frequently']) {
            case 1:
                return true;
            case 2:
                return $this->popupNotInSession($popup['popup_id']);
            case 3:
                if (!empty($this->getPopupCookie($popup['popup_id']))) {
                    return 0;
                }
                return true;
            default:
                return 0;
        }
        return 0;
    }

    /**
     * Get Customer Group Id
     *
     * @return int
     */
    public function getCustomerGroupId()
    {
        if ($this->customerSession->create()->isLoggedIn()) {
            $groupId = $this->customerSession->create()->getCustomerGroupId();
            return $groupId;
        }
        return 0;
    }
    
    /**
     * Get Bss Ajax Cart Enable
     *
     * @return bool
     */
    public function isAjaxCartBssEnable()
    {
        return $this->scopeConfig->isSetFlag(
            'ajaxcart/general/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Bss Ajax Cart Add To Cart Selector
     *
     * @return string
     */
    public function getAddToCartSelector()
    {
        return $this->scopeConfig->getValue(
            'ajaxcart/general/selector',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
