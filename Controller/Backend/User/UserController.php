<?php

namespace Egzakt\SystemBundle\Controller\Backend\User;

use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @param integer $id      The ID of the User to edit
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
     * Check if we can delete a user.
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws NotFoundHttpException
     */
    public function checkDeleteAction(Request $request, $id)
    {

        $userRepo = $this->getEm()->getRepository('EgzaktSystemBundle:User');
        $entity = $userRepo->find($id);

        if (null === $entity) {
            throw new NotFoundHttpException();
        }

        $result = $this->checkDeletable($entity);
        $output = $result->toArray();
        $output['template'] = $this->renderView('EgzaktSystemBundle:Backend/Core:delete_message.html.twig',
            array(
                'entity' => $entity,
                'result' => $result
            )
        );

        return new JsonResponse($output);

    }

    /**
     * Delete a user
     *
     * @param $id
     * @return RedirectResponse
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Exception
     */
    public function deleteAction($id)
    {
        $userRepo = $this->getEm()->getRepository('EgzaktSystemBundle:User');
        $entity = $userRepo->find($id);

        if (null === $entity) {
            throw new NotFoundHttpException();
        }

        // Don't delete some roles
        $result = $this->checkDeletable($entity);
        if ($result->isSuccess()) {
            $this->getEm()->remove($entity);
            $this->getEm()->flush();

            $this->addFlash('success', $this->get('translator')->trans(
                '%entity% has been deleted.',
                array('%entity%' => $entity)
            ));
            $this->get('egzakt_system.router_invalidator')->invalidate();
        } else {
            $this->addFlash('error', $result->getErrors());
        }

        return $this->redirect($this->generateUrl('egzakt_system_backend_user'));

    }
}
