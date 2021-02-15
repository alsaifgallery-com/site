define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'underscore',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Customer/js/model/customer',
        'Magento_Customer/js/action/login',
        'AlsaifGallery_MSCE/js/action/create',
        'mage/validation',
        'Magento_Checkout/js/model/authentication-messages',
        'Magento_Checkout/js/model/full-screen-loader',
        'mage/translate'
    ],
    function (
        $,
        ko,
        Component,
        _,
        stepNavigator,
        customer,
        loginAction,
        createAction,
        validation,
        messageContainer,
        fullScreenLoader,
        $t
    ) {
        'use strict';

        var checkoutConfig = window.checkoutConfig;

        /**
        * login - is the name of the component's .html template
        */
        return Component.extend({
            isGuestCheckoutAllowed: checkoutConfig.isGuestCheckoutAllowed,
            isCustomerLoginRequired: checkoutConfig.isCustomerLoginRequired,
            registerUrl: checkoutConfig.registerUrl,
            forgotPasswordUrl: checkoutConfig.forgotPasswordUrl,
            autocomplete: checkoutConfig.autocomplete,
            defaults: {
                template: 'AlsaifGallery_MSCE/login',
                errorMessage: $t('You did not sign in correctly or your account is temporarily disabled.')
            },

            //add here your logic to display step,
            isVisible: ko.observable(true),
            isLogedIn: customer.isLoggedIn(),
            //step code will be used as step content id in the component template
            stepCode: 'auth',
            //step title value
            stepTitle: 'Login',

            /**
            *
            * @returns {*}
            */
            initialize: function () {
                this._super();
                // register your step
                stepNavigator.registerStep(
                    this.stepCode,
                    //step alias
                    null,
                    this.stepTitle,
                    //observable property with logic when display step or hide step
                    this.isVisible,

                    _.bind(this.navigate, this),

                    /**
                    * sort order value
                    * 'sort order value' < 10: step displays before shipping step;
                    * 10 < 'sort order value' < 20 : step displays between shipping and payment step
                    * 'sort order value' > 20 : step displays after payment step
                    */
                    1
                );

                if (!this.isActive()) {
                  window.location = checkoutConfig.checkoutUrl + '#shipping';
                }

                return this;
            },

            /**
             * Is login form enabled for current customer.
             *
             * @return {Boolean}
             */
            isActive: function () {
                return !customer.isLoggedIn();
            },

            /**
             * Provide login action.
             *
             * @param {HTMLElement} loginForm
             */
            login: function (loginForm) {
                var loginData = {},
                    formDataArray = $(loginForm).serializeArray();

                formDataArray.forEach(function (entry) {
                    loginData[entry.name] = entry.value;
                });
                console.log(loginData);
                if ($(loginForm).validation() &&
                    $(loginForm).validation('isValid')
                ) {
                    fullScreenLoader.startLoader();
                    var self = this;
                    loginAction(loginData, checkoutConfig.checkoutUrl, undefined, undefined).always(function () {
                        fullScreenLoader.stopLoader();
                    });
                }
            },

            /**
             * Provide create account action.
             *
             * @param {HTMLElement} createForm
             */
            create: function (createForm) {
                var createData = {},
                    formDataArray = $(createForm).serializeArray();

                formDataArray.forEach(function (entry) {
                    createData[entry.name] = entry.value;
                });
                if ($(createForm).validation() &&
                    $(createForm).validation('isValid')
                ) {
                    fullScreenLoader.startLoader();
                    var self = this;
                    createAction(createData, checkoutConfig.checkoutUrl, undefined, undefined).always(function (response) {
                      console.log(response);
                      if(!response.errors) {
                        console.log("in");
                        loginAction(createData, checkoutConfig.checkoutUrl, undefined, undefined).always( function () {
                            fullScreenLoader.stopLoader();
                        });
                      }else {
                        fullScreenLoader.stopLoader();
                      }
                    });
                }
            },

            /**
            * The navigate() method is responsible for navigation between checkout step
            * during checkout. You can add custom logic, for example some conditions
            * for switching to your custom step
            */
            navigate: function () {

            },

            /**
            * @returns void
            */
            navigateToNextStep: function () {
                stepNavigator.next();
            },

            /**
            * @returns void
            */
            displayRegisterForm: function() {
              $('#login-form').hide();
              $('#register-form').show();
            },

            /**
            * @returns void
            */
            displayLoginForm: function() {
              $('#login-form').show();
              $('#register-form').hide();
            }
        });
    }
);
