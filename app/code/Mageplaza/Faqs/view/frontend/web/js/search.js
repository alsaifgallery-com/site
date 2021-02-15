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

define(['jquery', 'mpDevbridgeAutocomplete'], function ($) {
        return function(config){
            var layout = config.layout;
            var lookup = config.lookup; //string to array, lookup is an array === search offline
            var searchUrl = config.searchUrl;
            var searchBox = $('#mpfaqs-search__field__searchbox');
            var searchAction = $('.mpfaqs-search__action a');

            if (layout === 'mpfaqs_article_index' || layout === 'mpfaqs_category_view') {
                searchBox.keydown(function (e) {
                    if (e.keyCode === 13) {
                        e.preventDefault();
                        searchAction.trigger('click');
                    }
                });
                searchAction.on('click', function (e) {
                    searchBox.focus();
                    questionFilter($(searchBox));
                });

                function questionFilter(searchBox) {
                    var value = searchBox.val().toLowerCase();
                    $('.ln_overlay').show();
                    $.ajax({
                        url: (value) ? searchUrl : window.location.href,
                        data: {
                            filter: value
                        },
                        success: function (response) {
                            if (response.status) {
                                $('#mpfaqs-list-container').html($(response.faq_list).html());
                                $('.ln_overlay').hide();
                            }
                        }
                    });
                }
            }

            if (layout === 'mpfaqs_article_view') {
                searchAction.on('click', function () {
                    searchBox.focus();
                });

                searchBox.autocomplete({
                    lookup: lookup,
                    lookupLimit: 10,
                    maxHeight: 2000,
                    minChars: 1,
                    autoSelectFirst: true,
                    showNoSuggestionNotice: true,
                    triggerSelectOnValidInput: false,
                    onSelect: function (suggestion) {
                        window.location.href = suggestion.url;
                    },
                    formatResult: function (suggestion) {
                        var html = "<div class='mpfaqs-suggestion mpfaqs'>";
                        html += "<div class='mpfaqs-suggestion-content'><i class='far fa-file-alt'></i><span>" + suggestion.value + "</span></div></div>";
                        return html;
                    }
                });
            }
        }
    }
);