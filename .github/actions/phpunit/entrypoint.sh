#!/bin/sh -l
set -eu

#  Run phpunit Tests
vendor/bin/simple-phpunit $*
