/**
 * Main Pickup Time UIElement
 */
define([
    'ko',
    'jquery',
    'Magento_Ui/js/form/element/select',
    'Magento_Customer/js/customer-data',
    'Amasty_StorePickupWithLocator/js/model/pickup',
    'Amasty_StorePickupWithLocator/js/model/pickup/pickup-data-resolver',
    'Amasty_StorePickupWithLocator/js/view/pickup/pickup-date',
], function (ko, $, Component, customerData, pickup, pickupDataResolver) {
    'use strict';

    return Component.extend({
        defaults: {
            options: [],
            imports: {
                cartProductsDelay: '${$.parentName}.am_pickup_date:cartProductsDelay',
                selectedDayByName: '${$.parentName}.am_pickup_date:selectedDayByName',
                sameDayCutoffTime: '${$.parentName}.am_pickup_date:sameDayCutoffTime',
                storeScheduleSelected: '${$.parentName}.am_pickup_date:storeScheduleSelected',
                isTodaySelected: '${$.parentName}.am_pickup_date:isTodaySelected'
            },
            listens: {
                '${$.provider}:amStorepickup.date.change': 'setTimeIntervals'
            }
        },

        visibleComputed: ko.pureComputed(function () {
            return Boolean(pickupDataResolver.storeId() && pickupDataResolver.dateData() && pickup.isPickup());
        }),

        initialize: function () {
            this._super();

            var dateData = pickupDataResolver.dateData();

            if (dateData) {
                this.setTimeIntervals({
                    date: dateData,
                    store: pickupDataResolver.getCurrentStoreData()
                });
            }

            return this;
        },

        initConfig: function () {
            this._super();

            this.visible = this.visibleComputed();
            this.getPickupTimeFromCache();

            return this;
        },

        initObservable: function () {
            this._super();

            pickup.isPickup.subscribe(this.pickupStateObserver, this);
            this.visibleComputed.subscribe(this.visible);

            return this;
        },

        /**
         * Set time intervals by store schedule
         *
         * @param {Object} data
         */
        setTimeIntervals: function (data) {
            var selectedStore = data.store,
                timeIntervals,
                oldValue = this.value(),
                isOldTimeValid,
                isCachedTimeValid;

            if (data.date && selectedStore) {
                if (this.storeScheduleSelected || data.store.schedule_array) {
                    timeIntervals = selectedStore.handled_time[this.selectedDayByName];
                } else {
                    timeIntervals = selectedStore.handled_time;
                }

                if (timeIntervals) {
                    this.options(this.isTodaySelected
                        ? this.restrictTimeIntervals(timeIntervals)
                        : timeIntervals);
                }

                isOldTimeValid = this.options().some(function (interval) {
                    return interval.value === oldValue;
                });

                if (isOldTimeValid) {
                    this.value(oldValue);
                }

                if (this.getDataFromCache) {
                    isCachedTimeValid = this.options().some(function (interval) {
                        return interval.value === this.timeFromCache;
                    }.bind(this));
                }

                if (this.getDataFromCache && isCachedTimeValid) {
                    this.value(this.timeFromCache);
                }

                this.getDataFromCache = false;
            }

            this.disabled(!data.date);
        },

        /**
         * Restrict time intervals by store schedule
         *
         * @param {Array} intervals
         * @returns {*}
         */
        restrictTimeIntervals: function (intervals) {
            var currentStore = pickupDataResolver.getCurrentStoreData() || {},
                currentStoreTime = currentStore.current_timezone_time;

            intervals = intervals.filter(function (item) {
                return item.toInUnix > currentStoreTime + this.cartProductsDelay
                    && item.toInUnix <= this.sameDayCutoffTime;
            }.bind(this));

            return intervals;
        },

        onUpdate: function (pickupTime) {
            var pickupTimeOption = this.options().filter(function (elem) {
                return elem.value === pickupTime;
            })[0];

            pickupDataResolver.timeData(pickupTime);

            this.pickupTimeLabel = pickupTimeOption.label;
        },

        pickupStateObserver: function (isActive) {
            if (isActive) {
                this.getPickupTimeFromCache();
            }
        },

        getPickupTimeFromCache: function () {
            this.timeFromCache = pickupDataResolver.getDataByKey('am_pickup_time');
            this.getDataFromCache = !!this.timeFromCache;
        }
    });
});
