<?php

namespace ZnLib\Rest\Contract\Authorization;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\CacheItem;
use ZnCore\Base\Develop\Helpers\DeprecateHelper;
use ZnCore\Base\DotEnv\Domain\Exceptions\EnvConfigException;
use ZnCore\Base\FileSystem\Helpers\FilePathHelper;
use ZnLib\Components\Http\Enums\HttpHeaderEnum;
use ZnLib\Components\Http\Enums\HttpMethodEnum;

class BearerAuthorization implements AuthorizationInterface
{

    private $guzzleClient;
    private $authUri = 'auth';
    private $authCache;
    private $currentAuth = [];

    public function __construct(Client $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
        if (empty($_ENV['CACHE_DIRECTORY'])) {
            throw new EnvConfigException('Empty env config for CACHE_DIRECTORY');
        }
        $cacheDirectory = FilePathHelper::path($_ENV['CACHE_DIRECTORY']);
        $this->authCache = new ArrayAdapter(60);
    }

    public function getAuthUri(): string
    {
        return $this->authUri;
    }

    public function setAuthUri(string $authUri): void
    {
        $this->authUri = $authUri;
    }

    public function authByLogin(string $login, string $password = 'Wwwqqq111'): AuthorizationInterface
    {
        $this->currentAuth = [
            'login' => $login,
            'password' => $password,
        ];
        return $this;
    }

    public function logout(): AuthorizationInterface
    {
        $this->currentAuth = [];
        return $this;
    }

    public function getAuthToken(): ?string
    {
        DeprecateHelper::hardThrow();
        if (empty($this->currentAuth['login'])) {
            return null;
        }

        /** @var CacheItem $cacheItem */
        $cacheItem = $this->authCache->getItem('token_by_login_' . $this->currentAuth['login']);
        $authToken = $cacheItem->get();

        if ($authToken) {

        } else {
            $authToken = $this->authorization();
            //$this->setAuthToken($authToken);
        }
        return $authToken;
    }

    public function authorization()
    {
        DeprecateHelper::hardThrow();
        $options = [
            RequestOptions::FORM_PARAMS => [
                'login' => $this->currentAuth['login'],
                'password' => $this->currentAuth['password'],
            ],
        ];
        $response = $this->guzzleClient->request(HttpMethodEnum::POST, $this->authUri, $options);
        //$authToken = RestHelper::getBodyAttribute($response, 'token');
        dd($this->guzzleClient);
        $authToken = $response->getHeader(HttpHeaderEnum::AUTHORIZATION)[0];
        $this->setAuthToken($authToken);
        return $authToken;
    }

    protected function setAuthToken(string $authToken)
    {
        DeprecateHelper::hardThrow();
        /** @var CacheItem $cacheItem */
        $cacheItem = $this->authCache->getItem('token_by_login_' . $this->currentAuth['login']);
        $cacheItem->set($authToken);
        $cacheItem->expiresAfter(60);
        $this->authCache->save($cacheItem);
    }
}
