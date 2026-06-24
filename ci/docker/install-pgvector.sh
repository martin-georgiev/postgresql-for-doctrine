#!/bin/sh
set -eu

# Installs pgvector into an Alpine-based PostGIS container.
#
# The Alpine postgresql-pgvector package installs files to /usr/lib/postgresql*/
# and /usr/share/postgresql*/, but the PostGIS Docker image builds PostgreSQL
# from source into /usr/local/.

apk add --no-cache postgresql-pgvector > /dev/null 2>&1
cp /usr/lib/postgresql*/vector.so /usr/local/lib/postgresql/
cp /usr/share/postgresql*/extension/vector* /usr/local/share/postgresql/extension/
