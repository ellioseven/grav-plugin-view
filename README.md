# Grav Plugin - View

`View` is a [Grav](http://github.com/getgrav/grav) plugin that allows you find a page and send the collection to a template.

## Features

* Pass parameters to a collection for complete control.
* Assign any template to any view.
* Add views to modular pages, allowing you to manage, order, etc.

# Installation

Installing the View plugin can be done in one of two ways. Our GPM (Grav Package Manager) installation method enables you to quickly and easily install the plugin with a simple terminal command, while the manual method enables you to do so via a zip file.

## GPM Installation (Preferred)

The simplest way to install this plugin is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm) through your system's Terminal (also called the command line).  From the root of your Grav install type:

    bin/gpm install view

This will install the View plugin into your `/user/plugins` directory within Grav. Its files can be found under `/your/site/grav/user/plugins/view`.

## Manual Installation

To install this plugin, just download the zip version of this repository and unzip it under `/your/site/grav/user/plugins`. Then, rename the folder to `view`. You can find these files on [GitHub](https://github.com/ellioseven/grav-plugin-view).

You should now have all the plugin files under

	/your/site/grav/user/plugins/view

> NOTE: This plugin is a modular component for Grav which requires [Grav](http://github.com/getgrav/grav), the [Error](https://github.com/getgrav/grav-plugin-error) and [Problems](https://github.com/getgrav/grav-plugin-problems) plugins, and a theme to be installed in order to operate.

# Usage

1. Add modular to modular page
2. Select 'View' as Modular Template
3. Specify a page. Optionally, you can specify a template, pagination, and parameters.
4. If you specify a template, you will need to create it in your theme, 
`/your/site/grav/user/themes/templates/<template>`, where template is the path of your specified template.

## Using Parameters

You can also add parameters to the collection, allowing you complete control over the view collection:

```
items:
    '@taxonomy.category': 'Animals'
``` 

## View Template
 
You will have the `view` variable available to you, which contains the items and settings.

* `view.items` - The view's collection items.
* `view.settings` - The settings used to build the collection items.

A simple template may look like so:

```
<h1>{{ page.title }}</h1>
<ul>
    {% for item in view.collection %}
        <li>{{ item.title }}</li>
    {% endfor %}
</ul>
```

### Default View Template

You can also define a default template as a fallback in the plugin configuration )`http://example
.com/admin/plugins/view`).

[Page collections](http://learn.getgrav.org/content/collections) are very powerful here, and allow you to filter the 
output of the page collection into your view.

# Updating

As development for View continues, new versions may become available that add additional features and functionality, improve compatibility with newer Grav releases, and generally provide a better user experience. Updating View is easy, and can be done through Grav's GPM system, as well as manually.

## GPM Update (Preferred)

The simplest way to update this plugin is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm). You can do this with this by navigating to the root directory of your Grav install using your system's Terminal (also called command line) and typing the following:

    bin/gpm update view

This command will check your Grav install to see if your View plugin is due for an update. If a newer release is found, you will be asked whether or not you wish to update. To continue, type `y` and hit enter. The plugin will automatically update and clear Grav's cache.

## Manual Update

Manually updating View is pretty simple. Here is what you will need to do to get this done:

* Delete the `your/site/user/plugins/view` directory.
* Downalod the new version of the View plugin from [GitHub](hhttps://github.com/ellioseven/grav-plugin-view).
* Unzip the zip file in `your/site/user/plugins` and rename the resulting folder to `view`.
* Clear the Grav cache. The simplest way to do this is by going to the root Grav directory in terminal and typing `bin/grav clear-cache`.

> Note: Any changes you have made to any of the files listed under this directory will also be removed and replaced by the new set. Any files located elsewhere (for example a YAML settings file placed in `user/config/plugins`) will remain intact.