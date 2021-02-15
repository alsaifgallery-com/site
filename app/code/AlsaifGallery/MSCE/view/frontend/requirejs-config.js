var config = {
    'map': {
        '*': {
            "Magento_Ui/js/form/element/post-code": 'AlsaifGallery_MSCE/js/form/element/post-code'
        }
    },
    'config': {
        'mixins': {
           'Magento_Checkout/js/view/shipping': {
               'AlsaifGallery_MSCE/js/view/shipping-payment-mixin': true
           },
           'Magento_Checkout/js/view/payment': {
               'AlsaifGallery_MSCE/js/view/shipping-payment-mixin': true
           }
       }
    }
};
