<?php

namespace LinkORB\Component\XDns\Model;

class Provider
{
    private $name;
    private $adapter;

    public function __construct(string $name, $adapter)
    {
        $this->name = $name;
        $this->adapter = $adapter;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getAdapter()
    {
        return $this->adapter;
    }


    public static function fromConfig(string $name, array $config): self
    {
        $adapterName = $config['adapter'];
        $adapterClass = 'LinkORB\\Component\\XDns\\Adapter\\' . $adapterName . 'Adapter';

        $adapter = $adapterClass::fromConfig($config);
        $provider = new self($name, $adapter);
        return $provider;
    }

}
