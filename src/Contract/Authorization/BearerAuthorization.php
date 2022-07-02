<?php

namespace ZnLib\Rest\Contract\Authorization;

use GuzzleHttp\Client;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use ZnCore\Base\DotEnv\Domain\Exceptions\EnvConfigException;
use ZnCore\Base\FileSystem\Helpers\FilePathHelper;

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
}
