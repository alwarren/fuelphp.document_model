**A PHP Document Model**

*Version 1.0 Beta*

A system for storing document information and rendering tags and collections
of tags. This allows for a modular approach to manipulating and rendering
various components of an HTML document.

Written for FuelPHP

The model consists of two components:

- an abstract document class with properties, containers, and business logic
- a document class that extends DocumentAbstract and contains rendering methods

Requirements:

- PHP version 5.3 or greater
- The FuelPHP framework version 1.0

Dependencies:

- Fuel\Core\Asset
- Fuel\Core\Autoloader
- Fuel\Core\Config
- Fuel\Core\Html
- Fuel\Core\Log

For more information, see the inline documentation.