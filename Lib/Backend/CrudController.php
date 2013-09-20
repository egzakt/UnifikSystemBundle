<?php

namespace Egzakt\SystemBundle\Lib\Backend;

use Egzakt\SystemBundle\Lib\Frontend\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

abstract class CrudController extends BaseController
{

    /**
     * Return the name of the class which is used with the Crud.
     *
     * @return string
     */
    abstract protected function getEntityClassname();

    /**
     * Return the name of the base route.
     *
     * @return string
     */
    abstract protected function getBaseRoute();

    /**
     * Initiate a delete request.
     * If this action is called with Ajax, only a check is performed.
     *
     * The "Deletable Service" will be called to check if the entity can be deleted or not.
     * If you want to add more check for an entity, you can add listeners to the service.
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse|RedirectResponse
     */
    public function deleteAction(Request $request, $id)
    {
        $deleteService = $this->get('egzakt_system.deletable');

        $entity = $this->getEm()->getRepository($this->getEntityClassname())->find($id);
        if (null === $entity) {
            $this->createNotFoundException('Entity not found : '.$this->getEntityClassname().' with ID : '.$id);
        }

        if ($request->isXMLHttpRequest()) {
            $result = $deleteService->checkDeletable($entity);
            $output = $result->toArray();
            $output['template'] = $this->renderView('EgzaktSystemBundle:Backend/Core:delete_message.html.twig',
                array(
                    'entity' => $entity,
                    'result' => $result
                )
            );

            return new JsonResponse($output);
        }

        $result = $deleteService->checkDeletable($entity);
        if ($result->isSuccess()) {
            $this->getEm()->remove($entity);
            $this->getEm()->flush();

            $this->addFlash('success', $this->get('translator')->trans(
                '%entity% has been deleted.',
                array('%entity%' => $entity)
            ));

            return $this->redirect($this->generateUrl($this->getBaseRoute(), $entity->getRouteParams()));
        }

        return $this->redirect($this->generateUrl($this->getBaseRoute(), $entity->getRouteParams()));
    }

    /**
     * Initiate a reorder request.
     *
     * @param  Request      $request
     * @return JsonResponse
     */
    public function orderAction(Request $request)
    {
        $i = 0;
        $elements = explode(';', trim($request->get('elements'), ';'));

        foreach ($elements as $element) {

            $element = explode('_', $element);
            $entity = $this->getEm()->getRepository($this->getEntityClassName())->find($element[1]);

            if ($entity) {
                $entity->setOrdering(++$i);
                $this->getEm()->persist($entity);
            }

            $this->getEm()->flush();
        }

        return new JsonResponse('');
    }
}