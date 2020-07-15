<?php

namespace App\Controller;

use DateTime;
use Exception;
use App\Entity\Session;
use App\Entity\UserType;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RestApiSessionController extends FOSRestController
{
      /**
    * @Rest\Get("/api/session", name ="api_session")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function index()
    {
        $user= $this->getUser();
        $data = array(
            'id' => $user->getId()
        );
            if ($user->getUserType() === UserType::TYPE_ADMIN) {
                $repository = $this->getDoctrine()->getRepository(session::class);
                $session = $repository->findBy(array('removed'=>false));
                if (!is_null($session)) {
                    return View::create($session, JsonResponse::HTTP_OK, []);
        } else {
            return View::create('no session Found', JsonResponse::HTTP_NOT_FOUND);   
                  } 
        
            } 
            else {
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                      }
        }


         /**
    * @Rest\Get("/api/session/{id}", name ="search_session")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function searchSession($id)
    {
        $user= $this->getUser();
        $data = array(
            'id' => $user->getId()
        );
            if ($user->getUserType() === UserType::TYPE_ADMIN) {
                $repository = $this->getDoctrine()->getRepository(session::class);
                $session =  $repository->findOneBy(array('id' => $id,'removed' => false));
                if (!is_null($session)) {
                    return View::create($session, JsonResponse::HTTP_OK, []);
        } else {
            return View::create('no session Found', JsonResponse::HTTP_NOT_FOUND);   
                  } 
        
            } 
            else {
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                      }
        }
 /**
     * 
      * @Rest\Post("/api/session", name ="post_session")
     * @Rest\View(serializerGroups={"admin"})
     *
     * @return array
     */
    public function postsessionAction(Request $request)
    {
        $user= $this->getUser();
        if ((!$user->getUserType() === UserType::TYPE_PATIENT) || (!$user->getUserType() === UserType::TYPE_DOCTOR) || (!$user->getUserType() === UserType::TYPE_HOSPITAL) || (!$user->getUserType() === UserType::TYPE_ADMIN)) {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
        try {
            $data = $request->request->all();
                $session = new Session();
                $typeagent= gettype($data['user_agent']);
            if (isset($data['user_agent'])) {
                if($typeagent == "string"){
                $session->setUserAgent($data['user_agent']);
            } else {
                return View::create('session should be string!', JsonResponse::HTTP_BAD_REQUEST, []);
            }
            } else {
                return View::create('Missing user agent', JsonResponse::HTTP_BAD_REQUEST, []);
            }
            $typeaddress= gettype($data['ip_address']);
            if (isset($data['ip_address'])) {
                if($typeaddress == "string"){
                $session->setIpAddress($data['ip_address']);
            } else {
                return View::create('ipadress should be string!', JsonResponse::HTTP_BAD_REQUEST, []);
            }
            }
          
            $session->setIsValide(true);
            $session->setRemoved(false);
            $session->setCreatedBy($user);
            $session->setCreatedAt(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($session);
            $em->flush();
            $response=array(
                'message'=>'session created',
                'result'=>$session,
               
            );
            return View::create($response,Response::HTTP_CREATED, []);
       
        } catch (\Exception $ex) {
            return  View::create($ex->getMessage(), JsonResponse::HTTP_BAD_REQUEST, []);
        }
    }



    
}
