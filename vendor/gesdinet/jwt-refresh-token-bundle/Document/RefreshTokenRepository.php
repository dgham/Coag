<?php

namespace Gesdinet\JWTRefreshTokenBundle\Document;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository as MongoDBDocumentRepository;
use Gesdinet\JWTRefreshTokenBundle\Service\RefreshToken;

if (class_exists(MongoDBDocumentRepository::class)) {
    // Support for doctrine/mongodb-odm >= 2.0
    class BaseRepository extends MongoDBDocumentRepository
    {
    }
} else {
    // Support for doctrine/mongodb-odm < 2.0
    class BaseRepository extends DocumentRepository
    {
    }
}

/**
 * RefreshTokenRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RefreshTokenRepository extends BaseRepository
{
    /**
     * @param null $datetime
     *
     * @return RefreshToken[]
     */
    public function findInvalid($datetime = null)
    {
        $datetime = (null === $datetime) ? new \DateTime() : $datetime;

        $queryBuilder = $this->createQueryBuilder()
            ->field('valid')->lt($datetime);

        return $queryBuilder->getQuery()->execute();
    }
}