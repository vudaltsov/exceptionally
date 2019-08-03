#!/bin/sh -l
set -eu

vendor/bin/php-cs-fixer fix $*
