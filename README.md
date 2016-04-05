# OAuth2 Server Storage DBAL (Doctrine)

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

OAuth2 Server Bundle for Symfony

## Install

Via Composer

``` bash
$ composer require borisguery/oauth2-server-storage-dbal
```

## Configuration

Here are the keys to override if you wish to change the default table 
names.

``` php
$tableConfiguration = new TableConfiguration(
    [
            'oauth2_clients'        => 'oauth2_clients',
            'oauth2_access_tokens'  => 'oauth2_access_tokens',
            'oauth2_refresh_tokens' => 'oauth2_refresh_tokens',
    ]
);
```

## Usage

``` php
$accessTokenStorage  = new DbalAccessTokenStorage($dbalConnection, $tableConfiguration);
$refreshTokenStorage = new DbalRefreshTokenStorage($dbalConnection, $tableConfiguration);
$clientStorage       = new DbalClientStorage($dbalConnection, $tableConfiguration);
```

that's it!

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email guery.b@gmail.com instead of using the issue tracker.

## Credits

- [Boris Guéry][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/borisguery/oauth2-server-storage-dbal.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/borisguery/oauth2-server-storage-dbal/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/borisguery/oauth2-server-storage-dbal.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/borisguery/oauth2-server-storage-dbal.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/borisguery/oauth2-server-storage-dbal.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/borisguery/oauth2-server-storage-dbal
[link-travis]: https://travis-ci.org/borisguery/oauth2-server-storage-dbal
[link-scrutinizer]: https://scrutinizer-ci.com/g/borisguery/oauth2-server-storage-dbal/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/borisguery/oauth2-server-storage-dbal
[link-downloads]: https://packagist.org/packages/borisguery/oauth2-server-storage-dbal
[link-author]: https://github.com/borisguery
[link-contributors]: ../../contributors
