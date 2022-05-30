Osm Admin is a framework for defining database tables using PHP classes and having a user-friendly admin panel for managing data out of the box.

The ultimate goal to is for you to stop worrying about all the admin panel details of your application, and spend all your efforts on what actually matter to *your* users.

Currently, it's in active development. The rest of the document is written in present tense, but most of it is yet to be implemented.

## Getting Started

1. Create an application:

        composer create-project osmphp/admin-project project1
        cd project1
        bin/install.sh

2. Create and enable a [Nginx virtual host](https://osm.software/docs/framework/0.15/getting-started/web-server.html#nginx), for example, `project1.local`.

3. Define a data class:

        /**
         * @property string $sku
         * @property float $qty
         */
        class Product extends Record
        {
        }

4. Open the application in the browser:

        http://project1.local/

5. Open the `Products` menu, it should work as shown in [this video](https://www.youtube.com/watch?v=SrxXZa5SeMk).

## How It Works

PHP attributes in table definitions are used to specify how properties are stored, validated, computed as well as to define their UI behavior.

Table definitions (PHP classes) are parsed into *schema* - a set of PHP objects having all the information about tables and properties.

Schema changes are detected using a *diff algorithm* and applied to the database, automatically.

The `query()` function retrieves/modifies table data in bulks using SQL-like *formulas* and the current schema.

When data is modified, computed properties and search indexes that depend on it are *re-indexed*.

The `ui_query()` function adds faceted/search queries on the top of what the `query()` function does.

Based on table definitions, UI *grids* and *forms* are created, automatically. These views:

* provide HTTP routes for every table;
* register themselves in the main menu;
* are rendered using Laravel Blade templates and Tailwind CSS;
* attach client-size behavior to HTML elements, written in vanilla JS.

UI *controls* define how different properties behave in grids and forms.

## Features

1. Effortless changes of data structure while preserving existing data.
2. Faceted navigation and full-text search in every table.
3. Multi-record editing.
4. Unlimited number of properties.
5. Table class definition and data input validation.
6. Computed and overridable properties.
7. Table relations.
8. Multi-value properties.
9. ... and more.

## Contributing

### Installation

1. Clone the project to your machine. If you don't have write access to the `osmphp/admin` repository, fork the project on [GitHub](https://github.com/osmphp/admin) to your account, and use your account name instead of `osmphp` in the following command:

        cd ~/projects
        git clone git@github.com:osmphp/admin.git admin 

2. Install prerequisites:

    * [PHP 8.1 or later](https://www.php.net/manual/en/install.php), and enable `curl`, `fileinfo`, `intl`, `mbstring`, `openssl`, `pdo_mysql`, `pdo_sqlite`, `sqlite3`
      extensions
    * [MySql 8.0 or later](https://dev.mysql.com/downloads/)
    * [Node.js, the latest LTS version](https://nodejs.org/en/download/current/)
    * [Gulp 4 command line utility](https://gulpjs.com/docs/en/getting-started/quick-start#install-the-gulp-command-line-utility)
    * [ElasticSearch 7.14 or later](https://www.elastic.co/downloads/elasticsearch)
    * [PHPUnit](https://phpunit.de/)
    * [Osm Framework command line aliases](https://osm.software/blog/21/08/framework-command-line-aliases.html)

3. Create MySql database, for example `admin`. Avoid `_` and `-` symbols in the name.
4. In the project directory, create `.env.Osm_Admin_Samples` file. On Linux, use `bin/create-env.sh` command to create it from a template:

        NAME=... # same as MySql database name
        #PRODUCTION=true
        
        MYSQL_DATABASE="${NAME}"
        MYSQL_USERNAME=...
        MYSQL_PASSWORD=...
        
        SEARCH_INDEX_PREFIX="${NAME}_" 

5. Install the project. On Linux, run `bin/install.sh` in the project directory. On other platforms, run the following commands:

        # go to project directory
        cd admin
         
        # install dependencies
        composer install
        
        # compile the applications
        osmc Osm_Admin_Samples
        osmc Osm_Project
        osmc Osm_Tools
        
        # collect JS dependencies from all installed modules
        osmt config:npm
        
        # install JS dependencies
        npm install
        
        # build JS, CSS and other assets
        gulp
        
        # make `temp` directory writable
        find temp -type d -exec chmod 777 {} \;
        find temp -type f -exec chmod 666 {} \;
        
        # create tables in the MySql database
        php bin/run.php migrate:up --fresh

6. Create and enable a [Nginx virtual host](https://osm.software/docs/framework/0.15/getting-started/web-server.html#nginx), for example, `admin.local`. Use the commands below. In the `osmt config:nginx` command, consider adding the `--prevent_network_access` flag to make the website only available on your computer, but not the surrounding ones:

       osmt config:nginx --app=Osm_Admin_Samples --prevent_network_access
       sudo php vendor/osmphp/framework/bin/tools.php \
           config:host --app=Osm_Admin_Samples
       sudo php vendor/osmphp/framework/bin/tools.php \
           install:nginx --app=Osm_Admin_Samples

7. Open the product list page, <http://admin.local/admin/products/>.

8. Instead of `osm` command-line alias, use `php bin/run.php`, for example:

        php bin/run.php refresh
        php bin/run.php migrate:up --fresh 

9. In the command line, keep Gulp running, it will clear the cache and rebuild assets as needed:

         cd {project_dir}
         gulp && gulp watch

### Points Of Interest

After the project is up and running, put the project under debugger, try various operations in the browser, and in the command line, using `osm` command alias.

To better understand what's going on under the hood, put breakpoints in main entry points:

* In the `run()` methods of route (or controller) classes, located in the `src/Ui/Routes/Admin` directory.
* In the `run()` methods of command line commands, located in the `src/Schema/Commands` directory.

### Read Framework Docs

This project is based on Osm Framework. To better understand how and why this project is written, read [the documentation](https://osm.software/docs/framework/0.15/index.html) of Osm Framework.

### Join Chats

Finally, with all the questions and ideas, join the chats on [Discord](https://discord.gg/EfW4nXPj).