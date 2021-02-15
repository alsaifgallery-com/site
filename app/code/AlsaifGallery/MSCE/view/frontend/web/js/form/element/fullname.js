/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
    'Magento_Checkout/js/model/default-post-code-resolver'
], function ($, _, registry, Select, defaultPostCodeResolver) {
    'use strict';

    return Select.extend({
        defaults: {
            modules: {
                firstname: '${ $.parentName }.firstname',
                lastname: '${ $.parentName }.lastname'
            }
        },
        hasChanged: function () {
            this._super();
            this.setFirstLastNameFromFullName();
        },
        handleLocationError: function (browserHasGeolocation, infoWindow, pos) {
          infoWindow.setPosition(pos);
          infoWindow.setContent(browserHasGeolocation ?
                                'Error: The Geolocation service failed.' :
                                'Error: Your browser doesn\'t support geolocation.');
          infoWindow.open(map);
        },
        /**
         * Extract the 'First' and 'Last' Name from the 'Full Name'
         */
        setFirstLastNameFromFullName : function(){

            var fullValue = this.value().trim();
            var firstSpacePos = fullValue.indexOf(' ');
            if(firstSpacePos!=-1){
            //this means user has entered a space
            var first_name = fullValue.substring(0, firstSpacePos).trim();
            var last_name = fullValue.substring(firstSpacePos).trim();
            this.firstname().value(first_name);
            this.lastname().value(last_name);
            }
            else{
            //this means user has entered a single word
            this.firstname().value(fullValue);
            this.lastname().value(fullValue);
            }
        }
    });
});
