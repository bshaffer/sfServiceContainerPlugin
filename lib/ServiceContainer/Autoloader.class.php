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
 * sfServiceContainerAutoloader is an autoloader for the service container classes.
 *
 * @package    symfony
 * @subpackage dependency_injection
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
class ServiceContainer_Autoloader
{
  /**
   * Registers sfServiceContainerAutoloader as an SPL autoloader.
   */
  static public function register()
  {
    ini_set('unserialize_callback_func', 'spl_autoload_call');
    spl_autoload_register(array(new self, 'autoload'));
  }

  /**
   * Handles autoloading of classes.
   *
   * @param  string  $class  A class name.
   *
   * @return boolean Returns true if the class has been loaded
   */
  public function autoload($class)
  {
    if (0 === strpos($class, 'sfService'))
    {
      require dirname(__FILE__).'/../vendor/dependency-injection/lib/'.$class.'.php';

      return true;
    }

    if (0 === strpos($class, 'ServiceContainer_'))
    {
      require dirname(__FILE__).'/../'.str_replace('_', '/', $class).'.class.php';

      return true;
    }


    return false;
  }
}
