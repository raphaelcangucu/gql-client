
# GraphQL Laravel Client

A minimal GraphQL client for Laravel applications. This package is a continuation of the original work by David Gutierrez (BendeckDavid), who has discontinued this project. We've updated it with the latest dependencies, comprehensive testing, and continued maintenance under the new namespace.

## Credits

**Original Author:** David Gutierrez [@bendeckdavid](https://www.github.com/bendeckdavid)

This package is based on the original `bendeckdavid/graphql-client` package. All core functionality and design patterns are credited to the original author. This fork continues development with bug fixes, updates, and improvements.

## Requirements

- PHP ^8.0
- Laravel ^8.0|^9.0|^10.0|^11.0|^12.0
- Composer 2+

## Installation

Install Package (Composer 2+)
```bash
composer require raphaelcangucu/gql-client
```


## Usage

Enviroment variable 
```php
GRAPHQL_ENDPOINT="https://api.spacex.land/graphql/"
```


## Authentication

We provide a minimal authentication integration by appending the `Authorization` header to the request client. You can pass the credentials using an `env` variable.
```php
GRAPHQL_CREDENTIALS="YOUR_CREDENTIALS"
```

You can also pass auth credentials at runtime using `withToken($credentials)` method.


'Authorization' header and 'Bearer' Schema are used by default. You can override the default behaviour by defining following variables in your `.env` file.
```php
GRAPHQL_AUTHENTICATION_HEADER="Authorization"

// Allowed: basic, bearer, custom
GRAPHQL_AUTHENTICATION="bearer"
```


## Usage/Examples

Import GraphQL Client Facades
```php
use RaphaelCangucu\GqlClient\Facades\GraphQL;
```

Basic use

```php
return GraphQL::query('
    capsules {
        id
        original_launch
        status
        missions {
            name
            flight
        }
    }
')->get();
//->get('json'); //get response as json object
```

Mutation request:
```php
return GraphQL::mutation('
    insert_user(name: "David") {
        id
        name
        date_added
    }
')->get();
//->get('json');
```

You can access "query" or "mutation" as a shortcut if you are not passing variables, if is not the case you must use the "raw" attribute:

```php
return GraphQL::raw('
    mutation($name: String) {
        insert_user(name: $name) {
            id
            name
            date_added
        }
    }
')
->with(["name" => "David"])
->get();
//->get('json');
```

The `variables` or `payload` to the GraphQL request can also be passed using magic methods like:
```php
return GraphQL::raw('
    mutation($name: String) {
        insert_user(name: $name) {
            id
            name
            date_added
        }
    }
')
->withName("David")
->get();
//->get('json');
```

If you want to address te request to another endpoint, you can do :

```php
return GraphQL::endpoint("https://api.spacex.land/graphql/")
->query('
    capsules {
        id
        original_launch
        status
        missions {
            name
            flight
        }
    }
')->get();
//->get('json');
```


## Headers

You can include a header to the request by using the attribute "header" or add multiple headers by "withHeaders":
```php
return GraphQL::query($query)
->header('name', 'value')
->withHeaders([
    'name' => 'value',
    'name' => 'value'
])->get();
```

## Context

Add additional context to the request
```php
return GraphQL::query($query)
->context([
    'ssl' => [
         "verify_peer" => false,
         "verify_peer_name" => false,
    ]
  ])->get();
```

## Testing

This package comes with comprehensive test coverage. Run tests using:

```bash
composer test
```

For coverage reports:
```bash
composer test-coverage
```

## Configuration Publishing

Publish the configuration file to customize authentication and endpoint settings:

```bash
php artisan vendor:publish --provider="RaphaelCangucu\GqlClient\GraphqlClientServiceProvider" --tag="config"
```

## What's New in This Fork

- Updated to support PHP 8.0+ and Laravel 8-12
- Laravel 12 compatibility added in version 0.2.0
- Comprehensive test suite with 40+ tests
- Updated dependencies and security improvements
- Fixed deprecated method names (`mutator` → `mutation`)
- Improved error handling and documentation
- Full namespace migration for modern Laravel applications

## Original Author & Contributors

**Original Author:** David Gutierrez [@bendeckdavid](https://www.github.com/bendeckdavid)

**Original Top Contributors:**
- Ehsan Quddusi [@ehsanquddusi](https://github.com/ehsanquddusi)

**Original Contributors:**
- Ryan Mayberry [@kerkness](https://github.com/kerkness)
- Jamie Duong [@chiendv](https://github.com/chiendv)

## Current Maintainer

- Raphael Cangucu [@raphaelcangucu](https://github.com/raphaelcangucu)

## License

This project maintains the same license as the original package.


