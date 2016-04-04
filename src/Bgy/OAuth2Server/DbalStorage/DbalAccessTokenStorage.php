<?php

namespace Bgy\OAuth2Server\DbalStorage;

use Bgy\OAuth2\AccessToken;
use Bgy\OAuth2\ResourceOwner;
use Bgy\OAuth2\Storage\AccessTokenNotFound;
use Bgy\OAuth2\Storage\AccessTokenStorage;
use Doctrine\DBAL\Connection;

/**
 * @author Boris Guéry <guery.b@gmail.com>
 */
class DbalAccessTokenStorage implements AccessTokenStorage
{
    private $dbalConnection;
    private $tableConfiguration;

    public function __construct(Connection $dbalConnection, TableConfiguration $tableConfiguration)
    {
        $this->dbalConnection     = $dbalConnection;
        $this->tableConfiguration = $tableConfiguration;
    }

    public function save(AccessToken $accessToken)
    {
        $this->dbalConnection->insert(
            $this->tableConfiguration->getAccessTokenTableName(),
            [
                'access_token'        => $accessToken->getToken(),
                'resource_owner_id'   => (null === $accessToken->getResourceOwner())
                    ? null
                    : $accessToken->getResourceOwner()->getResourceOwnerId(),
                'resource_owner_type' => (null === $accessToken->getResourceOwner())
                    ? null
                    : $accessToken->getResourceOwner()->getResourceOwnerType(),
                'client_id'           => $accessToken->getClientId(),
                'scopes'              => implode(',', $accessToken->getScopes()),
                'expires_at'          => substr(
                    $accessToken->getExpiresAt()->format(\DateTime::ISO8601),
                    0,
                    -5
                )
            ]
        );
    }

    public function delete(AccessToken $accessToken)
    {
        $this->dbalConnection->delete(
            $this->tableConfiguration->getAccessTokenTableName(),
            ['access_token' => $accessToken->getToken()]
        );
    }

    public function findByToken($accessTokenId)
    {
        $qb = $this->dbalConnection->createQueryBuilder();
        $stmt = $qb->select('*')
            ->from($this->tableConfiguration->getAccessTokenTableName())
            ->where(
                $qb->expr()->like('access_token', ':accessToken')
            )
            ->setMaxResults(1)
            ->setParameter('accessToken', $accessTokenId)
            ->execute()
        ;

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        if (1 !== count($rows)) {

            throw new AccessTokenNotFound($accessTokenId);
        }

        return new AccessToken(
            $rows[0]['access_token'],
            \DateTimeImmutable::createFromFormat(
                \DateTime::ISO8601,
                $rows[0]['expires_at'],
                new \DateTimeZone('UTC')
            ),
            $rows[0]['client_id'],
            ($rows[0]['resource_owner_id'] && $rows[0]['resource_owner_type'])
                ? new ResourceOwner($rows[0]['resource_owner_id'], $rows[0]['resource_owner_type'])
                : null,
            explode(',', $rows[0]['scopes'])
        );
    }
}
