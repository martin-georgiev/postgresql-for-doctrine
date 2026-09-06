#!/bin/sh
set -eu

# Builds pgx_ulid (https://github.com/pksunkara/pgx_ulid) from source inside an
# Alpine-based PostGIS container and stages installable artifacts in
# /opt/pgx-ulid-artifacts.
#
# Upstream publishes only glibc packages, which cannot load on musl — so on
# Alpine the extension must be compiled natively with cargo-pgrx. CI caches the
# resulting artifacts per PostgreSQL major version, so this (slow) build only
# runs when the cache key rotates. Any failure exits non-zero.

PGX_ULID_VERSION="0.2.3"
OUT_DIR="/opt/pgx-ulid-artifacts"

apk add --no-cache build-base clang-dev clang-libs llvm-dev git curl bash rustup openssl-dev openssl-libs-static > /dev/null

rustup-init -y --default-toolchain stable --profile minimal > /dev/null
. "$HOME/.cargo/env"

cargo install cargo-pgrx --version "^0.17" --locked

pg_major="$(pg_config --version | sed -E 's/^PostgreSQL ([0-9]+).*$/\1/')"
cargo pgrx init "--pg${pg_major}" "$(command -v pg_config)"

rm -rf /tmp/pgx_ulid
git clone --depth 1 --branch "v${PGX_ULID_VERSION}" https://github.com/pksunkara/pgx_ulid /tmp/pgx_ulid
cd /tmp/pgx_ulid

# Statically-linked musl build scripts cannot dlopen libclang (needed by
# bindgen), so the extension build must link dynamically.
export RUSTFLAGS="-C target-feature=-crt-static"
cargo pgrx install --release --no-default-features --features "pg${pg_major}"

# Stage artifacts under the extension's packaged name (ulid) — matching the
# upstream release packages and what the integration tests expect.
mkdir -p "${OUT_DIR}"
cp "$(pg_config --pkglibdir)/pgx_ulid.so" "${OUT_DIR}/"
cd "$(pg_config --sharedir)/extension"
for f in pgx_ulid.control pgx_ulid--*.sql; do
    cp "$f" "${OUT_DIR}/$(echo "$f" | sed 's/^pgx_ulid/ulid/')"
done

echo "pgx_ulid built successfully for PostgreSQL ${pg_major}:"
ls -la "${OUT_DIR}"
