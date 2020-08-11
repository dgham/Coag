<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\User;
use ReflectionClass;
use App\Entity\Asset;
use App\Entity\Device;
use App\Entity\Doctor;
use App\Entity\Patient;
use App\Entity\Session;
use App\Entity\Hospital;
use App\Entity\UserType;
use App\Entity\Treatment;
use App\Entity\Diagnostic;
use Hoa\Exception\Exception;
use FOS\RestBundle\View\View;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RestApiMesureINRController extends FOSRestController
{
     /**
     * @Rest\Get("/api/diagnostic", name ="api_diagnostic")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function index()
    {
        $user=$this->getUser();
        $data = array(
            'id' => $user->getId()
        );
        if ($user->getUserType() === UserType::TYPE_PATIENT) {
            $repository = $this->getDoctrine()->getRepository(Diagnostic::class);
            $diagnostic = $repository->findBy(array('created_by'=> $data));
            return View::create($diagnostic, JsonResponse::HTTP_OK, []);
         }
         if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $patientrepository = $this->getDoctrine()->getRepository(Patient::class);
           $Assigned = $patientrepository->findBy(array('assignedBy'=> $data));
           foreach ($Assigned as $data) {
           $a[]= $data->getCreatedBy();
           }
            if (!is_null($Assigned)) {
                $diagnosticrepository = $this->getDoctrine()->getRepository(Diagnostic::class);
                $diagnostic = $diagnosticrepository->findBy(array('created_by'=> $a));
                return View::create($diagnostic, JsonResponse::HTTP_OK, []);
            }
            else {
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
            }
         }
    }
      /**
     * @Rest\Get("/api/diagnostic/{id}", name ="search_diagnostic")
     * @Rest\View(serializerGroups={"users"})
     */
    public function searchDiagnostic($id){
        $user=$this->getUser();
        if ($user->getUserType() === UserType::TYPE_PATIENT) {
        $repository = $this->getDoctrine()->getRepository(diagnostic::class);
        $diagnostic = $repository->findOneBy(array('id' => $id,'created_by' => $user->getId()));
        if (!is_null($diagnostic)) {
            return View::create($diagnostic, JsonResponse::HTTP_OK, []);
    }

    else {
        return View::create(' Not Found', JsonResponse::HTTP_NOT_FOUND);
              } 
            } 
            if ($user->getUserType() === UserType::TYPE_DOCTOR) {
                $patientrepository = $this->getDoctrine()->getRepository(Patient::class);
               $Assigned = $patientrepository->findBy(array('assignedBy'=> $user));
               foreach ($Assigned as $data) {
                $a[]= $data->getCreatedBy();
                }
               
                 if (!is_null($Assigned)) {
                     $diagnosticrepository = $this->getDoctrine()->getRepository(Diagnostic::class);
                     $diagnostic = $diagnosticrepository->findOneBy(array('id' => $id,'created_by'=> $a));
                     if (!is_null($diagnostic)){
                        return View::create($diagnostic, JsonResponse::HTTP_OK, []);
                     }
                     else {
                        return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                    }
                     
                 }
                 else {
                    return View::create(' Not Found', JsonResponse::HTTP_NOT_FOUND);
                 }
                }
                else {
                    return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                }
    }

     /**
     * @Rest\Post("/api/diagnostic", name ="post_diagnostic")
     * @Rest\View(serializerGroups={"users"})
     */
    public function create(Request $request,EntityManagerInterface $entity){
        $user = $this->getUser();
        
        try{
            if ($user->getUserType() === UserType::TYPE_PATIENT) {
                $value= $request->request->get('value');
                $typevalue= gettype($value);
                if (isset($value)) {
                    if($typevalue == "double"){
                $diagnostic = new Diagnostic();
                $diagnostic->setValue($value);
                    }
                    else{
                        return View::create('value of INR must be double!', JsonResponse::HTTP_BAD_REQUEST, []);
                    }
                } else{
                    return View::create('value INR is missing!', JsonResponse::HTTP_BAD_REQUEST, []);
                }

                $indication= $request->request->get('indication');
                $typeindication= gettype($indication);
                if (isset($indication)) {
                    if($typeindication == "string"){
                $diagnostic->setIndication($indication);
                $diagnostic->setCreatedBy($user);
                $diagnostic->setCreatedAt(new \DateTime());
                $entity ->persist($diagnostic);
                $entity->flush();
                return View::create($diagnostic, JsonResponse::HTTP_CREATED, []);
                    }
                    else{
                        return View::create('indication of INR should be string!', JsonResponse::HTTP_BAD_REQUEST, []);
                    }
                }
                    else {
                        return View::create('indication is missing!', JsonResponse::HTTP_BAD_REQUEST, []);
                    }


        
        }
        else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }

        }catch (Exception $e){
            return View::create($e->getMessage(), JsonResponse::HTTP_BAD_REQUEST, []);
    }  
}



    /**
     * @Rest\Post("/api/AddDiagnostic", name ="Add_diagnostic")
     * @Rest\View(serializerGroups={"users"})
     */
    public function AddDiagnostic(Request $request,EntityManagerInterface $entity){
        $user = $this->getUser();
        try{
            if ($user->getUserType() === UserType::TYPE_DOCTOR) {
                $value= $request->request->get('value');
                $typevalue= gettype($value);
                if (isset($value)) {
                    if($typevalue == "double"){
                $diagnostic = new Diagnostic();
                $diagnostic->setValue($value);
            }
            else{
                return View::create('value of INR must be double!', JsonResponse::HTTP_BAD_REQUEST, []);
            }
                $indication= $request->request->get('indication');
                $typeindication= gettype($indication);
                if (isset($indication)) {
                    if($typeindication == "string"){
                $diagnostic->setIndication($indication);
            }
            else{
                return View::create('indication of INR must be string!', JsonResponse::HTTP_BAD_REQUEST, []);
            }
                $createdBy= $request->request->get('created_by');
                if (isset($createdBy)) {
                    
                    $repository = $this->getDoctrine()->getRepository(Patient::class);
                    $create = $repository->findOneBy(array('assignedBy' => $user->getId(),'created_by'=>$createdBy));
                    if (!is_null($create)) {
                        $repository = $this->getDoctrine()->getRepository(User::class);
                        $userr = $repository->findOneBy(array('id' => $createdBy));
                        $diagnostic->setCreatedBy($userr);
                    }else{
                        return View::create('you are not assigned to this patient', JsonResponse::HTTP_BAD_REQUEST, []);
                    }
                }
                else{
                    return View::create('select your patient please', JsonResponse::HTTP_BAD_REQUEST, []);  
                }
                $diagnostic->setCreatedAt(new \DateTime());
                $entity ->persist($diagnostic);
                $entity->flush();
                return View::create($diagnostic, JsonResponse::HTTP_CREATED, []);
                    }
                    else {
                        return View::create('indication is missing!', JsonResponse::HTTP_BAD_REQUEST, []);
                    }
             }
             else {
                return View::create('value of the diagnostic is missing!', JsonResponse::HTTP_BAD_REQUEST, []);
            }
        }
        else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }

        }catch (Exception $e){
            return View::create($e->getMessage(), JsonResponse::HTTP_BAD_REQUEST, []);
    }  
}

