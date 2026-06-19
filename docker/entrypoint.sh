#!/bin/sh
set -e

mkdir -p bootstrap/cache \
    storage/logs \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/testing \
    storage/framework/views

chmod -R 777 bootstrap/cache storage

exec "$@"
