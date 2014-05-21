<?php

/*
 * This file is part of the symfony framework.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * sfServiceContainerLoaderFileXml loads YAML files service definitions.
 *
 * The YAML format does not support anonymous services (cf. the XML loader).
 *
 * @package    symfony
 * @subpackage dependency_injection
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfServiceContainerLoaderFileYaml.php 269 2009-03-26 20:39:16Z fabien $
 */
class ServiceContainer_LoaderFileYaml extends sfServiceContainerLoaderFileYaml
{
  protected function parseDefinition($service, $file)
  {
    $definition = $this->parentParseDefinition($service, $file, 'ServiceContainer_Definition');

    if (is_string($definition))
    {
        return $definition;
    }

    // adds support for "factory" option
    if (isset($service['factory']))
    {
        $factory = array(
            is_array($service['factory']) ? $service['factory'][0] : $service['factory'],
            is_array($service['factory']) && isset($service['factory'][1]) ? $service['factory'][1] : null,
            isset($service['arguments']) ? $service['arguments'] : array(),
        );

        $definition->setFactory($this->resolveServices($factory[0]), $factory[1], $factory[2]);
    }

    return $definition;
  }

  // we have to copy this method in its entirety from the parent so we can pass a new class for sfServiceDefinition
  protected function parentParseDefinition($service, $file, $definitionClass = 'sfServiceDefinition')
  {
    if (is_string($service) && 0 === strpos($service, '@'))
    {
      return substr($service, 1);
    }

    if (isset($service['factory']) && !isset($service['class']))
    {
      $service['class'] = ''; // we do not need to declare the class
    }

    $definition = new $definitionClass($service['class']);

    if (isset($service['shared']))
    {
      $definition->setShared($service['shared']);
    }

    if (isset($service['constructor']))
    {
      $definition->setConstructor($service['constructor']);
    }

    if (isset($service['file']))
    {
      $definition->setFile($service['file']);
    }

    if (isset($service['arguments']))
    {
      $definition->setArguments($this->resolveServices($service['arguments']));
    }

    if (isset($service['configurator']))
    {
      if (is_string($service['configurator']))
      {
        $definition->setConfigurator($service['configurator']);
      }
      else
      {
        $definition->setConfigurator(array($this->resolveServices($service['configurator'][0]), $service['configurator'][1]));
      }
    }

    if (isset($service['calls']))
    {
      foreach ($service['calls'] as $call)
      {
        $definition->addMethodCall($call[0], $this->resolveServices($call[1]));
      }
    }

    return $definition;
  }
}
