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

