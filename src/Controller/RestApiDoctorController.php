<?php

namespace App\Controller;

use App\Entity\User;
use ReflectionClass;
use App\Entity\Doctor;
use App\Entity\Patient;
use App\Entity\Hospital;
use App\Entity\UserType;
use App\Entity\Matricule;
use App\Entity\Diagnostic;
use FOS\RestBundle\View\View;
use App\Repository\DoctorRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RestApiDoctorController extends AbstractController
{

            /**
             * @Rest\Get("/api/doctor", name ="api_doctors")
             * @Rest\View(serializerGroups={"admin"})
             */
            public function index()
            {
                $user = $this->getUser();
                    if ($user->getUserType() === UserType::TYPE_HOSPITAL) {
                        $repository = $this->getDoctrine()->getRepository(Hospital::class);
                        $hospital = $repository->findOneBy(array('created_by' => $user->getId(),'removed' => false));
                        $reflectionClass = new ReflectionClass(get_class($hospital));
                        $array = array();
                        foreach ($reflectionClass->getProperties() as $property) {
                            $property->setAccessible(true);
                            $array[$property->getName()] = $property->getValue($hospital);
                            $property->setAccessible(false);
                        }
                    
                        $repository = $this->getDoctrine()->getRepository(Doctor::class);
                        $doctor = $repository->findBy(array('hospital' =>$array['id'],'removed' => false,'affiliate'=>true));
                    

                if (!is_null($hospital)) {
                    return View::create($doctor, JsonResponse::HTTP_OK, []);
                } else {
                    return View::create('doctors Not Found', JsonResponse::HTTP_NOT_FOUND);
                        } 
                } 

                else {
                    return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                        }

            
            }

             /**
             * @Rest\Get("/api/doctor/NotAffiliate", name ="NotAffiliate_doctor")
             * @Rest\View(serializerGroups={"admin"})
             */
            public function Affiliate()
            {
                $user = $this->getUser();
                    if ($user->getUserType() === UserType::TYPE_HOSPITAL) {
                        $repository = $this->getDoctrine()->getRepository(Hospital::class);
                        $hospital = $repository->findOneBy(array('created_by' => $user->getId(),'removed' => false));
                        $reflectionClass = new ReflectionClass(get_class($hospital));
                        $array = array();
                        foreach ($reflectionClass->getProperties() as $property) {
                            $property->setAccessible(true);
                            $array[$property->getName()] = $property->getValue($hospital);
                            $property->setAccessible(false);
                        }
                    
                        $repository = $this->getDoctrine()->getRepository(Doctor::class);
                        $doctor = $repository->findBy(array('hospital' =>$array['id'],'removed' => false,'affiliate'=>false));
                    

                if (!is_null($doctor)) {
                    return View::create($doctor, JsonResponse::HTTP_OK, []);
                } else {
                    return View::create('doctors Not Found', JsonResponse::HTTP_NOT_FOUND);
                        } 
                } 

                else {
                    return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                        }

            
            }


            /**
             * @param Request $request
             *
         * @Rest\Patch("/api/doctor/UpdateAffiliate", name ="patch_doctorr")
         * @Rest\View(serializerGroups={"doctors"})
         */
            public function patchdoctor(Request $request)
            {
                $user = $this->getUser();
                    if ($user->getUserType() === UserType::TYPE_DOCTOR) {
                        $hospitalid= $request->request->get('hospital_id');
                    if (isset($hospitalid)) {
                        
                        $repository = $this->getDoctrine()->getRepository(Hospital::class);
                        $hospital = $repository->findOneBy(array('id' => $hospitalid,'removed' => false));
                        if (!is_null($hospital)) {
                        $repository = $this->getDoctrine()->getRepository(Doctor::class);
                        $doctor = $repository->findOneBy(array('id'=>$user->getId(),'removed' => false));

                        if (!is_null($doctor)) {
                            $doctor->setHospital($hospital);
                            $doctor->setUpdatedBy($user);
                            $doctor->setUpdatedAt(new \DateTime());
                            $em = $this->getDoctrine()->getManager();
                            $em->flush();
                            $response=array(
                                'message'=>'affiliate changed with success',
                                'result'=>$doctor,
                            
                            );
                            return View::create($response, JsonResponse::HTTP_OK, []);
                            }  
                            
                            else{
                                return View::create('doctor not found', JsonResponse::HTTP_NOT_FOUND);  
                            }
                            }
                            else{
                                return View::create('hospital not found', JsonResponse::HTTP_NOT_FOUND);  
                            }
                            }
                            else{
                                return View::create('hospital MISSING', JsonResponse::HTTP_BAD_REQUEST);  
                            }
                            }
                            else {
                                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                                    }
                }
                /**
                 *
                 * @Rest\get("/api/doctorAffiliation", name ="affiliation_doctor")
                 * @Rest\View(serializerGroups={"admin"})
                     */
                    public function getAffiliationdoctor(Request $request)
                    {
                        $user = $this->getUser();
                            if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            
                        $repository = $this->getDoctrine()->getRepository(Doctor::class);
                        $doctor = $repository->findOneBy(array('id'=>$user->getId(),'affiliate'=>true,'removed' => false));
                        if (!is_null($doctor)) {
                        
                            return View::create($doctor, JsonResponse::HTTP_OK, []);
                            }  
                            
                            else{
                                return View::create('not affiliate',JsonResponse::HTTP_OK);  
                            }
                            }
                            
                            else {
                                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                                    }  
                }
        /**
         * @param Request $request
         *
         * @Rest\Patch("/api/doctor/AddAffiliation/{id}", name ="patch_affiliate")
         * @Rest\View(serializerGroups={"doctors"})
             */
            public function patchaffiliate(Request $request,$id)
            {
                $user = $this->getUser();
                    if ($user->getUserType() === UserType::TYPE_HOSPITAL) {
                        $repository = $this->getDoctrine()->getRepository(Hospital::class);
                        $hospital = $repository->findOneBy(array('created_by' => $user->getId(),'removed' => false));
                        $a=$hospital->getId();
                    
                        $repository = $this->getDoctrine()->getRepository(Doctor::class);
                        $doctor = $repository->findOneBy(array('created_by'=>$id,'hospital' => $a,'removed' => false));

                        if (!is_null($doctor)) {
                            $affiliate= $request->request->get('affiliate');
                            if (isset($affiliate)){
                            if ($affiliate == true || $affiliate == false){
                            if ($affiliate == false){
                                    $doctor->setHospital(null);
                                }
                            $doctor->setAffiliate($affiliate);
                            $doctor->setUpdatedBy($user);
                            $doctor->setUpdatedAt(new \DateTime());
                            $em = $this->getDoctrine()->getManager();
                            $em->flush();
                            $response=array(
                                'message'=>'affiliate changed with success',
                                'result'=>$doctor,
                            
                            );
                            return View::create($response, JsonResponse::HTTP_OK, []);
                            }    
                            
                            else{
                                return View::create('affiliate of doctor should be a boolean', JsonResponse::HTTP_BAD_REQUEST); 
                            }
                        }
                        else{
                            return View::create('Missing affiliate', JsonResponse::HTTP_BAD_REQUEST); 
                        }
                        }
                            else {
                                return View::create('doctor not found', JsonResponse::HTTP_NOT_FOUND); 
                        }
                    }
                    else{
                        return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                    }
                }
            /**
             * @Rest\Get("/api/doctor/{id}", name ="search_doctors")
             * @Rest\View(serializerGroups={"admin"})
             */
            public function search($id){
                $user = $this->getUser();

                    if ($user->getUserType() === UserType::TYPE_HOSPITAL) {
                        $repository = $this->getDoctrine()->getRepository(Hospital::class);
                        $hospital = $repository->findOneBy(array('created_by' => $user->getId(),'removed' => false));
                        $reflectionClass = new ReflectionClass(get_class($hospital));
                        $array = array();
                        foreach ($reflectionClass->getProperties() as $property) {
                            $property->setAccessible(true);
                            $array[$property->getName()] = $property->getValue($hospital);
                            $property->setAccessible(false);
                        }
                    
                        $repository = $this->getDoctrine()->getRepository(Doctor::class);
                        $doctor = $repository->findOneBy(array('created_by'=>$id,'hospital' =>$array['id'],'removed' => false));
                if (!is_null($doctor)) {
                    return View::create($doctor, JsonResponse::HTTP_OK, []);
                } else {
                    return View::create('doctors Not Found', JsonResponse::HTTP_NOT_FOUND);
                        } 
                } 

                else {
                    return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                }
        }

            
            /**
             * @Rest\Get("/api/assignedDoctor/{id}", name ="assigned_search")
             * @Rest\View(serializerGroups={"admin"})
             */
            public function doctorshearchAssigned($id){
                $user = $this->getUser();
                    if ($user->getUserType() === UserType::TYPE_HOSPITAL) {
                    $repository = $this->getDoctrine()->getRepository(Patient::class);
                    $patient = $repository->findByAssigned();
                    if (!is_null($patient)) {
                    foreach ($patient as $data){
                        $a[]= $data->getAssignedBy();
                    }
                    $repository = $this->getDoctrine()->getRepository(Hospital::class);
                    $hospital = $repository->findOneBy(array('created_by' => $user->getId(),'removed' => false));
                    $reflectionClass = new ReflectionClass(get_class($hospital));
                    $array = array();
                    foreach ($reflectionClass->getProperties() as $property) {
                        $property->setAccessible(true);
                        $array[$property->getName()] = $property->getValue($hospital);
                        $property->setAccessible(false);
                    }
                    $repository = $this->getDoctrine()->getRepository(Doctor::class);
                    $doctor = $repository->findOneBy(array('id'=>$id,'created_by'=>$a,'hospital' =>$array['id'],'removed' => false));

            if (!is_null($doctor)) {
                return View::create($doctor, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('doctors Not Found', JsonResponse::HTTP_NOT_FOUND);
                        } 
                    }
                    }  else {
                        return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                            }
                        
                    }


                /**
                 * @Rest\Delete("/api/DeleteAffiliation", name ="doctor_removehospital")
                * @Rest\View(serializerGroups={"doctors","hospitals"})
                */
            public function delete(){
                $user = $this->getUser();
                if ($user->getUserType() === UserType::TYPE_DOCTOR) {
                    $repository = $this->getDoctrine()->getRepository(Doctor::class);
                    $doctor = $repository->findOneBy(array('created_by' => $user->getId()));
                    if (!is_null($doctor)) {
                        $doctor->setHospital(null);
                        $doctor->setAffiliate(false);
                        $em = $this->getDoctrine()->getManager();
                        $em->flush();
                        return View::create('success ,you are not belong to hospital', JsonResponse::HTTP_OK, []);
                    }
                    
                    else {
                        return View::create('doctor not Found', JsonResponse::HTTP_NOT_FOUND);
                        } 
                    }
                    else {
                        return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                        } 
                    }

        /**
             * @Rest\Get("api/PatientNum", name ="counting")
             */

            public function count()
            {  
                $user = $this->getUser();
                if ($user->getUserType() === UserType::TYPE_DOCTOR) {
                    $repository = $this->getDoctrine()->getRepository(Patient::class);
                    $nbpatient = $repository->findcount($user);
                    if (!is_null($nbpatient)) {
                        $count= count($nbpatient);
                        $response=array(
                            'patient_number'=> $count,  
                        );
                        return View::create($response, JsonResponse::HTTP_OK, []);

                }
                else{
                    $count= count($nbpatient);
                        $response=array(
                            'patient_number'=> 0,  
                        );
                        return View::create($response, JsonResponse::HTTP_OK, []);
                }
            }
            else{
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
            }
        }
        
        

        /**
             * @Rest\Patch("api/hospital/AddAffiliate", name ="addd_affiliate")
             * @Rest\View(serializerGroups={"users"})
             */

            public function AddAffiliate(Request $request)
            {  
                $user = $this->getUser();
                if ($user->getUserType() === UserType::TYPE_HOSPITAL) {
                    $repository = $this->getDoctrine()->getRepository(Hospital::class);
                    $hospital = $repository->findOneBy(array("created_by"=>$user));
                    $matricule= $request->request->get('matricule');
                    if (isset($matricule)) {
                    $repository = $this->getDoctrine()->getRepository(Doctor::class);
                    $doctor = $repository->findOneBy(array("matricule"=>$matricule,"affiliate"=>false));
                    if (!is_null($doctor)) {
                    
                        $doctor->setAffiliate(true);
                        $doctor->setHospital($hospital);
                        $doctor->setUpdatedBy($user);
                        $doctor->setUpdatedAt(new \DateTime());
                        $em = $this->getDoctrine()->getManager();
                        $em->flush();
                        $response=array(
                            'message'=>'doctor affiliation added with success',
                            'result'=>$doctor,
                        
                        );
                        return View::create($response, JsonResponse::HTTP_OK, []);
                        }    
                else{
                    return View::create('matricule not available', JsonResponse::HTTP_NOT_FOUND);
                }
            }
            else{
                return View::create('Missing matricule', JsonResponse::HTTP_BAD_REQUEST);
            }
        }

            else{
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
        

        }





            /**
             * @Rest\Get("/api/Activate", name ="api_activate")
             * @Rest\View(serializerGroups={"admin"})
             */
            public function ActivateDoctor()
            {
                $user = $this->getUser();
                if ($user->getUserType() === UserType::TYPE_ADMIN) {
                    $repository = $this->getDoctrine()->getRepository(Doctor::class);
                    $doctor = $repository->findBy(array(),array('id'=>'DESC'));
                
            if (!is_null($doctor)) {
                return View::create($doctor, JsonResponse::HTTP_OK, []);
            }
                    }
                    else{
                        return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                }

            }
            
            /**
             * @Rest\PATCH("/api/Activate/{id}", name ="api_patchactivate")
             * @Rest\View(serializerGroups={"admin"})
             */
            public function patchAccountActivation($id,Request $request)
            {
                $user = $this->getUser();
                $activation= $request->request->get('enabled');
                if ($user->getUserType() === UserType::TYPE_ADMIN) {
                    $repository = $this->getDoctrine()->getRepository(User::class);
                    $userr = $repository->findOneBy(array('id'=>$id));
                
            if (!is_null($userr)) {
                $repository = $this->getDoctrine()->getRepository(Doctor::class);
                $doctor = $repository->findOneBy(array('created_by'=>$id));
            
        
                if (isset($activation)) {
                if ($activation == true || $activation == false ){
                    $userr->setEnabled($activation);
                    $em = $this->getDoctrine()->getManager();
                    $em->flush();
                    return View::create($doctor, JsonResponse::HTTP_OK, []);
                } else{
                    return View::create('error! enabled is boolean ', JsonResponse::HTTP_BAD_REQUEST);
            }
            
            }
                else{
                    return View::create('error! enabled missing', JsonResponse::HTTP_BAD_REQUEST);
            }
        

        }else{
                return View::create('User Account not found', JsonResponse::HTTP_NOT_FOUND);
            }
            }
                    else{
                        return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                }

            }

        /**
             * @Rest\Get("/api/verification/doctor/{id}", name ="api_verifcation")
             * @Rest\View(serializerGroups={"admin"})
             */
            public function matriculeVerification($id,Request $request)
            {
                $user = $this->getUser();
                if ($user->getUserType() === UserType::TYPE_ADMIN) {
                    $repository = $this->getDoctrine()->getRepository(User::class);
                    $userr = $repository->findOneBy(array('id'=>$id));
                
            if (!is_null($userr)) {
                $repository = $this->getDoctrine()->getRepository(Doctor::class);
                $doctor = $repository->findOneBy(array('created_by'=>$id));
                $matricule= $doctor->getMatricule();
                $repository = $this->getDoctrine()->getRepository(Matricule::class);
                $verife = $repository->findOneBy(array('doctor_matricule'=>$matricule));
                if (!is_null($verife)) {
                    return View::create('matricule verified successfuly', JsonResponse::HTTP_OK);
            
            }
            else{
                return View::create('matricule verifcation error, matricule not found', JsonResponse::HTTP_NOT_FOUND);
            }  
        }else{
                return View::create('User Account not found', JsonResponse::HTTP_NOT_FOUND);
            }
            }
                    else{
                        return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                }

            }

        }
