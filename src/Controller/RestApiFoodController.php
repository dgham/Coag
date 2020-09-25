<?php

namespace App\Controller;

use DateTime;
use Exception;
use App\Entity\Note;
use App\Entity\User;
use App\Entity\Food;
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

class RestApiFoodController extends FOSRestController
{

    /**
     * @Rest\Get("/api/food", name ="api_Food")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function index()
    {
        $user = $this->getUser();
        $data = array(
            'id' => $user->getId()
        );
        if (($user->getUserType() === UserType::TYPE_ADMIN) || ($user->getUserType() === UserType::TYPE_PATIENT)) {
            $repository = $this->getDoctrine()->getRepository(Food::class);
            $Food = $repository->findAll(array('id' => 'DESC', 'removed' => false));
            if (!empty($Food)) {
                return View::create($Food, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('No food found', JsonResponse::HTTP_OK, []);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }
    /**
     * @Rest\Get("/api/food/{id}", name ="search_Food")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function searchFood($id)
    {
        $user = $this->getUser();
        if (($user->getUserType() === UserType::TYPE_ADMIN) || ($user->getUserType() === UserType::TYPE_PATIENT)) {
            $repository = $this->getDoctrine()->getRepository(Food::class);
            $Food = $repository->findOneBy(array('id' => $id, 'removed' => false));
            if (!empty($Food)) {
                return View::create($Food, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('food not found', JsonResponse::HTTP_NOT_FOUND);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }


    /**
     * @Rest\Post("/api/food", name ="post_Food")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function create(Request $request, EntityManagerInterface $entity)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $name = $request->request;
           
            $typename = gettype($name);
            if (isset($name)) {
                $nb=0;
                    foreach ($name as $data){
                        $a[] = $data['name'];
                        $nb++;
                    }
             
                         $count=count($a);
                        for($i=0;$i<$nb;$i++){
                            $food = new Food();
                            $food->setName($a[$i]);
                            $food->setCreatedBy($user);
                            $food->setCreatedAt(new \DateTime());
                            $food->setRemoved(false);
                            $entity->persist($food);
                            $entity->flush();
                    }
                  
                   
                    $response = array(
                        'message' => 'food created successfully',
                    );
                    return View::create($response, JsonResponse::HTTP_CREATED, []); 
            } else {
                return View::create('missing food name!', JsonResponse::HTTP_BAD_REQUEST);
            }  
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }
    /**
     * @param Request $request
     *
     * @Rest\PATCH("/api/food/{id}", name ="patch_Food")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function patchAction(Request $request, $id)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $repository = $this->getDoctrine()->getRepository(Food::class);
            $Food = $repository->findOneBy(array('id' => $id, 'removed' => false));
            if (!is_null($Food)) {
                $name = $request->request->get('name');
                $typename = gettype($name);
                if (isset($name)) {
                    if ($typename == "string") {
                        $Food->setName($name);
                    } else {
                        return View::create('Food name must be a string', JsonResponse::HTTP_BAD_REQUEST);
                    }
                    $Food->setUpdatedBy($user);
                    $Food->setUpdatedAt(new \DateTime());
                    $em = $this->getDoctrine()->getManager();
                    $em->flush();
                    $response = array(
                        'message' => 'food updated',
                        'result' => $Food,
                    );
                    return View::create($response, JsonResponse::HTTP_OK, []);
                } else {
                    return View::create('food not Found', JsonResponse::HTTP_NOT_FOUND);
                }
            } else {
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }
    /**
     * @Rest\Delete("/api/food/{id}", name ="delete_Food")
     */
    public function delete($id)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $repository = $this->getDoctrine()->getRepository(Food::class);
            $Food = $repository->findOneBy(array('id' => $id, 'removed' => false));
            if (!is_null($Food)) {
                $Food->setRemoved(true);
                $Food->setRemovedBy($user);
                $Food->setRemovedAt(new \DateTime());
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                return View::create('food deleted', JsonResponse::HTTP_OK, []);
            } else {
                return View::create('food not Found', JsonResponse::HTTP_NOT_FOUND);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }
}
