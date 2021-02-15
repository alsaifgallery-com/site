<?php


namespace AlsaifGallery\ForgotPassword\Api\Data;

interface ForgotPasswordSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get forgotPassword list.
     * @return \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface[]
     */
    public function getItems();

    /**
     * Set customer_id list.
     * @param \AlsaifGallery\ForgotPassword\Api\Data\ForgotPasswordInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
