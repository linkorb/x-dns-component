> [!WARNING]
> This is a read-only repository used to release the subtree. Any issues and pull requests should be forwarded to the
> upstream Nebula repository.

# x-dns-component

Cross-provider DNS configuration management component for LinkORB applications.

## Features

* Manage DNS zones and records programmatically
* Provider abstraction supporting multiple DNS services
* **Array-based configuration** for flexible integration
* **Configuration error tracking** for graceful degradation
* File-based YAML storage adapter
* TransIP API integration
* Scaleway DNS support
* Extensible adapter pattern for additional providers
* Framework-agnostic design

## Installation

```bash
composer require linkorb/x-dns-component
```

## Usage

```php
use LinkORB\Component\XDns\XDnsService;

// Load from YAML file
$service = XDnsService::fromConfigFilename('config/dns.yaml');

// Or create from array configuration
$config = [
    'providers' => [
        'my-provider' => [
            'adapter' => 'TransIP',
            'username' => 'your-username',
            'key' => 'your-private-key'
        ]
    ]
];
$service = XDnsService::fromConfigArray($config);

// Manage zones and records
$zone = $service->getZoneByFqzn('example.com@my-provider');
$adapter = $provider->getAdapter();
$adapter->pushZone($zone);
```

## Supported Adapters

- **File** - YAML file storage
- **TransIP** - TransIP DNS API
- **Scaleway** - Scaleway DNS API

## Architecture

XDnsService manages multiple providers, each with their own adapter for interacting with DNS services. The component provides a unified interface for managing DNS zones and records across different providers.

## License

Proprietary - LinkORB Engineering

## Support

For issues or questions, contact LinkORB Engineering at engineering@linkorb.com
