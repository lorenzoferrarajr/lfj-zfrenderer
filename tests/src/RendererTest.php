<?php

use Lfj\ZfRenderer\Renderer;
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
        $rendererWithResolvers = $renderer->withResolvers(array($templatePathStack));

        $renderedContent = $rendererWithResolvers->render($template, array('name' => 'world'));

        $this->assertEquals('Zend\Stdlib\Response', get_class($renderedContent->getResponse()));
        $this->assertEquals($expectedContent, $renderedContent->getResponse()->getContent());
    }

    public function testRenderingAnotherTemplateInsideATemplate()
    {
        $expectedContent = 'hello world';

        $content = null;
        $template = realpath('view/template-with-renderer.phtml');

        $templatePathStack = new Resolver\TemplatePathStack();
        $templatePathStack->addPath(realpath('view'));

        $renderer = new Renderer();
        $rendererWithTemplatePathStack = $renderer->withResolvers(array($templatePathStack));

        $viewVariables = array(
            'name' => 'world',
            'renderer' => $rendererWithTemplatePathStack,
        );

        $renderedContent = $rendererWithTemplatePathStack->render($template, $viewVariables);

        $this->assertEquals('Zend\Stdlib\Response', get_class($renderedContent->getResponse()));
        $this->assertEquals($expectedContent, $renderedContent->getResponse()->getContent());
    }
}
