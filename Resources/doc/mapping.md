Mapping
=========================

## The mapping process

The Flexy distribution use a hierarchy of sections (this is a synonym for pages). In a typical web application, each of those section must land in a given action of a controller. Usually in Symfony2, we just use routes for this purposes, but the dynamic side of the sections hierarchy makes it complicated to handle. This is where the mapping process come into play.

There are different types of mapping:

- [A section to a route](#a-section-to-a-route)
- [A section to multiple routes](#a-section-to-multiple-routes)

### A section to a route

The most common mapping type is when you want to connect a section to a single route.
Let's say you want to connect the `Products` section to the `flexy_frontend_product_list` route.

#### route definition
```yml
flexy_frontend_product_list:
    pattern:  /{sectionsPath}/list
    defaults: { _controller: "FlexyProductBundle:Frontend/Product:list" }
```

(In the following data examples, [translation support](todo) is ignored and only used columns are displayed for the sake of simplicity)

#### section table

| id            | name          | slug
| ------------- | ------------- | ------
| 15            | Products      | products

#### mapping table

| section_id    | app_id        | type   | target
| ------------- | ------------- | ------ | ---------
| 15            | 2 (frontend)  | route  | flexy_frontend_product_list

The mapping entry read as follow: Connect the `flexy_frontend_product_list` route in the `frontend` application to the `products` section.

When the router process this mapping entry, it does two important things. First, it clone and rename the route to `section_id_15` and inject the flexy request attributes. The other important processing is the expansion of the {sectionPath} placeholder that gets replaced with the path of the section. The final result look like this:

```yml
section_id_15:
   pattern:  /products/list
   defaults: { _controller: "FlexyProductBundle:Frontend/Product:list" }
```

### A section to multiple routes

Another common mapping type is to map multiple routes to a section. This scenario is siminal to the single route mapping but it introduce a new option named `mapping_alias`

Let's say you want to map those routes to the `product` section having the id 15:

```yml
flexy_product_frontend_list:
   pattern:  /{sectionsPath}
   defaults: { _controller: "FlexyProductBundle:Frontend/Product:list" }

flexy_product_frontend_category:
   pattern:  /{sectionsPath}/category/{categorySlug}
   defaults: { _controller: "FlexyProductBundle:Frontend/Product:category" }

flexy_product_frontend_detail:
   pattern:  /{sectionsPath}/{productSlug}
   defaults: { _controller: "FlexyProductBundle:Frontend/Product:detail" }
```

If you apply the previous single route mapping technique the results will be as follow:

```bash
$ app/console router:debug
...
section_id_15            ANY    ANY    ANY  /products/{productSlug}
```

See the problem? Only the last mapped route got generated. This is because of a collision in the route names. The solution is to use an option called `mapping_alias` in the route definition. This option will be appended to the route name, making them unique.

```yml
flexy_product_frontend_list:
   pattern:  /{sectionsPath}
   defaults: { _controller: "FlexyProductBundle:Frontend/Product:list" }

flexy_product_frontend_category:
   pattern:  /{sectionsPath}/category/{categorySlug}
   defaults: { _controller: "FlexyProductBundle:Frontend/Product:category" }
   options:  { mapping_alias: "category" }

flexy_product_frontend_detail:
   pattern:  /{sectionsPath}/{productSlug}
   defaults: { _controller: "FlexyProductBundle:Frontend/Product:detail" }
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
