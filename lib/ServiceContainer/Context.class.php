<?php

class ServiceContainer_Context extends sfContext implements ServiceContainer_ContainerAwareInterface
{
    private $container;

    public function setContainer(sfServiceContainer $container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function loadContainer()
    {
        if (is_null($this->container)) {
            if (sfConfig::get('app_services_enable_cache') && file_exists($cacheDir = sprintf('%s/container.php', sfConfig::get('sf_app_cache_dir')))) {
                include_once ($cacheDir);
                $sc = new ProjectServiceContainer();
                $this->setContainer($sc);
            } else {
                $sc = new ServiceContainer_Builder();

                // notify the event dispatcher we are about to load the container
                $this->dispatcher->notify(new sfEvent($this, 'container.pre_load', array('container' => $sc)));

                if (sfConfig::get('app_services_set_symfony_config')) {
                    // Set core symfony parameters
                    foreach (sfConfig::getAll() as $name => $value) {
                        $sc->setParameter($name, $value);
                    }
                }

                $loader = new ServiceContainer_LoaderFileYaml($sc);

                // this can be an array of cascading service configs
                // TODO: cache this as PHP (sfServicesConfigHandler)
                $configPaths = sfConfig::get('app_services_config', 'config/services.yml');

                $loader->load($configPaths);

                $this->setContainer($sc);

                if (sfConfig::get('app_services_enable_cache')) {
                    $dumper = new ServiceContainer_DumperPhp($sc);
                    file_put_contents($cacheDir, $dumper->dump(array('base_class' => 'sfServiceContainerBuilder')));
                }

                // notify the event dispatcher we have finished loading the container
                $this->dispatcher->notify(new sfEvent($this, 'container.post_load', array('container' => $sc)));
            }
        }
    }
}
