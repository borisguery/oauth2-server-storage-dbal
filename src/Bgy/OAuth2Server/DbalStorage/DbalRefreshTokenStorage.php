<?php

namespace Bgy\OAuth2Server\DbalStorage;

use Bgy\OAuth2\AccessToken;
use Bgy\OAuth2\RefreshToken;
use Bgy\OAuth2\ResourceOwner;
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
        $revokedAt = $refreshToken->getRevokedAt()
            ? substr(
                $refreshToken->getRevokedAt()->format(\DateTime::ISO8601),
                0,
                -5
            )
            : null
        ;

        try {
            $this->findByToken($refreshToken->getToken());
            $this->dbalConnection->update(
                $this->tableConfiguration->getRefreshTokenTableName(),
                [
                    'associated_access_token' => $refreshToken->getAssociatedAccessToken()->getToken(),
                    'expires_at' => substr(
                        $refreshToken->getExpiresAt()->format(\DateTime::ISO8601),
                        0,
                        -5
                    ),
                    'revoked_at' => $revokedAt
                ],
                [
                    'refresh_token' => $refreshToken->getToken(),
                ]
            );
        } catch (RefreshTokenNotFound $e) {
            $this->dbalConnection->insert(
                $this->tableConfiguration->getRefreshTokenTableName(),
                [
                    'refresh_token' => $refreshToken->getToken(),
                    'associated_access_token' => $refreshToken->getAssociatedAccessToken()->getToken(),
                    'expires_at' => substr(
                        $refreshToken->getExpiresAt()->format(\DateTime::ISO8601),
                        0,
                        -5
                    ),
                    'revoked_at' => $revokedAt
                ]
            );
        }
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
        $stmt = $qb->select('*, r.expires_at AS refresh_token_expires_at')
            ->from($this->tableConfiguration->getRefreshTokenTableName(), 'r')
            ->innerJoin(
                'r',
                $this->tableConfiguration->getAccessTokenTableName(),
                'a',
                'a.access_token = r.associated_access_token'
            )
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

        $revokedAt = (null !== $rows[0]['revoked_at'])
            ? \DateTimeImmutable::createFromFormat(
                'Y-m-d H:i:s',
                $rows[0]['revoked_at'],
                new \DateTimeZone('UTC')
            )
            : null
        ;

        return new RefreshToken(
            $rows[0]['refresh_token'],
            new AccessToken(
                $rows[0]['access_token'],
                \DateTimeImmutable::createFromFormat(
                    'Y-m-d H:i:s',
                    $rows[0]['expires_at'],
                    new \DateTimeZone('UTC')
                ),
                $rows[0]['client_id'],
                ($rows[0]['resource_owner_id'] && $rows[0]['resource_owner_type'])
                    ? new ResourceOwner($rows[0]['resource_owner_id'], $rows[0]['resource_owner_type'])
                    : null,
                explode(',', $rows[0]['scopes'])
            ),
            \DateTimeImmutable::createFromFormat(
                'Y-m-d H:i:s',
                $rows[0]['refresh_token_expires_at'],
                new \DateTimeZone('UTC')
            ),
            $revokedAt
        );
    }
}
