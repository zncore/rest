<?php

namespace ZnLib\Rest\Helpers;

use ZnCore\Base\Enums\Http\HttpHeaderEnum;
use ZnCore\Base\Enums\Http\HttpMethodEnum;
use ZnCore\Base\Enums\Http\HttpServerEnum;

class CorsHelper
{

    public static function autoload($forceOrigin = false)
    {
        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // should do a check here to match $_SERVER['HTTP_ORIGIN'] to a
            // whitelist of safe domains
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            //header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }
// Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
            }

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            }

            exit;
        }
        return;


        $headers = self::generateHeaders();
        foreach ($headers as $headerKey => $headerValue) {
            header("$headerKey: $headerValue");
        }
        //$response = new Response('', 200, $headers);
        //$response->sendHeaders();
        if ($_SERVER[HttpServerEnum::REQUEST_METHOD] == HttpMethodEnum::OPTIONS) {
            exit;
        }
    }

    private static function generateHeaders($forceOrigin = false): array
    {
        //$headers = ArrayHelper::getValue($_SERVER, HttpServerEnum::HTTP_ACCESS_CONTROL_REQUEST_HEADERS);
        if (empty($headers)) {
            $headers = implode(', ', HttpHeaderEnum::values());
            $headers = mb_strtolower($headers);
        }
        $headers = [
            HttpHeaderEnum::ACCESS_CONTROL_ALLOW_ORIGIN => $_SERVER['HTTP_ORIGIN'],
            HttpHeaderEnum::ACCESS_CONTROL_ALLOW_HEADERS => $headers,
            HttpHeaderEnum::ACCESS_CONTROL_ALLOW_METHODS => implode(', ', HttpMethodEnum::values()),
            HttpHeaderEnum::ACCESS_CONTROL_ALLOW_CREDENTIALS => 'true',
            /*
            HttpHeaderEnum::ACCESS_CONTROL_ALLOW_ORIGIN => ArrayHelper::getValue($_SERVER, HttpServerEnum::HTTP_ORIGIN),

            HttpHeaderEnum::ACCESS_CONTROL_MAX_AGE => 3600,
            HttpHeaderEnum::ACCESS_CONTROL_EXPOSE_HEADERS => [
                HttpHeaderEnum::CONTENT_TYPE,
                HttpHeaderEnum::LINK,
                HttpHeaderEnum::ACCESS_TOKEN,
                HttpHeaderEnum::AUTHORIZATION,
                HttpHeaderEnum::TIME_ZONE,
                HttpHeaderEnum::TOTAL_COUNT,
                HttpHeaderEnum::PAGE_COUNT,
                HttpHeaderEnum::CURRENT_PAGE,
                HttpHeaderEnum::PER_PAGE,
                HttpHeaderEnum::X_ENTITY_ID,
                HttpHeaderEnum::X_AGENT_FINGERPRINT,
            ],
            */
        ];
        return $headers;
    }

}
