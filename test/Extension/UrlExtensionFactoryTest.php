<?php

/**
 * @see       https://github.com/mezzio/mezzio-platesrenderer for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio-platesrenderer/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio-platesrenderer/blob/master/LICENSE.md New BSD License
 */

namespace MezzioTest\Plates\Extension;

use Interop\Container\ContainerInterface;
use Mezzio\Helper\ServerUrlHelper;
use Mezzio\Helper\UrlHelper;
use Mezzio\Plates\Exception\MissingHelperException;
use Mezzio\Plates\Extension\UrlExtension;
use Mezzio\Plates\Extension\UrlExtensionFactory;
use PHPUnit_Framework_TestCase as TestCase;

class UrlExtensionFactoryTest extends TestCase
{
    public function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->urlHelper = $this->prophesize(UrlHelper::class);
        $this->serverUrlHelper = $this->prophesize(ServerUrlHelper::class);
    }

    public function testFactoryReturnsUrlExtensionInstanceWhenHelpersArePresent()
    {
        $this->container->has(UrlHelper::class)->willReturn(true);
        $this->container->get(UrlHelper::class)->willReturn($this->urlHelper->reveal());
        $this->container->has(ServerUrlHelper::class)->willReturn(true);
        $this->container->get(ServerUrlHelper::class)->willReturn($this->serverUrlHelper->reveal());

        $factory = new UrlExtensionFactory();
        $extension = $factory($this->container->reveal());
        $this->assertInstanceOf(UrlExtension::class, $extension);

        $this->assertAttributeSame($this->urlHelper->reveal(), 'urlHelper', $extension);
        $this->assertAttributeSame($this->serverUrlHelper->reveal(), 'serverUrlHelper', $extension);
    }

    public function testFactoryRaisesExceptionIfUrlHelperIsMissing()
    {
        $this->container->has(UrlHelper::class)->willReturn(false);
        $this->container->has(\Zend\Expressive\Helper\UrlHelper::class)->willReturn(false);
        $this->container->get(UrlHelper::class)->shouldNotBeCalled();
        $this->container->get(\Zend\Expressive\Helper\UrlHelper::class)->shouldNotBeCalled();
        $this->container->has(ServerUrlHelper::class)->shouldNotBeCalled();
        $this->container->has(\Zend\Expressive\Helper\ServerUrlHelper::class)->shouldNotBeCalled();
        $this->container->get(ServerUrlHelper::class)->shouldNotBeCalled();
        $this->container->get(\Zend\Expressive\Helper\ServerUrlHelper::class)->shouldNotBeCalled();

        $factory = new UrlExtensionFactory();

        $this->setExpectedException(MissingHelperException::class, UrlHelper::class);
        $factory($this->container->reveal());
    }

    public function testFactoryRaisesExceptionIfServerUrlHelperIsMissing()
    {
        $this->container->has(UrlHelper::class)->willReturn(true);
        $this->container->get(UrlHelper::class)->shouldNotBeCalled();
        $this->container->get(\Zend\Expressive\Helper\UrlHelper::class)->shouldNotBeCalled();
        $this->container->has(ServerUrlHelper::class)->willReturn(false);
        $this->container->has(\Zend\Expressive\Helper\ServerUrlHelper::class)->willReturn(false);
        $this->container->get(ServerUrlHelper::class)->shouldNotBeCalled();
        $this->container->get(\Zend\Expressive\Helper\ServerUrlHelper::class)->shouldNotBeCalled();

        $factory = new UrlExtensionFactory();

        $this->setExpectedException(MissingHelperException::class, ServerUrlHelper::class);
        $factory($this->container->reveal());
    }
}
