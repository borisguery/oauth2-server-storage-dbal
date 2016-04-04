<?php

namespace Bgy\OAuth2Server\DbalStorage;

use Bgy\OAuth2\RefreshToken;
use Bgy\OAuth2\Storage\RefreshTokenNotFound;
use Bgy\OAuth2\Storage\RefreshTokenStorage;
use Doctrine\DBAL\Connection;

/**
 * @author Boris Guéry <guery.b@gmail.com>
 */
class DbalRefreshTokenStorage implements RefreshTokenStorage
{
    private $dbalConnection;
    private $tableConfiguration;

    public function __construct(Connection $dbalConnection, TableConfiguration $tableConfiguration)
    {
        $this->dbalConnection     = $dbalConnection;
        $this->tableConfiguration = $tableConfiguration;
    }

    public function save(RefreshToken $refreshToken)
    {
        $this->dbalConnection->insert(
            $this->tableConfiguration->getRefreshTokenTableName(),
            [
                'refresh_token' => $refreshToken->getToken(),
            ]
        );
    }

    public function delete(RefreshToken $refreshToken)
    {
        $this->dbalConnection->delete(
            $this->tableConfiguration->getRefreshTokenTableName(),
            [
                'refresh_token' => $refreshToken->getToken(),
            ]
        );
    }

    public function findByToken($refreshTokenId)
    {
        $qb = $this->dbalConnection->createQueryBuilder();
        $stmt = $qb->select('*')
            ->from($this->tableConfiguration->getRefreshTokenTableName())
            ->where(
                $qb->expr()->like('refresh_token', ':refreshToken')
            )
            ->setMaxResults(1)
            ->setParameter('refreshToken', $refreshTokenId)
            ->execute()
        ;

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (1 !== count($rows)) {

            throw new RefreshTokenNotFound($refreshTokenId);
        }

        return new RefreshToken(
            $rows[0]['refresh_token']
        );
    }
}
