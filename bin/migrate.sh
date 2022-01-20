#!/bin/bash

# exit if any command fails
set -e

OSM="php bin/run.php"
$OSM migrate:up --fresh
$OSM migrate:schema
$OSM migrate:sample-data

