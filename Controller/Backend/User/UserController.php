<?php

namespace Egzakt\SystemBundle\Controller\Backend\User;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Egzakt\SystemBundle\Lib\Backend\BaseController;
use Egzakt\SystemBundle\Entity\User;
use Egzakt\SystemBundle\Form\Backend\UserType;

/**
 * User controller.
 */
class UserController extends BaseController
{

    /**
     * Lists all User entities.
     *
     * @return Response
     */
    public function indexAction()
    {
        $roles = $this->getEm()->getRepository('EgzaktSystemBundle:Role')->findWithUser();

        return $this->render('EgzaktSystemBundle:Backend/User/User:list.html.twig', array('roles' => $roles));
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @param integer $id The ID of the User to edit
     * @param Request $request The Request
     *
     * @return RedirectResponse|Response
     */
    public function editAction($id, Request $request)
    {
        $entity = $this->getEm()->getRepository('EgzaktSystemBundle:User')->find($id);

        if (!$entity) {
            $entity = new User();
        }

        $form = $this->createForm(new UserType(), $entity);

        if ($request->getMethod() == 'POST') {

            $oldPassword = $entity->getPassword();

            $form->bindRequest($request);

            if ($form->isValid()) {

                $entity->setPassword($entity->getPassword() ? md5($entity->getPassword()) : $oldPassword);
                $this->getEm()->persist($entity);
                $this->getEm()->flush();

                if ($request->request->has('save')) {
                    return $this->redirect($this->generateUrl($this->getBundleName()));
                }

                return $this->redirect($this->generateUrl($this->getBundleName() . '_edit', array(
                    'id' => $entity->getId() ? : 0
                )));
            }
        }

        return $this->render('EgzaktSystemBundle:Backend/User/User:edit.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));
    }

    /**
     * Deletes a User entity.
     *
     * @param $id
     * @return RedirectResponse|Response
     * @throws NotFoundHttpException
     */
    public function deleteAction($id)
    {
        $user = $this->getEm()->getRepository('EgzaktBackendUserBundle:User')->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        if ($this->get('request')->get('message')) {

            $connectedUser = $this->get('security.context')->getToken()->getUser();

            if ($connectedUser instanceof User && $connectedUser->getId() == $user->getId()) {
                $isDeletable = false;
                $template = $this->get('translator')->trans('You can\'t delete yourself.');
            } else {
                $isDeletable = $user->isDeletable();
                $template = $this->renderView('EgzaktBackendCoreBundle:Core:delete_message.html.twig', array(
                    'entity' => $user,
                    'truncateLength' => $this->getSectionBundle()->getParam('breadcrumbs_truncate_length')
                ));
            }

            return new Response(json_encode(array(
                'template' => $template,
                'isDeletable' => $isDeletable
            )));
        }

        $this->getEm()->remove($user);
        $this->getEm()->flush();

        return $this->redirect($this->generateUrl('EgzaktBackendUserBundle'));
    }
}
