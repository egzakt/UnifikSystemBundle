<?php

namespace Flexy\SystemBundle\Controller\Backend\User;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use Flexy\SystemBundle\Lib\Backend\BackendController;
use Flexy\SystemBundle\Entity\User;
use Flexy\SystemBundle\Form\Backend\UserType;
use Flexy\SystemBundle\Entity\Role;

/**
 * User controller.
 */
class UserController extends BackendController
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

        $this->createAndPushNavigationElement('Users', 'flexy_system_backend_user');

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
            $roles = $this->getEm()->getRepository('FlexySystemBundle:Role')->findAllExcept(array('ROLE_DEVELOPER', 'ROLE_BACKEND_ACCESS'));
        } else {
            $roles = $this->getEm()->getRepository('FlexySystemBundle:Role')->findAllExcept('ROLE_BACKEND_ACCESS');
        }

        return $this->render('FlexySystemBundle:Backend/User/User:list.html.twig', array('roles' => $roles));
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @param integer $id      The ID of the User to edit
     * @param Request $request The Request
     *
     * @return RedirectResponse|Response
     */
    public function editAction($id, Request $request)
    {
        $user = $this->getEm()->getRepository('FlexySystemBundle:User')->find($id);

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
                $backendAccessRole = $this->getEm()->getRepository('FlexySystemBundle:Role')->findOneBy(array('role' => 'ROLE_BACKEND_ACCESS'));
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

                // Forcing the selected locale by the user
                if ($locale = $user->getLocale()) {
                    $request->setLocale($locale);
                }

                $this->getEm()->persist($user);
                $this->getEm()->flush();

                $this->addFlashSuccess($this->get('translator')->trans(
                    '%entity% has been saved.',
                    array('%entity%' => $user))
                );

                if ($request->request->has('save')) {
                    return $this->redirect($this->generateUrl('flexy_system_backend_user'));
                }

                return $this->redirect($this->generateUrl('flexy_system_backend_user_edit', array(
                    'id' => $user->getId()
                )));
            } else {
                $this->addFlashError('Some fields are invalid.');
            }
        }

        return $this->render('FlexySystemBundle:Backend/User/User:edit.html.twig', array(
            'user' => $user,
            'form' => $form->createView()
        ));
    }

    /**
     * Check if we can delete a user.
     *
     * @param Request $request
     * @param $id
     *
     * @return JsonResponse
     */
    public function checkDeleteAction(Request $request, $id)
    {
        $user = $this->getEm()->getRepository('FlexySystemBundle:User')->find($id);
        $output = $this->checkDeleteEntity($user);

        return new JsonResponse($output);
    }

    /**
     * Delete a user
     *
     * @param $id
     *
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        $user = $this->getEm()->getRepository('FlexySystemBundle:User')->find($id);
        $this->deleteEntity($user);

        return $this->redirect($this->generateUrl('flexy_system_backend_user'));
    }
}
