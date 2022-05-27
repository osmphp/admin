#!/bin/bash

# exit if any command fails
set -e

# create .env file if it doesn't exist
if [ ! -f .env.My_Admin_Samples ]; then
    cp src/.env.Osm_Admin_Samples.template .env.My_Admin_Samples
fi
