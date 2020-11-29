<?php

namespace App\Controller;

use DateTime;
use Exception;
use App\Entity\User;
use App\Entity\Device;
use App\Entity\UserType;
use App\Entity\Notification;
use FOS\RestBundle\View\View;
use App\Entity\DoctorAssignement;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;

class RestApiNotificationController extends FOSRestController
{
    /**
     * @Rest\Get("/api/notification", name ="api_notifications")
     * @Rest\View(serializerGroups={"users"})
     */
    public function index()
    {
        $user = $this->getUser();
        $data = array(
            'id' => $user->getId(),
        );
        if (($user->getUserType() === UserType::TYPE_DOCTOR) || ($user->getUserType() === UserType::TYPE_PATIENT)) {
            $repository = $this->getDoctrine()->getRepository(Notification::class);
            $notification = $repository->findBy(array('recived_user' => $data, 'removed' => false), array('created_at' => 'DESC'));
            if (!empty($notification)) {
                return View::create($notification, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('No data found', JsonResponse::HTTP_OK, []);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }
    /**
     * @Rest\Get("/api/notification/{id}", name ="search_notifications")
     * @Rest\View(serializerGroups={"users"})
     */
    public function searchNote($id)
    {
        $user = $this->getUser();
        if (($user->getUserType() === UserType::TYPE_DOCTOR) || ($user->getUserType() === UserType::TYPE_PATIENT)) {
            $repository = $this->getDoctrine()->getRepository(Notification::class);
            $notification = $repository->findOneBy(array('id' => $id, 'recived_user' => $user->getId(), 'removed' => false));
            if (!is_null($notification)) {
                return View::create($notification, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }

    /**
     *
     * @Rest\Post("/api/notification", name ="post_notifications")
     * @Rest\View(serializerGroups={"users"})
     * @return array
     */
    public function postNotificationAction(Request $request)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_PATIENT) {
            try {
                $data = $request->request->all();
                $notification = new Notification();
                $deviceid = $request->request->get('device_id');
                $typedevice = gettype($deviceid);
                if (isset($deviceid)) {
                    if ($typedevice == "integer") {
                        $repository = $this->getDoctrine()->getRepository(Device::class);
                        $pushDevice = $repository->findOneBy(array('id' => $data['device_id'], 'created_by' => $user, 'removed' => false, 'enabled' => true));
                        if (!empty($pushDevice)) {
                            $notification->setPushDeviceId($pushDevice);
                        } else {
                            return View::create('error !Not the correct device', JsonResponse::HTTP_NOT_FOUND);
                        }
                    } else {
                        return View::create('device_id should be integer ', JsonResponse::HTTP_BAD_REQUEST, []);
                    }
                } else {
                    return View::create('Missing device!', JsonResponse::HTTP_BAD_REQUEST, []);
                }
                $typetitle = gettype($data['title']);
                if (isset($data['title'])) {
                    if ($typetitle == "string") {
                        $notification->setTitle($data['title']);
                    } else {
                        return View::create('title of notification must be string!!', JsonResponse::HTTP_BAD_REQUEST, []);
                    }
                } else {
                    return View::create('missing title!!', JsonResponse::HTTP_BAD_REQUEST, []);
                }
                $typebody = gettype($data['body']);
                if (isset($data['body'])) {
                    if ($typebody == "string") {
                        $notification->setBody($data['body']);
                    } else {
                        return View::create('body of notification must be string!!', JsonResponse::HTTP_BAD_REQUEST, []);
                    }
                } else {
                    return View::create('missing body!!', JsonResponse::HTTP_BAD_REQUEST, []);
                }
                $typedata = gettype($data['data']);
                if (isset($data['data'])) {
                    if ($typedata == "string") {
                        $notification->setData($data['data']);
                    } else {
                        return View::create('data of notification must be string!!', JsonResponse::HTTP_BAD_REQUEST, []);
                    }
                }
                if (isset($data['type'])) {
                    $notification->setType($data['type']);
                }
                $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
                $doctorassignement = $repository->findOneBy(array('id_patient' => $user->getId(),'status' => 'Accepted', 'removed' => false,'enabled'=>true));
            if (!empty($doctorassignement)){

                $notification->setRecivedUser($doctorassignement->getIdDoctor());
            }
            else{
                return View::create('please make sure you have a doctor that assigned you to make post notification :) ', JsonResponse::HTTP_BAD_REQUEST, []);
            }
            //     $createdid = $request->request->get('created_by');
            //     $typecretaedid= gettype($createdid);
            //     if (isset($createdid)) {
            //         if ($typecretaedid == "integer") {
            //     $repository = $this->getDoctrine()->getRepository(User::class);
            //     $userid = $repository->findOneBy(array('id' => $createdid, 'remove' => false, 'enabled' => true));
            // } else {
            //     return View::create('created_by of notification must be int!!', JsonResponse::HTTP_BAD_REQUEST, []);
            //         }
            //     }else{
            //         return View::create('created_by is missing !', JsonResponse::HTTP_BAD_REQUEST, []);
            //     }
              
                $notification->setEnabled(true);
                $notification->setReaded(false);
                $notification->setRemoved(false);
                $notification->setCreatedBy($user);
                $notification->setCreatedAt(new \DateTime());
                $em = $this->getDoctrine()->getManager();
                $em->persist($notification);
                $em->flush();
                return View::create($notification, JsonResponse::HTTP_CREATED);
            } catch (Exception $e) {
                return View::create($e->getMessage(), JsonResponse::HTTP_BAD_REQUEST, []);
            }
        }
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            try {
                $data = $request->request->all();
                $notification = new Notification();
                $deviceid = $request->request->get('device_id');
                $typedevice = gettype($deviceid);
                if (isset($deviceid)) {
                    if ($typedevice == "integer") {
                        $repository = $this->getDoctrine()->getRepository(Device::class);
                        $pushDevice = $repository->findOneBy(array('id' => $data['device_id'], 'created_by' => $user, 'removed' => false, 'enabled' => true));
                        if (!empty($pushDevice)) {
                            $notification->setPushDeviceId($pushDevice);
                        } else {
                            return View::create('error !Not the correct device', JsonResponse::HTTP_NOT_FOUND);
                        }
                    } else {
                        return View::create('device_id should be integer ', JsonResponse::HTTP_BAD_REQUEST, []);
                    }
                } else {
                    return View::create('Missing device!', JsonResponse::HTTP_BAD_REQUEST, []);
                }
                $typetitle = gettype($data['title']);
                if (isset($data['title'])) {
                    if ($typetitle == "string") {
                        $notification->setTitle($data['title']);
                    } else {
                        return View::create('title of notification must be string!!', JsonResponse::HTTP_BAD_REQUEST, []);
                    }
                } else {
                    return View::create('missing title!!', JsonResponse::HTTP_BAD_REQUEST, []);
                }
                $typebody = gettype($data['body']);
                if (isset($data['body'])) {
                    if ($typebody == "string") {
                        $notification->setBody($data['body']);
                    } else {
                        return View::create('body of notification must be string!!', JsonResponse::HTTP_BAD_REQUEST, []);
                    }
                } else {
                    return View::create('missing body!!', JsonResponse::HTTP_BAD_REQUEST, []);
                }
                $typedata = gettype($data['data']);
                if (isset($data['data'])) {
                    if ($typedata == "string") {
                        $notification->setData($data['data']);
                    } else {
                        return View::create('data of notification must be string!!', JsonResponse::HTTP_BAD_REQUEST, []);
                    }
                }
                if (isset($data['type'])) {
                    $notification->setType($data['type']);
                }
                $createdid = $request->request->get('recived_user');
                $typecretaedid= gettype($createdid);
                if (isset($createdid)) {
                    if ($typecretaedid == "integer") {
                $repository = $this->getDoctrine()->getRepository(User::class);
                $userid = $repository->findOneBy(array('id' => $createdid, 'remove' => false, 'enabled' => true));
                if (!empty($userid)){
                    $notification->setRecivedUser($userid);
                }
                else{
                    return View::create('error recived_user !', JsonResponse::HTTP_BAD_REQUEST, []);
                }
            } else {
                return View::create('recived_user of notification must be int!!', JsonResponse::HTTP_BAD_REQUEST, []);
                    }
                }else{
                    return View::create('recived_user is missing !', JsonResponse::HTTP_BAD_REQUEST, []);
                }
               
                $notification->setEnabled(true);
                $notification->setReaded(false);
                $notification->setRemoved(false);
                $notification->setCreatedBy($user);
                
                $notification->setCreatedAt(new \DateTime());
                $em = $this->getDoctrine()->getManager();
                $em->persist($notification);
                $em->flush();
                return View::create($notification, JsonResponse::HTTP_CREATED);
            } catch (Exception $e) {
                return View::create($e->getMessage(), JsonResponse::HTTP_BAD_REQUEST, []);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN);
        }
    }
    
    /**
     * @Rest\Patch("/api/notification/{id}", name ="patch_notifications")
     * @Rest\View(serializerGroups={"users"})
     *
     * @return array
     */
    public function patchNotificationActionWithId(Request $request, $id)
    {
        $user = $this->getUser();
        if (($user->getUserType() === UserType::TYPE_PATIENT) || ($user->getUserType() === UserType::TYPE_DOCTOR)) {
            $data = $request->request->all();
            $repository = $this->getDoctrine()->getRepository(Notification::class);
            $notification = $repository->findOneBy(array('id' => $id, 'recived_user' => $this->getUser()->getId(), 'removed' => false, "readed" => false));
            if (!is_null($notification)) {
                $readed = $request->request->get('readed');
                if (isset($readed)) {
                    $notification->setReaded($readed);
                } else {
                    return View::create('notification read missing', JsonResponse::HTTP_BAD_REQUEST, []);
                }
                $enabled = $request->request->get('enabled');
                if (isset($enabled)) {
                    $notification->setEnabled($enabled);
                }
                $notification->setUpdatedBy($user);
                $notification->setUpdatedAt(new \DateTime());
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                return View::create('notification updated', JsonResponse::HTTP_OK, []);
            } else {
                return View::create('Not Found', JsonResponse::HTTP_NOT_FOUND);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }
    /**
     * @Rest\Get("/api/notReadedNotification", name ="readed_notifications")
     * @Rest\View(serializerGroups={"users"})
     */
    public function readed()
    {
        $user = $this->getUser();
        $data = array(
            'id' => $user->getId(),
        );

        if (($user->getUserType() === UserType::TYPE_DOCTOR) || ($user->getUserType() === UserType::TYPE_PATIENT)) {
            $repository = $this->getDoctrine()->getRepository(Notification::class);
            $notification = $repository->findBy(array('recived_user' => $data, 'readed' => false));

            if (!is_null($notification)) {
                $readed = count($notification);
                $notReaded = array(
                    'Num_notificationNotreaded' => $readed,
                );
                return View::create($notReaded, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
            }
        }
    }
}