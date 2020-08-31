<?php

namespace App\Controller;

use DateTime;
use Exception;
use App\Entity\Note;
use App\Entity\User;
use App\Entity\Foods;
use App\Entity\Patient;
use App\Entity\UserType;
use FOS\RestBundle\View\View;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RestApiFoodsController extends FOSRestController
{

    /**
     * @Rest\Get("/api/foods", name ="api_foods")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function index()
    {
        $user=$this->getUser();
        $data = array(
            'id' => $user->getId()
        );
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $repository = $this->getDoctrine()->getRepository(Foods::class);
            $foods = $repository->findAll(array('id'=>'DESC','removed'=>false));
            if(!empty($foods)){
            return View::create($foods, JsonResponse::HTTP_OK, []);
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
     * @Rest\Get("/api/foods/{id}", name ="search_foods")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function searchFoods($id)
    {
        $user=$this->getUser();
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $repository = $this->getDoctrine()->getRepository(Foods::class);
            $foods = $repository->findOneBy(array('id' => $id,'removed' => false));
            if (!empty($foods)) {
                return View::create($foods, JsonResponse::HTTP_OK, []);
        } else {
            return View::create('no data found', JsonResponse::HTTP_NOT_FOUND);  
                  } 
                
            } else {
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
           } 
    }  


    /**
    * @Rest\Post("/api/foods", name ="post_foods")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function create(Request $request,EntityManagerInterface $entity)
    {
        $user = $this->getUser();
            if ($user->getUserType() === UserType::TYPE_ADMIN) {
                $name= $request->request->get('name');
                $typename= gettype($name);
                $foods = new Foods();
                if (isset($name)) {
                    if($typename == "string"){
                   $foods->setName($name);
                    }
                    else {
                        return View::create('erreur, food name must be a string!', JsonResponse::HTTP_BAD_REQUEST);
                    }
                }
                else {
                    return View::create('missing food name!', JsonResponse::HTTP_BAD_REQUEST);
                }
                $description = $request->request->get('description');
                $typename= gettype($description);
                if (isset($description)) {
                    if($typename == "string"){
                   $foods->setDescription($description);
                    }
                    else {
                        return View::create('erreur, food description must be a string!', JsonResponse::HTTP_BAD_REQUEST);
                    }
                }
              
                $quantity = $request->request->get('quantity');
                $typename= gettype($quantity);
                if (isset($quantity)) {
                    if($typename == "string"){
                   $foods->setQuantity($quantity);
                    }
                    else {
                        return View::create('erreur, food description must be a string!', JsonResponse::HTTP_BAD_REQUEST);
                    }
                }
                else {
                    return View::create('missing food quantiyy!', JsonResponse::HTTP_BAD_REQUEST);
                }
              

              
                        $foods->setCreatedBy($user);
                        $foods->setCreatedAt(new \DateTime());
                        $foods->setRemoved(false);
                        $entity ->persist($foods);
                        $entity->flush();
                        $response=array(
                            'message'=>'food created successfully',
                            'result'=> $foods,
                           
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
         * @Rest\PATCH("/api/foods/{id}", name ="patch_foods")
             * @Rest\View(serializerGroups={"admin"})
             */
            public function patchAction(Request $request,$id)
            {
                $user = $this->getUser();
                if ($user->getUserType() === UserType::TYPE_ADMIN) {
                    $repository = $this->getDoctrine()->getRepository(Foods::class);
                    $foods = $repository->findOneBy(array('id' => $id,'removed' => false));
                
                        if (!is_null($foods)) {
                            $name= $request->request->get('name');
                            $typename= gettype($name);
                            if (isset($name)) {
                                if($typename == "string"){
                            $foods->setName($name);
                                }
                                else {
                                    return View::create('Food name must be a string', JsonResponse::HTTP_BAD_REQUEST);
                                }

                                $description= $request->request->get('description');
                                $typename= gettype($description);
                                if (isset($description)) {
                                    if($typename == "string"){
                                $foods->setDescription($description);
                                    }
                                    else {
                                        return View::create('Food name must be a string', JsonResponse::HTTP_BAD_REQUEST);
                                    }
                            }

                            $quantity= $request->request->get('quantity');
                            $typename= gettype($quantity);
                            if (isset($quantity)) {
                                if($typename == "string"){
                            $foods->setQuantity($quantity);
                                }
                                else {
                                    return View::create('Food quantity must be a string', JsonResponse::HTTP_BAD_REQUEST);
                                }
                        }

                            
                            $foods->setUpdatedBy($user);
                            $foods->setUpdatedAt(new \DateTime());
                            $em = $this->getDoctrine()->getManager();
                            $em->flush();
                            $response=array(
                                'message'=>'food updated',
                                'result'=>$foods,
                            
                            );
                            return View::create($response, JsonResponse::HTTP_OK, []);
                            }    
                            else {
                                return View::create('food not Found', JsonResponse::HTTP_NOT_FOUND);
                            
                        }
                    }
                    else {
                        return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                    
                }
                }
            }
            /**
            * @Rest\Delete("/api/foods/{id}", name ="delete_foods")
            */
            public function delete($id){
                $user = $this->getUser();
                if ($user->getUserType() === UserType::TYPE_ADMIN) {
                    $repository = $this->getDoctrine()->getRepository(Foods::class);
                    $foods = $repository->findOneBy(array('id' => $id,'removed' => false));
                    if (!is_null($foods)) {
                            $foods->setRemoved(true);
                            $foods->setRemovedBy($user);
                            $foods->setRemovedAt(new \DateTime());
                            $em = $this->getDoctrine()->getManager();
                            $em->flush();
                            return View::create('food deleted', JsonResponse::HTTP_OK,[]);
                        } 
                        
                        else {
                            return View::create('food not Found', JsonResponse::HTTP_NOT_FOUND);
                }
                }
                    
                        else {
                            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                
                    
                
                }    
        }

        }