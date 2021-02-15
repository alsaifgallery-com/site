/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Faqs
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define(['jquery', 'mage/translate'], function ($, $t) {
        var faqCount, hiddenfaqItem, visiblefaqItem;

        return function (mpSelector, limitCount, isCollapsible) {
            mpSelector.listBlock.each(function () {
                var el = this;
                faqCount = $(el).find(mpSelector.item).length;
                $(el).find(mpSelector.item).slice(0, limitCount).show();
                if (faqCount <= limitCount) {
                    $(el).find(mpSelector.viewAll).hide();
                } else {
                    $(el).find(mpSelector.viewAllButton).on('click', function () {
                        hiddenfaqItem = $(el).find(mpSelector.itemHidden);
                        visiblefaqItem = $(el).find(mpSelector.item);
                        if ($(this).hasClass("load-more")) {
                            hiddenfaqItem.slideDown('fast');
                            $(this).parent().find('i').removeClass('fa-angle-double-down').addClass('fa-angle-double-up');
                            $(this).removeClass('load-more').addClass('back-to-top');
                            $(this).html('<a> ' + $t('Collapse') + '</a>');
                        } else {
                            visiblefaqItem.slice(limitCount, visiblefaqItem.length).slideUp('fast');
                            $(this).parent().find('i').removeClass('fa-angle-double-up').addClass('fa-angle-double-down');
                            $(this).removeClass('back-to-top').addClass('load-more');
                            $(this).html('<a> ' + $t('View all') + '</a>');
                        }
                    });
                }
            });
            if (isCollapsible) {
                //collapsible
                mpSelector.itemHeader.each(function () {
                    $(this).on('click', function () {
                        if ($(this).hasClass('in-active')) {
                            $(this).removeClass('in-active').addClass('active');
                            $(mpSelector.itemContent + $(this).attr('data-id')).slideDown('fast');
                        } else {
                            $(this).removeClass('active').addClass('in-active');
                            $(mpSelector.itemContent + $(this).attr('data-id')).slideUp('fast');
                        }
                    });
                });
            }
        };

    }
);