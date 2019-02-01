<?php
/**
 * Created by PhpStorm.
 * User: Sasha
 * Date: 31.01.2019
 * Time: 12:07
 */

namespace App\Utils;


class CircularReferenceHandler
{
    public function __invoke($object)
    {
        return $object->getId();
    }
}