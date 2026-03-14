# Network Address Functions

This document covers PostgreSQL network address functions available in this library, supporting the `inet` and `cidr` data types.

> 📖 **See also**: [Available Types](AVAILABLE-TYPES.md) for the `inet`, `cidr`, `macaddr`, and `macaddr8` DBAL types

## Network Address Functions

These functions operate on PostgreSQL `inet` and `cidr` values. They are available without any database extension.

| PostgreSQL function | Register for DQL as | Implemented by |
|---|---|---|
| abbrev(inet/cidr) | ABBREV | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Abbrev` |
| broadcast(inet) | BROADCAST | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Broadcast` |
| family(inet) | FAMILY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Family` |
| host(inet) | HOST | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Host` |
| hostmask(inet) | HOSTMASK | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Hostmask` |
| inet_merge(inet, inet) | INET_MERGE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\InetMerge` |
| inet_same_family(inet, inet) | INET_SAME_FAMILY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\InetSameFamily` |
| masklen(inet) | MASKLEN | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Masklen` |
| netmask(inet) | NETMASK | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Netmask` |
| network(inet) | NETWORK | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Network` |
| set_masklen(inet, int) | SET_MASKLEN | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\SetMasklen` |

## Registration

Register the functions with Doctrine ORM configuration:

```php
$configuration->addCustomStringFunction('ABBREV', \MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Abbrev::class);
$configuration->addCustomStringFunction('BROADCAST', \MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Broadcast::class);
$configuration->addCustomStringFunction('FAMILY', \MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Family::class);
$configuration->addCustomStringFunction('HOST', \MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Host::class);
$configuration->addCustomStringFunction('HOSTMASK', \MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Hostmask::class);
$configuration->addCustomStringFunction('INET_MERGE', \MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\InetMerge::class);
$configuration->addCustomStringFunction('INET_SAME_FAMILY', \MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\InetSameFamily::class);
$configuration->addCustomStringFunction('MASKLEN', \MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Masklen::class);
$configuration->addCustomStringFunction('NETMASK', \MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Netmask::class);
$configuration->addCustomStringFunction('NETWORK', \MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Network::class);
$configuration->addCustomStringFunction('SET_MASKLEN', \MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\SetMasklen::class);
```

## Usage Examples

```php
// Extract the host part of an IP address
$dql = 'SELECT HOST(e.ipAddress) FROM App\Entity\Server e';

// Get the broadcast address
$dql = 'SELECT BROADCAST(e.ipAddress) FROM App\Entity\Server e WHERE e.id = :id';

// Get the address family (4 for IPv4, 6 for IPv6)
$dql = 'SELECT FAMILY(e.ipAddress) FROM App\Entity\Server e';

// Get the netmask length in bits
$dql = 'SELECT MASKLEN(e.ipAddress) FROM App\Entity\Server e';

// Get the network address (host bits zeroed)
$dql = 'SELECT NETWORK(e.ipAddress) FROM App\Entity\Server e';

// Modify netmask length
$dql = 'SELECT SET_MASKLEN(e.ipAddress, 16) FROM App\Entity\Server e';

// Find servers in the same address family
$dql = 'SELECT e FROM App\Entity\Server e WHERE INET_SAME_FAMILY(e.ip1, e.ip2) = TRUE';

// Find the smallest network containing two addresses
$dql = 'SELECT INET_MERGE(e.ip1, e.ip2) FROM App\Entity\Server e';
```
