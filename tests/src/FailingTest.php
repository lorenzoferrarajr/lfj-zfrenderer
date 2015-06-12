<?php

use Lfj\ZfRenderer\Service\Renderer;
use Zend\EventManager\EventManager;
use Zend\View\HelperPluginManager;
use Zend\View\Resolver;

class FailingTest extends PHPUnit_Framework_TestCase
{
    public function testFailingBecauseEventManagerInjectedInView()
    {
        $content = null;
        $template = realpath('view/template-with-renderer.phtml');

        $templatePathStack = new Resolver\TemplatePathStack();
        $templatePathStack->addPath(realpath('view'));

        $renderer = new Renderer();

        $viewVariables = array(
            'renderer' => $renderer
        );

        $view = $renderer->render($template, $viewVariables, array($templatePathStack));

        $content = $view->getResponse()->getContent();
        echo $content;
    }
}