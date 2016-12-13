<?php

namespace Lfj\ZfRenderer\Service;

use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Stdlib\Response;
use Zend\View\HelperPluginManager;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver;
use Zend\View\Strategy\PhpRendererStrategy;
use Zend\View\View;

final class Renderer implements RendererInterface, EventManagerAwareInterface
{
    /**
     * @var HelperPluginManager
     */
    private $helperPluginManager;

    /**
     * @var EventManagerInterface
     */
    private $events;

    /**
     * @var array
     */
    private $resolvers;

    public function __construct()
    {
        $this->helperPluginManager = new HelperPluginManager();
        $this->events = new EventManager();
        $this->resolvers = array();
    }

    /**
     * Render
     *
     * @inheritdoc
     */
    public function render($template, array $variables = array(), array $resolvers = null)
    {
        if (null === $resolvers) {
            $resolvers = array();
        }

        $view = $this->createView('view', $template, $resolvers);

        $model = new ViewModel();
        $model->setVariables($variables);
        $model->setTemplate('view');

        $view->render($model);

        return $view;
    }

    /**
     * @param $name
     * @param $path
     * @param $resolvers
     * @return View
     */
    private function createView($name, $path, $resolvers)
    {
        $ar = new Resolver\AggregateResolver();

        $helper = $this->helperPluginManager;

        $map = new Resolver\TemplateMapResolver(array(
            $name => $path
        ));

        $ar->attach($map);

        foreach ($resolvers as $r) {
            $ar->attach($r);
        }

        $renderer = new PhpRenderer();
        $renderer->setHelperPluginManager($helper);
        $renderer->setResolver($ar);

        $strategy = new PhpRendererStrategy($renderer);

        $view = new View();
//        $view->setEventManager($this->events);
        $view->setResponse(new Response());
        $view->getEventManager()->attach($strategy);

        return $view;
    }

    /**
     * @inheritdoc
     */
    public function setEventManager(EventManagerInterface $events)
    {
        $events->setIdentifiers(array(
            __CLASS__,
            get_class($this),
        ));

        $this->events = $events;
    }

    /**
     * @inheritdoc
     */
    public function getEventManager()
    {
        return $this->events;
    }

    /**
     * @inheritdoc
     */
    public function setHelperPluginManager(HelperPluginManager $helperPluginManager)
    {
        $this->helperPluginManager = $helperPluginManager;
    }

    /**
     * @inheritdoc
     */
    public function getHelperPluginManager()
    {
        return $this->helperPluginManager;
    }
}
