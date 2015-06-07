<?php

use Lfj\ZfRenderer\Service\Renderer;
use Zend\EventManager\EventManager;
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

}
