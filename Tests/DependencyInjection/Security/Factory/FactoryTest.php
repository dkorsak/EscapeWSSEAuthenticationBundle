<?php

namespace Escape\WSSEAuthenticationBundle\Tests\DependencyInjection\Security\Factory;

use Escape\WSSEAuthenticationBundle\DependencyInjection\Security\Factory\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function getPosition()
    {
        $factory = new Factory();
        $result = $factory->getPosition();
        $this->assertEquals('pre_auth', $result);
    }

    /**
     * @test
     */
    public function getKey()
    {
        $factory = new Factory();
        $result = $factory->getKey();
        $this->assertEquals('wsse', $result);
        $this->assertEquals('wsse', $this->getFactory()->getKey());
    }

    protected function getFactory()
    {
        return $this->getMockForAbstractClass('Escape\WSSEAuthenticationBundle\DependencyInjection\Security\Factory\Factory', array());
    }

    public function testCreate()
    {
        $factory = $this->getFactory();

        $container = new ContainerBuilder();
        $container->register('escape_wsse_authentication.provider');

        list($authProviderId,
             $listenerId,
             $entryPointId
        ) = $factory->create(
            $container,
            'foo',
            array(
                'nonce_dir' => 'nonce',
                'lifetime' => 300,
                'realm' => 'EscapeWSSEAuthenticationBundle',
                'profile' => 'UsernameToken',
                'verbose' => false
            ),
            'user_provider',
            'entry_point'
        );

        // auth provider
        $this->assertEquals('escape_wsse_authentication.provider.foo', $authProviderId);
        $this->assertEquals('escape_wsse_authentication.listener.foo', $listenerId);
        $this->assertEquals('entry_point', $entryPointId);
        $this->assertTrue($container->hasDefinition('escape_wsse_authentication.listener.foo'));
        $definition = $container->getDefinition('escape_wsse_authentication.provider.foo');
        $this->assertEquals(array('index_0' => new Reference('user_provider'), 'index_1' => 'nonce', 'index_2' => 300), $definition->getArguments());
        $this->assertTrue($container->hasDefinition('escape_wsse_authentication.provider.foo'));
    }
}
