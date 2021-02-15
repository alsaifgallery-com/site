/**
 * @api
 */
define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/abstract'
], function (_, registry, Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            imports: {
                update: '${ $.parentName }.country_id:value'
            }
        },

        /**
         * @param {String} value
         */
        update: function (value) {
            var country = registry.get(this.parentName + '.' + 'country_id'),
                options = country.indexedOptions,
                option;

            if (!value) {
                return;
            }

            option = options[value];

            if (option['is_zipcode_optional']) { // from here custom code start
                    // if country is optional then make it hide,
                this.hide();
            } else {
                this.show();
                this.validation['required-entry'] = true;
            }

            this.required(!option['is_zipcode_optional']);
        }
    });
});
