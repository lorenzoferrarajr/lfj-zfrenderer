<?php

use Lfj\ZfRenderer\Service\Renderer;
use Zend\View\Resolver;

class StackTest extends PHPUnit_Framework_TestCase
{
    public function testRenderSimpleView()
    {
        $template = realpath('view/template-simple.phtml');
        $expectedContent = file_get_contents($template);

        $renderer = new Renderer();
        $renderedContent = $renderer->render($template);

        $this->assertEquals('Zend\Stdlib\Response', get_class($renderedContent->getResponse()));
        $this->assertEquals($expectedContent, $renderedContent->getResponse()->getContent());
    }

    public function testRenderMediumView()
    {
        $template = realpath('view/template-medium.phtml');
        $expectedContent = 'hello world';

        $renderer = new Renderer();
        $renderedContent = $renderer->render($template, array('name' => 'world'));

        $this->assertEquals('Zend\Stdlib\Response', get_class($renderedContent->getResponse()));
        $this->assertEquals($expectedContent, $renderedContent->getResponse()->getContent());
    }

    public function testRenderHardView()
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

}
