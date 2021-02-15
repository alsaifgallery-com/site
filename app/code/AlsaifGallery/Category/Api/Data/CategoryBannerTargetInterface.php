<?php

namespace AlsaifGallery\Category\Api\Data;

interface CategoryBannerTargetInterface {

    const KEY_KEY="key";
    const KEY_VALUE="value";
        /**
     * Set value for the given key
     *
     * @param string $key
     * @return $this
     */
    public function setKey($key);
    
    /**
     * Retrieves a value from the data array if set, or null otherwise.
     *
     * @param string $key
     * @return mixed|null
     */
    public function getKey();
    
    /**
     * Set value for the given key
     *
     * @param string $value
     * @return $this
     */
    public function setValue($value);
      /**
     * Retrieves a value from the data array if set, or null otherwise.
     *
     * @param string $key
     * @return mixed|null
     */
    public function getValue();
    
}
    