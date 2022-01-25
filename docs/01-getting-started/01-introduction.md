# Introduction

Osm Admin is the admin area, and the API for your next PHP application that has simple configuration, is optimized for mass-editing and is incredibly customizable.

{{ toc }}

### meta.abstract

Osm Admin is the admin area, and the API for your next PHP application that is optimized for mass-editing, has simple configuration and is incredibly customizable.

## Your Workflow

Once Osm Admin is installed, add grids and forms to your admin area using this simple workflow: 

1. Define data structure using PHP classes and properties.
2. Add to them visual look and behavior using PHP attributes.
3. Run a command to re-create database tables.

That's it! 

Open your fully working admin area in the browser, and, if needed, adjust the PHP classes as needed.  

## Configuration Example

Here is a sample task class for a simple to-do application:

    ...
    /**
     * @property string $todo #[
     *      Serialized,
     *      Grid\String_('To Do', edit_link: true),
     *      Form\String_(10, 'To Do'),
     * ]
     */
    #[
        Storage\Table('tasks'),
        Interface_\Table\Admin('/tasks', 'Task'),
        Grid(['todo']),
    ]
    class Task extends Object_
    {
        use Id;
    }
    
## What You Get

Osm Admin creates three things for you:

1. A fully working admin area for you to manage your data visually.
2. An API for managing your data remotely from a script.
3. An internal interface for rendering your data in the application frontend.

## Customize Anything

Everything is customizable! Create your own grid column or form field types, custom validation or indexing rules, and a lot more.
   