<?php

use Lfj\ZfRenderer\Renderer;
use Zend\EventManager\EventManager;
use Zend\View\Resolver;

class EventsTest extends PHPUnit_Framework_TestCase
{
    public function testEventsAreTriggered()
    {
        $template = realpath('view/template-simple.phtml');
        $expectedContent = file_get_contents($template);

        $class = new ReflectionClass('Lfj\ZfRenderer\Renderer');
        $property = $class->getProperty("events");
        $property->setAccessible(true);

        $renderer = new Renderer();

        $eventManager = new EventManager();

        $eventsToTrigger = [
            \Zend\View\ViewEvent::EVENT_RENDERER,
            \Zend\View\ViewEvent::EVENT_RENDERER_POST,
            \Zend\View\ViewEvent::EVENT_RESPONSE,
        ];

        $triggeredEvents = [];
        foreach ($eventsToTrigger as $e) {
            $eventManager->attach($e, function (\Zend\View\ViewEvent $e) use (&$triggeredEvents) {
                $triggeredEvents[] = $e->getName();
            });
        }

        $rendererWithEventManager = $renderer->withEventManager($eventManager);
        $this->assertEquals($eventsToTrigger, $property->getValue($rendererWithEventManager)->getEvents());

        $renderedContent = $rendererWithEventManager->render($template);

        $this->assertEquals($eventsToTrigger, $triggeredEvents);
        $this->assertEquals($expectedContent, $renderedContent->getResponse()->getContent());
    }
}
