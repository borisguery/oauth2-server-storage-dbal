<?php

namespace Bgy\OAuth2Server\DbalStorage;

use Bgy\OAuth2\Storage\ClientNotFound;
use Bgy\OAuth2\Storage\ClientStorage;
use Bgy\OAuth2\Client as ClientModel;
use Doctrine\DBAL\Connection;

/**
 * @author Boris Guéry <guery.b@gmail.com>
 */
class DbalClientStorage implements ClientStorage
{
    private $dbalConnection;
    private $tableConfiguration;

    public function __construct(Connection $dbalConnection, TableConfiguration $tableConfiguration)
    {
        $this->dbalConnection     = $dbalConnection;
        $this->tableConfiguration = $tableConfiguration;
    }

    public function save(ClientModel $client)
    {
        $this->dbalConnection->insert(
            $this->tableConfiguration->getClientTableName(),
            [
                'client_id'     => $client->getId(),
                'client_secret' => $client->getSecret(),
                'redirect_uris' => serialize($client->getRedirectUris()),
                'grant_types'   => serialize($client->getAllowedGrantTypes()),

            ]
        );
    }

    public function delete(ClientModel $client)
    {
        $this->dbalConnection->delete(
            $this->tableConfiguration->getClientTableName(),
            ['client_id' => $client->getId()]
        );
    }

    public function findById($clientId)
    {

        $qb = $this->dbalConnection->createQueryBuilder();
        $stmt = $qb->select('*')
            ->from($this->tableConfiguration->getClientTableName())
            ->where(
                $qb->expr()->like('client_id', ':clientId')
            )
            ->setMaxResults(1)
            ->setParameter('clientId', $clientId)
            ->execute()
        ;

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        if (1 !== count($rows)) {

            throw new ClientNotFound($clientId);
        }

        return new ClientModel(
            $rows[0]['client_id'],
            $rows[0]['client_secret'],
            unserialize($rows[0]['redirect_uris']),
            unserialize($rows[0]['grant_types'])
        );
    }
}
