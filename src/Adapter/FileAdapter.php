<?php

namespace LinkORB\Component\XDns\Adapter;

use LinkORB\Component\XDns\Model\Zone;
use LinkORB\Component\XDns\Model\Record;
use Symfony\Component\Yaml\Yaml;

class FileAdapter implements XDnsAdapterInterface
{
    protected $path;

    protected $zones = [];

    public static function fromConfig(array $config): self
    {
        $adapter = new self($config['path']);
        return $adapter;
    }

    public function __construct(string $path)
    {
        $this->path = rtrim($path, '/ ');
        if (!file_exists($path)) {
            throw new \RuntimeException('File not found: ' . $path);
        }
        // find all yaml and yml files in the path
        $files = glob($this->path . '/*.yml');
        $files = array_merge($files, glob($this->path . '/*.yaml'));
        foreach ($files as $file) {
            $yaml = trim(file_get_contents($file));
            if ($yaml) {
                $config = Yaml::parse($yaml);
                $zone = new Zone();
                if (!isset($config['name'])) {
                    throw new \RuntimeException('Zone name not found in file: ' . $file);
                }
                $zone->setName($config['name']);

                // optionally include multiple other yaml files for more records
                foreach ($config['includes'] ?? [] as $includeFilename) {
                    $includeFilename = $this->path . '/' . $includeFilename;
                    if (!file_exists($includeFilename)) {
                        throw new \RuntimeException('Include file not found: ' . $includeFilename);
                    }
                    $includeYaml = file_get_contents($includeFilename);
                    $includeConfig = Yaml::parse($includeYaml);
                    $config = array_merge_recursive($config, $includeConfig);
                }

                foreach ($config['targets'] ?? [] as $targetName) {
                    $zone->addTarget($targetName);
                }
                if (!is_array($config['records'])) {
                    throw new \RuntimeException('Zone without records: ' . $file);
                }

                foreach ($config['records'] as $recordName => $recordConfig) {
                    $record = new Record();
                    $record->setName(trim($recordConfig['name']));
                    $record->setType(trim(strtoupper($recordConfig['type'])));
                    $record->setValue(trim($recordConfig['value']));
                    $record->setTtl(trim($recordConfig['ttl']));
                    $zone->setRecord($record);
                }
                $this->zones[$zone->getName()] = $zone;
            }
        }
    }

    public function getZoneNames(): array
    {
        $zoneNames = [];
        foreach ($this->zones as $zone) {
            $zoneNames[] = $zone->getName();
        }
        return $zoneNames;
    }

    public function hasZone(string $zoneName): bool
    {
        return isset($this->zones[$zoneName]);
    }

    public function getZone(string $zoneName): Zone
    {
        if (!$this->hasZone($zoneName)) {
            throw new \RuntimeException('No such zone at provider: ' . $zoneName);
        }
        return $this->zones[$zoneName];
    }

}
