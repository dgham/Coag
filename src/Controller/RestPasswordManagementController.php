<?php
namespace App\Controller;

use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class RestPasswordManagementController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Change user password
     *
     * @Annotations\Post("/api/profile/change-password")
     */
    public function changeAction(Request $request)
    {
        $user = $this->getUser();
        // $user->setUsername('maya');
        // $user->setEmail('maya15@gmail.com');
        //dump($user);
        //die;
        $data = $request->request->all();
        $data['current_password'] = $data['currentPassword'];
        unset($data['currentPassword']);
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_INITIALIZE, $event);
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */

        $formFactory = $this->get('new_service_name');

        $form = $formFactory->createForm([
            'csrf_protection' => false, "allow_extra_fields" => true,
        ]);
        // $form->setData($user);

        $form->submit($data);

        if (!$form->isValid()) {
            return new JsonResponse(
                [
                    'errors' => $this->getErrorMessages($form),
                    'status' => false,
                ],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        /**  @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');

        $event = new FormEvent($form, $request);
        $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_SUCCESS, $event);
        $userManager->updateUser($user);
        $response = $event->getResponse();
        if ($response == null) {

            return new JsonResponse(
                [
                    'msg' => $this->get('translator')->trans('aaaaaaaaaaaa', [], 'FOSUserBundle'),
                    'status' => true
                ],
                JsonResponse::HTTP_OK
            );
        }

        $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

        return new JsonResponse(
            [
                'msg' => $this->get('translator')->trans('change_password.flash.success', [], 'FOSUserBundle'),
                'status' => true
            ],
            JsonResponse::HTTP_OK
        );
    }
    public function getErrorMessages(\Symfony\Component\Form\Form $form)
    {
        $errors = array();

        foreach ($form->getErrors() as $key => $error) {
            if ($form->isRoot()) {
                $errors['#'][] = $error->getMessage();
            } else {
                $errors[] = $error->getMessage();
            }
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        return $errors;
    }
}
