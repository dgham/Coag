<?php

namespace App\Controller;

use DateTime;
use App\Entity\Foods;
use App\Entity\Patient;
use App\Entity\UserType;
use App\Entity\Eatinghabits;
use FOS\RestBundle\View\View;
use App\Entity\DoctorAssignement;
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
     * @Rest\View(serializerGroups={"patients"})
     */
    public function index()
    {
        $user=$this->getUser();
        $data = array(
            'id' => $user->getId()
        );
        
        if ($user->getUserType() === UserType::TYPE_PATIENT) {
            $habitsrepository = $this->getDoctrine()->getRepository(Eatinghabits::class);
            $habits = $habitsrepository->findBy(array('created_by'=> $data,'remove' => false),array('id' => 'DESC'));
            if(!empty($habits)){
            return View::create($habits, JsonResponse::HTTP_OK, []);
         }
         else{
            return View::create('No data found :)', JsonResponse::HTTP_OK, []);
         }
        }
         if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
            $Assigned = $repository->findBy(array('id_doctor'=>$user->getId(),'status'=>'Accepted','removed'=>false));
           foreach ($Assigned as $data) {
           $a[]= $data->getIdPatient();
           }
           if (!empty($Assigned)){
                $habitsrepository = $this->getDoctrine()->getRepository(Eatinghabits::class);
                $habits = $habitsrepository->findBy(array('created_by'=> $a ,'remove' => false),array('id' => 'DESC'));
                if (!empty($habits)){
                return View::create($habits, JsonResponse::HTTP_OK, []);
            }
            else{
                return View::create('no data found', JsonResponse::HTTP_OK, []);
            }
        }
            else {
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
            
            }
    }
    else {
        return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
    }
    }
     /**
     * @Rest\Get("/api/habits/{id}", name ="search_habits")
     * @Rest\View(serializerGroups={"patients"})
     */
    public function searchhabits($id){
        $user=$this->getUser();
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
            $Assigned = $repository->findBy(array('id_doctor'=>$user->getId(),'status'=>'Accepted','removed'=>false));
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
            else{
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
            }
           
    }
    
    /**
     * @Rest\Post("/api/habits", name ="post_habits")
     * @Rest\View(serializerGroups={"patients"})
     */
    public function create(Request $request,EntityManagerInterface $entity){
        $user = $this->getUser();
        try{
            if ($user->getUserType() === UserType::TYPE_PATIENT) {
                $breakfast= $request->request->get('breakfast_food');
                $launch= $request->request->get('lunch_food');
                $dinner= $request->request->get('dinner_food');
                $typebreakfast= gettype($breakfast);
                $typelaunch= gettype($launch);
                $typedinner= gettype($dinner);
                $habits = new Eatinghabits();
                if (isset($breakfast)){
                    if ($typebreakfast === "string"){
                        $habits->setBreakfastFood($breakfast);
                    }
                    else{
                        return View::create('breakfast must be a type string', JsonResponse::HTTP_BAD_REQUEST, []);
                    }  
                }
                else{
                    return View::create('missing breakfast!', JsonResponse::HTTP_BAD_REQUEST, []);
                }  
                if (isset($launch)){
                    if ($typelaunch === "string"){
                        $habits->setLunchFood($launch);
                    }
                    else{
                        return View::create('lunch must be a type string', JsonResponse::HTTP_BAD_REQUEST, []);
                    }  
                }
                else{
                    return View::create('missing lunch!', JsonResponse::HTTP_BAD_REQUEST, []);
                }  
                if (isset($dinner)){
                    if ($typedinner === "string"){
                        $habits->setDinnerFood($dinner);
                    }
                    else{
                        return View::create('dinner must be a type string', JsonResponse::HTTP_BAD_REQUEST, []);
                    }  
                }
                else{
                    return View::create('missing dinner!', JsonResponse::HTTP_BAD_REQUEST, []);
                }  
                    $habits->setCreatedBy($user);
                    $habits->setCreatedAt(new \DateTime());
                    $habits->setRemove(false);
                    $entity ->persist($habits);
                    $entity->flush();
                    $response=array(
                        'message'=>'Foods habits created',
                        'result'=>$habits,
                    
                    );
                    return View::create($response, Response::HTTP_CREATED, []);
                }
               
                    }catch (Exception $ex){
                        return View::create($ex->getMessage(), JsonResponse::HTTP_BAD_REQUEST, []);
                }  
}


   /**
   * @param Request $request
   * @Rest\Patch("/api/habits/{id}", name ="patch_habits")
   * @Rest\View(serializerGroups={"patients"})
   */
    public function patchAction(Request $request,$id)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_PATIENT) {
            $repository = $this->getDoctrine()->getRepository(Eatinghabits::class);
            $habits = $repository->findOneBy(array('id' => $id,'created_by' => $user->getId(),'remove' => false));
                 if (!is_null($habits)){
                    $breakfast= $request->request->get('breakfast_food');
                    $launch= $request->request->get('launch_food');
                    $dinner= $request->request->get('dinner_food');
                    $typebreakfast= gettype($breakfast);
                    $typelaunch= gettype($launch);
                    $typedinner= gettype($dinner);
                    if (isset($breakfast)){  
                        if (($typebreakfast === "string")){
                        
                            $habits->setBreakfastFood($breakfast);
                        }
                        else{
                            return View::create('breakfast habits must be string', JsonResponse::HTTP_BAD_REQUEST); 
                        }  
                    }
                        if (isset($launch)) {
                     if ($typelaunch === "string"){
                        $habits->setLunchFood($launch);
                     } 
                     else{
                        return View::create('lunch habits must be string', JsonResponse::HTTP_BAD_REQUEST); 
                    }  
                }
                    if (isset($dinner)){
                        if($typedinner === "string"){
                        $habits->setDinnerFood($dinner);
                    }
                    else{
                        return View::create('dinner habits must be string', JsonResponse::HTTP_BAD_REQUEST); 
                     }
                    }
                  
                   
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
                    return View::create('foods habits not found', JsonResponse::HTTP_NOT_FOUND);
                } 
         
        }
        else{
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
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


    