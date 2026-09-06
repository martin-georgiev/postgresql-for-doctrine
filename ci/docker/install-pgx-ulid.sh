#!/bin/sh
set -u

# Best-effort pgx_ulid (https://github.com/pksunkara/pgx_ulid) install for the local docker-compose Alpine container;
# CI compiles from source instead (see build-pgx-ulid.sh). Upstream ships only glibc .debs, loaded here via gcompat.
# Only the known musl/glibc symbol incompatibility exits zero (the ulid integration tests then skip themselves);
# any other failure exits non-zero.

PGX_ULID_VERSION="0.2.3"

# SHA-256 checksums for the pinned release artifacts, computed from https://github.com/pksunkara/pgx_ulid/releases/tag/v0.2.3
checksum_for() {
    case "$1" in
        pg16-amd64) echo "b764c329b5fdb0a37c4946142440fbd2dcaada41d8748a649255ae76ae9f760c" ;;
        pg16-arm64) echo "0fc015eabf281ca6100fb5f47f0a1323ed82062a4dbe3fecdcb87a1a2e28c8bb" ;;
        pg17-amd64) echo "10c3c6721a341a05abd1a50a798c8159ed6dae297f0f67228db8409a93e56133" ;;
        pg17-arm64) echo "725fa001ee6fb94812146d31d18ffdb61759d9c11a5acedbf9ed4d605340c64e" ;;
        pg18-amd64) echo "4694d17ae5cc35d936dfbedae6ed7cc55515424b76d65005da4f3cafeb57404e" ;;
        pg18-arm64) echo "6ae481f635ac551648040b77776d30f7265ed775a143ab7e8e5845e5be6c528d" ;;
        *) return 1 ;;
    esac
}

fail() {
    echo "pgx_ulid: $1" >&2
    exit 1
}

install_pgx_ulid() {
    apk add --no-cache binutils gcompat tar xz zstd > /dev/null 2>&1 || fail "cannot install extraction tools via apk"

    pg_major="$(pg_config --version | sed -E 's/^PostgreSQL ([0-9]+).*$/\1/')"
    case "$(uname -m)" in
        x86_64) arch="amd64" ;;
        aarch64) arch="arm64" ;;
        *) fail "unsupported architecture $(uname -m)" ;;
    esac

    checksum="$(checksum_for "pg${pg_major}-${arch}")" || fail "no pinned checksum for PostgreSQL ${pg_major} on ${arch}; update the checksum table for this combination"

    workdir="$(mktemp -d)"
    deb="pgx_ulid-v${PGX_ULID_VERSION}-pg${pg_major}-${arch}-linux-gnu.deb"
    url="https://github.com/pksunkara/pgx_ulid/releases/download/v${PGX_ULID_VERSION}/${deb}"

    wget -q -O "${workdir}/${deb}" "${url}" || fail "download failed: ${url}"
    (cd "${workdir}" && echo "${checksum}  ${deb}" | sha256sum -c - > /dev/null 2>&1) || fail "checksum verification failed for ${deb}"
    (cd "${workdir}" && ar x "${deb}" && tar -xf data.tar.*) || fail "cannot extract ${deb}"

    find "${workdir}" -name '*.so' -exec cp {} "$(pg_config --pkglibdir)/" \; || fail "cannot copy shared library"
    find "${workdir}" -path '*/extension/*' \( -name '*.control' -o -name '*.sql' \) -exec cp {} "$(pg_config --sharedir)/extension/" \; || fail "cannot copy extension files"

    rm -rf "${workdir}"
}

install_pgx_ulid

so_path="$(pg_config --pkglibdir)/pgx_ulid.so"
[ -f "${so_path}" ] || fail "shared library not found at ${so_path} after installation"

ldd_output="$(ldd "${so_path}" 2>&1)"
ldd_status=$?

if printf '%s' "${ldd_output}" | grep -q "symbol not found"; then
    # Known upstream limitation, not an installation error.
    echo "pgx_ulid: library has unresolved glibc symbols on this platform; CREATE EXTENSION will fail and ulid integration tests will be skipped" >&2
elif [ "${ldd_status}" -ne 0 ]; then
    fail "cannot inspect ${so_path}: ${ldd_output}"
else
    echo "pgx_ulid installed successfully"
fi

exit 0
