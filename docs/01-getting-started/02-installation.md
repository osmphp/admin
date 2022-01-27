# Installation

Create new Osm Admin projects quickly using this step-by-step guide:

1. [Create a new project based on Osm Framework](https://osm.software/docs/framework/getting-started/installation.html).

2. Add Osm Admin package using Composer, and re-run the installation script (on Windows, [run installation commands one by one instead](https://osm.software/docs/framework/getting-started/installation.html#if-on-windows)):

        composer require osmphp/admin:v0.1.x-dev@dev

3. Add all Osm Admin modules as a dependency in `src/Base/Module.php` file:
   
        ...
        class Module extends BaseModule
        {
            ... 
            public static array $requires = [
                ...
                \Osm\Admin\All\Module::class,
            ];
        }             
 
4. Create a MySql database, and configure [MySql](https://osm.software/docs/framework/getting-started/configuration.html#mysql) and [ElasticSearch](https://osm.software/docs/framework/getting-started/configuration.html#elasticsearch) connections, or use other database/search engines. 

5. Re-run the installation script (on Windows, [run installation commands one by one instead](https://osm.software/docs/framework/getting-started/installation.html#if-on-windows)):

        bash bin/install.sh
 
6. Create initial database tables:

        osm migrate:up --fresh
        osm migrate:schema

That's it! 

### meta.abstract

Create new Osm Admin projects quickly using this step-by-step guide.

