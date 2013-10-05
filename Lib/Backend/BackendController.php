<?php

namespace Flexy\SystemBundle\Lib\Backend;

use Flexy\SystemBundle\Lib\Backend\BaseController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class BackendController extends BaseController
{
    /**
     * Checks whether an entity can be deleted or not.
     *
     * @param mixed $entity
     *
     * @return array
     *
     * @throws NotFoundHttpException
     */
    protected function checkDeleteEntity($entity)
    {
        if (null === $entity) {
            throw new NotFoundHttpException();
        }

        $result = $this->checkDeletable($entity);
        $output = $result->toArray();
        $output['template'] = $this->renderView('FlexySystemBundle:Backend/Core:delete_message.html.twig',
            array(
                'entity' => $entity,
                'result' => $result
            )
        );

        return $output;
    }

    /**
     * Performs the delete action on an entity.
     *
     * The "Deletable Service" will be called to check if the entity can be deleted or not.
     * If you want to add more check for an entity, you can add listeners to the service.
     *
     * @param mixed $entity
     *
     * @throws NotFoundHttpException
     */
    protected function deleteEntity($entity)
    {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find this ' . get_class($this) . ' entity.');
        }

        $result = $this->checkDeletable($entity);
        if ($result->isSuccess()) {
            $this->addFlashSuccess($this->get('translator')->trans(
                '%entity% has been deleted.',
                array('%entity%' => $entity)
            ));

            $this->getEm()->remove($entity);
            $this->getEm()->flush();
        } else {
            $this->addFlashError($result->getErrors());
        }
    }

    /**
     * Sets the order on entities
     *
     * @param Request $request
     * @param $repository
     */
    protected function orderEntities(Request $request, $repository)
    {
        if ($request->isXmlHttpRequest()) {

            $i = 0;
            $elements = explode(';', trim($request->get('elements'), ';'));

            foreach ($elements as $element) {

                $element = explode('_', $element);
                $entity = $repository->find($element[1]);

                if ($entity) {
                    $entity->setOrdering(++$i);
                    $this->getEm()->persist($entity);
                }

                $this->getEm()->flush();
            }
        }
    }
}
