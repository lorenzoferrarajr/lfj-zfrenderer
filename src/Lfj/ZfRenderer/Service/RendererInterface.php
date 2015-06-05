<?php

namespace Lfj\ZfRenderer\Service;

use Zend\View\Model\ViewModel;
use Zend\View\Resolver\ResolverInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\View\HelperPluginManager;

interface RendererInterface
{
    /**
     * @param $template
     * @param array $variables
     * @param array $resolvers collection of Zend\View\Resolver\ResolverInterface objects
     * @return ViewModel
     */
    public function render($template, array $variables = array(), array $resolvers = null);
    /**
     * @return EventManagerInterface
     */

    /**
     * @param EventManagerInterface $events
     */
    public function setEventManager(EventManagerInterface $events);

    /**
     * @return EventManagerInterface
     */
    public function getEventManager();

    /**
     * @param HelperPluginManager $helperPluginManager
     */
    public function setHelperPluginManager(HelperPluginManager $helperPluginManager);

    /**
     * @return HelperPluginManager
     */
    public function getHelperPluginManager();
}
