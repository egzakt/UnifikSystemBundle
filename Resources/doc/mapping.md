Mapping
=========================

## The mapping process

The Unifik distribution use a hierarchy of sections (this is a synonym for pages). In a typical web application, each of those section must land in a given action of a controller. Usually in Symfony2, we just use routes for this purposes, but the dynamic side of the sections hierarchy makes it complicated to handle. This is where the mapping process come into play.

There are different types of mapping:

- [A section to a route](#a-section-to-a-route)
- [A section to multiple routes](#a-section-to-multiple-routes)

### A section to a route

The most common mapping type is when you want to connect a section to a single route.
Let's say you want to connect the `Products` section to the `unifik_frontend_product_list` route.

#### route definition
```yml
unifik_frontend_product_list:
    pattern:  /{sectionsPath}/list
    defaults: { _controller: "UnifikProductBundle:Frontend/Product:list" }
```

(In the following data examples, [translation support](todo) is ignored and only used columns are displayed for the sake of simplicity)

#### section table

| id            | name          | slug
| ------------- | ------------- | ------
| 15            | Products      | products

#### mapping table

| section_id    | app_id        | type   | target
| ------------- | ------------- | ------ | ---------
| 15            | 2 (frontend)  | route  | unifik_frontend_product_list

The mapping entry read as follow: Connect the `unifik_frontend_product_list` route in the `frontend` application to the `products` section.

When the router process this mapping entry, it does two important things. First, it clone and rename the route to `section_id_15` and inject the unifik request attributes. The other important processing is the expansion of the {sectionPath} placeholder that gets replaced with the path of the section. The final result look like this:

```yml
section_id_15:
   pattern:  /products/list
   defaults: { _controller: "UnifikProductBundle:Frontend/Product:list" }
```

### A section to multiple routes

Another common mapping type is to map multiple routes to a section. This scenario is siminal to the single route mapping but it introduce a new option named `mapping_alias`

Let's say you want to map those routes to the `product` section having the id 15:

```yml
unifik_product_frontend_list:
   pattern:  /{sectionsPath}
   defaults: { _controller: "UnifikProductBundle:Frontend/Product:list" }

unifik_product_frontend_category:
   pattern:  /{sectionsPath}/category/{categorySlug}
   defaults: { _controller: "UnifikProductBundle:Frontend/Product:category" }

unifik_product_frontend_detail:
   pattern:  /{sectionsPath}/{productSlug}
   defaults: { _controller: "UnifikProductBundle:Frontend/Product:detail" }
```

If you apply the previous single route mapping technique the results will be as follow:

```bash
$ app/console router:debug
...
section_id_15            ANY    ANY    ANY  /products/{productSlug}
```

See the problem? Only the last mapped route got generated. This is because of a collision in the route names. The solution is to use an option called `mapping_alias` in the route definition. This option will be appended to the route name, making them unique.

```yml
unifik_product_frontend_list:
   pattern:  /{sectionsPath}
   defaults: { _controller: "UnifikProductBundle:Frontend/Product:list" }

unifik_product_frontend_category:
   pattern:  /{sectionsPath}/category/{categorySlug}
   defaults: { _controller: "UnifikProductBundle:Frontend/Product:category" }
   options:  { mapping_alias: "category" }

unifik_product_frontend_detail:
   pattern:  /{sectionsPath}/{productSlug}
   defaults: { _controller: "UnifikProductBundle:Frontend/Product:detail" }
   options:  { mapping_alias: "detail" }
```

Now the results look like:

```bash
$ app/console router:debug
...
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

Since the `Contact Us` section has a parent, the sectionPath will be `/our-company/contact-us`.

### Manual route - without the Unifik context

If you want to add a route that will not be mapped to a section you must add the `do_not_remove` option to the route definition. Without the `do_not_remove` option, the route will be removed from the router since it is not mapped to any section as it may cause conflits with previously mapped routes.

```yml
nelmio_api_doc_bundle:
    resource: "@NelmioApiDocBundle/Resources/config/routing.yml"
    prefix:   /api/doc
    options:  { do_not_remove: true }
```

Common use cases for manual routes without context are importing routes that comes from third party bundles.

### Manual route - with the Unifik context

If you want to add a route that will have access to the unifik context without being explicitly mapped, you must use the `force_mapping` option. Using this option, the router will force the injection of every parameters of the Unifik request into the route definition. 

By default, the `force_mapping` option will force map the route to the frontend Home section. If you want to force map to another section, just set the section id as the option value.  

```yml
unifik_sitemap_frontend_xml:
   pattern:  /sitemap.xml
   defaults: { _controller: "UnifikSitemapBundle:Frontend:xml" }
   options:  { force_mapping: true }
```

Common use cases for manual routes with context are global route that needs the Unifik context to work properly like RSS feeds or sitemaps.

### Trailing route

If you want to add a route at the end of the Router, you must use the `trailing_route` option. Using this option, the router will force the injection of this route at the end of the Router.

You may also specify use the `ordering` option to specify at which order you want the route to be mapped at the end of the Router.

```yml
unifik_last_trailing_route:
   pattern:  /last/trailing/route
   defaults: { _controller: "UnifikTrailingRouteBundle:Last:index" }
   options:  { trailing_route: true, ordering: 2 }

unifik_first_trailing_route:
   pattern:  /first/trailing/route
   defaults: { _controller: "UnifikTrailingRouteBundle:First:index" }
   options:  { trailing_route: true, ordering: 1 }
```