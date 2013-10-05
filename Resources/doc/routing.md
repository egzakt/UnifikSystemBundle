Routing
=========================

The routing component is a key part of the Flexy distribution. This component does three main things:

- Generate flexy enabled routes based on entries in the mapping table.
- Inject flexy attributes into mapped routes.
- Expand the {sectionPath} placeholder. 

## What is a flexy enabled request ?

A flexy enabled request is a normal symfony request with some specials attributes injected into the route that will trigger the bootstapping of all flexy related services.

### Attributes structure

There is two key attributes, the `_flexyEnabled` and the `_flexyRequest`.
Here is a typical structure:

```php
array(
     '_flexyEnabled': true,
     '_flexyRequest': array(
         'sectionId' => 1,
         'appId' => 1,
         'appPrefix' => 'admin',
         'appName' => 'backend'
     )
)
```

### Creating an flexy enabled request

Attributes are automatically injected when a route is mapped to a section. The mapping process is covered [here](#the-mapping-process).

Although this is not the recommanded method, you can manually create a compatible request straight from any routes definitions:

```yml
flexy_product_detail:
   pattern:  /{sectionsPath}/{productSlug}
   defaults:
      _controller: "FlexyProductBundle:Frontend/Product:detail"
      _flexyEnabled: true
      _flexyRequest:
         sectionId: 1
         appId: 2
         appName: 'frontend'
         appPrefix: ''
```

The drawback of this method is that every parameters are hardcoded into the route definition, this method is only usefull in rare special cases.

### Booting of a flexy enabled request

All of the booting process is done in a [listener](https://github.com/flexy/FlexySystemBundle/blob/master/Listener/ControllerListener.php) that looks for the presence of the flexy attributes in the current request. If thoses attributes are found and valid then the current application core is booted and the normal request flow continues.