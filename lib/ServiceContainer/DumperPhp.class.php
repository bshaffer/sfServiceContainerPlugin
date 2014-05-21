<?php

class ServiceContainer_DumperPhp extends sfServiceContainerDumperPhp
{
  protected function addService($id, $definition)
  {
    $name = sfServiceContainer::camelize($id);

    $code = <<<EOF

  protected function get{$name}Service()
  {

EOF;

    $code .=
      $this->addServiceInclude($id, $definition).
      $this->addServiceShared($id, $definition).
      $this->addServiceInstance($id, $definition).
      $this->addServiceMethodCalls($id, $definition).
      $this->addServiceConfigurator($id, $definition).
      $this->addServiceFactory($id, $definition).
      $this->addServiceReturn($id, $definition)
    ;

    return $code;
  }

  protected function addServiceInstance($id, $definition)
  {
    if (!$definition->getClass()) {
        return '';
    }

    return parent::addServiceInstance($id, $definition);
  }

  protected function addServiceFactory($id, $definition)
  {
    $arguments = array();
    foreach ($definition->getArguments() as $value)
    {
      $arguments[] = $this->dumpValue($value);
    }
    $arguments = implode(', ', $arguments);
    if ($factory = $definition->getFactory()) {

      if ($factory['service'] instanceof sfServiceReference) {
        $name = sfServiceContainer::camelize((string) $factory['service']);

        $code = <<<EOF
    \$factory = \$this->get{$name}Service();

    \$instance = \$factory->{$factory['method']}({$arguments});

EOF;
      } elseif (is_null($factory['method'])) {
        $code = <<<EOF
    \$instance = {$factory['service']}({$arguments});

EOF;
      } else {
        $code = <<<EOF
    \$instance = {$factory['service']}::{$factory['method']}({$arguments});

EOF;
      }

      return $code;
    }

    return '';
  }
}
