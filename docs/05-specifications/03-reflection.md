# Reflection

Osm Admin knows every tiny detail about your application objects and properties. And you can, too, using reflection.

***Status**. This specification is a work in progress.*

Contents:

{{ toc }}

### meta.abstract

Osm Admin knows every tiny detail about your application objects and properties. And you can, too, using reflection.

***Status**. This specification is a work in progress.*

## UI

Let's start with some diagrams.

UI renders results of query formulas, each having a resolved data type, designated UI control and filter types: 

![Data Types, UI Controls And Filters](data-types-ui-controls-filters.png)

Objects are shown in the UI as views:

![UI Views](ui-views.png)

### Views

There maybe N configured ways to show an object, or a list of objects, and these "ways" are called *views*.

There are several places in the UI where objects are displayed. Each such place has a dedicated property in the table or class definition. Standard places:

* `$table->list_views`are used on the object list page
* `$table->create_views` - on the object creation page
* `$table->edit_views` - on the object editing page

You can define custom places in the UI by adding more such properties to the table or class definition.

A UI place property, for example `$table->list_views`, is an array of views, having unique view name in its keys.

A view is shown using `?view=<name>` URL parameter.

If the `view` URL parameter is omitted, the default view, having a predefined name, is used:

* `$table->list_views['grid']` is a `Grid`
* `$table->create_views['form']` is a `Form(['mode' => 'create'])`
* `$table->edit_views['form']` is a `Form(['mode' => 'edit'])`

A UI place property expects every view in the list to extend some base class. For example, `$table->list_views` are expected to be instances of `List_`. The UI route and template code are programmed to the expected base class.

A default view is always there, and can't be deleted. However, you can customize it, and define more custom views.

Views can also be defined in `Property` classes, for example, `$record->select_views`.

**Implementation note**. During rendering a view object is cloned, then rendered, so the rendered view object is not a part of the schema.

### Controls

Then, a property or a formula of the same data type, say `int`, maybe represented in the UI in a number of ways, for example, `Input`, `Select` or `Hidden`. The "way" a property or a formula is represented and behaves in the UI, is called *control*. All supported controls for a data type are specified in the `$dataType->supported_controls` property.

Each data type has a default control specified in the `$dataType->default_control`. For example, by default, `int` properties are shown as `Input`.

A property uses the default control of its data type, or any other supported control. Either way, it's in the `$property->control` property.

`null` is a valid value for `control` and `default_control` properties. It means that the control is not displayed. 

**Implementation note**. During rendering a control object is cloned, then rendered. If a control displays a property, for example, `title`, it's cloned from `$property->control`. For more complex formulas, `$dataType->default_control` is used.

### Filters

A property is always filtered using the same URL parameter syntax. For example, `int` property understands `?qty=5+6+7` and `?qty=10-20+40-50` syntax.

However, there are several ways its filter can be rendered, for example, `int` property can be displayed as `Options` or `Slider`. All supported filters are listed in `$control->supported_filters`. 

Each control has a default filter referenced in the `$control->default_filter` property.

A property uses the default filter of its control, or any other supported filter. Either way, it's in the `$property->filter` property.

`null` is a valid value for `filter` and `default_filter` properties. It means that the filter is not displayed.

**Implementation note**. During rendering a filter object is cloned, then rendered. If a filter is based on a property, for example, `title`, it's cloned from `$property->filter`. If a filter is based on a more complex formula, `$control->default_filter` is used.

## What's Missing In This Specification

* schema
* tables and classes
* properties 
* implementation status/efforts required
* links to the code base
* comments in the code base (if you open a link to the code base, it should be an easy read)