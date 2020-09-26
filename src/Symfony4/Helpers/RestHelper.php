<?php

namespace ZnLib\Rest\Helpers;

class RestHelper
{

    public static function parseVersionFromUrl(string $uri)
    {
        $uri = trim($uri, '/');
        $isApi = preg_match('/^api\/v(\d+)\//', $uri, $matches);
        if ( ! $isApi) {
            return false;
        }
        return $matches[1];
    }

}
