<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Doctor;
use App\Entity\Patient;
use App\Entity\Hospital;
use App\Entity\UserType;
use FOS\RestBundle\View\View;
use Doctrine\ORM\EntityManager;
use Vich\UploaderBundle\Entity\File;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Validator\Constraints\Date;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RestApiProfileController extends FOSRestController
{
     /**
     * @Route("/api/login_check",name="api_login_check" )
     * @return JsonResponse
     */
    public function api_login(): JsonResponse
    {
        $user= $this->getUser();
        return new Response([
            "username" => $user->getUsername(),
            "email" => $user->getEmail(),
            "user_type" => $user->getUserType(),
        ]
    );
    }
    /**
     * @Rest\Get("/api/profile", name ="api_profile")
     * @Rest\View(serializerGroups={"users","admin"})
     */
    public function profile(){
        $user=$this->getUser();
        $data = array(
            'id' => $user->getId()
        );
         if ($user->getUserType() === UserType::TYPE_HOSPITAL) {
            $hospitalrepository = $this->getDoctrine()->getRepository(Hospital::class);
            $hosital = $hospitalrepository->findBy(array('created_by'=>$data));
            return View::create($hosital, JsonResponse::HTTP_OK);
         }
         if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $doctorrepository = $this->getDoctrine()->getRepository(Doctor::class);
            $doctor = $doctorrepository->findBy(array('created_by'=> $data));
           
            return View::create($doctor,JsonResponse::HTTP_OK,[]);
         }
        if ($user->getUserType() === UserType::TYPE_PATIENT) {
            $patientrepository = $this->getDoctrine()->getRepository(Patient::class);
            $patient = $patientrepository->findBy(array('created_by'=> $data));
            
            return View::create($patient, JsonResponse::HTTP_OK);
         } 
          if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $repository = $this->getDoctrine()->getRepository(User::class);
            $admin = $repository->findBy(array('id'=>$data));
            return View::create($admin, JsonResponse::HTTP_OK);
         }
    }
     /**
     * @param Request $request
     * @Rest\Patch("/api/profile", name ="patch_profile")
     * @Rest\View(serializerGroups={"users"})
     */
    public function patchAction(Request $request,UserPasswordEncoderInterface $encoder,SerializerInterface $serializer)
    {
        $user = $this->getUser();
        $data = $request-> getContent();
        $id = array(
            'id' => $user->getId()
        );
        $username= $request->request->get('username');

        if (isset($username)) {
            $user->setUsername($username);
        }
        $password= $request->request->get('password');
        if (isset($password)) {
            $hash = $encoder->encodePassword($user,$password);
            $user->setPassword($hash);
        }
        $country= $request->request->get('country');
        if (isset($country)) {
            $user->setCountry($country);
        }
        $gender= $request->request->get('gender');
    

        if (isset($gender)) {
            if ($gender == "Male" || $gender =="Female"){
            $user->setGender($gender);
            }
            else{
                return View::create('gender must be Male or Female', JsonResponse::HTTP_BAD_REQUEST, []);
            }
        }
        $city= $request->request->get('city');
        if (isset($city)) {
            $user->setCity($city);
        }
        $birth_date= $request->request->get('birth_date');
       $type= gettype($birth_date);
        if (isset($birth_date)) {
          
            $user->setBirthDate(new \DateTime($birth_date));
        }
        $zip_code= $request->request->get('zip_code');
        if (isset($zip_code)) {
            $user->setZipCode($zip_code);
        }
        $address= $request->request->get('address');
        if (isset($address)) {
            $user->setAddress($address);
        }
        $phone= $request->request->get('phone');
        if (isset($phone)) {
            $user->setPhone($phone);
        }
        $user->setUpdated($user);
        $user->setUpdatedAt(new \DateTime());
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        
        if ($user->getUserType() === UserType::TYPE_HOSPITAL) {
            $repository = $this->getDoctrine()->getRepository(Hospital::class);
            $hospital = $repository->findOneBy(array('created_by' => $user->getId()));
    
            if (!is_null($hospital)) {
                $type= $request->request->get('type');
                if (isset($type)) {
                    $hospital->setType($type);
                    $hospital->setUpdatedBy($user);
                    $hospital->setUpdatedAt(new \DateTime());
                    $em = $this->getDoctrine()->getManager();
                    $em->flush();
                    return View::create($hospital, JsonResponse::HTTP_OK, []);
                }
              
            }
        }
         if ($user->getUserType() === UserType::TYPE_PATIENT) {
            $repository = $this->getDoctrine()->getRepository(Patient::class);
            $patient = $repository->findOneBy(array('created_by' => $user->getId()));
                 if (!is_null($patient)) {
                    $weight= $request->request->get('weight');
                     if (isset($weight)) {
                    $patient->setWeight($weight);
                }
                $size= $request->request->get('size');
                     if (isset($size)) {
                    $patient->setSize($size);
                }
                $proffesion= $request->request->get('proffesion');
                     if (isset($proffesion)) {
                    $patient->setProffesion($proffesion);
                }
                     $patient->setUpdatedBy($user);
                     $patient->setUpdatedAt(new \DateTime());
                     $em = $this->getDoctrine()->getManager();
                     $em->flush();
                     return View::create($patient, JsonResponse::HTTP_OK, []);
                     
             }
        }
             if ($user->getUserType() === UserType::TYPE_DOCTOR) {
                $repository = $this->getDoctrine()->getRepository(Doctor::class);
                $doctor = $repository->findOneBy(array('created_by' => $user->getId()));
                if (!is_null($doctor)) {
                    $speciality= $request->request->get('speciality');
                    if (isset($speciality)) {
                        $doctor->setSpeciality($speciality);
                    }
                    $hospital= $request->request->get('hospital_id');
                    $hosp='hospital';
                    if (isset($hospital)){
                        $repository = $this->getDoctrine()->getRepository(Hospital::class);
                        $hospialid = $repository->findOneBy(array('id' => $hospital,'removed' => false));
                        if (!is_null($hospialid )) {
                            $doctor->setHospital($hospialid);
                        }
                        else{
                            return View::create('hospital not found', JsonResponse::HTTP_BAD_REQUEST, []);
                        }
                    }
                        $doctor->setUpdatedBy($user);
                        $doctor->setUpdatedAt(new \DateTime());
                        $em = $this->getDoctrine()->getManager();
                        $em->flush();
                        return View::create($doctor, JsonResponse::HTTP_OK, []);
                    
                  
                }
            }
           
          
    }
    /**
     * @param Request $request
     * @Rest\Patch("/api/UpdatePassword", name ="update_password")
     * @Rest\View(serializerGroups={"users"})
     */
    public function UpdatePassword(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = $this->getUser();

        $currentPassword = $request->request->get('current_password');
        $passwordValid = $encoder->isPasswordValid($user,$currentPassword );
        if($passwordValid){
            ($request->request->get('new_password')) ? $user->setPassword($encoder->encodePassword($user, $request->request->get('new_password'))) : '';  
         $em=$this->getDoctrine()->getManager();      
        $em->persist($user);
        $em->flush();
        }else{
            return View::create('your password is not valid', JsonResponse::HTTP_BAD_REQUEST, []);
        }
        $response=array(
            'message'=>'your password updated',
            'result'=>$user,
           
        );
        return View::create($response, JsonResponse::HTTP_OK, []);
    } 
    
    









   /**
     * @param Request $request
     * @return JsonResponse
     * @Rest\Post("/api/profile/picture", name ="patch_picture")
      * @Rest\View(serializerGroups={"users"})
     */
    public function uploadImage(Request $request)
    {
              
        $user = $this->getUser();
        $uploadedImage=$request->files->get('picture');
       
        /**
         * @var UploadedFile $image
         */
            $image=$uploadedImage;

            
            $imageName=md5(uniqid()).'.'.$image->guessExtension();
        
            $imagetype=$image->guessExtension();
           $path= $this->getParameter('image_directory');
           $serveur_ip=gethostbyname(gethostname());
           $path_uplaod='profile/images/';
             
           if ($imagetype == "jpeg" || $imagetype == "png" ){
                $image->move($path_uplaod,$imageName);
                
                $image_url=$path_uplaod.$imageName;
                $user->setPicture($image_url);
                $em=$this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                return View::create($user, JsonResponse::HTTP_OK, []);
              
            }
            else {
               
                return View::create("select picture please !", JsonResponse::HTTP_BAD_REQUEST, []);
            }
         

          
    }

}