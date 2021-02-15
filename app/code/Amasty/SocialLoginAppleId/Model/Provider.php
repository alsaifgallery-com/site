<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_SocialLoginAppleId
 */


namespace Amasty\SocialLoginAppleId\Model;

use Amasty\SocialLogin\Controller\Social\Login;
use Hybridauth\Adapter\OAuth2;
use Hybridauth\Storage\Session;
use Hybridauth\User;

class Provider extends OAuth2
{
    const BASE_URL = 'https://appleid.apple.com';

    /**
     * @var string
     */
    protected $apiBaseUrl = self::BASE_URL;

    /**
     * @var string
     */
    protected $authorizeUrl = 'https://appleid.apple.com/auth/authorize';

    /**
     * @var string
     */
    protected $accessTokenUrl = 'https://appleid.apple.com/auth/token';

    /**
     * @var string
     */
    protected $providerId = 'apple';

    /**
     * @var string
     */
    public $scope = 'name email';

    public function initialize()
    {
        $this->AuthorizeUrlParameters = [
            'response_type' => 'code id_token',
            'response_mode' => 'form_post',
            'client_id' => $this->clientId,
            'redirect_uri' => $this->callback,
            'scope' => $this->scope,
        ];

        $this->tokenExchangeParameters = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->callback
        ];

        $this->tokenRefreshParameters = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->getStoredData('refresh_token'),
        ];

        $this->apiRequestHeaders = [
            'Authorization' => 'Bearer ' . $this->getStoredData('access_token'),
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];
    }

    /**
     * @return User\Profile|void
     * @throws \Hybridauth\Exception\RuntimeException
     */
    public function getUserProfile()
    {
        $storage = new Session();
        $appleParams = $storage->get(Login::AMSOCIAL_LOGIN_PARAMS) ?: [];
        $userProfile = new User\Profile();
        if (isset($appleParams['id_token'])) {
            $claims = explode('.', $appleParams['id_token'])[1];
            $data = $this->unserialize($claims);
            $userProfile->identifier = $data['sub'];
            $userProfile->email = $data['email'];
        }

        return $userProfile;
    }

    /**
     * @param $string
     * @return array
     */
    public function unserialize($string)
    {
        // @codingStandardsIgnoreStart
        $result = json_decode(base64_decode($string), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return json_decode($string, true);
        }
        // @codingStandardsIgnoreEnd

        return $result;
    }
}
