<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use ReflectionClass;
use App\Entity\Doctor;
use App\Entity\Patient;
use App\Entity\Hospital;
use App\Entity\UserType;
use App\Entity\Diagnostic;
use FOS\RestBundle\View\View;
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
      $patient=$patientRepository->findAll();
        return View::create($patient, JsonResponse::HTTP_OK, []);
            }   

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
                foreach ($doctor as $data){
                    $a[]= $data->getCreatedBy()->getId();
                   }
                   $repository = $this->getDoctrine()->getRepository(Patient::class);
                   $patient = $repository->findBy(array('assignedBy'=>$a));
                
                   return View::create($patient, JsonResponse::HTTP_OK,[]);
            }

            if ($user->getUserType() === UserType::TYPE_DOCTOR) {
                $patientRepository = $this->getDoctrine()->getRepository(Patient::class);
                $patient= $patientRepository->findBy(array('assignedBy'=> $user->getId()));
                if(!is_null($patient)){
                    return View::create($patient, JsonResponse::HTTP_OK,[]);
                }
                else {
                    return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                        }   
                    }
                else {
                    return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                    } 
        }
     /**
     * @Rest\Get("/api/patient/{id}", name ="search_patient")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function searchPatient($id){
        $user=$this->getUser();
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $repository = $this->getDoctrine()->getRepository(Patient::class);
            $patient = $repository->findOneBy(array('id' => $id,'assignedBy'=> $user->getId()));
            if (!is_null($patient)) {
                return View::create($patient, JsonResponse::HTTP_OK, []);
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                  } 
                }
        
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
             $doctor = $repository->findBy(array('hospital' =>$array['id'],'removed' => false));
             foreach ($doctor as $data){
                 $a[]= $data->getCreatedBy()->getId();
                }
                $repository = $this->getDoctrine()->getRepository(Patient::class);
                $patient = $repository->findOneBy(array('id'=>$id,'assignedBy'=>$a));


                if (!is_null($patient)) {
                 return View::create($patient, JsonResponse::HTTP_OK, []);
         } else {
             return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                   } 
         }


        
            }

     //pour les hopitaux on a la liste de patients qui ont un medecin pour suivre///

    /**
     * @Rest\Get("/api/assigned", name ="showassigned_patient")
     * @Rest\View(serializerGroups={"hospitals"})
     */
    public function showasseigned()
    {
        $user = $this->getUser();
            if ($user->getUserType() === UserType::TYPE_HOSPITAL) {
        $patientRepository = $this->getDoctrine()->getRepository(Patient::class);
        $patient= $patientRepository->findByAssigned();
        return View::create($patient, JsonResponse::HTTP_OK, []);
            }   
          
                else {
                    return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                    } 
        }
    /**
     * @Rest\Get("/api/assigned/{id}", name ="searechassigned_patient")
     * @Rest\View(serializerGroups={"hospitals"})
     */
    public function searchasseigned($id)
    {
        $user = $this->getUser();
            if ($user->getUserType() === UserType::TYPE_HOSPITAL) {
        $patientRepository = $this->getDoctrine()->getRepository(Patient::class);
        $patient= $patientRepository->findByAssignedid($id);
         if (!is_null($patient)) {
        return View::create($patient, JsonResponse::HTTP_OK, []);
            }   
            else {
                return View::create('patient Not found', JsonResponse::HTTP_NOT_FOUND, []);
            }
        }
        else {
              return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                    } 
        }
   
                
      /**
      * @param Request $request
      * @Rest\Patch("/api/AddAssigned/{id}", name ="patch_patient")
      * @Rest\View(serializerGroups={"doctors","hospitals"})
      */
    public function addAssinged(Request $request,$id)
    {
        $user = $this->getUser();
            if (($user->getUserType() === UserType::TYPE_DOCTOR)) {
             $patientRepository = $this->getDoctrine()->getRepository(Patient::class);
             $patient = $patientRepository->findOneBy(array('id' => $id));
             if (!is_null($patient)) {
                $assinged= $request->request->get('assignedBy');
                if (isset($assinged)) {
                    $doctorRepository = $this->getDoctrine()->getRepository(User::class);
                    $doctor = $doctorRepository->findOneBy(array('id' => $assinged, 'userType' => UserType::TYPE_DOCTOR,'remove' => false));
                    if (!is_null($doctor)) {
                     $patient->setAssignedBy($doctor);
                     $patient->setUpdatedBy($user);
                     $patient->setUpdatedAt(new \DateTime());
                     $em = $this->getDoctrine()->getManager();
                     $em->flush();
                     $response=array(
                        'message'=>'patient assigned by the doctor with success',
                        'result'=>$patient,
                       
                    );
                     return View::create($response, JsonResponse::HTTP_OK, []);
                      }    
                      else {
                        return View::create('doctor not Found', JsonResponse::HTTP_NOT_FOUND);
                     
                  }

             }
             else {
                return View::create('assignedBy is missing', JsonResponse::HTTP_BAD_REQUEST, []);
               
         }
            }
            else {
                return View::create('This patient not found', JsonResponse::HTTP_BAD_REQUEST);
         }
           
        }
        else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
     }
    }

         /**
          * @Rest\Delete("/api/DeleteAssigned/{id}", name ="patient_removeassigned")
          * @Rest\View(serializerGroups={"doctors","hospitals"})
          */
    public function delete($id){
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $repository = $this->getDoctrine()->getRepository(Patient::class);
            $patient = $repository->findOneBy(array('id' => $id,'assignedBy'=> $user->getId()));
            if (!is_null($patient)) {
                $patient->setAssignedBy(null);
                $patient->setUpdatedBy($user);
                $patient->setUpdatedAt(new \DateTime());
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                return View::create('success ,you are not patient assinged now', JsonResponse::HTTP_OK, []);
            }
            
            else {
                return View::create('patient not Found', JsonResponse::HTTP_NOT_FOUND);
                } 
            }
            else {
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                } 
            }


             /**
     * @Rest\Get("/api/patientAssignedNumber", name ="num_patient")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function countPatient()
    {  $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $patientRepository = $this->getDoctrine()->getRepository(Patient::class);
            $patient= $patientRepository->findBy(array('assignedBy'=> $user->getId()));
            if(!is_null($patient)){
                $nb=count($patient);
                $response=array(
                    'PatientAssigned_number'=>$nb,   
                );

                return View::create($response, JsonResponse::HTTP_OK,[]);
        }
    }
    else {
        return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        } 
    }



    /**
     * @Rest\Get("/api/patientbyuser/{id}", name ="search_byuser")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function searchPatientbyUser($id){
        $user=$this->getUser();
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
     * @Rest\Get("/api/latestMesure", name ="hospital_latest")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function getLatestMesure()
    {
        $a=array();
        $user=$this->getUser();
        $data = array(
            'id' => $user->getId()
        );
        if ($user->getUserType() === UserType::TYPE_HOSPITAL) {
            $patientrepository = $this->getDoctrine()->getRepository(Hospital::class);
            $hospital = $patientrepository->findOneBy(array('created_by'=> $user->getId()));
            $patientrepository = $this->getDoctrine()->getRepository(Doctor::class);
            $doctor = $patientrepository->findBy(array('hospital'=> $hospital->getId(),'affiliate'=>true));
            foreach ($doctor as $dataa) {
                $doctors[] = $dataa->getCreatedBy()->getId();
                }


            $patientrepository = $this->getDoctrine()->getRepository(Patient::class);
            $Assigned = $patientrepository->findBy(array('assignedBy'=> $doctors));
            foreach ($Assigned as $dataa) {
           array_push($a,$dataa->getCreatedBy()->getId());
            }
            
                $diagnosticrepository = $this->getDoctrine()->getRepository(Diagnostic::class);
                $latestdate = $diagnosticrepository->findByMesuremaxDate($a);
             
                $response=array(
                    'latest_result'=>$latestdate
                   
                );

                return View::create($response, JsonResponse::HTTP_OK, []);
            }
    
    else {
        return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        } 
    }


}



