EgzaktSystemBundle - Routing concept
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

### Creating a egzakt enabled request

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

## The mapping process

The Egzakt distribution use a hierarchy of sections (this is a synonym for pages). In a typical web application, each of those section must land in a given action of a controller. Usually in Symfony2, we just use routes for this purposes, but the dynamic side of the sections hierarchy makes it complicated to handle. This is where the mapping process come into play.

### A section to single route

The most common mapping type is when you want to connect a section to a single route. Let's say you want to connect the following route

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

When the router process this mapping entry it does two important things. First, it clone and rename the route to `section_id_15` and inject the egzakt request attributes. The other important processing is the expansion of the {sectionPath} placeholder that gets replaced with the path of the section. The final result look like this:

```yml
section_id_15:
   pattern:  /products/list
   defaults: { _controller: "EgzaktProductBundle:Frontend/Product:list" }
```

### A section to multiple routes

Another common mapping type is to map multiple route to a section. This scenario is siminal to the single route mapping but it introduce a new option named `mapping_alias`

Let's say you want to map those routes to the `product` section having the id 15:

```yml
/{sectionsPath}
/{sectionsPath}/category/{categorySlug}
/{sectionsPath}/{productSlug}
```

If you apply the previous single route mapping technique the results will be as follow:

```bash
$ app/console router:debug

section_id_15       ANY    ANY    ANY  /products/{productSlug}
```

See the problem? Only the last mapped route got generated. This is because of a collision in the route names. The solution is to use an option called `mapping_alias` in the route definition. This option will be appended to the route name, making them unique.

```yml
egzakt_product_frontend_list:
   pattern:  /{sectionsPath}
   defaults: { _controller: "EgzaktProductBundle:Frontend/Product:list" }
   
egzakt_product_frontend_category:
   pattern:  /{sectionsPath}/category/{categorySlug}
   defaults: { _controller: "EgzaktProductBundle:Frontend/Product:category" }
   option:   { mapping_alias: "category" }
   
egzakt_product_frontend_detail:
   pattern:  /{sectionsPath}/{productSlug}
   defaults: { _controller: "EgzaktProductBundle:Frontend/Product:detail" }  
   option:   { mapping_alias: "detail" }
```

Now the results look like:

```bash
$ app/console router:debug
section_id_15            ANY    ANY    ANY  /products/{productSlug}
section_id_15_category   ANY    ANY    ANY  /products/category/{categorySlug}
section_id_15_detail     ANY    ANY    ANY  /products/details/{categorySlug}
```

### The {sectionPath} placeholder

When a route is mapped to a section, a special placeholder if used to inject the path of the mapped section. The path of a section is made of the section slug and prefixed with every parents slugs.

For example given these values: 

| id  | parent_id | name          | slug  
| --- | --------- | ------------- | ------
| 3   | NULL      | Our Company   | our-company 
| 4   | 3         | Contact Us    | contact-us 

As the `Contact Us` section has a parent, the sectionPath will be `/our-company/contact-us`.


