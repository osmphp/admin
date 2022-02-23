# Objects

Application data is made of objects. Use standard PHP class and attribute syntax to: 

* define the structure of application objects,
* define how they are stored in the database,
* specify computation logic,
* represent them in the user interface. 

***Status**. The text of this specification is a work in progress.*

Contents:

{{ toc }}

### meta.abstract

Application data is made of objects. Use standard PHP class and attribute syntax to:

* define the structure of application objects,
* define how they are stored in the database,
* specify computation logic,
* represent them in the user interface.

***Status**. The text of this specification is a work in progress.*

## Object Types

### Records

Top-level objects are stored in database records. For example, product objects are stored as records in `products` table.

Top-level objects are instances of classes that extend from the `Record` class. 

Let’s call them just *records*. 

For example, products are instances of `Product` class:

    /**
     * @property int $qty       Qty in stock
     * @property float $price   Regular price
     * @property bool $enabled 
     */
    class Product extends Record
    {
    }

In addition to all properties defined in the class, every record has two standard properties:

* `id` - Unique auto-incremented record ID. It's used for creating relationships between objects, and for object selection in a grid.
* `title` - Record title. It's used for in various places of the admin area, for example, while displaying the object in dropdown option list, or in the page title of an edit form.

### Objects

Some objects don't have a designated table that stores them. Instead, they are stored in the parent object's record. 

Let's call such child objects just *objects*.

For example, product images can be stored in the product record:

    /**
     * ...
     * @property Image[] $images 
     */
    class Product extends Record
    {
    }

    /**
     * @property Product $parent
     * @property string $path
     * @property int $sort_order
     */
    class Image extends Object_
    {
    
    }

### Subtypes

Some records of the same table may have different structure.

For example, simple products and configurable products have many common properties that can be put into the base `Product` class, but some other properties differ. A configurable product contains properties that a user should specify while ordering a product, and a list of the underlying simple products that will actually be shipped. Use `#[Type]` attribute to define a record subtype:

    /**
     * @property string[] $config_properties
     * @property Product[] $config_products
     */
    #[Type('configurable')] 
    class Configurable extends Product
    {
    
    }

## Properties

Objects store property values. For example, products may be of different color or size, have different quantity in stock. All of these are properties.

Properties are defined in the doc comment of the object's class using `@property` tag: 

    /**
     * @property int $qty       Qty in stock
     * @property float $price   Regular price
     * @property bool $enabled 
     */
    class Product extends Record
    {
    }

If you accidentally assign an undefined property a value, such assignment is ignored.

Each property definition has mandatory type (e.g. `int`) and name (e.g. `qty`). The property can be a scalar, an object, or an array.

### Scalars

Scalar properties store regular, simple values. 

To define a scalar property, use `int`, `string`, `bool`, `float`, `\DateTime` (or `Carbon` its better alternative) or `mixed` property type:

    /**
     * @property string $name
     * @property int $qty
     * @property float $price
     * @property bool $enabled
     * @property Carbon $created_at
     */
    class Product extends Record
    {
    
    }

In PHP, `mixed` means "any type", while in Osm Admin, it means "any scalar type". In general, avoid `mixed` properties, and use them only if absolutely necessary.

### Objects

An object property contains an objects, or references a record.
 
To define an object property, use the class name of the referenced record (should extend `Record`) or contained object (should extend `Object_`), for example: 

    /**
     * @property Product $parent
     * ...
     */
    class Image extends Object_
    {
    
    }

### Arrays

An array property contains an array of scalars or objects. 

To define an array property, use a scalar or object property type followed by `[]`:

    /**
     * ...
     * @property Image[] $images 
     */
    class Product extends Record
    {
    }

## Nullable Properties

Mark property as *nullable* using `?` syntax if some object may have no value for it. For example, a root product category has no parent category, hence it should be nullable:

    /**
     * @property string $name
     * @property ?Category $parent
     * @property Category[] $children
     */
    class Category extends Record
    {
    
    }

User has to provide values for all non-nullable properties, so, nullable properties make object creation easier. In fact, consider marking all properties nullable, unless a value is really, really required.

## Default Values

Another way to simplify object creation is providing sensible default values using `#[Default_]` attribute. Let’s refine the definition of the product class:

    /**
     * @property ?string $name
     * @property int $qty #[Default_(0)]
     * @property float $price #[Default_(0.0)]
     * @property bool $enabled #[Default_(true)]
    */
    class Product extends Record
    {
    
    }

## Table Columns

Records of the same class are stored in one table. For example, all product objects are stored as records in the `products` table.

