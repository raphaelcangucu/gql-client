<?php

namespace RaphaelCangucu\GqlClient\Tests\Feature;

use RaphaelCangucu\GqlClient\Tests\TestCase;
use RaphaelCangucu\GqlClient\Classes\Client;
use RaphaelCangucu\GqlClient\Facades\GraphQL;
use RaphaelCangucu\GqlClient\Enums\Format;
use Mockery;

class GraphqlClientIntegrationTest extends TestCase
{
    public function test_facade_resolves_to_client_instance()
    {
        $this->assertInstanceOf(Client::class, GraphQL::getFacadeRoot());
    }

    public function test_client_can_build_complete_query_request()
    {
        $client = new Client('https://api.example.com/graphql');
        
        $result = $client
            ->query('users(first: $limit) { id name email }')
            ->with(['limit' => 10])
            ->header('X-Custom-Header', 'test-value')
            ->withHeaders(['X-Another' => 'another-value']);
        
        $this->assertInstanceOf(Client::class, $result);
        $this->assertEquals(['limit' => 10], $result->variables);
        
        $headers = $result->headers;
        $this->assertContains('X-Custom-Header: test-value', $headers);
        $this->assertContains('X-Another: another-value', $headers);
    }

    public function test_client_can_build_complete_mutation_request()
    {
        $client = new Client('https://api.example.com/graphql');
        
        $result = $client
            ->mutation('createUser(input: $userInput) { id name email }')
            ->with(['userInput' => ['name' => 'John', 'email' => 'john@example.com']])
            ->context(['ssl' => ['verify_peer' => false]]);
        
        $this->assertInstanceOf(Client::class, $result);
        $this->assertEquals(['userInput' => ['name' => 'John', 'email' => 'john@example.com']], $result->variables);
        $this->assertEquals(['ssl' => ['verify_peer' => false]], $result->context);
    }

    public function test_client_can_build_raw_query_request()
    {
        $client = new Client('https://api.example.com/graphql');
        
        $rawQuery = 'query GetUsers($limit: Int) { users(first: $limit) { id name } }';
        $result = $client->raw($rawQuery);
        
        $this->assertInstanceOf(Client::class, $result);
        $this->assertEquals($rawQuery, $result->raw_query);
    }

    public function test_client_endpoint_can_be_changed()
    {
        $client = new Client('https://api.example.com/graphql');
        
        $newEndpoint = 'https://new-api.example.com/graphql';
        $result = $client->endpoint($newEndpoint);
        
        $this->assertInstanceOf(Client::class, $result);
    }

    public function test_client_authentication_integration()
    {
        // Set up config for authentication
        config(['graphqlclient.auth_credentials' => 'test-bearer-token']);
        config(['graphqlclient.auth_scheme' => 'bearer']);
        config(['graphqlclient.auth_header' => 'Authorization']);
        config(['graphqlclient.auth_schemes' => [
            'bearer' => 'Bearer ',
            'basic' => 'Basic ',
            'custom' => null
        ]]);
        
        $client = new Client('https://api.example.com/graphql');
        $headers = $client->headers;
        
        $this->assertContains('Authorization: Bearer test-bearer-token', $headers);
    }

    public function test_client_custom_authentication_integration()
    {
        // Set up config for custom authentication
        config(['graphqlclient.auth_credentials' => 'custom-api-key']);
        config(['graphqlclient.auth_scheme' => 'custom']);
        config(['graphqlclient.auth_header' => 'X-API-Key']);
        config(['graphqlclient.auth_schemes' => [
            'bearer' => 'Bearer ',
            'basic' => 'Basic ',
            'custom' => null
        ]]);
        
        $client = new Client('https://api.example.com/graphql');
        $headers = $client->headers;
        
        $this->assertContains('X-API-Key: custom-api-key', $headers);
    }

    public function test_mutator_dynamic_methods_integration()
    {
        $client = new Client('https://api.example.com/graphql');
        
        $result = $client
            ->query('users { id name }')
            ->withUserId(123)
            ->withUserName('John Doe')
            ->withIsActive(true);
        
        $expected = [
            'userId' => 123,
            'userName' => 'John Doe',
            'isActive' => true
        ];
        
        $this->assertEquals($expected, $result->variables);
    }

    public function test_request_building_integration()
    {
        $client = new Client('https://api.example.com/graphql');
        
        $client
            ->query('users { id name }')
            ->with(['limit' => 5])
            ->header('User-Agent', 'Test Client');
        
        $request = $client->request;
        
        $this->assertIsResource($request);
        
        // Verify context structure
        $options = stream_context_get_options($request);
        $this->assertArrayHasKey('http', $options);
        $this->assertEquals('POST', $options['http']['method']);
        $this->assertJson($options['http']['content']);
        
        $content = json_decode($options['http']['content'], true);
        $this->assertArrayHasKey('query', $content);
        $this->assertArrayHasKey('variables', $content);
        $this->assertEquals(['limit' => 5], $content['variables']);
    }

    public function test_full_workflow_integration()
    {
        $client = new Client('https://api.example.com/graphql');
        
        // Build a complete request
        $client
            ->query('users(first: $limit, where: $filters) { 
                id 
                name 
                email 
                createdAt 
            }')
            ->with([
                'limit' => 10,
                'filters' => ['status' => 'active']
            ])
            ->withHeaders([
                'X-Request-ID' => 'test-123',
                'X-Client-Version' => '1.0.0'
            ])
            ->context([
                'http' => [
                    'timeout' => 30
                ]
            ]);
        
        // Verify the query is properly formatted
        $expectedQuery = 'query {users(first: $limit, where: $filters) { 
                id 
                name 
                email 
                createdAt 
            }}';
        
        $this->assertEquals($expectedQuery, $client->raw_query);
        
        // Verify variables are set
        $this->assertEquals([
            'limit' => 10,
            'filters' => ['status' => 'active']
        ], $client->variables);
        
        // Verify headers include custom ones
        $headers = $client->headers;
        $this->assertContains('X-Request-ID: test-123', $headers);
        $this->assertContains('X-Client-Version: 1.0.0', $headers);
        
        // Verify context is set
        $this->assertEquals([
            'http' => [
                'timeout' => 30
            ]
        ], $client->context);
    }
}