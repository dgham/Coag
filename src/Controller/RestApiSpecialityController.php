<?php

namespace App\Controller;

use DateTime;
use Exception;
use App\Entity\Note;
use App\Entity\User;
use App\Entity\Patient;
use App\Entity\UserType;
use App\Entity\Speciality;
use FOS\RestBundle\View\View;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RestApiSpecialityController extends FOSRestController
{

    /**
     * @Rest\Get("/api/speciality", name ="api_speciality")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function index()
    {
        $user=$this->getUser();
        $data = array(
            'id' => $user->getId()
        );
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $repository = $this->getDoctrine()->getRepository(Speciality::class);
            $speciality = $repository->findAll(array('id'=>'DESC','removed'=>false));
            if(!empty($speciality)){
            return View::create($speciality, JsonResponse::HTTP_OK, []);
            }
            else{
                return View::create('No data found', JsonResponse::HTTP_OK, []);
            }
        }  
        else{
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }


     /**
    * @Rest\Get("/api/speciality/{id}", name ="search_speciality")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function searchSpeciality($id)
    {
        $user=$this->getUser();
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $repository = $this->getDoctrine()->getRepository(Speciality::class);
            $speciality = $repository->findOneBy(array('id' => $id,'removed' => false));
            if (!empty($speciality)) {
                return View::create($speciality, JsonResponse::HTTP_OK, []);
        } else {
            return View::create('no data found', JsonResponse::HTTP_NOT_FOUND);  
                  } 
                
            } else {
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
           } 
    }  


    /**
    * @Rest\Post("/api/speciality", name ="post_speciality")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function create(Request $request,EntityManagerInterface $entity)
    {
        $user = $this->getUser();
            if ($user->getUserType() === UserType::TYPE_ADMIN) {
                $name= $request->request->get('speciality_name');
                $typename= gettype($name);
                $speciality = new Speciality();
                if (isset($name)) {
                    if($typename == "string"){
                   $speciality->setSpecialityName($name);
                    }
                    else {
                        return View::create('speciality name must be a string', JsonResponse::HTTP_BAD_REQUEST);
                    }
                }
                else {
                    return View::create('missing speciality_name!', JsonResponse::HTTP_BAD_REQUEST);
                }
              
              
                        $speciality->setCreatedBy($user);
                        $speciality->setCreatedAt(new \DateTime());
                        $speciality->setRemoved(false);
                        $entity ->persist($speciality);
                        $entity->flush();
                        $response=array(
                            'message'=>'speciality created',
                            'result'=> $speciality,
                           
                        );
                        return View::create($response, JsonResponse::HTTP_CREATED, []);
                    
                      
            }
           
                else {
               
                    return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                    }
            }


        /**
             * @param Request $request
             *
         * @Rest\PATCH("/api/speciality/{id}", name ="patch_speciality")
             * @Rest\View(serializerGroups={"admin"})
             */
            public function patchAction(Request $request,$id)
            {
                $user = $this->getUser();
                if ($user->getUserType() === UserType::TYPE_ADMIN) {
                    $repository = $this->getDoctrine()->getRepository(Speciality::class);
                    $speciality = $repository->findOneBy(array('id' => $id,'removed' => false));
                
                        if (!is_null($speciality)) {
                            $name= $request->request->get('speciality_name');
                            $typename= gettype($name);
                            if (isset($name)) {
                                if($typename == "string"){
                            $speciality->setSpecialityName($name);
                                }
                                else {
                                    return View::create('speciality name must be a string', JsonResponse::HTTP_BAD_REQUEST);
                                }
                            }
                            $speciality->setUpdatedBy($user);
                            $speciality->setUpdatedAt(new \DateTime());
                            $em = $this->getDoctrine()->getManager();
                            $em->flush();
                            $response=array(
                                'message'=>'speciality updated',
                                'result'=>$speciality,
                            
                            );
                            return View::create($response, JsonResponse::HTTP_OK, []);
                            }    
                            else {
                                return View::create('speciality not Found', JsonResponse::HTTP_NOT_FOUND);
                            
                        }
                    }
                    else {
                        return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                    
                }
                }
            /**
            * @Rest\Delete("/api/speciality/{id}", name ="delete_speciality")
            */
            public function delete($id){
                $user = $this->getUser();
                if ($user->getUserType() === UserType::TYPE_ADMIN) {
                    $repository = $this->getDoctrine()->getRepository(Speciality::class);
                    $speciality = $repository->findOneBy(array('id' => $id,'removed' => false));
                    if (!is_null($speciality)) {
                            $speciality->setRemoved(true);
                            $speciality->setRemovedBy($user);
                            $speciality->setRemovedAt(new \DateTime());
                            $em = $this->getDoctrine()->getManager();
                            $em->flush();
                            return View::create('speciality deleted', JsonResponse::HTTP_OK,[]);
                        } 
                        
                        else {
                            return View::create('speciality not Found', JsonResponse::HTTP_NOT_FOUND);
                }
                }
                    
                        else {
                            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                
                    
                
                }    
        }

        }