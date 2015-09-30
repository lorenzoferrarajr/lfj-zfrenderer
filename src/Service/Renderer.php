<?php

namespace Lfj\ZfRenderer\Service;

use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Zend\Stdlib\Response;
use Zend\View\HelperPluginManager;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver;
use Zend\View\Strategy\PhpRendererStrategy;
use Zend\View\View;

final class Renderer implements RendererInterface
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

    public function __construct(
        HelperPluginManager $helperPluginManager = null,
        EventManager $events = null,
        array $resolvers = array()
    )
    {
        $this->events = null !== $events ? $events : new EventManager();
        $this->helperPluginManager = null !== $helperPluginManager ? $helperPluginManager : new HelperPluginManager();
        $this->resolvers = $resolvers;
    }

    /**
     * Render
     *
     * @inheritdoc
     */
    public function render($template, array $variables = array())
    {
        $view = $this->createView('view', $template);

        $model = new ViewModel();
        $model->setVariables($variables);
        $model->setTemplate('view');

        $view->render($model);

        return $view;
    }

    /**
     * @param $name
     * @param $path
     * @return View
     */
    private function createView($name, $path)
    {
        $ar = new Resolver\AggregateResolver();

        $helper = $this->helperPluginManager;

        $map = new Resolver\TemplateMapResolver(array(
            $name => $path
        ));

        $ar->attach($map);

        foreach ($this->resolvers as $r) {
            $ar->attach($r);
        }

        $renderer = new PhpRenderer();
        $renderer->setHelperPluginManager($helper);
        $renderer->setResolver($ar);

        $strategy = new PhpRendererStrategy($renderer);

        $view = new View();
        $view->setEventManager(clone $this->events);
        $view->setResponse(new Response());
        $view->getEventManager()->attach($strategy);

        return $view;
    }

    /**
     * @inheritdoc
     */
    public function withEventManager(EventManagerInterface $events)
    {
        $clone = clone $this;

        $events->setIdentifiers(array(
            __CLASS__,
            get_class($this),
        ));

        $clone->events = $events;

        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function withHelperPluginManager(HelperPluginManager $helperPluginManager)
    {
        $clone = clone $this;
        $clone->helperPluginManager = $helperPluginManager;
        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function withResolvers(array $resolvers)
    {
        $clone = clone $this;
        $clone->resolvers = $resolvers;
        return $clone;
    }

    public function __clone()
    {
        $this->events = clone $this->events;

        $resolvers = array();
        foreach ($this->resolvers as $r) {
            $resolvers[] = clone ($r);
        }

        $this->resolvers = $resolvers;
    }
}
