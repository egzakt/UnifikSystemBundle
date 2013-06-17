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
        $user = $this->getEm()->getRepository('EgzaktSystemBundle:User')->find($id);

        if (!$user) {
            $user = new User();
            $user->setContainer($this->container);
        }

        $form = $this->createForm(new UserType(), $user, array(
            'validation_groups' => $user->getId() ? 'edit' : 'new',
            'self_edit' => $user == $this->getUser()
        ));

        if ($request->getMethod() == 'POST') {

            $previousEncodedPassword = $user->getPassword();

            $form->submit($request);

            if ($form->isValid()) {

                // New password set
                if ($form->get('password')->getData()) {
                    $encoder = $this->get('security.encoder_factory')->getEncoder($user);
                    $encodedPassword = $encoder->encodePassword($user->getPassword(), $user->getSalt());
                } else {
                    $encodedPassword = $previousEncodedPassword;
                }

                $user->setPassword($encodedPassword);

                $this->getEm()->persist($user);
                $this->getEm()->flush();

                if ($request->request->has('save')) {
                    return $this->redirect($this->generateUrl('egzakt_system_backend_user'));
                }

                return $this->redirect($this->generateUrl('egzakt_system_backend_user_edit', array(
                    'id' => $user->getId()
                )));
            }
        }

        return $this->render('EgzaktSystemBundle:Backend/User/User:edit.html.twig', array(
            'user' => $user,
            'form' => $form->createView()
        ));
    }

    /**
     * Deletes a User entity.
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     * @throws NotFoundHttpException
     */
    public function deleteAction(Request $request, $id)
    {
        $user = $this->getEm()->getRepository('EgzaktSystemBundle:User')->find($id);
        $connectedUser = $this->getUser();

        if (!$user) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        if ($request->get('message')) {

            if ($connectedUser instanceof User && $connectedUser->getId() == $user->getId()) {
                $isDeletable = false;
                $template = $this->get('translator')->trans('You can\'t delete yourself.');
            } else {
                $isDeletable = $user->isDeletable();
                $template = $this->renderView('EgzaktSystemBundle:Backend/Core:delete_message.html.twig', array(
                    'entity' => $user
                ));
            }

            return new Response(json_encode(array(
                'template' => $template,
                'isDeletable' => $isDeletable
            )));
        }

        if ($connectedUser instanceof User && $connectedUser->getId() != $user->getId()) {
            $this->getEm()->remove($user);
            $this->getEm()->flush();
        }

        return $this->redirect($this->generateUrl('egzakt_system_backend_user'));
    }
}
