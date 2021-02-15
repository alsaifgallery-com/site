define([
    'jquery',
    'jquery/ui',
    'Magento_ConfigurableProduct/js/configurable'
], function($){

    $.widget('magentoexperts.configurable', $.mage.configurable, {

        /**
         * Configure an option, initializing it's state and enabling related options, which
         * populates the related option's selection and resets child option selections.
         * @private
         * @param {*} element - The element associated with a configurable option.
         */
        _configureElement: function (element) {
            this.simpleProduct = this._getSimpleProductId(element);

            if (element.value) {
                this.options.state[element.config.id] = element.value;

                if (element.nextSetting) {
                    element.nextSetting.disabled = false;
                    this._fillSelect(element.nextSetting);
                    this._resetChildren(element.nextSetting);
                } else {
                    if (!!document.documentMode) { //eslint-disable-line
                        this.inputSimpleProduct.val(element.options[element.selectedIndex].config.allowedProducts[0]);
                    } else {
                        this.inputSimpleProduct.val(element.selectedOptions[0].config.allowedProducts[0]);
                    }
                }
            } else {
                this._resetChildren(element);
            }

            if (this.options.spConfig.hasOwnProperty('productname')) {

                var productName = this.options.spConfig.productname[this.simpleProduct];
                if (productName != '') {
                    $('[data-ui-id="page-title-wrapper"]').html(productName);
                }

            }

            if (this.options.spConfig.hasOwnProperty('productdescription')) {

                var productDescription = this.options.spConfig.productdescription[this.simpleProduct];
                if (productDescription != '') {
                    $('[data-role="content"]').find('.description .value').html(productDescription);
                }
            }

            if (this.options.spConfig.hasOwnProperty('productsku')) {

                var productSku = this.options.spConfig.productsku[this.simpleProduct];
                if (productSku != '') {
                    $('.product.sku .value').html(productSku);
                }

            }

            if (this.options.spConfig.hasOwnProperty('productshortdescription')) {

                var productShortDescription = this.options.spConfig.productshortdescription[this.simpleProduct];
                if (productShortDescription != '') {
                    $('.product.overview .value').html(productShortDescription);
                }
            }

            if (this.options.spConfig.hasOwnProperty('additionaldata')) {

                var additionalData = this.options.spConfig.additionaldata[this.simpleProduct];
                if (additionalData != '') {
                    $('[data-role="content"]').find('.additional-attributes-wrapper').html(additionalData);
                }

            }

            this._reloadPrice();
            this._displayRegularPriceBlock(this.simpleProduct);
            this._displayTierPriceBlock(this.simpleProduct);
            this._displayNormalPriceLabel();
            this._changeProductImage();
        },



    });

    return $.magentoexperts.configurable;
});