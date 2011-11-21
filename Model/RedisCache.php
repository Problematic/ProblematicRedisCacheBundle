<?php

namespace Problematic\RedisCacheBundle\Model;

use JMS\SerializerBundle\Serializer\SerializerInterface;
use Symfony\Bridge\Monolog\Logger;

class RedisCache implements RedisCacheInterface
{

    protected $redis;
    protected $serializer;
    protected $logger;

    public function __construct(\Predis\Client $redis, SerializerInterface $serializer, Logger $logger = null)
    {
        $this->redis = $redis;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    public function bust($type, $id)
    {
        if ($this->logger) {
            $this->logger->info(sprintf('Busting cache for %s:%s', $type, $id));
        }
        $this->redis->del(sprintf('object.cache:%s:%s', $type, $id));
    }

    public function cache($object, $id)
    {
        if ($this->logger) {
            $this->logger->info(sprintf('Serializing and caching %s:%s', get_class($object), $id));
        }
        $data = $this->serializer->serialize($object, 'json');
        $this->redis->setex(sprintf('object.cache:%s:%s', get_class($object), $id), 604800, $data);
    }

    public function get($type, $id)
    {
        if ($this->logger) {
            $this->logger->info(sprintf('Getting and deserializing %s:%s', $type, $id));
        }
        $data = $this->redis->get(sprintf('object.cache:%s:%s', $type, $id));
        if ($data) {
            return $this->serializer->deserialize($data, $type, 'json');
        }

        return null;
    }

}
