#!/bin/sh -l
set -eu

vendor/bin/phpunit $*
