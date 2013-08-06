EgzaktSystemBundle - Routing concept
=========================

The routing component is a key part of the Egzakt distribution. This component does three main things:

- Generate egzakt enabled routes based on entries in the mapping table.
- Inject egzakt attributes into mapped routes.
- Expand the {sectionPath} placeholder. 

## What is a egzakt enabled request ?

A egzakt enabled request is a normal symfony request with some specials attributes injected in the route that will trigger the bootstapping of all egzakt related services.

### Attributes structure

There is two key attributes, the `_egzaktEnabled` and the `_egzaktRequest`.
Here is a typical request attributes structure:

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

### Creating a egzakt enabled request

Attributes are automatically injected when a route is mapped to a section. The mapping process is covered [here](#todo).
Although this is not the recommanded method, you can manually create a compatible request straight from any routes definitions:

```yml
egzakt_product_detail:
   pattern:  /{sectionsPath}/{productSlug}
   defaults:
      _controller: "EgzaktSystemBundle:Frontend/Text:index"
      _egzaktEnabled: true
      _egzaktRequest:
         sectionId: 1
         appId: 2
         appName: 'frontend'
         appPrefix: ''
```

The drawback of this method is that every parameters are hardcoded into the route definition, this method is only usefull in rare special cases.

### Booting of a egzakt enabled request

The booting process is done in a [listener](https://github.com/egzakt/EgzaktSystemBundle/blob/master/Listener/ControllerListener.php) that looks for the presence of the egzakt attributes in the current request. If thoses attributes are found and are valid, then the current application core is booted and the normal request flow continues.

## The mapping process

... TODO different mapping type

The most common mapping type is when you want to connect a section to a specific route. Let's say with want to connect the following route

#### route definition
```yml
egzakt_frontend_product_list:
    pattern:  /{sectionsPath}/list
    defaults: { _controller: "EgzaktProductBundle:Frontend/Product:list" }
```

Values in the database:
(translation support is ignored for the sake of simplicity)

#### section table

| id            | name          | slug  
| ------------- | ------------- | ------
| 15            | Products      | products 

#### app table

| id   | name     
| ---- | -------
| 2    | frontend

#### mapping table

| section_id    | app_id        | type   | target   
| ------------- | ------------- | ------ | ---------
| 15            | 2             | route  | egzakt_frontend_product_list

The mapping entry read as follow: Connect the `egzakt_frontend_product_list` route which belong to the `frontend` application to the `products` section.

When the router process this mapping entry it does two important things. First, it clone and rename the route to `section_id_15` and inject the egzakt request attributes. The other important processing is the expansion of the {sectionPath} placeholder that gets replaced with the section slug. The final result look like this:

```yml
section_id_15:
   pattern:  /products/list
   defaults: { _controller: "EgzaktProductBundle:Frontend/Product:list" }
```


