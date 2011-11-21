<?php

namespace Problematic\RedisCacheBundle\Model;

interface RedisCacheInterface
{

    function cache($object, $id);

    function get($type, $id);

    function bust($type, $id);

}
