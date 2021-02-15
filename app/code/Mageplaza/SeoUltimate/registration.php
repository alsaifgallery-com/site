<?php
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
 * @package     Mageplaza_SeoUltimate
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

$seoMapUltimate = [];

/**
 * Require child module registration
 * Init map Psr-4 for each module
 */
$path  = __DIR__ . '/*/registration.php';
$files = glob($path, GLOB_NOSORT);
foreach ($files as $file) {
    include $file;
}

/**
 * Get loader from composer autoload
 * Set Psr-4 namespace for each child module
 */
$vendorDir      = require VENDOR_PATH;
$vendorAutoload = BP . "/{$vendorDir}/autoload.php";
$loader         = require $vendorAutoload;

foreach ($seoMapUltimate as $namespace => $path) {
    $loader->setPsr4($namespace, $path);
}
