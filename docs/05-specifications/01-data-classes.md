# Data Classes

{{ toc }}

## Top And Child Objects

### Records

Application data is made of objects. Top-level objects are stored in database records. For example, product objects are stored as records in `products` table.

Top-level objects are instances of classes that extend from the `Record` class. 

Let’s call them just *records*. 

For example, products are instances of `Product` class:

    class Product extends Record
    {
    
    }

As defined in the `Record` class, every record has two standard properties:

* `id` - Unique auto-incremented record ID. It's used for creating relationships between objects, and for object selection in a grid.
* `title` - Record title. It's used for in various places of the admin area, for example, while displaying the object in dropdown option list, or in the page title of an edit form.

### Objects

Some objects may not have a designated table that stores them. Instead, they are stored in the parent object's record. 

Let's call these child objects just *objects*.

For example, product stock settings may be stored in an object that is a part of the product object:

    /**
     * @property StockSettings $stock_settings
     */
    class Product extends Record
    {
    }

    class StockSettings extends Object_ {
    }

### Scalar Properties

A record stores property values. Each property is of some predefined [type](https://www.php.net/manual/en/language.types.intro.php).

Osm Admin supports scalar PHP types (`string`, `int`, `float` and `bool`), and `Carbon` class for date/time values:

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

### Compound Properties

It also supports object and array types:

    /**
     * @property string $name
     * @property Category $parent
     * @property Category[] $children
     */
    class Category extends Record
    {
    
    }

### Child Objects

Not every object is stored as a database record. Some objects - let’s call them *child* objects - are stored in a property of some parent object. For example, product images can be stored in the product record. For such object extend the `Object_` class instead of the `Record` class:

    /**
     * @property Product $product
     * @property string $path
     * @property int $sort_order
     */
    class Image extends Object_
    {
    
    }

### Nullable Properties

Mark property as *nullable* using `?` syntax if some object may have no value for it. For example, a root product category has no parent category, hence it should be nullable:

    /**
     * @property string $name
     * @property ?Category $parent
     * @property Category[] $children
     */
    class Category extends Record
    {
    
    }

User has to provide values for all non-nullable properties, so, nullable properties make object creation easier. In fact, consider marking all properties nullable, unless a vlue is really, really required.

### Mixed Properties

Use `mixed` type to allow any property value:

    /**
     * @property string $name
     * @property mixed $value
     */
    class Property extends Record
    {
    
    }

All `mixed` properties are nullable.

In general, avoid `mixed` properties, and use them only if absolutely necessary.

### Default Values

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

### Object Types

[Previous implementation](https://osm.software/blog/21/11/data-subclasses.html)

Some objects being in the same table may have different structure. For example, simple products and configurable products have many common properties that can be put into the base `Product` class, but some other properties differ. For example, a configurable product specifies properties that a user should specify while ordering a product, and a list of the underlying simple products that will actually be shipped:

    /**
     * @property string[] $config_properties
     * @property Product[] $config_products
     */
    class Configurable extends Product
    {
    
    }

## Database Schema

### Table Name

By default, the table name is inferred from the short class name. For example, `Product` instances are stored in `products` table.

You may specify the table name using `#[Table]` attribute:

    /**
     * ...
    */
    #[Table('my_products')]
    class Product extends Record
    {
    
    }

### Implicit Columns

By default, records are stored in the underlying table using two columns:

- `id` is unique auto-incremented unsigned integer
- `data` JSON column stored all the properties. Null values are not stored.

For example:

    products
    
    id      data
    -------------------------------------------
    1       {"name": "Pink Bag", "qty": 5}
    2       {"name": "Blue Dress", "qty": 10}
    3       {"name": "Black Jacket", "qty": 20}

### Explicit Columns

You may add `#[Explicit]` attribute to create an explicit table column for the property:

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

### `string` Columns

By default, an explicit `string` property is stored as `TEXT`.  You can force it to be `VARCHAR` using `#[Length]` attribute specifying a value that is small enough for it:

    /**
     * @property ?string $name #[Explicit, Length(255)]
     * ...
    */
    class Product extends Record
    {
    
    }

### `int` Columns

By default, an explicit `int` property is stored as signed `INT`.  Change that using `#[Tiny]`, [`Small]`,  `#[Long]` and `#[Unsigned]` attributes:

    /**
     * @property int $qty #[Default_(0), Explicit, Unsigned, Long]
    */
    class Product extends Record
    {
    
    }

### `float` Columns

By default, an explicit `float` property is stored as `DECIMAL(18, 2)`.  Change that using `#[Precision]`  and `[Scale]` attributes:

    /**
     * @property float $price #[Default_(0.0), Explicit, Scale(4)]
    */
    class Product extends Record
    {
    
    }

### `Carbon` Columns

These columns are stored as `DATETIME` columns, in UTC timezone.

### Object Columns

Non-record objects can’t be explicit - they are always stored in the `data` column.

On the contrary, a record object can’t be implicit. For it, a foreign key column is explicitly created. For example:

    /**
     * @property ?Category $parent #[Explicit]
     * ...
     */
    class Category extends Record
    {
    
    }

This property creates `parent_id UNSIGNED INT` column, an index and a foreign key contraint. By default, the constraint rule has a `ON DELETE CASCASE` clause, you may use `#[OnDeleteRestrict]` or `[OnDeleteSetNull]` to change that.

### Virtual Properties

Some properties are only computed while PHP code executes, but never stored in the database. Mark such properties as `#[Virtual]`.

### Arrays

Most arrays can’t be explicit - they are stored in the `data` column.

However, a record array is neither stored in `data` column, nor in an explicit column. In fact, it is stored in a child table, so it has to be marked as `#[Virtual]`. For example:

    /**
     * @property Line[] $lines [Virtual]
     */
    class Order extends Record
    {
    }
    
    /**
     * @property Order $order [Explicit]
     */
    class Line extends Record
    {
    }

## Queries And Formulas

[Previous implementation](https://osm.software/blog/21/11/data-query-formulas.html)

### Basic Syntax

    $category = query(Category::class)
        ->where('parent.id = 1 OR id > 5')
        ->orderBy('name DESC')
        ->first('id', 'name', 'parent.name');

### Virtual Properties

You may also compute property values on the fly, and use them in queries:

    /**
     * @property ?Category $parent #[Explicit]
     * @property bool $root #[Virtual("parent.id IS NULL")]
     * @property bool $top #[Virtual("parent.parent.id IS NULL")]
     * ...
     */
    class Category extends Record
    {
    
    }
    ...
    $category = query(Category::class)
        ->where('top OR id > 5')
        ->orderBy('name DESC')
        ->first('id', 'name', 'parent.name');

### Computed Properties

Alternatively, you may store computed value in the database:

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

### Overridable Properties

Finally, you may allow user to override the computed value:

    /**
     * @property ?Category $parent #[Explicit]
     * @property string $name #[Overridable("parent.name")]
     * ...
     */
    class Category extends Record
    {
    
    }

### Compound Queries

You may query a parent table and its child table at the same time:

    $orders = query(Order::class)
            ->query('lines', fn(Query $query) => $query->get('*'))
        ->get('*');

It also works on hierarchical tables:

    $categories = query(Category::class)
            ->query('children', fn(Query $query) => $query->get('*'))
        ->get('*');

## User Interface

### Forms

Previous implementation

All properties, except `#[System]`, are shown on the form page:

- most properties are displayed as `#[Input]`
- date/time properties as displayed as `#[Date]`
- `bool` properties are displayed as `#[Switch_]`
- record properties are displayed as `#[Relation]`
- object array properties are displayed as grids
- scalar array properties are displayed as `#[Multiselect]`

You may change the default behavior using `#[Select]`, `#[File]` and (later) other attributes.

Change field order using `#[Before]` and `#[After]` attributes.

Organize fields into fieldsets, sections and chapters using `#[In_]` attribute.

### Grids

Properties behave in a grid according to their behavior in a form.

However, by default, only the `title` property is displayed. To change that, define a `Grid` -derived class, and specify columns there.