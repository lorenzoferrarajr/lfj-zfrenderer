<?php

use Lfj\ZfRenderer\Service\Renderer;
use Zend\EventManager\EventManager;
use Zend\View\HelperPluginManager;
use Zend\View\Resolver;

class RendererTest extends PHPUnit_Framework_TestCase
{
    public function testRenderFileWithoutOptionalParameters()
    {
        $template = realpath('view/template-simple.phtml');
        $expectedContent = file_get_contents($template);

        $renderer = new Renderer();
        $renderedContent = $renderer->render($template);

        $this->assertEquals('Zend\Stdlib\Response', get_class($renderedContent->getResponse()));
        $this->assertEquals($expectedContent, $renderedContent->getResponse()->getContent());
    }

    public function testRenderFileWithParameters()
    {
        $template = realpath('view/template-medium.phtml');
        $expectedContent = 'hello world';

        $renderer = new Renderer();
        $renderedContent = $renderer->render($template, array('name' => 'world'));

        $this->assertEquals('Zend\Stdlib\Response', get_class($renderedContent->getResponse()));
        $this->assertEquals($expectedContent, $renderedContent->getResponse()->getContent());
    }

    public function testRenderFileContainingViewPartial()
    {
        $template = realpath('view/template-hard.phtml');
        $expectedContent = 'hello world';

        $templatePathStack = new Resolver\TemplatePathStack();
        $templatePathStack->addPath(realpath('view'));

        $renderer = new Renderer();
        $renderedContent = $renderer->render($template, array('name' => 'world'), array($templatePathStack));

        $this->assertEquals('Zend\Stdlib\Response', get_class($renderedContent->getResponse()));
        $this->assertEquals($expectedContent, $renderedContent->getResponse()->getContent());
    }

    public function testEventManagerSetterAndGetter()
    {
        $eventManager = new EventManager();
        $renderer = new Renderer();
        $renderer->setEventManager($eventManager);

        $this->assertSame($eventManager, $renderer->getEventManager());
    }

    public function testHelperPluginManagerSetterAndGetter()
    {
        $helperPluginManager = new HelperPluginManager();
        $renderer = new Renderer();
        $renderer->setHelperPluginManager($helperPluginManager);

        $this->assertSame($helperPluginManager, $renderer->getHelperPluginManager());
    }

    public function testEventsAreTriggered()
    {
        $template = realpath('view/template-simple.phtml');
        $expectedContent = file_get_contents($template);

        $renderer = new Renderer();

        $eventsToTrigger = [
            \Zend\View\ViewEvent::EVENT_RENDERER,
            \Zend\View\ViewEvent::EVENT_RENDERER_POST,
            \Zend\View\ViewEvent::EVENT_RESPONSE,
        ];

        $triggeredEvents = [];
        foreach ($eventsToTrigger as $e) {
            $renderer->getEventManager()->attach($e, function(\Zend\View\ViewEvent $e) use (&$triggeredEvents) {
                $triggeredEvents[] = $e->getName();
            });
        }

        $this->assertEquals($eventsToTrigger, $renderer->getEventManager()->getEvents());

        $renderedContent = $renderer->render($template);

        $this->assertEquals($eventsToTrigger, $triggeredEvents);
        $this->assertEquals($expectedContent, $renderedContent->getResponse()->getContent());
    }
}
