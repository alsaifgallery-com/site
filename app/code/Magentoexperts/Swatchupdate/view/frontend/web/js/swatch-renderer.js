define([
    'jquery',
    'jquery/ui',
    'magento-swatch.renderer'
], function($){

    $.widget('magentoexperts.SwatchRenderer', $.mage.SwatchRenderer, {

        /**
         * Event for swatch options
         *
         * @param {Object} $this
         * @param {Object} $widget
         * @private
         */
        _OnClick: function ($this, $widget) {
            var $parent = $this.parents('.' + $widget.options.classes.attributeClass),
                $wrapper = $this.parents('.' + $widget.options.classes.attributeOptionsWrapper),
                $label = $parent.find('.' + $widget.options.classes.attributeSelectedOptionLabelClass),
                attributeId = $parent.attr('attribute-id'),
                $input = $parent.find('.' + $widget.options.classes.attributeInput);

            if ($widget.inProductList) {
                $input = $widget.productForm.find(
                    '.' + $widget.options.classes.attributeInput + '[name="super_attribute[' + attributeId + ']"]'
                );
            }

            if ($this.hasClass('disabled')) {
                return;
            }

            if ($this.hasClass('selected')) {
                $parent.removeAttr('option-selected').find('.selected').removeClass('selected');
                $input.val('');
                $label.text('');
                $this.attr('aria-checked', false);
            } else {
                $parent.attr('option-selected', $this.attr('option-id')).find('.selected').removeClass('selected');
                $label.text($this.attr('option-label'));
                $input.val($this.attr('option-id'));
                $input.attr('data-attr-name', this._getAttributeCodeById(attributeId));
                $this.addClass('selected');
                $widget._toggleCheckedAttributes($this, $wrapper);
            }

            $widget._Rebuild();

            if ($widget.options.jsonConfig.hasOwnProperty('productname')) {

                var productName = $widget.options.jsonConfig.productname[this.getProduct()];
                if (productName != '') {
                    $('[data-ui-id="page-title-wrapper"]').html(productName);
                }

            }

            if ($widget.options.jsonConfig.hasOwnProperty('productdescription')) {

                var productDescription = $widget.options.jsonConfig.productdescription[this.getProduct()];
                if (productDescription != '') {
                    $('[data-role="content"]').find('.description .value').html(productDescription);
                }
            }

            if ($widget.options.jsonConfig.hasOwnProperty('productsku')) {

                var productSku = $widget.options.jsonConfig.productsku[this.getProduct()];
                if (productSku != '') {
                    $('.product.sku .value').html(productSku);
                }

            }

            if ($widget.options.jsonConfig.hasOwnProperty('productshortdescription')) {

                var productShortDescription = $widget.options.jsonConfig.productshortdescription[this.getProduct()];
                if (productShortDescription != '') {
                    $('.product.overview .value').html(productShortDescription);
                }
            }

            if ($widget.options.jsonConfig.hasOwnProperty('additionaldata')) {

                var additionalData = $widget.options.jsonConfig.additionaldata[this.getProduct()];
                if (additionalData != '') {
                    $('[data-role="content"]').find('.additional-attributes-wrapper').html(additionalData);
                }

            }

            if ($widget.element.parents($widget.options.selectorProduct)
                .find(this.options.selectorProductPrice).is(':data(mage-priceBox)')
            ) {
                $widget._UpdatePrice();
            }

            $widget._loadMedia();
            $input.trigger('change');
        }

    });

    return $.magentoexperts.SwatchRenderer;
});