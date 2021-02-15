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

define([
        'jquery',
        'Bss_Popup/js/mfpopup'
    ],
    function($) {
        return function(config){
            $(document).ready(function(){
                var autoClose = config.hideAfter;
                var timeDelay = (config.effectDisplay)? 500 : 0;
                var displayed = false;
                var allowDisplay = config.popupIsAllowedDisplay;

                function displayPopup(allowDisplay, autoClose, timeDelay)
                {
                    $.ajax({
                        url: config.checkTimeUrl,
                        type: 'POST',
                        data: {
                            popupId: config.popupId
                        },
                        success : function(result) {

                            if (allowDisplay && result.res === true) {
                                $(".popup_wrapper").css({"display":"block"});
                                $.magnificPopup.open({
                                    items: {
                                        src: '.popup_wrapper'
                                    },
                                    removalDelay: timeDelay,
                                    mainClass: config.animation,
                                    alignTop: config.flagTop
                                });

                                if (autoClose) {
                                    setTimeout(function(){
                                        $.magnificPopup.close();
                                    },autoClose*1000);
                                }
                            }
                        }
                    });

                }

                function updatePopupDisplayed(popupId)
                {
                    $.ajax({
                        url: config.updateUrl,
                        type: 'POST',
                        data: {
                            popupId: popupId
                        }
                    });
                }

                var eventDisplay = config.eventDisplay;

                switch(eventDisplay) {
                    case 1:
                        setTimeout(function() {
                            displayPopup(allowDisplay, autoClose, timeDelay);
                            updatePopupDisplayed(config.popupId);
                        }, config.afterLoad*1000);
                        break;
                    case 2:
                        $(window).on('scroll', function() {
                            var scrollPosition = config.afterScroll*$(window).height()/100;
                            if ($(this).scrollTop() >= scrollPosition && !displayed) {
                                displayPopup(allowDisplay, autoClose, timeDelay);
                                updatePopupDisplayed(config.popupId);
                                displayed = true;
                            }
                        });
                        break;
                    case 3:
                        var pagesViewed = config.pagesViewed;
                        var popupPages = config.popupPages;
                        if (popupPages<= pagesViewed) {
                            displayPopup(allowDisplay, autoClose, timeDelay);
                            updatePopupDisplayed(config.popupId);
                        }
                        break;
                    case 4:
                        displayPopup(allowDisplay, autoClose, timeDelay);
                        updatePopupDisplayed(config.popupId);
                        break;
                    default:
                        break;
                };
            });
        }
    });