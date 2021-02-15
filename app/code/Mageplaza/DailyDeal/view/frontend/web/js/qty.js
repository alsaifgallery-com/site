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
 * @package     Mageplaza_DailyDeal
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';

    $.widget('mageplaza.dailydeal_qty', {
        _create: function () {
            this._updateQty();
        },

        _updateQty: function () {
            var self = this,
                productId = this.options.prodId,
                isSimpleProduct = this.options.isSimpleProduct;
            if (isSimpleProduct) {
                $.ajax({
                    url: 'dailydeal/deal/qty',
                    dataType: 'json',
                    data: {'id': productId},
                    cache: false,
                    success: function (res) {
                        var remainQty = res.qtyRemain,
                            soldQty = res.qtySold;
                        self._initializeQty(remainQty, soldQty);
                    },
                });
            } else {
                var remainQty = this.options.qtyRemain,
                    soldQty = this.options.qtySold;
                self._initializeQty(remainQty, soldQty);
            }
        },

        _initializeQty: function (remainQty, soldQty) {
            var itemLeft = $('.remaining-qty-items').find('.count-items'),
                itemSold = $('.sold-qty-items').find('.count-items'),
                floatRemain = $('.float-remain').find('.widget-qty'),
                floatSold = $('.float-sold').find('.widget-qty');

            itemLeft.html(remainQty);
            itemSold.html(soldQty);
            floatRemain.html(remainQty);
            floatSold.html(soldQty);
        }
    });

    return $.mageplaza.dailydeal_qty;
});