Routing
=========================

The routing component is a key part of the Egzakt distribution. This component does three main things:

- Generate egzakt enabled routes based on entries in the mapping table.
- Inject egzakt attributes into mapped routes.
- Expand the {sectionPath} placeholder. 

## What is a egzakt enabled request ?

A egzakt enabled request is a normal symfony request with some specials attributes injected into the route that will trigger the bootstapping of all egzakt related services.

### Attributes structure

There is two key attributes, the `_egzaktEnabled` and the `_egzaktRequest`.
Here is a typical structure:

```php
array(
     '_egzaktEnabled': true,
     '_egzaktRequest': array(
         'sectionId' => 1,
         'appId' => 1,
         'appPrefix' => 'admin',
         'appName' => 'backend'
     )
)
```

### Creating an egzakt enabled request

Attributes are automatically injected when a route is mapped to a section. The mapping process is covered [here](#the-mapping-process).

Although this is not the recommanded method, you can manually create a compatible request straight from any routes definitions:

```yml
egzakt_product_detail:
   pattern:  /{sectionsPath}/{productSlug}
   defaults:
      _controller: "EgzaktProductBundle:Frontend/Product:detail"
      _egzaktEnabled: true
      _egzaktRequest:
         sectionId: 1
         appId: 2
         appName: 'frontend'
         appPrefix: ''
```

The drawback of this method is that every parameters are hardcoded into the route definition, this method is only usefull in rare special cases.

### Booting of a egzakt enabled request

All of the booting process is done in a [listener](https://github.com/egzakt/EgzaktSystemBundle/blob/master/Listener/ControllerListener.php) that looks for the presence of the egzakt attributes in the current request. If thoses attributes are found and valid then the current application core is booted and the normal request flow continues.