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
use App\Entity\DrugType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RestApiDrugTypeController extends FOSRestController
{

    /**
     * @Rest\Get("/api/drugType", name ="api_medication")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function index()
    {
        $user = $this->getUser();
        $data = array(
            'id' => $user->getId()
        );
        if (($user->getUserType() === UserType::TYPE_ADMIN) || ($user->getUserType() === UserType::TYPE_DOCTOR)) {
            $repository = $this->getDoctrine()->getRepository(DrugType::class);
            $speciality = $repository->findAll(array('id' => 'DESC', 'removed' => false));
            if (!empty($speciality)) {
                return View::create($speciality, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('No data found', JsonResponse::HTTP_OK, []);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }


    /**
     * @Rest\Get("/api/drugType/{id}", name ="search_medication")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function searchMedication($id)
    {
        $user = $this->getUser();
        if (($user->getUserType() === UserType::TYPE_ADMIN) || ($user->getUserType() === UserType::TYPE_DOCTOR)) {
            $repository = $this->getDoctrine()->getRepository(DrugType::class);
            $DrugType = $repository->findOneBy(array('id' => $id, 'removed' => false));
            if (!empty($DrugType)) {
                return View::create($DrugType, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('no type found', JsonResponse::HTTP_NOT_FOUND);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }


    /**
     * @Rest\Post("/api/drugType", name ="post_DrugType")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function create(Request $request, EntityManagerInterface $entity)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $name = $request->request;
           
          
            if (isset($name)) {
                $nb=0;
                    foreach ($name as $data){
                        $a[] = $data['type'];
                        $nb++;
                    }
                  
                    if($nb ==0 ){
                        return View::create('missing type of drugs', JsonResponse::HTTP_BAD_REQUEST); 
                    }
                         $count=count($a);
                        for($i=0;$i<$nb;$i++){
                            $drugtype = new DrugType();
                            if (getType($a[$i]) === "string"){
                                $drugtype->setType($a[$i]);
                                $drugtype->setCreatedBy($user);
                                $drugtype->setCreatedAt(new \DateTime());
                                $drugtype->setRemoved(false);
                                $entity->persist($drugtype);
                                $entity->flush();
                            }
                            else{
                                return View::create('type must be string!', JsonResponse::HTTP_BAD_REQUEST);
                            }
                          
                    }
                    $response = array(
                        'message' => 'drug type created',
                    );
                    return View::create($response, JsonResponse::HTTP_CREATED, []); 
                
              
            } else {
                return View::create('missing type!', JsonResponse::HTTP_BAD_REQUEST);
            }  

            $DrugType->setCreatedBy($user);
            $DrugType->setCreatedAt(new \DateTime());
            $DrugType->setRemoved(false);
            $entity->persist($DrugType);
            $entity->flush();
            $response = array(
                'message' => 'drug type created',
                'result' => $DrugType,

            );
            return View::create($response, JsonResponse::HTTP_CREATED, []);
        } else {

            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }


    /**
     * @param Request $request
     * @Rest\PATCH("/api/drugType/{id}", name ="patch_DrugType")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function patchAction(Request $request, $id)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $repository = $this->getDoctrine()->getRepository(DrugType::class);
            $DrugType = $repository->findOneBy(array('id' => $id, 'removed' => false));

            if (!is_null($DrugType)) {
                $type = $request->request->get('type');
                $typename = gettype($type);
                if (isset($type)) {
                    if ($typename == "string") {

                        $DrugType->setType($type);
                    } else {
                        return View::create('type name must be a string', JsonResponse::HTTP_BAD_REQUEST);
                    }
                }
                $DrugType->setUpdatedBy($user);
                $DrugType->setUpdatedAt(new \DateTime());
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                $response = array(
                    'message' => 'medication type updated',
                    'result' => $DrugType,

                );
                return View::create($response, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('medication type not Found', JsonResponse::HTTP_NOT_FOUND);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }
    /**
     * @Rest\Delete("/api/drugType/{id}", name ="delete_DrugType")
     */
    public function delete($id)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $repository = $this->getDoctrine()->getRepository(DrugType::class);
            $DrugType = $repository->findOneBy(array('id' => $id, 'removed' => false));
            if (!is_null($DrugType)) {
                $DrugType->setRemoved(true);
                $DrugType->setRemovedBy($user);
                $DrugType->setRemovedAt(new \DateTime());
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                return View::create('drug type deleted successfully', JsonResponse::HTTP_OK, []);
            } else {
                return View::create('drug type not Found', JsonResponse::HTTP_NOT_FOUND);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }
}
