
define([
    'jquery',
    'underscore',
    'Magento_Ui/js/grid/massactions',
    'uiRegistry',
    'mageUtils',
    'Magento_Ui/js/lib/collapsible',
    'Magento_Ui/js/modal/confirm',
    'Magento_Ui/js/modal/alert',
    'mage/translate'
], function ($, _, Massactions, registry, utils, Collapsible, confirm, alert, $t) {
    'use strict';

    return Massactions.extend({
        changeStatus: function (action) {
            var data = this.getSelections(),
                callback;

            if (!data.total) {
                alert({
                    content: this.noItemsMsg
                });

                return this;
            }

            var me = this;
            callback = function() {me.changeStatusCallback(action, data)};

            action.confirm ?
                this._confirmStatus(action, callback) :
                callback();

            return this;
        },

        /**
         * Default action callback. Sends selections data
         * via POST request.
         *
         * @param {Object} action - Action data.
         * @param {Object} data - Selections data.
         */
        defaultCallback: function (action, data) {
            var itemsType = data.excludeMode ? 'excluded' : 'selected',
                selections = {};

            selections[itemsType] = data[itemsType];

            if (!selections[itemsType].length) {
                selections[itemsType] = false;
            }

            _.extend(selections, data.params || {});

            utils.submit({
                url: action.url,
                data: selections
            });
        },

        applyAction: function (actionIndex) {
            var data = this.getSelections(),
                action,
                callback;

            if (!data.total) {
                alert({
                    content: this.noItemsMsg
                });

                return this;
            }

            action   = this.getAction(actionIndex);
            if (action.type && action.url.indexOf('oaction') && action.url.indexOf('ship') > 0) {
                var selected = data['selected'];
                var oaction = {};
                var shippings = $('table.data-grid .aoaction-cell');
                if (!shippings.length) {
                    alert({
                        content: 'Please make "Shipping" coluumn visible.'
                    });
                    return;
                }

                shippings.each(function(index, value) {
                    var tr = $(value).parents('tr');
                    if (tr.length) {
                        var input = tr.find('input.admin__control-checkbox[id*="check"]')
                        var id = input.val();
                        if ($.inArray(id, selected) >= 0) {
                            oaction[id] = {};
                            $(value).find('[name^="amasty"]').each(function(key, element) {
                                element = $(element);
                                oaction[id][element.attr('name')] = element.val();
                            });
                        }
                    }
                });

                data['params']['oaction'] = oaction;
            }



            callback = this._getCallback(action, data);

            action.confirm ?
                this._confirm(action, callback) :
                callback();

            return this;
        },

        changeStatusCallback: function (action, data) {
            var itemsType = data.excludeMode ? 'excluded' : 'selected',
                selections = {};

            selections[itemsType] = data[itemsType];


            if (!selections[itemsType].length) {
                selections[itemsType] = false;
            }

            var oactionElement = $('.action-submenu._active .amasty-oaction-form select');
            if (oactionElement.length) {
                if (oactionElement[0] && oactionElement[0].value) {
                    selections['status'] = oactionElement[0].value;
                } else if(oactionElement[1]) {
                    selections['status'] = oactionElement[1].value;
                }
            }

            _.extend(selections, data.params || {});

            utils.submit({
                url: action.url,
                data: selections
            });
        },


        /**
         * Shows actions' confirmation window.
         *
         * @param {Object} action - Actions' data.
         * @param {Function} callback - Callback that will be
         *      invoked if action is confirmed.
         */
        _confirmStatus: function (action, callback) {
            var confirmData = action.confirm;

            confirm({
                title: confirmData.title,
                content: confirmData.message,
                actions: {
                    confirm: callback,
                    cancel: function () {
                        var oactionElement = $('.action-submenu._active .amasty-oaction-form select');
                        if (oactionElement.length) {
                            oactionElement.val("");
                        }
                    }
                }
            });
        }

    });
});