/**
     * @Rest\Get("/api/TotalMesure", name ="nb_diagnostic")
     * @Rest\View(serializerGroups={"users"})
     */
    public function countDiagnostic()
    {
        $user=$this->getUser();
        $data = array(
            'id' => $user->getId()
        );
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $patientrepository = $this->getDoctrine()->getRepository(Patient::class);
           $Assigned = $patientrepository->findBy(array('assignedBy'=> $data));
           foreach ($Assigned as $data) {
           $a[]= $data->getCreatedBy();
           }
            if (!is_null($Assigned)) {
                $diagnosticrepository = $this->getDoctrine()->getRepository(Diagnostic::class);
                $diagnostic = $diagnosticrepository->findBy(array('created_by'=> $a));
                $nb=count($diagnostic);
                $response=array(
                    'ResultINR_Total'=>$nb,
                   
                );

                return View::create($response, JsonResponse::HTTP_OK, []);
            }
        
        }
    }
    /**
     * @Rest\Get("/api/NormalMesure", name ="normal_diagnostic")
     * @Rest\View(serializerGroups={"users"})
     */
    public function countNormalDiagnostic()
    {
        $a=array();
        $user=$this->getUser();
        $data = array(
            'id' => $user->getId()
        );
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $patientrepository = $this->getDoctrine()->getRepository(Patient::class);
           $Assigned = $patientrepository->findBy(array('assignedBy'=> $data));
           foreach ($Assigned as $dataa) {
           $a[] = $dataa->getCreatedBy()->getId();
           }
            if (!is_null($Assigned)) {
                $normall='normal mesure';
                $diagnosticrepository = $this->getDoctrine()->getRepository(Diagnostic::class);
                $diagnosticNormal = $diagnosticrepository->findByINRMesureNormal($a,$normall);
                $diagnostictotal = $diagnosticrepository->findBy(array('created_by'=> $a));
                $normal=count($diagnosticNormal);
                $total=count($diagnostictotal);
                $nbnormal= $normal*100/$total;
               

                $response=array(
                    'ResultINR_Total'=>$nbnormal,
                );

                return View::create($response, JsonResponse::HTTP_OK, []);
            }
        
           }
        else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
            } 
    }
     /**
     * @Rest\Get("/api/AnormalMesure", name ="Anormal_diagnostic")
     * @Rest\View(serializerGroups={"users"})
     */
    public function CountInormalDiagnostic()
    {
        $a=array();
        $user=$this->getUser();
        $data = array(
            'id' => $user->getId()
        );
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $patientrepository = $this->getDoctrine()->getRepository(Patient::class);
           $Assigned = $patientrepository->findBy(array('assignedBy'=> $data));
           foreach ($Assigned as $dataa) {
          $a[] = $dataa->getCreatedBy()->getId();
           }
           
            if (!is_null($Assigned)) {
                $anormal='anormal mesure';
                $diagnosticrepository = $this->getDoctrine()->getRepository(Diagnostic::class);
                $diagnosticANormal = $diagnosticrepository->findByINRMesureINormal($a,$anormal);
               
                $diagnostictotal = $diagnosticrepository->findBy(array('created_by'=> $a));
              
                $normal=count($diagnosticANormal);
                $total=count($diagnostictotal);
            
                $nbAnormal= $normal*100/$total;
                $response=array(
                    'ResultINR_Total'=>$nbAnormal,
                   
                );

                return View::create($response, JsonResponse::HTTP_OK, []);
            }
        
        
    }
    else {
        return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        } 
    }

     /**
     * @Rest\Get("/api/PatientMesureCount", name ="countt_diagnostic")
     * @Rest\View(serializerGroups={"users"})
     */
    public function CountpatientDiagnostic()
    {
        $a=array();
        $user=$this->getUser();
        $data = array(
            'id' => $user->getId()
        );
        if ($user->getUserType() === UserType::TYPE_PATIENT) {
                $diagnosticrepository = $this->getDoctrine()->getRepository(Diagnostic::class);
                $diagnostic = $diagnosticrepository->findby(array('created_by'=>$user->getId()));
               $nbdiag=count($diagnostic);
           
                $response=array(
                    'ResultINR_Total'=>$nbdiag,
                   
                );

                return View::create($response, JsonResponse::HTTP_OK, []);
            }
        
        
    
    else {
        return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        } 
    }



     /**
     * @Rest\Get("/api/PatientDiagnostic/{id}", name ="patient_diagnostic")
     * @Rest\View(serializerGroups={"users"})
     */
    public function ResultbyPatient($id)
    {
        $user=$this->getUser();
        $data = array(
            'id' => $user->getId()
        );
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {

            $repository = $this->getDoctrine()->getRepository(Patient::class);
            $patient = $repository->findOneBy(array('id' => $id));
            if (!is_null($patient)) {
                $createduser=$patient->getCreatedBy()->getId();
                $repository = $this->getDoctrine()->getRepository(Diagnostic::class);
                $diagnostic = $repository->findBy(array('created_by' => $createduser));
               return View::create($diagnostic, JsonResponse::HTTP_OK, []);
               
            }
        }
            else {
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
            }
         }
          /**
     * @Rest\Get("/api/UserDiagnostic/{id}", name ="user_diagnostic")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function ResultbyUser($id)
    {
        $user=$this->getUser();
        $data = array(
            'id' => $user->getId()
        );
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {

            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->findOneBy(array('id' => $id));
            if (!is_null($user)) {
                $repository = $this->getDoctrine()->getRepository(Diagnostic::class);
                $diagnostic = $repository->findBy(array('created_by' => $user));
               return View::create($diagnostic, JsonResponse::HTTP_OK, []);
               
            }
        }
            else {
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
            }
         }

 



          /**
     * @Rest\Get("/api/PatientMesureIndication/{id}", name ="countt_indication")
     * @Rest\View(serializerGroups={"users"})
     */
    public function Countpatientmesure($id)
    {
        $a=array();
        $user=$this->getUser();
        $data = array(
            'id' => $user->getId()
        );
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
                $diagnosticrepository = $this->getDoctrine()->getRepository(Diagnostic::class);
                $diagnostic = $diagnosticrepository->findby(array('created_by'=>$id,'indication'=>'normal mesure'));
                $diagnosticanormal = $diagnosticrepository->findby(array('created_by'=>$id,'indication'=>'anormal mesure'));
                $diagnostictotal = $diagnosticrepository->findBy(array('created_by'=>$id));
                $nbnormal=count($diagnostic);
                $nbanormal=count($diagnosticanormal);
               $nbdiagtotal=count($diagnostictotal);
               $normal=$nbnormal*100/$nbdiagtotal;
               $annormal=$nbanormal*100/$nbdiagtotal;
                $response=array(
                    'result_noraml'=>$normal,
                    'result_anormal'=>$annormal,
                    'total_mesure'=> $nbdiagtotal
                   
                );

                return View::create($response, JsonResponse::HTTP_OK, []);
            }
        
        
    
    else {
        return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        } 
    }


        /**
     * @Rest\Get("/api/latestResult/{id}", name ="latest_result")
     * @Rest\View(serializerGroups={"users"})
     */
    public function getLatestResult($id)
    {
        $a=array();
        $user=$this->getUser();
        $data = array(
            'id' => $user->getId()
        );
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
                $diagnosticrepository = $this->getDoctrine()->getRepository(Diagnostic::class);
                $latestdate = $diagnosticrepository->findBymaxDate($id);
               
                $response=array(
                    'latest_result'=>$latestdate
                   
                );

                return View::create($response, JsonResponse::HTTP_OK, []);
            }
        
        
    
    else {
        return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        } 
    }


     /**
     * @Rest\Get("/api/latestPatientsResult", name ="latestpatient_result")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function getLatestPatientResult()
    {

        $a=array();
        $user=$this->getUser();
        $data = array(
            'id' => $user->getId()
        );
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $patientrepository = $this->getDoctrine()->getRepository(Patient::class);
            $Assigned = $patientrepository->findBy(array('assignedBy'=> $user->getId()));
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
              
  
    /**
     * @Rest\Get("/api/patient/normalMesure", name ="normal_Normal")
     * @Rest\View(serializerGroups={"users"})
     */
    public function hospitalCountNormal()
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
           $a[] = $dataa->getCreatedBy()->getId();
           }
            if (!is_null($Assigned)) {
                $normall='normal mesure';
                $diagnosticrepository = $this->getDoctrine()->getRepository(Diagnostic::class);
                $diagnosticNormal = $diagnosticrepository->findByINRMesureNormal($a,$normall);
                $diagnostictotal = $diagnosticrepository->findBy(array('created_by'=> $a));
                $normal=count($diagnosticNormal);
                $total=count($diagnostictotal);
                $nbnormal= $normal*100/$total;
               

                $response=array(
                    'ResultINR_Total'=>$nbnormal,
                );

                return View::create($response, JsonResponse::HTTP_OK, []);
            }
        
           }
        else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
            } 
    }


     /**
     * @Rest\Get("/api/patient/AnormalMesure", name ="Anormal_patient")
     * @Rest\View(serializerGroups={"users"})
     */
    public function hospitalcountAnormal()
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
           $a[] = $dataa->getCreatedBy()->getId();
           }
           dump($a);
           die;
            if (!is_null($Assigned)) {
                $anormal='anormal mesure';
                $diagnosticrepository = $this->getDoctrine()->getRepository(Diagnostic::class);
                $diagnosticANormal = $diagnosticrepository->findByINRMesureINormal($a,$anormal);
               
                $diagnostictotal = $diagnosticrepository->findBy(array('created_by'=> $a));
              dump($diagnostictotal);
              die;
                $normal=count($diagnosticANormal);
                $total=count($diagnostictotal);
            
                $nbAnormal= $normal*100/$total;
                $response=array(
                    'ResultINR_Total'=>$nbAnormal,
                   
                );

                return View::create($response, JsonResponse::HTTP_OK, []);
            }
        
        
    }
    else {
        return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        } 
    }

 /**
     * @Rest\Get("/api/countDoctor", name ="doctorNumber")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function hospitalcountDoctor()
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
            
            $number=count($doctor);
            $response=array(
                'doctorNumber'=>$number,
               
            );
                return View::create($response, JsonResponse::HTTP_OK, []);
            }
        
        

    else {
        return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        } 


    }
   
    
     /**
     * @Rest\Get("/api/countPatient", name ="PatientNumber")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function hospitalcountPatient()
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
            $number=count($Assigned);
            $response=array(
                'patientNumber'=>$number,
               
            );
                return View::create($response, JsonResponse::HTTP_OK, []);
            }
        
        

    else {
        return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        } 


    }

    
    

     /**
     * @Rest\Get("/api/patient/TotalMesure", name ="total_mesure")
     * @Rest\View(serializerGroups={"users"})
     */
    public function hospitalcountMesure()
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
           $a[] = $dataa->getCreatedBy()->getId();
           }
           
            if (!is_null($Assigned)) {
                $diagnosticrepository = $this->getDoctrine()->getRepository(Diagnostic::class);
                $diagnostictotal = $diagnosticrepository->findBy(array('created_by'=> $a));
                $total=count($diagnostictotal);
                $response=array(
                    'ResultINR_Total'=>$total,
                   
                );

                return View::create($response, JsonResponse::HTTP_OK, []);
            }
        
        
    }
    else {
        return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        } 
    }

 /**
     * @Rest\Get("/api/MesurebyGender", name ="hospital_mesureGender")
     * @Rest\View(serializerGroups={"users"})
     */
    public function hospitalDiagnosticBygender()
    {
        $indication='anormal mesure';
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
               $a[] = $dataa->getCreatedBy()->getId();
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
                    else {
                        return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                        } 
                }


    }
   
