<?php

namespace RaphaelCangucu\GqlClient\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use RaphaelCangucu\GqlClient\GraphqlClientServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            GraphqlClientServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('graphqlclient.graphql_endpoint', 'https://api.example.com/graphql');
        $app['config']->set('graphqlclient.auth_credentials', 'test-token');
        $app['config']->set('graphqlclient.auth_scheme', 'bearer');
    }
}