<?php

namespace App\Controller;

use DateTime;
use App\Entity\Foods;
use App\Entity\Patient;
use App\Entity\UserType;
use App\Entity\Eatinghabits;
use FOS\RestBundle\View\View;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RestApiHabitsController extends FOSRestController
{
    /**
     * @Rest\Get("/api/habits", name ="api_habits")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function index()
    {
        $user=$this->getUser();
        $data = array(
            'id' => $user->getId()
        );
        
        if ($user->getUserType() === UserType::TYPE_PATIENT) {
            $habitsrepository = $this->getDoctrine()->getRepository(Eatinghabits::class);
            $habits = $habitsrepository->findBy(array('created_by'=> $data,'remove' => false));
            if(!empty($habits)){
            return View::create($habits, JsonResponse::HTTP_OK, []);
         }
         else{
            return View::create('No data found :)', JsonResponse::HTTP_OK, []);
         }
         if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $patientrepository = $this->getDoctrine()->getRepository(Patient::class);
           $Assigned = $patientrepository->findBy(array('assignedBy'=> $data));
           foreach ($Assigned as $data) {
           $a[]= $data->getCreatedBy();
           }
          
            if (!is_null($Assigned)) {
                $habitsrepository = $this->getDoctrine()->getRepository(Eatinghabits::class);
                $habits = $habitsrepository->findBy(array('created_by'=> $a ,'remove' => false));
                return View::create($habits, JsonResponse::HTTP_OK, []);
            }
            else {
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
            }
         }
    }
    }
     /**
     * @Rest\Get("/api/habits/{id}", name ="search_habits")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function searchhabits($id){
        $user=$this->getUser();
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $patientrepository = $this->getDoctrine()->getRepository(Patient::class);
           $Assigned = $patientrepository->findBy(array('assignedBy'=> $user));
           foreach ($Assigned as $data) {
            $a[]= $data->getCreatedBy();
            }
           
             if (!is_null($Assigned)) {
                 $habitsrepository = $this->getDoctrine()->getRepository(Eatinghabits::class);
                 $habits = $habitsrepository->findOneBy(array('id'=>$id,'created_by'=> $a,'remove' => false));
                 if (!is_null($habits)) {
                 return View::create($habits, JsonResponse::HTTP_OK, []);
             }
             else {
                return View::create('Habits Not Found', JsonResponse::HTTP_NOT_FOUND);
             }
             }
        }
        if ($user->getUserType() === UserType::TYPE_PATIENT) {
        $repository = $this->getDoctrine()->getRepository(Eatinghabits::class);
        $habits = $repository->findOneBy(array('id' => $id,'created_by' => $user->getId(),'remove' => false));
        if (!is_null($habits)) {
            return View::create($habits, JsonResponse::HTTP_OK, []);
    }

    else {
        return View::create('Habits Not Found', JsonResponse::HTTP_NOT_FOUND);
              } 
            } 
           
    }
    
    /**
     * @Rest\Post("/api/habits", name ="post_habits")
     * @Rest\View(serializerGroups={"users"})
     */
    public function create(Request $request,EntityManagerInterface $entity){
        $user = $this->getUser();
        try{
            if ($user->getUserType() === UserType::TYPE_PATIENT) {
                $foodId= $request->request->get('food_description_id');
                $typedescription= gettype($foodId);
                if (isset($foodId)) {
                    if($typedescription == "integer"){
                        $repository = $this->getDoctrine()->getRepository(Foods::class);
                        $foods = $repository->findOneBy(array('id' => $foodId,'removed' => false));
                        if (!is_null($foods)) { 
                            $habits = new Eatinghabits();
                            $habits->setFoodDescription($foods);
                            $habits->setCreatedBy($user);
                            $habits->setCreatedAt(new \DateTime());
                            $habits->setRemove(false);
                            $entity ->persist($habits);
                            $entity->flush();
                            $response=array(
                                'message'=>'health habits created',
                                'result'=>$habits,
                            
                            );
                            return View::create($response, Response::HTTP_CREATED, []);
                                
                            }
                            else{
                                return View::create('food description not exist !', JsonResponse::HTTP_BAD_REQUEST);
                            }
                        }
                                else{
                                    return View::create('health habit description must be an int', JsonResponse::HTTP_BAD_REQUEST); 
                                }
                            } else {
                                return View::create('missing food_description_id !!', JsonResponse::HTTP_BAD_REQUEST);
                            }
                        }
                    }catch (Exception $ex){
                        return View::create($ex->getMessage(), JsonResponse::HTTP_BAD_REQUEST, []);
                }  
}

   /**
   * @param Request $request
   * @Rest\Patch("/api/habits/{id}", name ="patch_habits")
   * @Rest\View(serializerGroups={"users"})
   */
    public function patchAction(Request $request,$id)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_PATIENT) {
            $repository = $this->getDoctrine()->getRepository(Eatinghabits::class);
            $habits = $repository->findOneBy(array('id' => $id,'created_by' => $user->getId(),'remove' => false));
                 if (!is_null($habits)){
                    $foodId= $request->request->get('food_description_id');
                    $typedescription= gettype($foodId);
                    if (isset($foodId)) {
                    if($typedescription == "integer"){
                    $repository = $this->getDoctrine()->getRepository(Foods::class);
                    $foods = $repository->findOneBy(array('id' => $foodId,'removed' => false));

                    if (!is_null($foods)) { 
                    $habits->setFoodDescription($foods);
                    $habits->setUpdatedBy($user);
                    $habits->setUpdatedAt(new \DateTime());
                    $em = $this->getDoctrine()->getManager();
                    $em->flush();
                    $response=array(
                        'message'=>'Health habits updated',
                        'result'=>$habits,
                       
                    );
                    return View::create($response, JsonResponse::HTTP_OK, []);
                     }  
                     else{
                        return View::create('foods habits not exist', JsonResponse::HTTP_BAD_REQUEST); 
                    }  
                }
                    
                    else{
                        return View::create('health habit description must be a string', JsonResponse::HTTP_BAD_REQUEST); 
                    }
                }
                     else {
                        return View::create('Missing description', JsonResponse::HTTP_BAD_REQUEST);
                    
                 }
            }
        }
    }
   /**
    * @Rest\Delete("/api/habits/{id}", name ="delete_habits")
     */
    public function delete($id){
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_PATIENT) {
            $repository = $this->getDoctrine()->getRepository(Eatinghabits::class);
            $habits = $repository->findOneBy(array('id' => $id,'created_by' => $user->getId(),'remove' => false));
            if (!is_null($habits)) {
                    $habits->setRemove(true);
                    $habits->setRemovedBy($user);
                    $habits->setRemovedAt(new \DateTime());
                    $em = $this->getDoctrine()->getManager();
                    $em->flush();
               
                        return View::create("Habits deleted", JsonResponse::HTTP_OK, []);
                 } 
                 else {
                    return View::create('habits Not Found', JsonResponse::HTTP_NOT_FOUND);
                }   
        
            }
            else {
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
            }   
           
        }    
        
     }


    