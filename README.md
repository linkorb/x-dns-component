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

## CLI Commands

### x-dns:provider:list

Lists all configured DNS providers from the database. Shows successfully loaded providers and displays configuration errors for any providers that failed to load (missing credentials, invalid adapters, etc.).

```bash
bin/console x-dns:provider:list
```

### x-dns:zone:list

Lists all zones available at a specific DNS provider. Use this to discover which domains are managed by a provider.

```bash
bin/console x-dns:zone:list <providerName>
```

- `providerName` - Name of the provider from database

### x-dns:zone:show

Displays complete zone information including all DNS records (A, CNAME, MX, TXT, etc.) with their values and TTL settings.

```bash
bin/console x-dns:zone:show <fqzn>
```

- `fqzn` - Fully Qualified Zone Name in format `zonename@provider` (e.g., `example.com@production-dns`)

### x-dns:zone:diff

Compares DNS records between two zones to identify differences. Useful for verifying zone synchronization or reviewing changes before pushing updates.

```bash
bin/console x-dns:zone:diff <fqznA> [target]
```

- `fqznA` - Source zone in format `zonename@provider`
- `target` - (Optional) Target zone FQZN or provider name. Omit to compare against all configured targets.

```bash
# Compare specific zones
bin/console x-dns:zone:diff example.com@production-dns example.com@backup-dns

# Compare same zone across providers
bin/console x-dns:zone:diff example.com@production-dns backup-dns
```

### x-dns:zone:push

Pushes zone configuration and DNS records from one source to target provider(s). Use this to synchronize zones across providers or deploy changes.

```bash
bin/console x-dns:zone:push <fqzn> [providerName]
```

- `fqzn` - Source zone to push in format `zonename@provider`
- `providerName` - (Optional) Target provider name. Omit to push to all configured targets.

```bash
# Push to specific provider
bin/console x-dns:zone:push example.com@file-backup production-dns

# Push to all configured targets
bin/console x-dns:zone:push example.com@file-backup
```

## Architecture

XDnsService manages multiple providers, each with their own adapter for interacting with DNS services. The component provides a unified interface for managing DNS zones and records across different providers.

## License

Proprietary - LinkORB Engineering

## Support

For issues or questions, contact LinkORB Engineering at engineering@linkorb.com
