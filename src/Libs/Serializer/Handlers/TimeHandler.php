<?php

namespace PhpLab\Rest\Libs\Serializer\Handlers;

use DateTime;
use PhpLab\Core\Libs\Serializer\ArraySerializer;
use PhpLab\Core\Libs\Serializer\Handlers\SerializerHandlerInterface;

class TimeHandler implements SerializerHandlerInterface
{

    public $properties = [];
    public $recursive = true;

    /** @var ArraySerializer */
    public $parent;

    public function encode($object)
    {
        if ($object instanceof DateTime) {
            $object = $this->objectHandle($object);
        }
        return $object;
    }

    protected function objectHandle(DateTime $object): string
    {
        return $object->format('c');
    }
}