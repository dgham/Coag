<?php

namespace App\Controller;

use App\Entity\Speciality;
use App\Entity\UserType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class RestApiSpecialityController extends FOSRestController
{

  
      /**
     * @Rest\Get("/speciality", name ="api_speciality")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function takeSpeciality()
    {
        $user = $this->getUser();
        $data = array(
            'id' => $user->getId(),
        );
            $repository = $this->getDoctrine()->getRepository(Speciality::class);
            $speciality = $repository->findAll(array('id' => 'DESC', 'removed' => false));
            if (!empty($speciality)) {
                return View::create($speciality, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('No data found', JsonResponse::HTTP_OK, []);
            }
       
    }


  
     /**
     * @Rest\Get("/speciality/{id}", name ="search_speciality")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function searchSpecialitybyid($id)
    {
        $user = $this->getUser();
      
            $repository = $this->getDoctrine()->getRepository(Speciality::class);
            $speciality = $repository->findOneBy(array('id' => $id, 'removed' => false));
            if (!empty($speciality)) {
                return View::create($speciality, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('no data found', JsonResponse::HTTP_NOT_FOUND);
            }

    }
    

    /**
     * @Rest\Post("/api/speciality", name ="post_speciality")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function create(Request $request, EntityManagerInterface $entity)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $name = $request->request->get('speciality_name');
            $typename = gettype($name);
        
            if (isset($name)) {
                if ($typename == "string") {
                    foreach ($name as $data){
                        $a[] = $data['name'];
                        $nb++;
                    }
             
                         $count=count($a);
                        for($i=0;$i<$nb;$i++){
                    $speciality = new Speciality();
                    $speciality->setSpecialityName($name);
                    $speciality->setCreatedBy($user);
                    $speciality->setCreatedAt(new \DateTime());
                    $speciality->setRemoved(false);
                    $entity->persist($speciality);
                    $entity->flush();
                        }
                        $response = array(
                            'message' => 'speciality created',
                            'result' => $speciality,
            
                        );
                        return View::create($response, JsonResponse::HTTP_CREATED, []);
                } else {
                    return View::create('speciality name must be a string', JsonResponse::HTTP_BAD_REQUEST);
                }
            } else {
                return View::create('missing speciality_name!', JsonResponse::HTTP_BAD_REQUEST);
            }

         
           
        } else {

            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }

    /**
     * @param Request $request
     *
     * @Rest\PATCH("/api/speciality/{id}", name ="patch_speciality")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function patchAction(Request $request, $id)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $repository = $this->getDoctrine()->getRepository(Speciality::class);
            $speciality = $repository->findOneBy(array('id' => $id, 'removed' => false));

            if (!is_null($speciality)) {
                $name = $request->request->get('speciality_name');
                $typename = gettype($name);
                if (isset($name)) {
                    if ($typename == "string") {
                        $speciality->setSpecialityName($name);
                    } else {
                        return View::create('speciality name must be a string', JsonResponse::HTTP_BAD_REQUEST);
                    }
                }
                $speciality->setUpdatedBy($user);
                $speciality->setUpdatedAt(new \DateTime());
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                $response = array(
                    'message' => 'speciality updated',
                    'result' => $speciality,

                );
                return View::create($response, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('speciality not Found', JsonResponse::HTTP_NOT_FOUND);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }
    /**
     * @Rest\Delete("/api/speciality/{id}", name ="delete_speciality")
     */
    public function delete($id)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $repository = $this->getDoctrine()->getRepository(Speciality::class);
            $speciality = $repository->findOneBy(array('id' => $id, 'removed' => false));
            if (!is_null($speciality)) {
                $speciality->setRemoved(true);
                $speciality->setRemovedBy($user);
                $speciality->setRemovedAt(new \DateTime());
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                return View::create('speciality deleted', JsonResponse::HTTP_OK, []);
            } else {
                return View::create('speciality not Found', JsonResponse::HTTP_NOT_FOUND);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }
}