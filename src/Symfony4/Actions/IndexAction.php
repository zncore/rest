<?php

namespace ZnLib\Rest\Symfony4\Actions;

use ZnCore\Base\Libs\DataProvider\Libs\DataProvider;
use ZnLib\Rest\Symfony4\Base\BaseAction;
use ZnLib\Rest\Libs\Serializer\JsonRestSerializer;
use Symfony\Component\HttpFoundation\JsonResponse;

class IndexAction extends BaseAction
{

    public function run(): JsonResponse
    {
        $response = new JsonResponse;

        $page = $this->request->get("page", 1);
        $pageSize = $this->request->get("per-page", 10);
        $dataProvider = new DataProvider($this->service, $this->query, $page, $pageSize);

        $serializer = new JsonRestSerializer($response);
        $serializer->serializeDataProviderEntity($dataProvider->getAll());
        return $response;
    }

}