The table name is inferred from the short class name, `Product`. You can specify a custom table name using the `#[Table]` attribute:

    /**
     * ...
    */
    #[Table('my_products')]
    class Product extends Record
    {
    }

By default, a table has two columns:

- `id` is unique auto-incremented unsigned integer
- `data` JSON column stored all the properties. Null values are not stored.

For example:

    products
    
    id      data
    -------------------------------------------
    1       {"name": "Pink Bag", "qty": 5}
    2       {"name": "Blue Dress", "qty": 10}
    3       {"name": "Black Jacket", "qty": 20}

You may add `#[Explicit]` attribute to a property definition to create an explicit table column:

    /**
     * @property ?string $name #[Explicit]
     * ...
    */
    class Product extends Record
    {
    
    }

The underlying table changes as follows:

    products
    
    id      name            data
    -----------------------------------
    1       Pink Bag        {"qty": 5}
    2       Blue Dress      {"qty": 10}
    3       Black Jacket    {"qty": 20}

Column type used, and other database schema details are dependent on the property type and additional attributes. Nullable explicit properties make nullable table columns.

### `string`

By default, an explicit `string` property is stored as `TEXT`.  You can force it to be `VARCHAR` using `#[Length]` attribute specifying a value that is small enough for it:

    /**
     * @property ?string $name #[Explicit, Length(255)]
     * ...
    */
    class Product extends Record
    {
    
    }

### `int`

By default, an explicit `int` property is stored as signed `INT`.  Change that using `#[Tiny]`, [`Small]`,  `#[Long]` and `#[Unsigned]` attributes:

    /**
     * @property int $qty #[Default_(0), Explicit, Unsigned, Long]
    */
    class Product extends Record
    {
    
    }

### `float`

By default, an explicit `float` property is stored as `DECIMAL(18, 2)`.  Change that using `#[Precision]`  and `[Scale]` attributes:

    /**
     * @property float $price #[Default_(0.0), Explicit, Scale(4)]
    */
    class Product extends Record
    {
    
    }

### `Carbon` (or `\DateTime`)

These columns are stored as `DATETIME` columns, in UTC timezone.

### Objects

Non-record explicit objects are stored in a JSON column.

For record objects, foreign key columns are explicitly created. For example:

    /**
     * @property ?Category $parent #[Explicit]
     * ...
     */
    class Category extends Record
    {
    
    }

This property creates `parent_id UNSIGNED INT` column, an index and a foreign key constraint. By default, the constraint rule has a `ON DELETE CASCASE` clause, you may use `#[OnDeleteRestrict]` or `[OnDeleteSetNull]` to change that.

## Computed Properties

You may have a property computed based on a SQL-like formula:

    /**
     * @property ?Category $parent #[Explicit]
     * @property ?int $level #[Computed("(parent.level ?? -1) + 1")]
     * @property ?string $id_path #[
     *      Computed("(parent.id_path IS NOT NULL " . 
     *          "? parent.id_path + '/' : '') + id")
     * ]
     * ...
     */
    class Category extends Record
    {
    
    }

Computed values are stored in the database, and updated as needed.

Actually there are two more variations of computed properties: virtual properties and overridable properties. 

### Virtual Properties

Rather than storing computed value in the database, you may compute it whenever it's needed. In this case, use `#[Virtual]` attribute:

    /**
     * @property ?Category $parent #[Explicit]
     * @property bool $root #[Virtual("parent.id IS NULL")]
     * @property bool $top #[Virtual("parent.parent.id IS NULL")]
     * ...
     */
    class Category extends Record
    {
    
    }

### Overridable Properties

Finally, you may allow user to override the computed value using the `#[Overridable]` attribute:

    /**
     * @property ?Category $parent #[Explicit]
     * @property string $name #[Overridable("parent.name")]
     * ...
     */
    class Category extends Record
    {
    
    }

## What's Missing In This Specification

* controls
* grids
* forms
* filterable, sortable, searchable

## Implementation Status / Efforts Required

Currently, Osm Admin doesn't validate property definitions and applied attributes.

Although you can define any property in a data class, currently only `int` and `string` properties are supported.

Arrays are especially tricky, be it an array of scalars, an array of objects, or an array of record references. How they should be stored, queried, displayed? 

Upgrading a database is a non-trivial task. What if property type changes? What if property is renamed? Deleted? What if nullability changes? How the data is preserved? How computed properties are preserved? This topic is so wide that it requires a separate specification.

Computed properties are well, not computed.

**R**. Validate property definitions and applied attributes. 

**R**. Support all the rest property types.

**R**. Support arrays.

**R**. Test both implicit and explicit property versions.

**R**. Specify how database upgrades work.

**R**. Implement computed properties.
