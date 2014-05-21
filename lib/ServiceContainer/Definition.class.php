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
 * sfServiceDefinitionExtended represents a service definition.
 *
 * @package    symfony
 * @subpackage dependency_injection
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfServiceDefinition.php 269 2009-03-26 20:39:16Z fabien $
 */
class ServiceContainer_Definition extends sfServiceDefinition
{
  protected
    $factory      = null;

  /**
   * Sets a factory to call after the service is fully initialized.
   *
   * @param  mixed               $callable A PHP callable
   *
   * @return sfServiceDefinition The current instance
   */
  public function setFactory($service, $method, $arguments = array())
  {
    $this->factory = compact('service', 'method', 'arguments');

    return $this;
  }

  /**
   * Gets the factory to call after the service is fully initialized.
   *
   * @return mixed The PHP callable to call
   */
  public function getFactory()
  {
    return $this->factory;
  }
}
