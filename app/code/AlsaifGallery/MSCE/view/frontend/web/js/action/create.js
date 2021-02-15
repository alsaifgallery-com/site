/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/storage',
    'Magento_Ui/js/model/messageList',
    'Magento_Customer/js/customer-data',
    'mage/translate'
], function ($, storage, globalMessageList, customerData, $t) {
    'use strict';

    var callbacks = [],

        /**
         * @param {Object} createData
         * @param {String} redirectUrl
         * @param {*} isGlobal
         * @param {Object} messageContainer
         */
        action = function (createData, redirectUrl, isGlobal, messageContainer) {
            messageContainer = messageContainer || globalMessageList;

            return storage.post(
                'customer/ajax/create',
                JSON.stringify(createData),
                isGlobal
            ).done(function (response) {
                if (response.errors) {
                    messageContainer.addErrorMessage(response);
                    callbacks.forEach(function (callback) {
                        callback(createData);
                    });
                } else {
                    callbacks.forEach(function (callback) {
                        callback(createData);
                    });
                }
            }).fail(function () {
                messageContainer.addErrorMessage({
                    'message': $t('Could not authenticate. Please try again later')
                });
                callbacks.forEach(function (callback) {
                    callback(createData);
                });
            });
        };

    /**
     * @param {Function} callback
     */
    action.registerLoginCallback = function (callback) {
        callbacks.push(callback);
    };

    return action;
});
