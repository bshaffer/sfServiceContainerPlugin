<?php

class sfServiceContainerPlugin_FactoryTest extends PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $sc = new ServiceContainer_Builder();
        $yaml = <<<EOF
services:
  my_factory:
    class: MyFactory

  service_from_factory:
    factory: [@my_factory, createService, [test-service, 'This service was created from a factory']]

EOF;
        file_put_contents($file = tempnam(sys_get_temp_dir(), 'sc.yaml'), $yaml);
        $loader = new ServiceContainer_LoaderFileYaml($sc);

        $loader->load($file);
        $service = $sc->getService('service_from_factory');

        $this->assertEquals($service->name, 'test-service');
        $this->assertEquals($service->description, 'This service was created from a factory');
    }
}

class MyFactory
{
    public function createService($name, $description)
    {
        $service = new StdClass();
        $service->name = $name;
        $service->description = $description;

        return $service;
    }
}