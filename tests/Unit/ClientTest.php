<?php

namespace RaphaelCangucu\GqlClient\Tests\Unit;

use RaphaelCangucu\GqlClient\Tests\TestCase;
use RaphaelCangucu\GqlClient\Classes\Client;
use RaphaelCangucu\GqlClient\Enums\Format;
use RaphaelCangucu\GqlClient\Enums\Request;
use Exception;
use Mockery;

class ClientTest extends TestCase
{
    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new Client('https://api.example.com/graphql');
    }

    public function test_client_instantiation()
    {
        $client = new Client('https://api.example.com/graphql');
        $this->assertInstanceOf(Client::class, $client);
    }

    public function test_client_can_be_instantiated_with_null_endpoint()
    {
        $client = new Client(null);
        $this->assertInstanceOf(Client::class, $client);
    }

    public function test_query_method_sets_query_type_and_query()
    {
        $query = 'users { id name email }';
        $result = $this->client->query($query);
        
        $this->assertInstanceOf(Client::class, $result);
        $this->assertEquals(Request::QUERY, $result->queryType);
    }

    public function test_mutation_method_sets_query_type_and_query()
    {
        $mutation = 'createUser(input: { name: "John" }) { id name }';
        $result = $this->client->mutation($mutation);
        
        $this->assertInstanceOf(Client::class, $result);
        $this->assertEquals(Request::MUTATION, $result->queryType);
    }

    public function test_raw_method_sets_query_type_and_query()
    {
        $rawQuery = 'query { users { id name } }';
        $result = $this->client->raw($rawQuery);
        
        $this->assertInstanceOf(Client::class, $result);
        $this->assertEquals(Request::RAW, $result->queryType);
    }

    public function test_header_method_adds_header()
    {
        $this->client->header('X-Custom-Header', 'custom-value');
        $headers = $this->client->headers;
        
        $this->assertContains('X-Custom-Header: custom-value', $headers);
    }

    public function test_with_headers_method_adds_multiple_headers()
    {
        $headers = [
            'X-Custom-1' => 'value1',
            'X-Custom-2' => 'value2'
        ];
        
        $this->client->withHeaders($headers);
        $clientHeaders = $this->client->headers;
        
        $this->assertContains('X-Custom-1: value1', $clientHeaders);
        $this->assertContains('X-Custom-2: value2', $clientHeaders);
    }

    public function test_with_method_adds_variables()
    {
        $variables = ['id' => 1, 'name' => 'John'];
        $this->client->with($variables);
        
        $this->assertEquals($variables, $this->client->variables);
    }

    public function test_context_method_sets_context()
    {
        $context = ['ssl' => ['verify_peer' => false]];
        $result = $this->client->context($context);
        
        $this->assertInstanceOf(Client::class, $result);
        $this->assertEquals($context, $result->context);
    }

    public function test_endpoint_method_sets_endpoint()
    {
        $newEndpoint = 'https://new-api.example.com/graphql';
        $result = $this->client->endpoint($newEndpoint);
        
        $this->assertInstanceOf(Client::class, $result);
    }

    public function test_get_raw_query_attribute_for_query()
    {
        $query = 'users { id name }';
        $this->client->query($query);
        
        $rawQuery = $this->client->raw_query;
        $expected = "query {{$query}}";
        
        $this->assertEquals($expected, $rawQuery);
    }

    public function test_get_raw_query_attribute_for_mutation()
    {
        $mutation = 'createUser(input: {}) { id }';
        $this->client->mutation($mutation);
        
        $rawQuery = $this->client->raw_query;
        $expected = "mutation {{$mutation}}";
        
        $this->assertEquals($expected, $rawQuery);
    }

    public function test_get_raw_query_attribute_for_raw()
    {
        $rawQuery = 'query { users { id name } }';
        $this->client->raw($rawQuery);
        
        $result = $this->client->raw_query;
        
        $this->assertEquals($rawQuery, $result);
    }

    public function test_get_headers_attribute_includes_default_headers()
    {
        $headers = $this->client->headers;
        
        $this->assertContains('Content-Type: application/json', $headers);
        $this->assertContains('User-Agent: Laravel GraphQL client', $headers);
    }

    public function test_get_headers_attribute_includes_auth_when_token_present()
    {
        // Mock the config to return auth settings
        config(['graphqlclient.auth_credentials' => 'test-token']);
        config(['graphqlclient.auth_scheme' => 'bearer']);
        config(['graphqlclient.auth_header' => 'Authorization']);
        config(['graphqlclient.auth_schemes' => ['bearer' => 'Bearer ']]);
        
        $client = new Client('https://api.example.com/graphql');
        $headers = $client->headers;
        
        $this->assertContains('Authorization: Bearer test-token', $headers);
    }

    public function test_get_request_attribute_builds_context()
    {
        $query = 'users { id }';
        $this->client->query($query)->with(['limit' => 10]);
        
        $request = $this->client->request;
        $this->assertIsResource($request);
    }

    public function test_include_authentication_throws_exception_for_invalid_scheme()
    {
        // Set invalid auth scheme
        config(['graphqlclient.auth_scheme' => 'invalid']);
        config(['graphqlclient.auth_credentials' => 'token']);
        config(['graphqlclient.auth_schemes' => ['bearer' => 'Bearer ']]);
        
        $client = new Client('https://api.example.com/graphql');
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid Graphql Client Auth Scheme');
        
        $client->headers; // This will trigger includeAuthentication
    }

    public function test_mutator_with_methods()
    {
        $this->client->withUserId(123);
        $this->assertEquals(123, $this->client->variables['userId']);
        
        $this->client->withUserName('John');
        $this->assertEquals('John', $this->client->variables['userName']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}