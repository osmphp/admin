# Specifications

What will Osm Admin look like? What is missing right now? What effort is needed to close this gap? All these, and some other questions are covered in specifications. 

{{ child_pages }}

### meta.abstract

What will Osm Admin look like? What is missing right now? What effort is needed to close this gap? All these, and some other questions are covered in specifications. 

---

## How To Write Specifications

In free form, explain the future usage of a given feature, and how Osm Admin will internally handle it. 

Then, add *Implementation Status / Efforts Required* section. 

In this section, specify what is not implemented yet. By convention, anything *not* listed in this section is already implemented and works as described, so be explicit.

Then, list implementation efforts required. Use task-like wording: "do this", "do that". Start each task in its own paragraph, and prefix it with bold R (stands for requirement):  

    **R**. Validate property definitions and applied attributes.
    
When implementing a specification, refine the task list by making each item doable in one working day. Then assign unique numbers:

    **R01**. Only allow `int` and `string` property types.
