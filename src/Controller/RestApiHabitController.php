<?php

namespace App\Controller;

use DateTime;
use App\Entity\Food;
use App\Entity\Patient;
use App\Entity\UserType;
use App\Entity\EatingHabit;
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

class RestApiHabitController extends FOSRestController
{
    /**
     * @Rest\Get("/api/habit", name ="api_habits")
     * @Rest\View(serializerGroups={"patients"})
     */
    public function index()
    {
        $user = $this->getUser();
        $data = array(
            'id' => $user->getId()
        );
        if ($user->getUserType() === UserType::TYPE_PATIENT) {
            $habitsrepository = $this->getDoctrine()->getRepository(EatingHabit::class);
            $habits = $habitsrepository->findBy(array('created_by' => $data, 'remove' => false), array('id' => 'DESC'));
            if (!empty($habits)) {
                return View::create($habits, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('No data found :)', JsonResponse::HTTP_OK, []);
            }
        }
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
            $Assigned = $repository->findBy(array('id_doctor' => $user->getId(), 'status' => 'Accepted', 'removed' => false));
            foreach ($Assigned as $data) {
                $a[] = $data->getIdPatient();
            }
            if (!empty($Assigned)) {
                $habitsrepository = $this->getDoctrine()->getRepository(EatingHabit::class);
                $habits = $habitsrepository->findBy(array('created_by' => $a, 'remove' => false), array('id' => 'DESC'));
                if (!empty($habits)) {
                    return View::create($habits, JsonResponse::HTTP_OK, []);
                } else {
                    return View::create('no data found', JsonResponse::HTTP_OK, []);
                }
            } else {
                return View::create('not data found', JsonResponse::HTTP_OK, []);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }
    /**
     * @Rest\Get("/api/habit/{id}", name ="search_habits")
     * @Rest\View(serializerGroups={"patients"})
     */
    public function searchhabits($id)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
            $Assigned = $repository->findBy(array('id_doctor' => $user->getId(), 'status' => 'Accepted', 'removed' => false));
            foreach ($Assigned as $data) {
                $a[] = $data->getCreatedBy();
            }
            if (!is_null($Assigned)) {
                $habitsrepository = $this->getDoctrine()->getRepository(EatingHabit::class);
                $habits = $habitsrepository->findOneBy(array('id' => $id, 'created_by' => $a, 'remove' => false));
                if (!is_null($habits)) {
                    return View::create($habits, JsonResponse::HTTP_OK, []);
                } else {
                    return View::create('Habits Not Found', JsonResponse::HTTP_NOT_FOUND);
                }
            }
        }
        if ($user->getUserType() === UserType::TYPE_PATIENT) {
            $repository = $this->getDoctrine()->getRepository(EatingHabit::class);
            $habits = $repository->findOneBy(array('id' => $id, 'created_by' => $user->getId(), 'remove' => false));
            if (!is_null($habits)) {
                return View::create($habits, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('Habits Not Found', JsonResponse::HTTP_NOT_FOUND);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }

    /**
     * @Rest\Post("/api/habit", name ="post_habits")
     * @Rest\View(serializerGroups={"patients"})
     */
    public function create(Request $request, EntityManagerInterface $entity)
    {
        $user = $this->getUser();
        
            if ($user->getUserType() === UserType::TYPE_PATIENT) {
                $name = $request->request;
                $food = $request->request->get('food');
                $quantity = $request->request->get('quantity');
                $unit = $request->request->get('unit');
                $typefood = gettype($food);
                $typequantity = gettype($quantity);
                $typeunit = gettype($unit);
                $nb=0;
               
            //    if ($food == null) {
            //     return View::create('missing food habits!!!', JsonResponse::HTTP_BAD_REQUEST, []);
            //    } 
            //    if ($quantity ==null) {
            //     return View::create('missing quantity!!!', JsonResponse::HTTP_BAD_REQUEST, []);
            //    } 
            //    if ($unit == null){
            //     return View::create('missing unit!!!', JsonResponse::HTTP_BAD_REQUEST, []);
            //    }
            //     else{              
                foreach ($name as $data){
             
                if (sizeof($data) >= 3){
                    $foods[] = $data['food'];
                    $quantitys[] = $data['quantity'];
                    $units[] = $data['unit'];
                    $nb++;
                }
            }
        
                if($nb==0){
                    return View::create('missing food or quantity or unit,check your request data', JsonResponse::HTTP_FORBIDDEN, []);
                }
                else{
                    for($i=0;$i<$nb;$i++){
                $habits = new EatingHabit();      
                $habits->setFood($foods[$i]);
                $habits->setQuantity($quantitys[$i]);
                $habits->setUnit($units[$i]);
                $habits->setCreatedBy($user);
                $habits->setCreatedAt(new \DateTime());
                $habits->setRemove(false);
                $entity->persist($habits);
                $entity->flush();
            }
                $response = array(
                    'message' => 'Foods habits created',
                );
                return View::create($response, Response::HTTP_CREATED, []);
            }
    
            
        }
        else{
            return View::create('Not authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    
     
     
    }


    /**
     * @param Request $request
     * @Rest\Patch("/api/habit/{id}", name ="patch_habits")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function patchAction(Request $request, $id)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_PATIENT) {
            $repository = $this->getDoctrine()->getRepository(EatingHabit::class);
            $habits = $repository->findOneBy(array('id' => $id, 'created_by' => $user->getId(), 'remove' => false));
            if (!is_null($habits)) {
                $food = $request->request->get('food');
                $quantity = $request->request->get('quantity');
                $unit = $request->request->get('unit');
                $typefood = gettype($food);
                $typequantity = gettype($quantity);
                $typeunit = gettype($unit);
                if (isset($food)) {
                    if (($typefood === "string")) {

                        $habits->setFood($food);
                    } else {
                        return View::create('food habits must be string', JsonResponse::HTTP_BAD_REQUEST);
                    }
                }
                if (isset($quantity)) {
                    if ($typequantity === "double") {
                        $habits->setQuantity($quantity);
                    } else {
                        return View::create('quantity must be double', JsonResponse::HTTP_BAD_REQUEST);
                    }
                }
                if (isset($unit)) {
                    if ($typeunit === "string") {
                        $habits->setUnit($unit);
                    } else {
                        return View::create('unit must be string', JsonResponse::HTTP_BAD_REQUEST);
                    }
                }
                $habits->setUpdatedBy($user);
                $habits->setUpdatedAt(new \DateTime());
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                $response = array(
                    'message' => 'Health habits updated',
                    'result' => $habits,
                );

                return View::create($response, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('Food habits not found', JsonResponse::HTTP_NOT_FOUND);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }
    /**
     * @Rest\Delete("/api/habit/{id}", name ="delete_habits")
     */
    public function delete($id)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_PATIENT) {
            $repository = $this->getDoctrine()->getRepository(EatingHabit::class);
            $habits = $repository->findOneBy(array('id' => $id, 'created_by' => $user->getId(), 'remove' => false));
            if (!is_null($habits)) {
                $habits->setRemove(true);
                $habits->setRemovedBy($user);
                $habits->setRemovedAt(new \DateTime());
                $em = $this->getDoctrine()->getManager();
                $em->flush();

                return View::create("Habits deleted", JsonResponse::HTTP_OK, []);
            } else {
                return View::create('habits Not Found', JsonResponse::HTTP_NOT_FOUND);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }
}