<?php

namespace ZnLib\Rest\Helpers;

use ZnCore\Base\Enums\Http\HttpHeaderEnum;
use ZnCore\Base\Enums\Http\HttpMethodEnum;
use ZnCore\Base\Enums\Http\HttpServerEnum;
use ZnCore\Base\Helpers\EnumHelper;

class CorsHelper
{

    protected static function getAllowOrigin(): ?string
    {
        $clientOrigin = $_SERVER[HttpServerEnum::HTTP_ORIGIN] ?? null;
        $envOrigins = $_ENV['CORS_ALLOW_ORIGINS'] ?? null;
        if (empty($clientOrigin) || empty($envOrigins)) {
            return null;
        }
        // should do a check here to match $_SERVER['HTTP_ORIGIN'] to a whitelist of safe domains
        $origins = explode(',', $envOrigins);
        $isAllow = $envOrigins == '*' || in_array($clientOrigin, $origins);
        if ($isAllow) {
            return $clientOrigin;
        }
    }

    public static function autoload($forceOrigin = false): void
    {
        // Allow from any origin
        $allowOrigin = self::getAllowOrigin();
        if (!$allowOrigin) {
            return;
        }

        self::header(HttpHeaderEnum::ACCESS_CONTROL_ALLOW_ORIGIN, $allowOrigin);
        self::header(HttpHeaderEnum::ACCESS_CONTROL_ALLOW_CREDENTIALS, 'true');
        if (!empty($_ENV['CORS_MAX_AGE'])) {
            self::header(HttpHeaderEnum::ACCESS_CONTROL_MAX_AGE, $_ENV['CORS_MAX_AGE']);
        }
        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER[HttpServerEnum::REQUEST_METHOD] == HttpMethodEnum::OPTIONS) {
            if (isset($_SERVER[HttpServerEnum::HTTP_ACCESS_CONTROL_REQUEST_METHOD])) {
                self::header(HttpHeaderEnum::ACCESS_CONTROL_ALLOW_METHODS, "GET, POST, PUT, DELETE, OPTIONS");
            }
            if (isset($_SERVER[HttpServerEnum::HTTP_ACCESS_CONTROL_REQUEST_HEADERS])) {
                self::header(HttpHeaderEnum::ACCESS_CONTROL_ALLOW_HEADERS, $_SERVER[HttpServerEnum::HTTP_ACCESS_CONTROL_REQUEST_HEADERS]);
            }
            exit;
        }


        /*$headers = self::generateHeaders();
        foreach ($headers as $headerKey => $headerValue) {
            header("$headerKey: $headerValue");
        }
        //$response = new Response('', 200, $headers);
        //$response->sendHeaders();
        if ($_SERVER[HttpServerEnum::REQUEST_METHOD] == HttpMethodEnum::OPTIONS) {
            exit;
        }*/
    }

    protected static function header($key, $value): void
    {
        header("$key: $value");
    }

    private static function generateHeaders($forceOrigin = false): array
    {
        //$headers = ArrayHelper::getValue($_SERVER, HttpServerEnum::HTTP_ACCESS_CONTROL_REQUEST_HEADERS);
        if (empty($headers)) {
            $headers = implode(', ', EnumHelper::getValues(HttpMethodEnum::class));
            $headers = mb_strtolower($headers);
        }
        $headers = [
            HttpHeaderEnum::ACCESS_CONTROL_ALLOW_ORIGIN => $_SERVER['HTTP_ORIGIN'],
            HttpHeaderEnum::ACCESS_CONTROL_ALLOW_HEADERS => $headers,
            HttpHeaderEnum::ACCESS_CONTROL_ALLOW_METHODS => implode(', ', EnumHelper::getValues(HttpMethodEnum::class)),
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
