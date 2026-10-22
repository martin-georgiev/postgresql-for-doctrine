#!/bin/sh
set -eu

# Installs pgvector into an Alpine-based PostGIS container.
#
# The Alpine postgresql-pgvector package installs files to /usr/lib/postgresql*/
# and /usr/share/postgresql*/, but the PostGIS Docker image builds PostgreSQL
# from source into /usr/local/.
#
# The package is built for the PostgreSQL major Alpine ships, so a server of any
# other major (such as a release candidate) cannot load it and gets pgvector
# compiled from source instead.

PGVECTOR_VERSION="0.8.7"

pg_major="$(pg_config --version | sed -E 's/^PostgreSQL ([0-9]+).*$/\1/')"

apk add --no-cache postgresql-pgvector > /dev/null 2>&1
if [ -f "/usr/lib/postgresql${pg_major}/vector.so" ]; then
    cp "/usr/lib/postgresql${pg_major}/vector.so" /usr/local/lib/postgresql/
    cp "/usr/share/postgresql${pg_major}/extension/"vector* /usr/local/share/postgresql/extension/
    exit 0
fi

apk add --no-cache build-base git > /dev/null
rm -rf /tmp/pgvector
git clone --quiet -c advice.detachedHead=false --depth 1 --branch "v${PGVECTOR_VERSION}" https://github.com/pgvector/pgvector.git /tmp/pgvector
# with_llvm=no: the image has no clang to emit the JIT bitcode PostgreSQL's build flags ask for.
make -C /tmp/pgvector OPTFLAGS="" with_llvm=no > /dev/null
make -C /tmp/pgvector install with_llvm=no > /dev/null
