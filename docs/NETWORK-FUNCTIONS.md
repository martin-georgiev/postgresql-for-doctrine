# Network Address Functions

> 📖 **See also**: [Available Types](AVAILABLE-TYPES.md) for the `inet`, `cidr`, `macaddr`, and `macaddr8` DBAL types

| PostgreSQL functions | Register for DQL as | Implemented by |
|---|---|---|
| abbrev | ABBREV | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Abbrev` |
| broadcast | BROADCAST | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Broadcast` |
| family | FAMILY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Family` |
| host | HOST | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Host` |
| hostmask | HOSTMASK | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Hostmask` |
| inet_merge | INET_MERGE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\InetMerge` |
| inet_same_family | INET_SAME_FAMILY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\InetSameFamily` |
| masklen | MASKLEN | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Masklen` |
| netmask | NETMASK | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Netmask` |
| network | NETWORK | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Network` |
| set_masklen | SET_MASKLEN | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\SetMasklen` |

## Usage Examples

```sql
-- Extract the host address
SELECT HOST(e.ipAddress) FROM App\Entity\Server e

-- Get the broadcast address for a network
SELECT BROADCAST(e.ipAddress) FROM App\Entity\Server e WHERE e.id = :id

-- Address family: 4 for IPv4, 6 for IPv6
SELECT FAMILY(e.ipAddress) FROM App\Entity\Server e

-- Subnet mask length in bits
SELECT MASKLEN(e.ipAddress) FROM App\Entity\Server e

-- Network address with host bits zeroed
SELECT NETWORK(e.ipAddress) FROM App\Entity\Server e

-- Change the subnet mask length
SELECT SET_MASKLEN(e.ipAddress, 16) FROM App\Entity\Server e

-- Filter servers in the same address family
SELECT e FROM App\Entity\Server e WHERE INET_SAME_FAMILY(e.ip1, e.ip2) = TRUE

-- Smallest network containing two addresses
SELECT INET_MERGE(e.ip1, e.ip2) FROM App\Entity\Server e
```
