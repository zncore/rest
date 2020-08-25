<?php

namespace PhpLab\Rest\Helpers;

use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

class SymfonyRequestHelper
{

    public static function prepareRequest() {
        if ($_SERVER['APP_DEBUG']) {
            umask(0000);

            Debug::enable();
        }

        if ($trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? $_ENV['TRUSTED_PROXIES'] ?? false) {
            Request::setTrustedProxies(explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST);
        }

        if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? $_ENV['TRUSTED_HOSTS'] ?? false) {
            Request::setTrustedHosts([$trustedHosts]);
        }
    }

}
