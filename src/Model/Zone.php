<?php

namespace LinkORB\Component\XDns\Model;

use RuntimeException;

class Zone
{
    protected $name;
    protected $records = [];
    protected $provider = null;
    protected $targets = [];

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function addTarget(string $providerName)
    {
        $this->targets[] = $providerName;
    }

    public function getTargets(): array
    {
        return $this->targets;
    }

    public function getFqzn(): string
    {
        $providerName = 'null';
        if ($this->provider) {
            $providerName = $this->provider->getName();
        }
        return $this->name . '@' . $providerName;
    }

    public function getProvider()
    {
        return $this->provider;
    }

    public function setProvider($provider)
    {
        $this->provider = $provider;
    }

    public function getRecords()
    {
        return $this->records;
    }

    public function setRecord(Record $record)
    {
        $key = $record->getKey();
        $this->records[$key] = $record;
    }

    public function hasKey(string $key): bool
    {
        return isset($this->records[$key]);
    }

    public function removeRecord(Record $record)
    {
        $key = $record->getKey();
        if (!$this->hasRecord($key)) {
            throw new RuntimeException("Record not found: " . $record->getKey());
        }
        unset($this->records[$key]);
    }

    public function getRecord($key)
    {
        if (!$this->hasRecord($key)) {
            throw new RuntimeException("Record not found: " . $key);
        }
        return $this->records[$key];
    }

    public function hasRecord($key)
    {
        return isset($this->records[$key]);
    }
}
