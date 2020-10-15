<?php

namespace App\Controller;

use App\Entity\Drug;
use App\Entity\DrugType;
use App\Entity\User;
use App\Entity\UserType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RestApiDrugController extends FOSRestController
{

    /**
     * @Rest\Get("/api/drug", name ="medication_listt")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function index()
    {
        $user = $this->getUser();
        $data = array(
            'id' => $user->getId(),
        );
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $repository = $this->getDoctrine()->getRepository(Drug::class);
            $treatment = $repository->findBy(array('removed' => false));
            if (!empty($treatment)) {
                return View::create($treatment, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('no medication found', JsonResponse::HTTP_NOT_FOUND, []);
            }
        }
        
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $repository = $this->getDoctrine()->getRepository(Drug::class);
            $treatment = $repository->findBy(array('removed' => false));
            if (!empty($treatment)) {
                return View::create($treatment, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('no medication found', JsonResponse::HTTP_NOT_FOUND, []);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }

    /**
     * @Rest\Get("/api/drug/{id}", name ="Drug_search")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function searchMedication($id)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $repository = $this->getDoctrine()->getRepository(Drug::class);
            $treatment = $repository->findOneBy(array('id' => $id, 'removed' => false));
            if (!empty($treatment)) {
                return View::create($treatment, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('no medication found', JsonResponse::HTTP_NOT_FOUND, []);
            }
      
        }
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $repository = $this->getDoctrine()->getRepository(Drug::class);
            $treatment = $repository->findOneBy(array('id' => $id, 'removed' => false));
            if (!empty($treatment)) {
                return View::create($treatment, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('no medication found', JsonResponse::HTTP_NOT_FOUND, []);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }

    /**
     * @Rest\Post("/api/drug", name ="create_ListMedication")
     * @Rest\View(serializerGroups={"users"})
     */
    public function create(Request $request, EntityManagerInterface $entity)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $treatment = new Drug();
            $name = $request->request->get('name');
            $typname = gettype($name);
            if (isset($name)) {
                if ($typname == "string") {
                    $treatment->setName($name);
                } else {
                    return View::create(' name of medication should be string!', JsonResponse::HTTP_BAD_REQUEST, []);
                }
            } else {
                return View::create('missing name of treatment!!', JsonResponse::HTTP_BAD_REQUEST, []);
            }

            $effect = $request->request->get('effect');

            $typeeffect = gettype($effect);
            if (isset($effect)) {
                if ($typeeffect == "string") {
                    $treatment->setEffect($effect);
                } else {
                    return View::create(' effect of medication should be string!', JsonResponse::HTTP_BAD_REQUEST, []);
                }
            } else {
                return View::create('missing effect of the medication !!', JsonResponse::HTTP_BAD_REQUEST, []);
            }
            $uploadedImage = $request->files->get('picture');
            if (!is_null($uploadedImage)) {

                /**
                 * @var UploadedFile $image
                 */
                $image = $uploadedImage;

                $imageName = md5(uniqid()) . '.' . $image->guessExtension();
                $type = $image->getType();
                $size = $image->getSize();
                $imagetype = $image->guessExtension();
                $path = $this->getParameter('treatment_directory');
                $path_uplaod = 'Assets/Treatments/';
                if ($imagetype == "jpeg" || $imagetype == "png") {
                    $image->move($path_uplaod, $imageName);
                    $image_url = $path_uplaod . $imageName;
                    $treatment->setPicture($image_url);
                } else {

                    return View::create("select picture please !", JsonResponse::HTTP_BAD_REQUEST, []);
                }
            }
            $treatment->setCreatedBy($user);
            $treatment->setCreatedAt(new \DateTime());
            $treatment->setRemoved(false);
            $entity->persist($treatment);
            $entity->flush();
            $response = array(
                'message' => 'medication Added',
                'result' => $treatment,

            );
            return View::create($response, Response::HTTP_CREATED, []);
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }

    /**
     * @param Request $request
     * @Rest\Patch("/api/drug/{id}", name ="patch_Drug")
     * @Rest\View(serializerGroups={"users"})
     */
    public function patchAction(Request $request, $id)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $repository = $this->getDoctrine()->getRepository(Drug::class);
            $treatment = $repository->findOneBy(array('id' => $id, 'removed' => false));
            if (!is_null($treatment)) {
                $name = $request->request->get('name');
                $typname = gettype($name);
                if (isset($name)) {
                    if ($typname == "string") {
                        $treatment->setName($name);
                    } else {
                        return View::create(' name of medication should be string!', JsonResponse::HTTP_BAD_REQUEST, []);
                    }
                }
                $effect = $request->request->get('effect');
                $typeeffect = gettype($effect);
                if (isset($effect)) {

                    if ($typeeffect == "string") {
                        $treatment->setEffect($effect);
                    } else {
                        return View::create(' effect of medication should be string!', JsonResponse::HTTP_BAD_REQUEST, []);
                    }
                }
                $treatment->setUpdatedBy($user);
                $treatment->setUpdatedAt(new \DateTime());
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                $response = array(
                    'message' => 'medication updated',
                    'result' => $treatment,

                );
                return View::create($response, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('treatment Not Found', JsonResponse::HTTP_NOT_FOUND);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }


    /**
     * @Rest\Delete("/api/drug/{id}", name ="delete_Drug")
     * @Rest\View(serializerGroups={"users"})
     */
    public function delete($id)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $repository = $this->getDoctrine()->getRepository(Drug::class);
            $treatment = $repository->findOneBy(array('id' => $id, 'removed' => false));
            if (!is_null($treatment)) {
                $treatment->setRemoved(true);
                $treatment->setRemovedBy($user);
                $treatment->setRemovedAt(new \DateTime());
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                return View::create('medication deleted', JsonResponse::HTTP_OK, []);
            } else {
                return View::create(' Not Found', JsonResponse::HTTP_NOT_FOUND);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }
}