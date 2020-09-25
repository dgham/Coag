<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use ReflectionClass;
use App\Entity\Doctor;
use App\Entity\Patient;
use App\Entity\Hospital;
use App\Entity\UserType;
use App\Entity\Measure;
use FOS\RestBundle\View\View;
use App\Entity\DoctorAssignement;
use App\Repository\PatientRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RestApiPatientController extends FOSRestController
{
    /**
     * @Rest\Get("/api/patient", name ="api_patient")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function index()
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $patientRepository = $this->getDoctrine()->getRepository(Patient::class);
            $patient = $patientRepository->findAll();
            return View::create($patient, JsonResponse::HTTP_OK, []);
        }
        if ($user->getUserType() === UserType::TYPE_HOSPITAL) {
            $repository = $this->getDoctrine()->getRepository(Hospital::class);
            $hospital = $repository->findOneBy(array('created_by' => $user->getId(), 'removed' => false));
            $reflectionClass = new ReflectionClass(get_class($hospital));
            $array = array();
            foreach ($reflectionClass->getProperties() as $property) {
                $property->setAccessible(true);
                $array[$property->getName()] = $property->getValue($hospital);
                $property->setAccessible(false);
            }
            $repository = $this->getDoctrine()->getRepository(Doctor::class);
            $doctor = $repository->findBy(array('hospital' => $array['id'], 'removed' => false, 'affiliate' => true));
            foreach ($doctor as $data) {
                $a[] = $data->getCreatedBy()->getId();
            }
            $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
            $doctorassignement = $repository->findBy(array('id_doctor' => $a, 'status' => 'Accepted', 'removed' => false));
            if (!empty($doctorassignement)) {
                return View::create($doctorassignement, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('no doctor found !', JsonResponse::HTTP_NOT_FOUND, []);
            }

            if ($user->getUserType() === UserType::TYPE_DOCTOR) {
                $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
                $doctorassignement = $repository->findBy(array('id_doctor' => $user->getId(), 'status' => 'Accepted', 'removed' => false));
                if (!is_null($doctorassignement)) {
                    return View::create($doctoeassignement, JsonResponse::HTTP_OK, []);
                } else {
                    return View::create('No data found', JsonResponse::HTTP_OK, []);
                }
            } else {
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
            }
        }
    }
    /**
     * @Rest\Get("/api/patient/{id}", name ="search_patient")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function searchPatient($id)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
            $doctorassignement = $repository->findBy(array('id_doctor' => $user->getId(), 'status' => 'Accepted', 'removed' => false));
            if (!empty($doctoeassignement)) {
                $id_patient = $doctorassignement->getIdPatient();
                $repository = $this->getDoctrine()->getRepository(Patient::class);
                $patient = $repository->findOneBy(array('created_by' => $id_patient));
                if (!is_null($patient)) {
                    return View::create($patient, JsonResponse::HTTP_OK, []);
                } else {
                    return View::create('not authorized', JsonResponse::HTTP_FORBIDDEN, []);
                }
            } else {
                return View::create('Sorry you are not assigned to patient', JsonResponse::HTTP_NOT_FOUND, []);
            }
        }
        if ($user->getUserType() === UserType::TYPE_HOSPITAL) {
            $repository = $this->getDoctrine()->getRepository(Hospital::class);
            $hospital = $repository->findOneBy(array('created_by' => $user->getId(), 'removed' => false));
            $reflectionClass = new ReflectionClass(get_class($hospital));
            $array = array();
            foreach ($reflectionClass->getProperties() as $property) {
                $property->setAccessible(true);
                $array[$property->getName()] = $property->getValue($hospital);
                $property->setAccessible(false);
            }
            $repository = $this->getDoctrine()->getRepository(Doctor::class);
            $doctor = $repository->findBy(array('hospital' => $array['id'], 'removed' => false));
            foreach ($doctor as $data) {
                $a[] = $data->getCreatedBy()->getId();
            }

            $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
            $doctorassignement = $repository->findBy(array('id_doctor' => $a, 'status' => 'Accepted', 'removed' => false));
            foreach ($doctorassignement as $data) {
                $b[] = $data->getIdPatient()->getId();
            }

            $repository = $this->getDoctrine()->getRepository(Patient::class);
            $patient = $repository->findOneBy(array('id' => $id, 'created_by' => $b));
            if (!is_null($patient)) {
                return View::create($patient, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('No patient found', JsonResponse::HTTP_NOT_FOUND, []);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }



    /**
     * @Rest\Get("/api/patientAssignedNumber", name ="num_patient")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function countPatient()
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $patientRepository = $this->getDoctrine()->getRepository(Patient::class);
            $patient = $patientRepository->findBy(array('assignedBy' => $user->getId()));
            if (!is_null($patient)) {
                $nb = count($patient);
                $response = array(
                    'PatientAssigned_number' => $nb,
                );
                return View::create($response, JsonResponse::HTTP_OK, []);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }



    /**
     * @Rest\Get("/api/patientByUser/{id}", name ="search_byuser")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function searchPatientbyUser($id)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $repository = $this->getDoctrine()->getRepository(Patient::class);
            $patient = $repository->findOneBy(array('created_by' => $id));

            if (!is_null($patient)) {
                return View::create($patient, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }

    /**
     * @Rest\Get("/api/latestMesure", name ="mesure_latest")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function getLatestMesure()
    {
        $a = array();
        $user = $this->getUser();
        $data = array(
            'id' => $user->getId()
        );
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $patientrepository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
            $Assigned = $patientrepository->findBy(array('id_doctor' => $user->getId(), 'removed' => false, 'status' => 'Accepted'));
            foreach ($Assigned as $dataa) {
                array_push($a, $dataa->getIdPatient());
            }
            $Measurerepository = $this->getDoctrine()->getRepository(Measure::class);
            $latestdate = $Measurerepository->findByMesuremaxDate($a);
            $response = array(
                'latest_result' => $latestdate

            );

            return View::create($response, JsonResponse::HTTP_OK, []);
        }

        if ($user->getUserType() === UserType::TYPE_PATIENT) {

            $Measurerepository = $this->getDoctrine()->getRepository(Measure::class);
            $latestdate = $Measurerepository->findByMesuremaxDate($user->getId());
            $response = array(
                'latest_result' => $latestdate

            );

            return View::create($response, JsonResponse::HTTP_OK, []);
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }
}
