<?php


namespace AlsaifGallery\Product\Api\Data;

interface FilterItemInterface
{
    const FIELD='field';
    const VALUE='value';

    /**
     * GET field
     
     *  @return  string
     */
    public function getField();
    /**
     * set field
     
     *  @return  \AlsaifGallery\Product\Api\Data\FilterItemInterface
     */
    public function setField($field);
    /**
     * get value 
     
     *  @return string
     */
    public function getValue();
    /**
     * set value 
     
     *  @return  \AlsaifGallery\Product\Api\Data\FilterItemInterface
     */
    public function setValue($value);
}
