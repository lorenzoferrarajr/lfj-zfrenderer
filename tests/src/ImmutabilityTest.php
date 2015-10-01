<?php

use Lfj\ZfRenderer\Renderer;
use Zend\EventManager\EventManager;
use Zend\View\HelperPluginManager;
use Zend\View\Resolver;

class ImmutabilityTest extends PHPUnit_Framework_TestCase
{
    public function testClone()
    {
        $eventManager = new EventManager();
        $helpers = new HelperPluginManager();

        $class = new ReflectionClass('Lfj\ZfRenderer\Renderer');
        $eventsProperty = $class->getProperty("events");
        $eventsProperty->setAccessible(true);
        $helpersProperty = $class->getProperty("helpers");
        $helpersProperty->setAccessible(true);

        $renderer = new Renderer($helpers, $eventManager);
        $clone = clone $renderer;

        $this->assertNotSame($eventsProperty->getValue($renderer), $eventsProperty->getValue($clone));
        $this->assertSame($helpersProperty->getValue($renderer), $helpersProperty->getValue($clone));
    }

    public function testNewRendererWithEventManager()
    {
        $events1 = new EventManager();
        $events2 = new EventManager();

        $class = new ReflectionClass('Lfj\ZfRenderer\Renderer');
        $property = $class->getProperty("events");
        $property->setAccessible(true);

        $rendererWithEvents1 = new Renderer(null, $events1);
        $rendererWithEvents2 = $rendererWithEvents1->withEventManager($events2);

        $this->assertSame($property->getValue($rendererWithEvents1), $events1);
        $this->assertSame($property->getValue($rendererWithEvents2), $events2);
        $this->assertNotSame($rendererWithEvents1, $rendererWithEvents2);
        $this->assertNotSame($property->getValue($rendererWithEvents1), $property->getValue($rendererWithEvents2));
    }

    public function testNewRendererWithHelperPluginManager()
    {
        $helpers1 = new HelperPluginManager();
        $helpers2 = new HelperPluginManager();

        $class = new ReflectionClass('Lfj\ZfRenderer\Renderer');
        $property = $class->getProperty("helpers");
        $property->setAccessible(true);

        $rendererWithHelpers1 = new Renderer($helpers1);
        $rendererWithHelpers2 = $rendererWithHelpers1->withHelperPluginManager($helpers2);

        $this->assertSame($property->getValue($rendererWithHelpers1), $helpers1);
        $this->assertSame($property->getValue($rendererWithHelpers2), $helpers2);
        $this->assertNotSame($rendererWithHelpers1, $rendererWithHelpers2);
        $this->assertNotSame($property->getValue($rendererWithHelpers1), $property->getValue($rendererWithHelpers2));

    }

    public function testNewRendererWithResolvers()
    {
        $templatePathStack1 = new Resolver\TemplatePathStack();
        $templatePathStack1->addPath(realpath('view'));

        $templatePathStack2 = new Resolver\TemplatePathStack();
        $templatePathStack2->addPath(realpath('view'));

        $resolvers1 = array($templatePathStack1);
        $resolvers2 = array($templatePathStack2);

        $class = new ReflectionClass('Lfj\ZfRenderer\Renderer');
        $property = $class->getProperty("resolvers");
        $property->setAccessible(true);

        $rendererResolvers1 = new Renderer(null, null, $resolvers1);
        $rendererResolvers2 = $rendererResolvers1->withResolvers($resolvers2);

        $resolversFromRendererResolvers1 = $property->getValue($rendererResolvers1);
        $resolversFromRendererResolvers2 = $property->getValue($rendererResolvers2);

        // all resolvers of $rendererResolvers1 must be the same as the ones in $resolvers1
        // but must be different from the ones in $resolvers2

        foreach ($resolversFromRendererResolvers1 as $i => $v) {
            $this->assertSame($resolvers1[$i], $v);
            $this->assertNotSame($resolvers2[$i], $v);
        }

        // all resolvers of $rendererResolvers2 must be the same as the ones in $resolvers2
        // but must be different from the ones in $resolvers1

        foreach ($resolversFromRendererResolvers2 as $i => $v) {
            $this->assertSame($resolvers2[$i], $v);
            $this->assertNotSame($resolvers1[$i], $v);
        }
    }
}