/**
     * @Rest\Get("/api/CountMesure/{id}", name ="DiagnosticNumber")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function hospitalcount($id)
    {
        $a=array();
        $user=$this->getUser();
        $data = array(
            'id' => $user->getId()
        );
        if ($user->getUserType() === UserType::TYPE_HOSPITAL) {
            $doctorrepository = $this->getDoctrine()->getRepository(Doctor::class);
            $Doctor = $doctorrepository->findOneBy(array('id'=> $id));
            $patientrepository = $this->getDoctrine()->getRepository(Patient::class);
            $patient = $patientrepository->findBy(array('assignedBy'=> $id));
            if (!is_null($patient)) {
                foreach ($patient as $data){
                 $a[]= $data->getCreatedBy()->getId();
                }
            $diagnosticrepository = $this->getDoctrine()->getRepository(Diagnostic::class);
            $diagnostic = $diagnosticrepository->findBy(array('created_by'=>$a));
            $reportrepository = $this->getDoctrine()->getRepository(Note::class);
            $report = $reportrepository->findBy(array('created_by'=>$Doctor->getCreatedBy()->getId() ,'remove'=>false));
            $treatmentrepository = $this->getDoctrine()->getRepository(Treatment::class);
            $treatment = $treatmentrepository->findBy(array('created_by'=>$id,'remove'=>false));

            $patientNumber=count($patient);
            $diagnosticnumber=count($diagnostic);
            $reportNumber=count($report);
            $treatment=count($treatment);

            $response=array(
                'patientNumber'=>$patientNumber,
                'diagnosticNumber'=>$diagnosticnumber,
                'treatmentNumber'=>$treatment,
                'reportNumber'=>$reportNumber,
               
            );
                return View::create($response, JsonResponse::HTTP_OK, []);
            }
        
        

    else {
        return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        } 


    }

    }






    /**
     * @Rest\Get("/api/count/mesureIndiction", name ="indicationDiagnostic")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function MeasureIndiction()
    {
        $user=$this->getUser();
    
        if ($user->getUserType() === UserType::TYPE_PATIENT) {
                $repository = $this->getDoctrine()->getRepository(Diagnostic::class);
                $diagnostic = $repository->findBy(array('created_by' => $user));
                if (!is_null($diagnostic)) {
                    $total=count($diagnostic);
                }
                
                $normall='normal mesure';
                $anormall='anormal mesure';
                $diagnosticNormal = $repository->findBy(array('created_by' => $user,"indication"=>$normall));
                $diagnosticANormal = $repository->findBy(array('created_by' => $user,"indication"=>$anormall));
                    $normal=count($diagnosticNormal);
                  
                    $anormal=count($diagnosticANormal);
                   
            $response=array(
                'total_mesure'=>$total,
                'INRNormal'=>$normal,
                'INRAnormal'=>$anormal,
               
               
            );
                return View::create($response, JsonResponse::HTTP_OK, []);
               
            }
            else{
                return View::create('No measurements found ', JsonResponse::HHTP_NOT_FOUND, []);
            }
       
    
         
        }
 
/**
     * @Rest\Get("/api/admin/statistic", name ="admin_statistique")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function Adminstatistics()
    {
        $user=$this->getUser();
    
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $repository = $this->getDoctrine()->getRepository(Patient::class);
            $patient = $repository->findAll();
            $countpatient=count($patient);
            $repository = $this->getDoctrine()->getRepository(Doctor::class);
            $doctor = $repository->findAll();
            $countdoctor=count($doctor);
            $repository = $this->getDoctrine()->getRepository(Hospital::class);
            $hospital = $repository->findAll();
            $counthospital=count($hospital);
            $repository = $this->getDoctrine()->getRepository(Asset::class);
            $assets = $repository->findAll();
            $countassets=count($assets);
            $repository = $this->getDoctrine()->getRepository(Session::class);
            $session = $repository->findAll();
            $countsession=count($session);
            $repository = $this->getDoctrine()->getRepository(Device::class);
            $device = $repository->findAll();
            $countdevice=count($device);
            $response=array(
                'patient'=>$countpatient,
                'doctor'=>$countdoctor,
                'hospital'=>$counthospital,
                'assets'=>$countassets,
                'session'=>$countsession,
                'device'=>$countdevice,
               
               
            );
                return View::create($response, JsonResponse::HTTP_OK, []);
               
        }
        else{

            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);

        }
    }
            
            

}