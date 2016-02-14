# v1.3.2
## 02/14/2016

1. [](#bugfix)
    * Fix default items in params if no params are set
    * Fixed documentation typos

# v1.3.1
## 02/08/2016

1. [](#new)
    * Added support for pagination
    * Added getParams method to parse params
    * Added getCollection method to get view collection
    * Added filter field to filter collection to selected page children
1. [](#improved)
    * Rename view.page field to view.reference to avoid confusion
    * Rename view.item to view.collection to avoid confusion
    * Using twig vars instead of dynamic header vars
    * Removed pagination field
    * Show root in modular template field
    * Removed all logic from view.html.twig
1. [](#bugfix)
    * Check for malformed yaml before parsing

# v1.1.2
## 02/06/2016

1. [](#improved)
    * Cleaner modular template by changing passed variables
1. [](#bugfix)
    * Check for view header before parsing variables in view.php
    * Fixed parsing of settings in view.html.twig

# v1.1.1
## 01/25/2016

1. [](#improved)
    * Fixed default template not being called.

# v1.1.0
## 01/25/2016

1. [](#improved)
    * Removed param merge with referencing page.

# v1.0.0
## 01/23/2016

1. [](#new)
    * ChangeLog started...