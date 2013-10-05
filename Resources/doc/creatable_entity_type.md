CreatableEntity Type
=========================

The CreatableEntity Type extends the Entity Type and allows to quickly add a new item in the choices list via an ajax dialog.
It works with all three choices types : select box, checkboxes and radio buttons.

## How to use

Let's take for example the FlexyNewsBundle, in which an article can belong to many categories.

### Controller
In the controller, you will need to add a `quickCreateAction`:

```php
<?php
// Flexy/NewsBundle/Controller/Backend/CategoryController.php

/**
 * Simplified form to quickly create a new category via an AJAX popup
 *
 * @param Request $request
 *
 * @return JsonResponse
 */
public function quickCreateAction(Request $request)
{
    $category = $this->initEntity(new Category());

    $form = $this->createForm(new QuickCreateCategoryType(), $category, array('action' => $this->generateUrl('flexy_news_backend_category_quick_create')));

    if ('POST' == $request->getMethod()) {

        $form->submit($request);

        if ($form->isValid()) {

            $this->getEm()->persist($category);
            $this->getEm()->flush();

            return new JsonResponse(array(
                'createSuccess' => true,
                'entity' => array(
                    'id' => $category->getId(),
                    'name' => (string) $category
                )
            ));
        }
    }

    return new JsonResponse(array(
        'response' => $this->renderView('FlexySystemBundle:Backend/Core:quick_create.html.twig', array(
            'category' => $category,
            'form' => $form->createView()
        ))
    ));
}
```

### Routing
Then, you will need to create a route that points to that action:

``` yaml
# Flexy/NewsBundle/Resources/config/routing_backend.yml
flexy_news_backend_category_quick_create:
    pattern:  /category/{sectionId}/quick-create
    defaults: { _controller: "FlexyNewsBundle:Backend/Category:quickCreate" }
```

### Create a simplified form type

This part is optional, but you'd usually want to show only the most important fields in the quick create form.
To do that, you need to create a new form type that extends your base type and just call remove on the fields you don't want.

```php
<?php
// Flexy/NewsBundle/Form/Backend/QuickCreateCategoryType.php

class QuickCreateCategoryType extends CategoryType
{
    /**
     * Build Form
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('translation', new QuickCreateCategoryTranslationType());
    }
}
```

```php
<?php
// Flexy/NewsBundle/Form/Backend/QuickCreateCategoryTranslationType.php

class QuickCreateCategoryTranslationType extends CategoryTranslationType
{
    /**
     * Build Form
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->remove('slug');
    }
}
```

### Change the field type
Finally, in your form type, locate your entity field and change its type for `creatable_entity`. You will also have to provide the `quick_create_route` you created earlier.
```php
<?php
// Flexy/NewsBundle/Form/Backend/ArticleType.php

$builder->add('categories', 'creatable_entity', array(
    'class' => 'Flexy\NewsBundle\Entity\Category',
    'required' => false,
    'expanded' => true,
    'multiple' => true,
    'quick_create_route' => 'flexy_news_backend_category_quick_create'
));
```
