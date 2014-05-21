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
 * sfServiceContainerBuilder is a DI container that provides an interface to build the services.
 *
 * @package    symfony
 * @subpackage dependency_injection
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfServiceContainerBuilder.php 269 2009-03-26 20:39:16Z fabien $
 */
class ServiceContainer_Builder extends sfServiceContainerBuilder
{
  /**
   * Creates a service for a service definition.
   *
   * @param  sfServiceDefinition $definition A service definition instance
   *
   * @return object              The service described by the service definition
   */
  protected function createService(sfServiceDefinition $definition)
  {
    if (null !== $definition->getFile())
    {
      require_once $this->resolveValue($definition->getFile());
    }

    $arguments = $this->resolveServices($this->resolveValue($definition->getArguments()));

    if (null !== $definition->getConstructor())
    {
      $service = call_user_func_array(array($this->resolveValue($definition->getClass()), $definition->getConstructor()), $arguments);
    }
    elseif (null !== $factory = $definition->getFactory())
    {
      $service = $this->hasService((string) $factory['service']) ? $this->getService((string) $factory['service']) : $factory['service'];

      if (is_string($service) && is_null($factory['method'])) {
        $callable = $service;
      } else {
        $callable = array(
          $service,
          $factory['method']
        );
      }
      
      if (!is_callable($callable))
      {
        throw new InvalidArgumentException(sprintf('The configure callable for class "%s" is not a callable.', is_object($service) ? get_class($service) : print_r($callable, true)));
      }

      $service = call_user_func_array($callable, $factory['arguments']);
    }
    else
    {
      $r = new ReflectionClass($this->resolveValue($definition->getClass()));
      $service = null === $r->getConstructor() ? $r->newInstance() : $r->newInstanceArgs($arguments);
    }

    foreach ($definition->getMethodCalls() as $call)
    {
      call_user_func_array(array($service, $call[0]), $this->resolveServices($this->resolveValue($call[1])));
    }

    return $service;
  }
}
