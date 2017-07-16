DCFRAME

Lightweight model controller framework, intended for quick, powerful development.

***SUPPORTS***

1) DONE Models - Eloquent
2) DONE Controllers
3) DONE Cron Scripts (def-load file and cli helper)
4) DONE Multiple sites
5) DONE Environment override
6) Templates/themes
7) Plugins
8) DONE app wrapper class
9) DONE Router/Router Config

***.env Override TODO***
- add env to public root directory
- add step to App::bootstrap that checks for and includes envfile
- instead of env(), automatically override

***UTILITIEDS**
-form/inputs
-DONE cache
-middleware (user auth)


*** App Wrapper Class ***
- loads config, overrides with environment, loads controller
- config get/set functions


*** Controllers ***
- private url property that can be matched, following url/segment/:variable/:optional?
- loadJS(), loadCSS() with 0-infinity ordering
- Controllers inherit router traits for url-to-action mapping
- TODO: add '*' to end of pattern, if controller allows segments, or if exists('params') in pattern.  If static segments have not been met and no '*' exists, then route should not be found
- TODO: add additional params to MVCtoController, $useIndexController, $useIndexAction.  If false, do not suggest.  If numeric, must be less than
- TODO: in router->controller(), if action = true, then offset the segments list, if any, and auto set action, if no segments, index, else failed

*** Router / Router Config ***
- Config loads list of approved controllers to autoload
- Router can pre-process url and run additional callback functions pre-controller load


*** Plugins ***
- Namespaced 
- Extend a plugin class
- Helper Plugins functions plugin(‘namespace’, ‘function’)
- Default functs:  Scripts() - loads js, css scripts, output() - meant to insert into page content, autoload() - load libraries and dependencies
- inherit router traits for url-to-action mapping

