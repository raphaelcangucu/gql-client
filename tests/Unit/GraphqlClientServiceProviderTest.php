<?php

namespace RaphaelCangucu\GqlClient\Tests\Unit;

use RaphaelCangucu\GqlClient\Tests\TestCase;
use RaphaelCangucu\GqlClient\Classes\Client;
use RaphaelCangucu\GqlClient\GraphqlClientServiceProvider;

class GraphqlClientServiceProviderTest extends TestCase
{
    public function test_service_provider_registers_client_binding()
    {
        $client = $this->app->make('graphqlClient');
        
        $this->assertInstanceOf(Client::class, $client);
    }

    public function test_service_provider_registers_config()
    {
        $this->assertNotNull(config('graphqlclient.graphql_endpoint'));
        $this->assertNotNull(config('graphqlclient.auth_scheme'));
    }

    public function test_service_provider_is_in_providers_list()
    {
        $providers = $this->getPackageProviders($this->app);
        
        $this->assertContains(GraphqlClientServiceProvider::class, $providers);
    }
}