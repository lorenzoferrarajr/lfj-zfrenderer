<?php

namespace Lfj\ZfRenderer;

use Zend\View\Model\ViewModel;
use Zend\View\Resolver\ResolverInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\View\HelperPluginManager;

interface RendererInterface
{
    /**
     * @param $template
     * @param array $variables
     * @return ViewModel
     */
    public function render($template, array $variables = array());
    /**
     * @return EventManagerInterface
     */

    /**
     * @param EventManagerInterface $events
     * @return Renderer
     */
    public function withEventManager(EventManagerInterface $events);

    /**
     * @param HelperPluginManager $helpers
     * @return Renderer
     */
    public function withHelperPluginManager(HelperPluginManager $helpers);

    /**
     * @param array $resolvers collection of Zend\View\Resolver\ResolverInterface objects
     * @return Renderer
     */
    public function withResolvers(array $resolvers);
}
