<?php

namespace ZnLib\Rest\Yii2\Helpers;

use ZnCore\Base\Enums\Http\HttpHeaderEnum;
use ZnCore\Base\Enums\Http\HttpMethodEnum;
use ZnCore\Base\Enums\Http\HttpServerEnum;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;

class CorsHelper
{

    public static function autoload()
    {
        $headers = self::generateHeaders();
        foreach ($headers as $headerName => $headerValue) {
            \Yii::$app->response->headers->add($headerName, $headerValue);
        }
        if ($_SERVER[HttpServerEnum::REQUEST_METHOD] == HttpMethodEnum::OPTIONS) {
            \Yii::$app->response->send();
            exit;
        }
    }

    private static function generateHeaders(): array
    {
        $headers = [
            HttpHeaderEnum::ACCESS_CONTROL_ALLOW_ORIGIN => '*',
            HttpHeaderEnum::ACCESS_CONTROL_ALLOW_HEADERS => ArrayHelper::getValue($_SERVER, HttpServerEnum::HTTP_ACCESS_CONTROL_REQUEST_HEADERS),
            HttpHeaderEnum::ACCESS_CONTROL_ALLOW_METHODS => implode(', ', HttpMethodEnum::values()),
        ];
        return $headers;
    }

}
