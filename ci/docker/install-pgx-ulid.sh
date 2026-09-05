#!/bin/sh
set -u

# Installs pgx_ulid (https://github.com/pksunkara/pgx_ulid) into an
# Alpine-based PostGIS container.
#
# pgx_ulid publishes only glibc .deb packages (no musl/apk builds), so this
# script extracts the matching .deb and relies on gcompat for glibc
# compatibility. The install is best-effort and never fails the container
# startup: when the extension cannot be installed or loaded, the ulid
# integration tests skip themselves.

PGX_ULID_VERSION="0.2.3"

install_pgx_ulid() {
    apk add --no-cache binutils gcompat tar xz zstd > /dev/null 2>&1 || return 1

    pg_major="$(pg_config --version | sed -E 's/^PostgreSQL ([0-9]+).*$/\1/')"
    case "$(uname -m)" in
        x86_64) arch="amd64" ;;
        aarch64) arch="arm64" ;;
        *)
            echo "pgx_ulid: unsupported architecture $(uname -m)" >&2
            return 1
            ;;
    esac

    workdir="$(mktemp -d)"
    deb="pgx_ulid-v${PGX_ULID_VERSION}-pg${pg_major}-${arch}-linux-gnu.deb"
    url="https://github.com/pksunkara/pgx_ulid/releases/download/v${PGX_ULID_VERSION}/${deb}"

    wget -q -O "${workdir}/${deb}" "${url}" || return 1
    (cd "${workdir}" && ar x "${deb}" && tar -xf data.tar.*) || return 1

    find "${workdir}" -name '*.so' -exec cp {} "$(pg_config --pkglibdir)/" \; || return 1
    find "${workdir}" -path '*/extension/*' \( -name '*.control' -o -name '*.sql' \) -exec cp {} "$(pg_config --sharedir)/extension/" \; || return 1

    rm -rf "${workdir}"

    return 0
}

if install_pgx_ulid; then
    if ldd "$(pg_config --pkglibdir)/pgx_ulid.so" 2>&1 | grep -q "symbol not found"; then
        echo "pgx_ulid: library has unresolved glibc symbols on this platform; CREATE EXTENSION will fail and ulid integration tests will be skipped" >&2
    else
        echo "pgx_ulid installed successfully"
    fi
else
    echo "pgx_ulid installation failed; ulid integration tests will be skipped" >&2
fi

exit 0
