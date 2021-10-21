#!/bin/bash

# exit if any command fails
set -e

# `osm`, `osmt` and other Bash aliases don't work in Bash files.
# The following variables are used instead of these Bash aliases
OSMC="php vendor/osmphp/core/bin/compile.php"
OSMT="php vendor/osmphp/framework/bin/tools.php"

# compile the applications
$OSMC Osm_App
$OSMC Osm_Project
$OSMC Osm_Tools

# collect JS dependencies from all installed modules
$OSMT config:npm

# install JS dependencies
npm install

# build JS, CSS and other assets
gulp

# make `temp` directory writable
find temp -type d -exec chmod 777 {} \;
find temp -type f -exec chmod 666 {} \;

