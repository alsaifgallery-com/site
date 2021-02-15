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

define([
    'jquery', 'magnific'
], function ($) {
    return function (config) {
        var formMessengerBox = config.formMessengerBox;
        var productId = config.productId;
        var ajaxUrl = config.ajaxUrl;
        var isGoogleCapcha = Boolean(config.isGoogleCapcha);

        var formContainer = $('.mpfaqs-form');
        var nameBox = $('#mpfaqs-form-fieldset__name-input');
        var emailBox = $('#mpfaqs-form-fieldset__email-input');
        var questionBox = $('#mpfaqs-form-fieldset__question-content');
        var notifyEmail = $('#mpfaqs-form-fieldset__email-notify input');
        var termConditions = $('#mpfaqs-form-fieldset__term-conditions');
        var submitButton = $('.mpfaqs-form-fieldset__submit-form');

        questionBox.val('').parent().find('label').removeClass('active');

        $('.hiddendiv.common').hide();

        /** init popup link */
        $('#mpfaqs-term').magnificPopup({
            type: 'inline',
            midClick: true,
            showCloseBtn: true
        });

        $('#term-popup a').on('click', function (e) {
            $('#mpfaqs-form-fieldset__term-conditions input').attr('checked', 'checked');
            $('.mfp-close').trigger('click');
        });
        submitButton.on('click', function () {
            if (nameBox.valid()) {
                nameBox.addClass('valid');
                nameBox.parent().find('label').addClass('active');
            }
            if (emailBox.valid()) {
                emailBox.addClass('valid');
                emailBox.parent().find('label').addClass('active');
            }
            if (formContainer.find('form').valid()) {
                if (typeof(termConditions.find('input').val()) === "undefined" || termConditions.find('input').is(':checked')) {

                    /** compatible with mageplaza google capcha */
                    if (isGoogleCapcha) {
                        compatibleGoogleCapcha(formContainer);
                    }
                    /** end compatible with mageplaza google capcha */

                    $(this).addClass('disabled');
                    $('.faqs-loader').show();
                    $.ajax({
                        type: 'POST',
                        url: ajaxUrl,
                        data: {
                            name: (typeof nameBox.val() !== 'undefined') ? nameBox.val() : '',
                            email: (typeof emailBox.val() !== 'undefined') ? emailBox.val() : '',
                            content: questionBox.val(),
                            is_notify: isEmailNotification(),
                            product_id: productId
                        },
                        success: function (response) {
                            if (response.status) {
                                submitButton.removeClass('disabled');
                                if (typeof nameBox.val() !== 'undefined') {
                                    nameBox.val('').removeClass('valid');
                                    nameBox.parent().find('label').removeClass('active');
                                }
                                if (typeof emailBox.val() !== 'undefined') {
                                    emailBox.val('').removeClass('valid');
                                    emailBox.parent().find('label').removeClass('active');
                                }
                                questionBox.val('').removeClass('valid');
                                questionBox.parent().find('label').removeClass('active');
                                $('.faqs-loader').hide();
                                formContainer.append(formMessengerBox.submitSuccess);
                                setTimeout(function () {
                                    $('.mpfaqs-form').find('.messages').remove();
                                }, 4000);
                            }
                        }
                    });
                } else {
                    termConditions.append('<div for="mpfaqs-form-fieldset__term-conditions" generated="true" class="mage-error" id="mpfaqs-form-fieldset__term-conditions-error" style="display: block;">This is a required field.</div>')
                }
            }
        });

        function isEmailNotification() {
            if (typeof(notifyEmail.val()) !== "undefined") {
                notifyEmail.is(':checked') ? $(notifyEmail).val(1) : $(notifyEmail).val(0);
                notifyEmail.change(function () {
                    ($(this).is(':checked')) ? $(this).val(1) : $(this).val(0);
                });

                return notifyEmail.val();
            }
            return '0';
        }

        function compatibleGoogleCapcha(formContainer) {
            var formDataArray = formContainer.find('form').serializeArray();
            var capcha = '';

            formDataArray.forEach(function (entry) {
                if (entry.name.includes('g-recaptcha-response')) {
                    capcha = entry.value;
                }
            });
            if (capcha === '') {
                return false;
            }
        }
    }
});
