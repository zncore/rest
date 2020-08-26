<?php

namespace PhpLab\Rest\Helpers;

use PhpLab\Core\Domain\Entities\DataProviderEntity;
use PhpLab\Core\Enums\Http\HttpHeaderEnum;
use PhpLab\Core\Legacy\Yii\Helpers\ArrayHelper;
use PhpLab\Core\Legacy\Yii\Helpers\FileHelper;
use PhpLab\Core\Libs\Store\Store;
use Psr\Http\Message\ResponseInterface;

class RestResponseHelper
{

    public static function forgeDataProviderEntity(ResponseInterface $response): DataProviderEntity
    {
        $entity = new DataProviderEntity;
        $entity->setPageSize($response->getHeader(HttpHeaderEnum::PER_PAGE)[0]);
        $entity->setPage($response->getHeader(HttpHeaderEnum::CURRENT_PAGE)[0]);
        $entity->setTotalCount($response->getHeader(HttpHeaderEnum::TOTAL_COUNT)[0]);
        //$entity->pageCount = $response->getHeader(HttpHeaderEnum::PAGE_COUNT)[0];
        return $entity;
    }

    public static function getLastInsertId(ResponseInterface $response): int
    {
        $entityId = $response->getHeader(HttpHeaderEnum::X_ENTITY_ID)[0];
        return intval($entityId);
    }

    public static function getDataFromResponse(ResponseInterface $response)
    {
        $contentType = $response->getHeaderLine(HttpHeaderEnum::CONTENT_TYPE);
        $data = $response->getBody()->getContents();
        if ($contentType == 'application/json') {
            $data = json_decode($data, true);
        }
        return $data;
    }

    public static function getBody(ResponseInterface $response, string $body)
    {
        $contentTypeItems = self::extractHeaderValues($response, 'content-type');
        if($contentTypeItems) {
            $extension = self::mimeToFileExtension($contentTypeItems[0]);
        } else {
            $extension = null;
        }
        if ($extension == 'php' || empty($extension)) {
            $extension = 'html';
        }
        $encoder = new Store($extension);
        $body = $encoder->decode($body);
        return $body;
    }

    public static function extractHeaderValues(ResponseInterface $response, string $name)
    {
        $value = $response->getHeaderLine($name);
        if (empty($value)) {
            return [];
        }
        $parts = explode(';', $value);
        $parts = array_map('trim', $parts);
        return $parts;
    }

    private static function mimeToFileExtension(string $contentType, string $default = 'html'): string
    {
        $mimeTypes = include __DIR__ . '/../../../../zncore/base/src/Legacy/Yii/Helpers/mimeTypes.php';
        $mimeTypes = array_flip($mimeTypes);
        $extension = ArrayHelper::getValue($mimeTypes, $contentType, $default);
        return strtolower($extension);
    }

}
