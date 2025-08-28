<?php

namespace RaphaelCangucu\GqlClient\Tests\Unit;

use RaphaelCangucu\GqlClient\Tests\TestCase;
use RaphaelCangucu\GqlClient\Classes\Mutator;
use BadMethodCallException;

class MutatorTest extends TestCase
{
    protected TestMutator $mutator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mutator = new TestMutator();
    }

    public function test_magic_get_calls_attribute_method()
    {
        $this->assertEquals('test value', $this->mutator->test_attribute);
    }

    public function test_magic_get_returns_null_for_non_existent_property()
    {
        $this->assertNull($this->mutator->non_existent);
    }

    public function test_magic_call_with_property()
    {
        $result = $this->mutator->withTestProperty('test value');
        
        $this->assertInstanceOf(TestMutator::class, $result);
        $this->assertEquals('test value', $this->mutator->testProperty);
    }

    public function test_magic_call_with_attribute()
    {
        $result = $this->mutator->withCustomAttribute('custom value');
        
        $this->assertInstanceOf(TestMutator::class, $result);
        $this->assertEquals('custom value', $this->mutator->variables['customAttribute']);
    }

    public function test_magic_call_throws_exception_for_invalid_method()
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Method [invalidMethod] does not exist.');
        
        $this->mutator->invalidMethod();
    }
}

// Test class to extend Mutator
class TestMutator extends Mutator
{
    public $testProperty;
    public $variables = [];
    protected $attributes = [];

    public function getTestAttributeAttribute()
    {
        return 'test value';
    }
}