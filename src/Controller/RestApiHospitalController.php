<?php

namespace App\Controller;

use App\Entity\Doctor;
use App\Entity\Hospital;
use App\Entity\UserType;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use ReflectionClass;
use Symfony\Component\HttpFoundation\JsonResponse;

class RestApiHospitalController extends FOSRestController
{

    /**
     * @Rest\Get("/api/hospital", name ="getAll_hosptital")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function getAllHospital()
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $repository = $this->getDoctrine()->getRepository(Hospital::class);
            $hospital = $repository->findAll();
            if (!empty($hospital)) {
                return View::create($hospital, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('no hospital Found', JsonResponse::HTTP_OK);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }

    /**
     * @Rest\Get("/api/hospital", name ="get_hosptital")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function index()
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $repository = $this->getDoctrine()->getRepository(Hospital::class);
            $hospital = $repository->findAll();
            if (!empty($hospital)) {
                return View::create($hospital, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('hospital not Found', JsonResponse::HTTP_NOT_FOUND);
            }
        }
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $repository = $this->getDoctrine()->getRepository(Doctor::class);
            $doctor = $repository->findOneBy(array('created_by' => $user->getId(), 'removed' => false));
            $hospital = $doctor->getHospital();

            if (!empty($hospital)) {
                $reflectionClass = new ReflectionClass(get_class($doctor));
                $array = array();
                foreach ($reflectionClass->getProperties() as $property) {
                    $property->setAccessible(true);
                    $array[$property->getName()] = $property->getValue($doctor);
                    $property->setAccessible(false);
                }

                return View::create($hospital, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('you are not belong to a hospital', JsonResponse::HTTP_NOT_FOUND);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }

    /**
     * @Rest\Get("/api/hospital/{id}", name ="search_hosptital")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function searchHospital($id)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $repository = $this->getDoctrine()->getRepository(Hospital::class);
            $hospital = $repository->findOneBy(array('id' => $id, 'removed' => false));
            if (!empty($hospital)) {
                return View::create($hospital, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('hospital not Found', JsonResponse::HTTP_NOT_FOUND);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }
}
