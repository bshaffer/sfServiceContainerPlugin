sfServiceContainerPlugin
========================

A Symfony 1.4 Plugin for using the Service Container

This plugin is compatible for php 5.2 and above

Initializing the Container
--------------------------

First, use the context ServiceContainer_Context in your frontend controllers

```php
# web/index.php

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');
$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', $env, $debug);
sfContext::createInstance($configuration, null, 'ServiceContainer_Context')->dispatch();

```

> Notice the third argument to `sfContext::createInstance` is the new ServiceContainer context class

Second, set the container file in your app.yml

```yaml
# config/app.yml

all:
    service_config:
        - %sf_config_dir%/services/services.yml

test:
    services_config:
        - %sf_config_dir%/services/services.yml
        - %sf_config_dir%/services/test.yml

```

Using Container Events
----------------------

Optionally, you can hook into the `container.pre_load` and `container.post_load` events in order to specify
the container files and manipulate the container respectively:

```php
# config/ProjectConfiguration.class.php

class ProjectConfiguration extends sfProjectConfiguration
{
    public function setup()
    {
        // listen to core events here
        $this->dispatcher->connect('container.pre_load', array($this, 'observeContainerPreLoad'));
        $this->dispatcher->connect('container.post_load', array($this, 'observeContainerPostLoad'));
    }

    /** hook into the container before its been built */
    public function observeContainerPreLoad(sfEvent $event)
    {
        $services_config = array();
        $sf_env = sfConfig::get('sf_environment');
        $services_config[] = sprintf('%s/services/%s.yml', sfConfig::get('sf_config_dir'), $sf_env))) {

        // ... add other configs if necessary

        // set the service container config file to load
        sfConfig::set('app_services_config', $services_config);
    }

    /** hook into the container after its been built */
    public function observeContainerPostLoad(sfEvent $event)
    {
        // set container from environment variables
        foreach ($_SERVER as $key => $value) {
            // only set variables prefixed with "SF_"
            if ('SF_' === substr($key, 0, 3)) {
                $event['container']->setParameter(strtolower($key), $value);
            }
        }
    }
}

```

Container Features
------------------

This supports all the standard features in the [Symfony Service Container](http://symfony.com/doc/current/book/service_container.html).

Currently, it expects YAML files, but this can be changed easily by submitting an [Issue or Pull Request](https://github.com/bshaffer/sfServiceContainerPlugin/issues)
