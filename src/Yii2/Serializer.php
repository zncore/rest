<?php

namespace ZnLib\Rest\Yii2;

use ZnCore\Domain\Entities\DataProviderEntity;
use ZnCore\Domain\Libs\DataProvider;
use ZnCore\Base\Enums\Http\HttpHeaderEnum;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Yii;
use yii\rest\Serializer as YiiSerializer;
use yii2rails\extension\develop\helpers\Debug;

class Serializer extends YiiSerializer
{

    /** @var NormalizerInterface */
    public $normalizer;
    public $context;

    protected function serializeModel($model)
    {
        return $this->serializeData($model);
    }

    protected function serializeModels(array $models)
    {
        return $this->serializeData($models);
    }

    private function addRuntimeHeader()
    {
        if ( ! YII_ENV_DEV) {
            return;
        }
        $runtime = Debug::getRuntime();
        Yii::$app->response->headers->set(HttpHeaderEnum::X_RUNTIME, $runtime . ' s');
    }

    private function serializeData($data)
    {
        $jsonContent = $this->normalizer->normalize($data, null, $this->context);
        return $jsonContent;
    }

    public function serializeDataProviderEntity(DataProviderEntity $entity)
    {
        $this->serialize($entity->getCollection());
        Yii::$app->response->headers->set(HttpHeaderEnum::PER_PAGE, $entity->getPageSize());
        Yii::$app->response->headers->set(HttpHeaderEnum::PAGE_COUNT, $entity->getPageCount());
        Yii::$app->response->headers->set(HttpHeaderEnum::TOTAL_COUNT, $entity->getTotalCount());
        Yii::$app->response->headers->set(HttpHeaderEnum::CURRENT_PAGE, $entity->getPage());
        return $this;
    }

    public function serialize($data)
    {
        $this->addRuntimeHeader();
        //Yii::$app->response->format = Response::FORMAT_JSON;
        //Yii::$app->response->headers->add(\ZnCore\Base\Enums\Http\HttpHeaderEnum::CONTENT_TYPE, 'application/json');
        if ($data instanceof DataProvider) {
            $dataProviderEntity = $data->getAll();
            $data = $dataProviderEntity->getCollection();
            $this->serializeDataProviderEntity($dataProviderEntity);
        }
        $serializedData = $this->serializeData($data);
        return $serializedData;
    }

}
