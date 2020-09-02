<?php

namespace App\Controller;

use App\Entity\Doctor;
use App\Entity\DoctorAssignement;
use App\Entity\Hospital;
use App\Entity\Patient;
use App\Entity\User;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RestApiUserController extends FOSRestController
{

    /**
     * @Rest\Post("/Createuser")
     * @Rest\View(serializerGroups={"users"})
     */
    public function create(Request $request, SerializerInterface $s, EntityManagerInterface $entity, UserPasswordEncoderInterface $encoder, ValidatorInterface $validator)
    {
        $r = $request->getContent();
        try {
            $user = $s->deserialize($r, User::class, 'json');
            $errors = $validator->validate($user);
            if (count($errors) > 0) {
                return $this->json($errors, 400);
            }
            $usertype = $request->request->get('user_type');
            if ($usertype == "doctor" || $usertype == "patient") {
                $hash = $encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($hash);
                $qrcode = md5(uniqid());
                $user->setQRCode($qrcode);
                $user->setEnabled(true);
                $user->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
                $user->setCreatedAt(new \DateTime());
            } else {
                $hash = $encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($hash);
                $user->setEnabled(true);
                $user->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
                $user->setCreatedAt(new \DateTime());
            }
            $usertype = $request->request->get('user_type');
            if ($usertype == "patient" || $usertype == "doctor" || $usertype == "hospital" || $usertype == "admin") {
                $user->setUserType($usertype);
            } else {
                return View::create("user type must be one of  patient/doctor/hospital/admin", JsonResponse::HTTP_BAD_REQUEST, []);
            }

            $user->setRemove(false);
            $entity->persist($user);
            $entity->flush();
            if ($usertype == "patient") {
                $patient = new Patient();
                $patient->setCreatedBy($user);
                $patient->setCreatedAt(new \DateTime());
                $entity->persist($patient);
                $entity->flush();
            }
            if ($usertype == "doctor") {
                $doctor = new Doctor();
                $matricule = $request->request->get('medical_identity');
                $typematricule = gettype($matricule);
                if (isset($matricule)) {
                    if ($typematricule == "string") {
                        $doctor->setMatricule($matricule);
                    } else {
                        return View::create('medical_identity should be string!', JsonResponse::HTTP_BAD_REQUEST, []);
                    }
                } else {
                    return View::create('missing medical_identity of doctor !!', JsonResponse::HTTP_BAD_REQUEST, []);
                }
                $doctor->setAffiliate(false);
                $doctor->setCreatedBy($user);
                $doctor->setCreatedAt(new \DateTime());
                $doctor->setRemoved(false);
                $entity->persist($doctor);
                $entity->flush();
                $repository = $this->getDoctrine()->getRepository(User::class);
                $patientid = $repository->findOneBy(array('id' => 132));
                $doctorAssignment = new DoctorAssignement();
                $doctorAssignment->setIdPatient($patientid);
                $doctorAssignment->setIdDoctor($user);
                $doctorAssignment->setRequestDate(new \DateTime());
                $doctorAssignment->setStatus("Accepted");
                $doctorAssignment->setCreatedBy($user);
                $doctorAssignment->setEnabled(true);
                $doctorAssignment->setRemoved(false);
                $doctorAssignment->setCreatedAt(new \DateTime());
                $doctorAssignment->setInvitationToken(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
                $entity->persist($doctorAssignment);
                $entity->flush();
            }
            if ($usertype == "hospital") {
                $hospital = new Hospital();
                $hospital->setCreatedBy($user);
                $hospital->setRemoved(false);
                $hospital->setCreatedAt(new \DateTime());
                $entity->persist($hospital);
                $entity->flush();
            }

            $response = array(
                'message' => "account created with success",
                'result' => $user,

            );
            return View::create($user, Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            $response = array(
                'message' => "error",
                'result' => "cannot create this account",

            );
            return View::create($response, JsonResponse::HTTP_BAD_REQUEST);
        }
    }
    /**
     * @Route("/api/user/{id}", name ="api_delete", methods={"DELETE"})
     */
    public function delete(UserRepository $user, EntityManagerInterface $entity, $id)
    {
        $existingUser = $user->find($id);
        if (empty($existingUser)) {
            $response = array(
                'code' => 1,
                'message' => 'user Not found !',
                'errors' => null,
                'result' => null,
            );
            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }
        $entity->remove($existingUser);
        $entity->flush();
        $response = array(
            'code' => 0,
            'message' => 'account deleted successfuly!',
            'errors' => null,
            'result' => null,
        );
        return new JsonResponse($response, 200);
    }
    /**
     * @Route("/api/UserNumber", name="number_user", methods={"GET"})
     */
    public function userNumber(UserRepository $userRepository)
    {
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $num = $userRepository->findAll();
        $number = count($num);

        $response = array(
            'user_number' => $number);

        return View::create($response, JsonResponse::HTTP_CREATED, []);

    }

    /**
     * @Route("/api/PatientNumber", name="number_patient", methods={"GET"})
     */
    public function PatientNumber()
    {
        $userRepository = $this->getDoctrine()->getRepository(Patient::class);
        $num = $userRepository->findAll();
        $number = count($num);

        $response = array(
            'patient_number' => $number);

        return View::create($response, JsonResponse::HTTP_CREATED, []);

    }
   
    /**
     * @Rest\Post("/register")
     * @Rest\View(serializerGroups={"users"})
     */
    public function register(Request $request, SerializerInterface $s, EntityManagerInterface $entity, UserPasswordEncoderInterface $encoder, ValidatorInterface $validator)
    {
        $r = $request->getContent();
        try {
            $user = $s->deserialize($r, User::class, 'json');
            $errors = $validator->validate($user);
            if (count($errors) > 0) {
                return $this->json($errors, 400);
            }
            $usertype = $request->request->get('user_type');
            if ($usertype == "doctor" || $usertype == "patient") {
                $hash = $encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($hash);
                $qrcode = md5(uniqid());
                $user->setQRCode($qrcode);
                $user->setEnabled(true);
                $user->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
                $user->setCreatedAt(new \DateTime());
            } else {
                $hash = $encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($hash);
                $user->setEnabled(true);
                $user->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
                $user->setCreatedAt(new \DateTime());
            }
            $usertype = $request->request->get('user_type');
            if ($usertype == "patient" || $usertype == "doctor" || $usertype == "hospital" || $usertype == "admin") {
                $user->setUserType($usertype);
            } else {
                return View::create("user type must be one of  patient/doctor/hospital/admin", JsonResponse::HTTP_BAD_REQUEST, []);
            }

            $user->setRemove(false);
            $entity->persist($user);
            $entity->flush();
            if ($usertype === "patient") {
                $patient = new Patient();
                $weight = $request->request->get('weight');
                $typeweight = gettype($weight);
                if(isset($weight)){
                    if($typeweight === "double"){
                $patient->setWeight($weight); 
                    }else{
                        return View::create("weight must be double!", JsonResponse::HTTP_BAD_REQUEST, []);
                    }  
                }
                else{
                    return View::create("missing weight!", JsonResponse::HTTP_BAD_REQUEST, []);
                }
                $size = $request->request->get('size');
                $typesize = gettype($size);
                if(isset($size)){
                    if($typesize === "double"){
                    $patient->setSize($size);   
                }
                else{
                    return View::create("size must be double!", JsonResponse::HTTP_BAD_REQUEST, []);
                }
            }
                else{
                    return View::create("missing size!", JsonResponse::HTTP_BAD_REQUEST, []);
                }
                $pathology = $request->request->get('pathology');
                $typepathology = gettype($pathology);
                if(isset($pathology)){
                    if($typepathology === "string"){
                    $patient->setPathology($pathology);   
                }
                else{
                    return View::create("pathology must be string!", JsonResponse::HTTP_BAD_REQUEST, []);
                }
            }
                else{
                    return View::create("missing pathology!", JsonResponse::HTTP_BAD_REQUEST, []);
                }
                $proffesion = $request->request->get('proffesion');
                $typeprofesion = gettype($proffesion);
                if(isset($proffesion)){
                    if($typeprofesion === "string"){
                    $patient->setProffesion($proffesion);   
                }
                else {
                    return View::create("proffesion must be string!", JsonResponse::HTTP_BAD_REQUEST, []);
                }
            }
            
                $patient->setCreatedBy($user);
                $patient->setCreatedAt(new \DateTime());
                $entity->persist($patient);
                $entity->flush();
            }
            if ($usertype == "doctor") {
                $doctor = new Doctor();
                $speciality = $request->request->get('speciality');
                $typespeciality = gettype($speciality);
                if (isset($speciality)) {
                    if ($typespeciality == "string") {
                        $doctor->setSpeciality($speciality);
                    } else {
                        return View::create('speciality should be string!', JsonResponse::HTTP_BAD_REQUEST, []);
                    }
                } else {
                    return View::create('missing speciality!', JsonResponse::HTTP_BAD_REQUEST, []);
                }
                $matricule = $request->request->get('medical_identity');
                $typematricule = gettype($matricule);
                if (isset($matricule)) {
                    if ($typematricule == "string") {
                        $doctor->setMatricule($matricule);
                    } else {
                        return View::create('medical_identity should be string!', JsonResponse::HTTP_BAD_REQUEST, []);
                    }
                } else {
                    return View::create('missing medical_identity of doctor !!', JsonResponse::HTTP_BAD_REQUEST, []);
                }
                $doctor->setAffiliate(false);
                $doctor->setCreatedBy($user);
                $doctor->setCreatedAt(new \DateTime());
                $doctor->setRemoved(false);
                $entity->persist($doctor);
                $entity->flush();
                $repository = $this->getDoctrine()->getRepository(User::class);
                $patientid = $repository->findOneBy(array('id' => 132));
                $doctorAssignment = new DoctorAssignement();
                $doctorAssignment->setIdPatient($patientid);
                $doctorAssignment->setIdDoctor($user);
                $doctorAssignment->setRequestDate(new \DateTime());
                $doctorAssignment->setStatus("Accepted");
                $doctorAssignment->setCreatedBy($user);
                $doctorAssignment->setEnabled(true);
                $doctorAssignment->setRemoved(false);
                $doctorAssignment->setCreatedAt(new \DateTime());
                $doctorAssignment->setInvitationToken(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
                $entity->persist($doctorAssignment);
                $entity->flush();
            }
            if ($usertype == "hospital") {
                $hospital = new Hospital();
                $hospital->setCreatedBy($user);
                $hospital->setRemoved(false);
                $hospital->setCreatedAt(new \DateTime());
                $entity->persist($hospital);
                $entity->flush();
            }

            $response = array(
                'message' => "account created with success",
                'result' => $user,

            );
            return View::create($user, Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            $response = array(
                'message' => "error",
                'result' => "cannot create this account",

            );
            return View::create($response, JsonResponse::HTTP_BAD_REQUEST);
        }
    }



}

