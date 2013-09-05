<?php

namespace Egzakt\SystemBundle\Controller\Backend\User;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use Egzakt\SystemBundle\Lib\Backend\BaseController;
use Egzakt\SystemBundle\Entity\User;
use Egzakt\SystemBundle\Form\Backend\UserType;
use Egzakt\SystemBundle\Entity\Role;

/**
 * User controller.
 */
class UserController extends BaseController
{
    /**
     * @var bool
     */
    protected $isDeveloper;

    /**
     * Init
     */
    public function init()
    {
        parent::init();

        // Check if the current User has the privileges
        if (!$this->get('security.context')->isGranted('ROLE_BACKEND_ADMIN')) {
            throw new AccessDeniedHttpException();
        }

        $this->createAndPushNavigationElement('Users', 'egzakt_system_backend_user');

        $this->isDeveloper = $this->get('security.context')->isGranted('ROLE_DEVELOPER');
    }

    /**
     * Lists all User entities.
     *
     * @return Response
     */
    public function indexAction()
    {
        if (!$this->isDeveloper) {
            $roles = $this->getEm()->getRepository('EgzaktSystemBundle:Role')->findAllExcept(array('ROLE_DEVELOPER', 'ROLE_BACKEND_ACCESS'));
        } else {
            $roles = $this->getEm()->getRepository('EgzaktSystemBundle:Role')->findAllExcept('ROLE_BACKEND_ACCESS');
        }

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

        $this->pushNavigationElement($user);

        $form = $this->createForm(new UserType(), $user, array(
            'validation_groups' => $user->getId() ? 'edit' : 'new',
            'self_edit' => $user == $this->getUser(),
            'developer' => $this->isDeveloper
        ));

        if ($request->getMethod() == 'POST') {

            $previousEncodedPassword = $user->getPassword();

            $form->submit($request);

            if ($form->isValid()) {

                // All Users are automatically granted the ROLE_BACKEND_ACCESS Role
                $backendAccessRole = $this->getEm()->getRepository('EgzaktSystemBundle:Role')->findOneBy(array('role' => 'ROLE_BACKEND_ACCESS'));
                if (!$backendAccessRole) {
                    $backendAccessRole = new Role();
                    $backendAccessRole->setRole('ROLE_BACKEND_ACCESS');
                    $this->getEm()->persist($backendAccessRole);
                }

                $user->addRole($backendAccessRole);

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

                $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans(
                    '%entity% has been updated.',
                    array('%entity%' => $user))
                );

                if ($request->request->has('save')) {
                    return $this->redirect($this->generateUrl('egzakt_system_backend_user'));
                }

                return $this->redirect($this->generateUrl('egzakt_system_backend_user_edit', array(
                    'id' => $user->getId()
                )));
            } else {
                $this->get('session')->getFlashBag()->add('error', 'Some fields are invalid.');
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

            // Call the translator before we flush the entity so we can have the real __toString()
            $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans(
                '%entity% has been deleted.',
                array('%entity%' => $user != '' ? $user : $user->getEntityName()))
            );

            $this->getEm()->remove($user);
            $this->getEm()->flush();
        }

        return $this->redirect($this->generateUrl('egzakt_system_backend_user'));
    }
}
