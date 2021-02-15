<?php


namespace AlsaifGallery\Product\Api\Data;

interface SpecificationsInterface
{
    const KEY='key';
    const VALUE='value';

    /**
     * GET key
     
     *  @return  string
     */
    public function getKey();
    /**
     * set key
     
     *  @return  \AlsaifGallery\Product\Api\Data\SpecificationsInterface
     */
    public function setKey($key);
    /**
     * get value 
     
     *  @return  string[]
     */
    public function getValue();
    /**
     * set value 
     
     *  @return  \AlsaifGallery\Product\Api\Data\SpecificationsInterface
     */
    public function setValue($value);
}
