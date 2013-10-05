Deletable Service
=========================

This service is a core functionality of this framework. It's intended to be used when you want to delete an entity from your application.
In fact, the process runs in the background, you will never call the service directly. But how does it work ?

## Inside the controller

In your controller, you will have to retrieve your EntityRepository. In the parent of this object, a new method has been added : delete().
This is the method you will have to call. Let's see a bit of code :
```php
<?php
public function delete($id)
{
    $repository = $this->getRepository('MyNameSpace:Entity');
    $entity = $repository->find($id);
    $result = $repository->delete($entity);
    
    /*
     * We have now :
     * $result->isFail() / $result->isSuccess() / $result->getMessage() / $result->getErrors()
     */
}
?>
```

Yes, it's simple. First, get your entity by calling find().
Then, call delete() and it's done.

Ok, this was the easy part. Now, how do we add some restriction to our entity ? Let's say... I don't want my entity to be removed if we are in development.

## How it works

To get this result, we need to know how things work :

Inside the repository :
 - our delete() method call the "deletable service" and send a request to know if the entity is deletable or not.
 - if it's the case, the service will return a "deletable" status and the repository will delete it.
 - if it's not the case, the service will return a "fail" status and repository will send the same "fail" to our controller.

Inside the service :
 - when we receive the request, the service will go through an array of listeners defined in our configuration file.
 - one by one, each listener will return a boolean if the entity pass all requirement.
 - if one listener fail, the error is return to the service.

## Inside the listener

Knowing that, it's time for us to create a listener and bind him to our service. To create it, we must implements Flexy\SystemBundle\Lib\DeletableListener.
Also, a base implementation already exists and you can subclass it : Flexy\SystemBundle\BaseDeletableListener. We will do this :

```php
<?php

use Flexy\SystemBundle\Lib\BaseDeletableListener

class MyEntityListener extends BaseDeletableListener
{

    private $env;

    public function __construct($env)
    {
        parent::construct();
        $this->env = $env;
    }

    public function isDeletable($entity)
    {
        if ('dev' === $this->env) {
            $this->addError('This entity can\'t be deleted in dev-mode.');
        }

        return $this->validate();
    }

}
?>
```

So, we just created our listener. Now it's time to bind it with our service and we will do it with the Symfony DIC.
The services.yml in /resources/config of this bundle looks like this :
```yml
    flexy_system.deletable:
        class: %flexy_system.deletable.class%
```

To push a listener into this service, do something like that :
```yml
    mynamespacebundle.entitylistener:
        class: My\NamespaceBundle\Listener\MyEntityListener
        arguments: [ %kernel.environment% ]
        tags:
          - { name: flexy_system.deletable, entity: My\NameSpaceBundle\Entity\Entity }
```

What you have to change :
 - service name
 - class
 - arguments
 - entity in tags.


Why do we need to specify the entity ? Because when the service will go through each listener, it will cycle only to the listeners bounded to the entity.
By doing this, you don't need to check the class of $entity inside your listener. You will know exactly what kind of entity you will have.

You can also register more than one listener for the same entity if you want to have multiples checks on a same entity, or you can do all your checks in a single listener.

## What if we want to bypass the service ?

Because all repositories extends BaseEntityRepository, 2 new methods are added :
 - delete(Object $entity)
 - removeAndFlush(Object $entity)

The first method will call the "Deletable Service" whereas the second one will not.
