<?php
/**
 * Created by PhpStorm.
 * User: Sasha
 * Date: 01.02.2019
 * Time: 10:00
 */

namespace App\Model\Post;


use App\Entity\Post;

class ArrayOfPost extends  \ArrayObject
{

    public function offsetSet($index, $newval)
    {
        if($newval instanceof Post){
            parent::offsetSet($index,$newval);
        }
        throw new \InvalidArgumentException('Value must be a Post');
    }
}