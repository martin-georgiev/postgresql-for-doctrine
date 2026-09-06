#!/bin/sh
set -eu

# Builds pgx_ulid (https://github.com/pksunkara/pgx_ulid) from source and stages installable artifacts in
# /opt/pgx-ulid-artifacts. Upstream publishes only glibc packages, which cannot load on musl, so Alpine needs a native
# cargo-pgrx build. CI caches the artifacts per PostgreSQL major version.

PGX_ULID_VERSION="0.2.3"
# sync with the pgx_ulid cache key in .github/workflows/integration-tests.yml.
PGX_ULID_COMMIT="c22451dba167dd83cf64a7b0fa6af668f926f221"
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

checked_out_commit="$(git rev-parse HEAD)"
if [ "${checked_out_commit}" != "${PGX_ULID_COMMIT}" ]; then
    echo "pgx_ulid: tag v${PGX_ULID_VERSION} resolved to unexpected commit ${checked_out_commit} (pinned: ${PGX_ULID_COMMIT})" >&2
    exit 1
fi

# Statically-linked musl binaries cannot dlopen libclang (needed by bindgen).
export RUSTFLAGS="-C target-feature=-crt-static"
cargo pgrx install --release --no-default-features --features "pg${pg_major}"

# Source builds register as "pgx_ulid"; the upstream packages (and the integration tests) use "ulid"
# as the stage name under the packaged name.
mkdir -p "${OUT_DIR}"
cp "$(pg_config --pkglibdir)/pgx_ulid.so" "${OUT_DIR}/"
cd "$(pg_config --sharedir)/extension"
for f in pgx_ulid.control pgx_ulid--*.sql; do
    cp "$f" "${OUT_DIR}/$(echo "$f" | sed 's/^pgx_ulid/ulid/')"
done

echo "pgx_ulid built successfully for PostgreSQL ${pg_major}:"
ls -la "${OUT_DIR}"
