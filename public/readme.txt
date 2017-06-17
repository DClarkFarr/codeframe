DCFRAME

Lightweight model controller framework, intended for quick, powerful development.

***SUPPORTS***

1) Models - Eloquent ?
2) Controllers
3) Scripts (def-load file and cli helper)
4) Multiple sites
5) Environment override
6) Templates/themes
7) Plugins
8) app wrapper class
9) Router/Router Config


***UTILITIED***
-form/inputs
-cache
-middleware (user auth)


*** App Wrapper Class ***
- loads config, overrides with environment, loads controller
- config get/set functions


*** Controllers ***
- private url property that can be matched, following url/segment/:variable/:optional?
- loadJS(), loadCSS() with 0-infinity ordering
- Controllers inherit router traits for url-to-action mapping
 

*** Router / Router Config ***
- Config loads list of approved controllers to autoload
- Router can pre-process url and run additional callback functions pre-controller load


*** Plugins ***
- Namespaced 
- Extend a plugin class
- Helper Plugins functions plugin(‘namespace’, ‘function’)
- Default functs:  Scripts() - loads js, css scripts, output() - meant to insert into page content, autoload() - load libraries and dependencies
- inherit router traits for url-to-action mapping

