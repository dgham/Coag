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
use App\Entity\MedicationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RestApiMedicationTypeController extends FOSRestController
{

    /**
     * @Rest\Get("/api/MedicationType", name ="api_medication")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function index()
    {
        $user=$this->getUser();
        $data = array(
            'id' => $user->getId()
        );
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $repository = $this->getDoctrine()->getRepository(MedicationType::class);
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
    * @Rest\Get("/api/MedicationType/{id}", name ="search_medication")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function searchMedication($id)
    {
        $user=$this->getUser();
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $repository = $this->getDoctrine()->getRepository(MedicationType::class);
            $medicationtype = $repository->findOneBy(array('id' => $id,'removed' => false));
            if (!empty($medicationtype)) {
                return View::create($medicationtype, JsonResponse::HTTP_OK, []);
        } else {
            return View::create('no data found', JsonResponse::HTTP_NOT_FOUND);  
                  } 
                
            } else {
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
           } 
    }  


    /**
    * @Rest\Post("/api/MedicationType", name ="post_medicationtype")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function create(Request $request,EntityManagerInterface $entity)
    {
        $user = $this->getUser();
            if ($user->getUserType() === UserType::TYPE_ADMIN) {
                $type= $request->request->get('type');
                $typename= gettype($type);
                $medicationType = new MedicationType();
                if (isset($type)) {
                    if($typename == "string"){
                   $medicationType->setType($type);
                    }
                    else {
                        return View::create('type must be a string', JsonResponse::HTTP_BAD_REQUEST);
                    }
                }
                else {
                    return View::create('missing medication type!', JsonResponse::HTTP_BAD_REQUEST);
                }
                        
                        $medicationType->setCreatedBy($user);
                        $medicationType->setCreatedAt(new \DateTime());
                        $medicationType->setRemoved(false);
                        $entity ->persist($medicationType);
                        $entity->flush();
                        $response=array(
                            'message'=>'medication type created',
                            'result'=> $medicationType,
                           
                        );
                        return View::create($response, JsonResponse::HTTP_CREATED, []);
                    
                      
            }
           
                else {
               
                    return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                    }
            }


        /**
             * @param Request $request
             * @Rest\PATCH("/api/MedicationType/{id}", name ="patch_medicationType")
             * @Rest\View(serializerGroups={"admin"})
             */
            public function patchAction(Request $request,$id)
            {
                $user = $this->getUser();
                if ($user->getUserType() === UserType::TYPE_ADMIN) {
                    $repository = $this->getDoctrine()->getRepository(MedicationType::class);
                    $medicationType = $repository->findOneBy(array('id' => $id,'removed' => false));
                
                        if (!is_null($medicationType)) {
                            $type= $request->request->get('type');
                            $typename= gettype($type);
                            if (isset($type)) {
                                if($typename == "string"){
                                   
                            $medicationType->setType($type);
                                }
                                else {
                                    return View::create('type name must be a string', JsonResponse::HTTP_BAD_REQUEST);
                                }
                            }
                            $medicationType->setUpdatedBy($user);
                            $medicationType->setUpdatedAt(new \DateTime());
                            $em = $this->getDoctrine()->getManager();
                            $em->flush();
                            $response=array(
                                'message'=>'medication type updated',
                                'result'=>$medicationType,
                            
                            );
                            return View::create($response, JsonResponse::HTTP_OK, []);
                            }    
                            else {
                                return View::create('medication type not Found', JsonResponse::HTTP_NOT_FOUND);
                            
                        }
                    }
                    else {
                        return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                    
                }
                }
            /**
            * @Rest\Delete("/api/MedicationType/{id}", name ="delete_medicationType")
            */
            public function delete($id){
                $user = $this->getUser();
                if ($user->getUserType() === UserType::TYPE_ADMIN) {
                    $repository = $this->getDoctrine()->getRepository(MedicationType::class);
                    $medicationType = $repository->findOneBy(array('id' => $id,'removed' => false));
                    if (!is_null($medicationType)) {
                            $medicationType->setRemoved(true);
                            $medicationType->setRemovedBy($user);
                            $medicationType->setRemovedAt(new \DateTime());
                            $em = $this->getDoctrine()->getManager();
                            $em->flush();
                            return View::create('medication type deleted successfully', JsonResponse::HTTP_OK,[]);
                        } 
                        
                        else {
                            return View::create('medication type not Found', JsonResponse::HTTP_NOT_FOUND);
                }
                }
                    
                        else {
                            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                
                    
                
                }    
        }

        }