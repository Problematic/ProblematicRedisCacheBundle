<?php

namespace Problematic\RedisCacheBundle\Entity;

use Problematic\RedisCacheBundle\Model\RedisCache;
use JMS\SerializerBundle\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Monolog\Logger;

class ORMRedisCache extends RedisCache
{

    protected $em;

    public function __construct(EntityManager $em, \Predis\Client $redis, SerializerInterface $serializer, Logger $logger = null)
    {
        parent::__construct($redis, $serializer, $logger);
        $this->em = $em;
    }

    public function get($type, $id)
    {
        $entity = parent::get($type, $id);
        if (null === $entity) {
            $entity = $this->em->getRepository($type)->find($id);

            if (null !== $entity) {
                $this->cache($entity, $id);
            }
        }

        return $entity;
    }

}
