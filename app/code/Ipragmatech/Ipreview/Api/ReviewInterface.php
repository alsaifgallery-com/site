<?php
namespace Ipragmatech\Ipreview\Api;

/**
 * Interface ReviewInterface
 * @api
 */
interface ReviewInterface
{
    /**
     * Return Added review item.
     *
     * @param int $productId
     * @return string[]|boolean
     *
     */
    public function getReviewsList($productId);

    /**
     * Return Rating options.
     *
     * @param int $store_id
     * @return string[]|boolean
     *
     */
    public function getRatings($store_id = null);

    /**
     * Added review and rating for the product.
     * @param int $productId
     * @param string $title
     * @param string $nickname
     * @param string $detail
     * @param string $rating_id
     * @param string $rating_code
     * @param string $rating_value
     * @param int $customer_id
     * @param int $store_id
     * @return boolean
     *
     */
    public function writeReviews(
        $productId,
        $nickname,
        $title,
        $detail,
        $rating_id,
        $rating_code,
        $rating_value,
        $customer_id = null,
        $storeId
    );
}
