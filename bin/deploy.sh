#!/bin/bash

# exit if any command fails
set -e

# `osm`, `osmt` and other Bash aliases don't work in Bash files.
# The following variables are used instead of these Bash aliases
OSM="php vendor/osmphp/framework/bin/console.php"
OSMC="php vendor/osmphp/core/bin/compile.php"
OSMT="php vendor/osmphp/framework/bin/tools.php"

# Current Git branch
BRANCH=$(git rev-parse --abbrev-ref HEAD)

git fetch

$OSM http:down
git merge origin/$BRANCH
composer install
$OSMC Osm_Project
$OSMC Osm_Tools
$OSMT config:npm
npm install
gulp
$OSM migrate:up
$OSM http:up
echo "APP UPDATED!"
