# Installation

Create new Osm Admin projects quickly using the command line.

### meta.abstract

Create new Osm Admin projects quickly using the command line.

## Step By Step

1. [Create a new project based on Osm Framework](https://osm.software/docs/framework/getting-started/installation.html).

2. Add Osm Admin package using Composer, and re-run the installation script (on Windows, [run installation commands one by one instead](https://osm.software/docs/framework/getting-started/installation.html#if-on-windows)):

        composer require osmphp/admin:v0.1-dev@dev
        bash bin/install.sh
            
3. Create a MySql database, and configure [MySql](https://osm.software/docs/framework/getting-started/configuration.html#mysql) and [ElasticSearch](https://osm.software/docs/framework/getting-started/configuration.html#elasticsearch) connections, or use other database/search engines. 

4. Create initial database tables:

        osm migrate:up --fresh
        osm migrate:schema

That's all! 