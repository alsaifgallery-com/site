<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace AlsaifGallery\MSCE\Plugin\Magento\Checkout\Block\Checkout;

class LayoutProcessor
{

    /**
    * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
    * @param array $jsLayout
    * @return array
    */

    public function afterProcess(
      \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
      array  $jsLayout
    ) {

        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'])) {

            /* Hide First name & Last name*/
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['firstname']['visible'] = false;
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['lastname']['visible'] = false;

            /* Zip code numeric validation*/
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['postcode']['validation'] = ['validate-number' => true];

            /* country_id */
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['country_id']['sortOrder'] = 65;

            /* region_id */
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['region_id']['sortOrder'] = 66;


            /* region_id */
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['city']['sortOrder'] = 67;
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['city']['label'] = __("District");

            /* Add hidden FullName field*/
            $customAttributeCode = 'fullname';
            $customField = [
                'component' => 'AlsaifGallery_MSCE/js/form/element/fullname',
                'config' => [
                    // customScope is used to group elements within a single form (e.g. they can be validated separately)
                    'customScope' => 'shippingAddress.custom_attributes',
                    'customEntry' => '',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/input',
                ],
                'dataScope' => 'shippingAddress.custom_attributes' . '.' . $customAttributeCode,
                'label' => __('Full name'),
                'provider' => 'checkoutProvider',
                'sortOrder' => 50,
                'validation' => [
                    'required-entry' => true
                ],
                'options' => [],
                'filterBy' => null,
                'customEntry' => null,
                'visible' => true,
                'value' => '' // value field is used to set a default value of the attribute
            ];
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children'][$customAttributeCode] = $customField;

            /* Add map field*/
              $map = 'map';
              $mapField = [
                  'component' => 'AlsaifGallery_MSCE/js/form/element/map',
                  'config' => [
                      // customScope is used to group elements within a single form (e.g. they can be validated separately)
                      'customScope' => 'shippingAddress.custom_attributes',
                      'customEntry' => null,
                      'template' => 'ui/form/field',
                      'elementTmpl' => 'AlsaifGallery_MSCE/form/element/map',
                  ],
                  'dataScope' => 'shippingAddress.custom_attributes' . '.' . $map,
                  // 'label' => __('Select from Map'),
                  'provider' => 'checkoutProvider',
                  'sortOrder' => 20,
                  'validation' => [
                     'required-entry' => false
                  ],
                  'options' => [],
                  'filterBy' => null,
                  'customEntry' => null,
                  'visible' => true,
                  'additionalClasses' => 'full_width',
                  'value' => '' // value field is used to set a default value of the attribute
              ];
              $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children'][$map] = $mapField;

        }

      return $jsLayout;
      }
}
