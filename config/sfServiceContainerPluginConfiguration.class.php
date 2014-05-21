<?php

/**
*
*/
class sfServiceContainerPluginConfiguration extends sfPluginConfiguration
{
    public function configure()
    {
        // add database config handler to autoloader
        // required for custom database config handler (we should add custom autoloader for these classes)
        require_once dirname(__FILE__).'/../lib/ServiceContainer/DatabaseConfigHandler.class.php';
        require_once dirname(__FILE__).'/../lib/ServiceContainer/FactoryConfigHandler.class.php';
    }

    public function initialize()
    {
        // make sure the service container gets loaded first
        if (count($listeners = $this->dispatcher->getListeners('context.load_factories')) > 0) {
            $this->dispatcher->connect('context.load_factories', array($this, 'listenContextLoadFactories'));
            foreach ($listeners as $listener) {
                $this->dispatcher->disconnect('context.load_factories', $listener);
                $this->dispatcher->connect('context.load_factories', $listener);
            }
        } else {
            $this->dispatcher->connect('context.load_factories', array($this, 'listenContextLoadFactories'));
        }
    }

    public function listenContextLoadFactories(sfEvent $event)
    {
        $context = $event->getSubject();

        if ($context instanceof ServiceContainer_ContainerAwareInterface) {
            $context->loadContainer();

            // Set basic services
            $container = $context->getContainer();
            $container->setService('sf_event_dispatcher', $context->getEventDispatcher());
            $container->setService('sf_request', $context->getRequest());
            $container->setService('sf_response', $context->getResponse());
            $container->setService('sf_session', $context->getUser());
            $container->setService('sf_logger', $context->getLogger());
            $container->setService('sf_container', $container);
        }
    }
}
