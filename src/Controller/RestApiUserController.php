<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Doctor;
use DateTimeInterface;
use App\Entity\Patient;
use App\Entity\Hospital;
use App\Entity\UserType;
use App\Entity\Diagnostic;
use FOS\RestBundle\View\View;
use App\Repository\UserRepository;
use FOS\UserBundle\Model\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Endroid\QrCodeBundle\Response\QrCodeResponse;
use Endroid\QrCode\Factory\QrCodeFactoryInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RestApiUserController extends FOSRestController
{
   
    /**
    * @Rest\Get("/api/user/{id}", name ="search_user")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function searchUser($id)
    {
        $user=$this->getUser();
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $repository = $this->getDoctrine()->getRepository(User::class);
            $userr = $repository->findOneBy(array('id' => $id));
            if (!is_null($userr)) {
                return View::create($userr, JsonResponse::HTTP_OK, []);
        } else {
           return View::create('user Not Found', JsonResponse::HTTP_NOT_FOUND);
                  } 
                }
            else {
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
            }
            }

    /**
     * @Rest\Post("/Createuser")
     * @Rest\View(serializerGroups={"users"})
     */
    public function create(Request $request,SerializerInterface $s,EntityManagerInterface $entity,UserPasswordEncoderInterface $encoder,ValidatorInterface $validator){
        $r = $request-> getContent();
        try{
            $user= $s->deserialize($r,User::class,'json');
            $errors= $validator->validate($user);
            if(count($errors)> 0){
                return $this->json($errors,400);
            }
            $usertype= $request->request->get('user_type');
            if ($usertype == "doctor" || $usertype == "patient" ){
                $hash = $encoder->encodePassword($user,$user->getPassword());
                $user->setPassword($hash);
                $qrcode=md5(uniqid());
                $user->setQRCode($qrcode);
                $user->setEnabled(true);
                $user->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
                $user->setCreatedAt(new \DateTime());
            }else{
                $hash = $encoder->encodePassword($user,$user->getPassword());
                $user->setPassword($hash);
                $user->setEnabled(true);
                $user->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
                $user->setCreatedAt(new \DateTime());
            }
            $usertype= $request->request->get('user_type');
            if ($usertype == "patient" || $usertype =="doctor" || $usertype == "hospital" || $usertype == "admin"){
                $user->setUserType($usertype);
            }
            else{
                return View::create("user type must be one of  patient/doctor/hospital/admin", JsonResponse::HTTP_BAD_REQUEST, []);
            }
            
            $user->setRemove(false);
            $entity ->persist($user);
            $entity->flush();
            if ($usertype == "patient"){
                $patient = new Patient();
                $patient->setCreatedBy($user);
                $patient->setCreatedAt(new \DateTime());
                $entity ->persist($patient);
                $entity->flush();
               }
               if ($usertype == "doctor"){
                     $doctor = new Doctor();
                     $matricule= $request->request->get('matricule');
                       $doctor->setMatricule($matricule);
                
                        $doctor->setAffiliate(false);
                        $doctor->setCreatedBy($user);
                        $doctor->setCreatedAt(new \DateTime());
                        $doctor->setRemoved(false);
                        $entity ->persist($doctor);
                        $entity->flush();                   
                }
                    if ($usertype == "hospital"){
                        $hospital = new Hospital();
                        $hospital->setCreatedBy($user);
                        $hospital->setRemoved(false);
                        $hospital->setCreatedAt(new \DateTime());
                        $entity ->persist($hospital);
                        $entity->flush();
                    }
                    $response=array(
                        'message'=>"account created with success",
                        'result'=> $user,
                      
                    );
                    return View::create($user, Response::HTTP_CREATED);
                } catch (\Exception $ex) {
                    $response=array(
                        'message'=>"error",
                        'result'=>"cannot create this account",
                        
                    );
                    return View::create($response, JsonResponse::HTTP_BAD_REQUEST);
        }
    }
    /**
     * @Route("/api/user/{id}", name ="api_delete", methods={"DELETE"})
     */
    public function delete(UserRepository $user,EntityManagerInterface $entity,$id){
        $existingUser = $user->find($id);
        if (empty($existingUser))
        {
            $response=array(
                'code'=>1,
                'message'=>'user Not found !',
                'errors'=>null,
                'result'=>null
            );
            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }
        $entity->remove($existingUser);
        $entity->flush();
        $response=array(
            'code'=>0,
            'message'=>'account deleted successfuly!',
            'errors'=>null,
            'result'=>null
        );
        return new JsonResponse($response,200);
    }
    /**
     * @Route("/api/UserNumber", name="number_user", methods={"GET"})
     */
    public function userNumber( UserRepository $userRepository)
    {
        $userRepository = $this->getDoctrine()->getRepository(User::class);
       $num=$userRepository->findAll();
       $number=count($num);

       $response=array(
        'user_number'=>$number);
       
        return View::create($response, JsonResponse::HTTP_CREATED, []);    

    }

    /**
     * @Route("/api/PatientNumber", name="number_patient", methods={"GET"})
     */
    public function PatientNumber()
    {
        $userRepository = $this->getDoctrine()->getRepository(Patient::class);
       $num=$userRepository->findAll();
       $number=count($num);

       $response=array(
        'patient_number'=>$number);
       
        return View::create($response, JsonResponse::HTTP_CREATED, []);    

    }
     /**
     * @Rest\Get("/api/MesureGender", name ="bygender_diagnostic")
     * @Rest\View(serializerGroups={"users"})
     */
    public function countBygender()
    {
        $indication='anormal mesure';
        $a=array();
        $user=$this->getUser();
        $data = array(
            'id' => $user->getId()
        );
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $patientrepository = $this->getDoctrine()->getRepository(Patient::class);
           $Assigned = $patientrepository->findBy(array('assignedBy'=> $user->getId()));
           foreach ($Assigned as $dataa) {
           $a[]=$dataa->getCreatedBy()->getId();
           }
            if (!is_null($Assigned)) {
                $diagnosticrepository = $this->getDoctrine()->getRepository(Diagnostic::class);
                $diagnostic = $diagnosticrepository->getassigned($a,$indication);
                $diagnosticttotal = $diagnosticrepository->findBy(array('created_by'=> $a));
                $total=count($diagnostic);
                $totale=count($diagnosticttotal);
                if (!is_null($diagnostic)) {
                   $NBMale=0;
                    foreach ($diagnostic as $dataa) {
                        if($dataa->getCreatedBy()->getGender()=="Male")
                        $NBMale=$NBMale+1; 
                        }
                        $purcentageMale=intval($NBMale*100/$total);
                       
                        $NBFemale=0;
                        foreach ($diagnostic as $dataa) {
                            if($dataa->getCreatedBy()->getGender()=="Female")
                            $NBFemale=$NBFemale+1;
                        }
                        $purcentaFemale=intval($NBFemale*100/$total);
                        $response=array(
                            'MaleMesure'=>$purcentageMale ,
                            'FemaleMesure'=>$purcentaFemale  
                        );
                return View::create($response, JsonResponse::HTTP_OK, []);
            }
           }
        
    }

    }


}