<?php

namespace RaphaelCangucu\GqlClient\Tests\Feature;

use RaphaelCangucu\GqlClient\Tests\TestCase;
use RaphaelCangucu\GqlClient\Facades\GraphQL;
use RaphaelCangucu\GqlClient\Classes\Client;

class FacadeTest extends TestCase
{
    public function test_facade_returns_client_instance()
    {
        $client = GraphQL::getFacadeRoot();
        
        $this->assertInstanceOf(Client::class, $client);
    }

    public function test_facade_can_call_client_methods()
    {
        $result = GraphQL::query('users { id name }');
        
        $this->assertInstanceOf(Client::class, $result);
    }

    public function test_facade_maintains_fluent_interface()
    {
        $result = GraphQL::query('users { id name }')
            ->with(['limit' => 10])
            ->header('X-Test', 'value');
        
        $this->assertInstanceOf(Client::class, $result);
        $this->assertEquals(['limit' => 10], $result->variables);
    }

    public function test_facade_can_build_mutations()
    {
        $result = GraphQL::mutation('createUser(input: $input) { id }')
            ->with(['input' => ['name' => 'John']]);
        
        $this->assertInstanceOf(Client::class, $result);
        $this->assertEquals(['input' => ['name' => 'John']], $result->variables);
    }

    public function test_facade_can_handle_raw_queries()
    {
        $rawQuery = 'query { users { id name } }';
        $result = GraphQL::raw($rawQuery);
        
        $this->assertInstanceOf(Client::class, $result);
        $this->assertEquals($rawQuery, $result->raw_query);
    }
}