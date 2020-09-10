<?php

namespace ZnLib\Rest\Yii2\Base;

use ZnLib\Rest\Yii2\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;
use yii\base\Module;
use yii\rest\Controller;
use yii2rails\domain\traits\entity\BehaviorTrait;

class BaseController extends Controller
{

    use BehaviorTrait;

    public function __construct(string $id, Module $module, array $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->serializer = [
            'class' => Serializer::class,
            'normalizer' => $this->createNormalizer(),
            'context' => $this->normalizerContext(),
        ];
    }

    protected function normalizerContext(): array
    {
        return [];
    }

    protected function createNormalizer(): NormalizerInterface
    {
        $encoders = [
            new XmlEncoder,
            new JsonEncoder,
        ];
        $normalizers = [
            new DateTimeNormalizer,
            new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter),
        ];
        $serializer = new SymfonySerializer($normalizers, $encoders);
        return $serializer;
    }

}
