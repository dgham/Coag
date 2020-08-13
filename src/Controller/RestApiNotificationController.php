<?php

namespace App\Controller;

use DateTime;
use Exception;
use App\Entity\Device;
use App\Entity\UserType;
use App\Entity\Notification;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RestApiNotificationController extends FOSRestController
{
    /**
    * @Rest\Get("/api/notification", name ="api_notifications")
     * @Rest\View(serializerGroups={"users"})
     */
    public function index()
    {
        $user=$this->getUser();
        $data = array(
            'id' => $user->getId()
        );
        if (($user->getUserType() === UserType::TYPE_DOCTOR) || ($user->getUserType() === UserType::TYPE_PATIENT)) {
            $repository = $this->getDoctrine()->getRepository(Notification::class);
            $notification = $repository->findBy(array('created_by'=> $data,'removed' => false),array('created_at'=>'DESC'));
            if (!is_null($notification)) {
                return View::create($notification, JsonResponse::HTTP_OK, []);
    }
    else {
        return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
    }
} 

  }
   /**
    * @Rest\Get("/api/notification/{id}", name ="search_notifications")
     * @Rest\View(serializerGroups={"users"})
     */
    public function searchNote($id){
        $user=$this->getUser();
        if (($user->getUserType() === UserType::TYPE_DOCTOR) ||  ($user->getUserType() === UserType::TYPE_PATIENT))  {
            $repository = $this->getDoctrine()->getRepository(Notification::class);
            $notification = $repository->findOneBy(array('id' => $id,'created_by' => $user->getId(),'removed' => false));
            if (!is_null($notification)) {
                return View::create($notification, JsonResponse::HTTP_OK, []);
     
                }
            else {
                return View::create('Notification Not Found', JsonResponse::HTTP_NOT_FOUND);
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
        $user= $this->getUser();
        if (($user->getUserType() === UserType::TYPE_PATIENT) || ($user->getUserType() === UserType::TYPE_DOCTOR) ) {
            try{
            $data = $request->request->all();
             $notification= new Notification();
             $deviceid= $request->request->get('device_id');
             $typedevice= gettype($deviceid);
            if (isset($deviceid)) {
                if($typedevice == "integer"){  
                $repository = $this->getDoctrine()->getRepository(Device::class);
                $pushDevice = $repository->findOneBy(array('id' => $data['device_id'],'created_by'=>$user, 'removed' => false));
                if (!is_null($pushDevice)) {
                $notification->setPushDeviceId($pushDevice);
            } else {
                return View::create('device Not Found', JsonResponse::HTTP_NOT_FOUND);
            }
        } else {
            return View::create('device_id should be integer ', JsonResponse::HTTP_BAD_REQUEST,[]);
        }

            } else {
                return View::create('Missing device!', JsonResponse::HTTP_BAD_REQUEST, []);
            }
            $typetitle= gettype($data['title']);
            if (isset($data['title'])) {
                if($typetitle == "string"){
                $notification->setTitle($data['title']);
            } else {
                return View::create('title of notification must be string!!', JsonResponse::HTTP_BAD_REQUEST, []);
            }
            } else {
                return View::create('missing title!!', JsonResponse::HTTP_BAD_REQUEST, []);
            }
            $typebody= gettype($data['body']);
            if (isset($data['body'])) {
                if($typebody == "string"){
                $notification->setBody($data['body']);
            } else {
                return View::create('body of notification must be string!!', JsonResponse::HTTP_BAD_REQUEST, []);
            }
            }
             else {
                return View::create('missing body!!', JsonResponse::HTTP_BAD_REQUEST, []);
            }
            $typedata= gettype($data['data']);
            if (isset($data['data'])) {
                if($typedata == "string"){
                $notification->setData($data['data']);
            } else {
                return View::create('data of notification must be string!!', JsonResponse::HTTP_BAD_REQUEST, []);
            }  
            }
          
            if (isset($data['type'])) {
               
                $notification->setType($data['type']);
            
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
        }catch (Exception $e){
            return View::create($e->getMessage(), JsonResponse::HTTP_BAD_REQUEST, []);
    }  
    }else {
        return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN);
                  }
                  
                }
            
    /**
    
    * @Rest\Patch("/api/notification/{id}", name ="patch_notifications")
     * @Rest\View(serializerGroups={"users"})
     *
     * @return array
     */
    public function patchNotificationAction(Request $request,$id)
    {
        $user= $this->getUser();
        if (($user->getUserType() === UserType::TYPE_PATIENT) || ($user->getUserType() === UserType::TYPE_DOCTOR) ) {
            $data = $request->request->all();
            $repository = $this->getDoctrine()->getRepository(Notification::class);
            $notification = $repository->findOneBy(array('id' => $id, 'created_by' => $this->getUser()->getId(),'removed' => false,"readed" => false));
            if (!is_null($notification)) {
                if (isset($data['readed'])) {
                        $notification->setReaded($data['readed']);
                    
                }
                else{
                    return View::create('notification read missing', JsonResponse::HTTP_BAD_REQUEST, []);
                }
                if (isset($data['enabled'])) {
                    $notification->setEnabled($data['enabled']);
                }
                
                    $notification->setUpdatedBy($user);
                    $notification->setUpdatedAt(new \DateTime());
                    $em = $this->getDoctrine()->getManager();
                    $em->flush();
                    return View::create('notification updated', JsonResponse::HTTP_OK, []);
                 
            }
            else{
                return View::create('Not Found', JsonResponse::HTTP_NOT_FOUND);
        } 
            
    }
        
    else{
        return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
    }
    
            
        }
         /**
    * @Rest\Get("/api/ReadedNotification", name ="readed_notifications")
     * @Rest\View(serializerGroups={"users"})
     */
    public function readed()
    {
        $user=$this->getUser();
        $data = array(
            'id' => $user->getId()
        );
        if (($user->getUserType() === UserType::TYPE_DOCTOR) || ($user->getUserType() === UserType::TYPE_PATIENT)) {
            $repository = $this->getDoctrine()->getRepository(Notification::class);
            $notification = $repository->findBy(array('created_by'=> $data,'readed' => false));
    
            if (!is_null($notification)) {
                $readed=count($notification);
                $notReaded = array(
                 'not-readed' => $readed
             );
                return View::create($notReaded, JsonResponse::HTTP_OK, []);
    }
    else {
        return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
    }
} 
    }
    
}
        
    
   

  
