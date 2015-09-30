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
        $helperPluginManager = new HelperPluginManager();

        $class = new ReflectionClass('Lfj\ZfRenderer\Renderer');
        $eventsProperty = $class->getProperty("events");
        $eventsProperty->setAccessible(true);
        $helperPluginManagerProperty = $class->getProperty("helperPluginManager");
        $helperPluginManagerProperty->setAccessible(true);

        $renderer = new Renderer($helperPluginManager, $eventManager);
        $clone = clone $renderer;

        $this->assertNotSame($eventsProperty->getValue($renderer), $eventsProperty->getValue($clone));
        $this->assertSame($helperPluginManagerProperty->getValue($renderer), $helperPluginManagerProperty->getValue($clone));
    }

    public function testNewRendererWithEventManager()
    {
        $eventManager1 = new EventManager();
        $eventManager2 = new EventManager();

        $class = new ReflectionClass('Lfj\ZfRenderer\Renderer');
        $property = $class->getProperty("events");
        $property->setAccessible(true);

        $rendererWithEventManager1 = new Renderer(null, $eventManager1);
        $rendererWithEventManager2 = $rendererWithEventManager1->withEventManager($eventManager2);

        $this->assertSame($property->getValue($rendererWithEventManager1), $eventManager1);
        $this->assertSame($property->getValue($rendererWithEventManager2), $eventManager2);
        $this->assertNotSame($rendererWithEventManager1, $rendererWithEventManager2);
        $this->assertNotSame($property->getValue($rendererWithEventManager1), $property->getValue($rendererWithEventManager2));
    }

    public function testNewRendererWithHelperPluginManager()
    {
        $helperPluginManager1 = new HelperPluginManager();
        $helperPluginManager2 = new HelperPluginManager();

        $class = new ReflectionClass('Lfj\ZfRenderer\Renderer');
        $property = $class->getProperty("helperPluginManager");
        $property->setAccessible(true);

        $rendererWithHelperPluginManager1 = new Renderer($helperPluginManager1);
        $rendererWithHelperPluginManager2 = $rendererWithHelperPluginManager1->withHelperPluginManager($helperPluginManager2);

        $this->assertSame($property->getValue($rendererWithHelperPluginManager1), $helperPluginManager1);
        $this->assertSame($property->getValue($rendererWithHelperPluginManager2), $helperPluginManager2);
        $this->assertNotSame($rendererWithHelperPluginManager1, $rendererWithHelperPluginManager2);
        $this->assertNotSame($property->getValue($rendererWithHelperPluginManager1), $property->getValue($rendererWithHelperPluginManager2));

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
