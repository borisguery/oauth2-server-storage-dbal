<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Bgy\OAuth2Server\DbalStorage;


class TableConfiguration
{
    private $tableNames = [
        'oauth2_clients'        => 'oauth2_clients',
        'oauth2_access_tokens'  => 'oauth2_access_tokens',
        'oauth2_refresh_tokens' => 'oauth2_refresh_tokens',
    ];

    public function __construct(array $tableNames)
    {
        $this->tableNames = array_merge($this->tableNames, $tableNames);
    }

    public function getAccessTokenTableName()
    {
        return $this->tableNames['oauth2_access_tokens'];
    }

    public function getRefreshTokenTableName()
    {
        return $this->tableNames['oauth2_refresh_tokens'];
    }

    public function getClientTableName()
    {
        return $this->tableNames['oauth2_clients'];
    }
}
