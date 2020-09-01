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
use App\Entity\DoctorAssignement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Validator\Constraints\Date;
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
            if(!empty($diagnostic)){
                return View::create($diagnostic, JsonResponse::HTTP_OK, []);
            }
            return View::create('No data found', JsonResponse::HTTP_OK, []);
         }
         if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
            $Assigned = $repository->findBy(array('id_doctor'=>$user->getId(),'status'=>'Accepted','removed'=>false));
           foreach ($Assigned as $data) {
           $a[]= $data->getIdPatient();
           }
            if (!is_null($Assigned)) {
                $diagnosticrepository = $this->getDoctrine()->getRepository(Diagnostic::class);
                $diagnostic = $diagnosticrepository->findBy(array('created_by'=> $a));
                if(!empty($diagnostic)){
                    return View::create($diagnostic, JsonResponse::HTTP_OK, []);
                }
                return View::create('No data found', JsonResponse::HTTP_OK, []);
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
     * @Rest\Get("/api/diagnostic/{id}", name ="search_diagnostic")
     * @Rest\View(serializerGroups={"doctors"})
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
                $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
                $Assigned = $repository->findBy(array('id_doctor'=>$user->getId(),'status'=>'Accepted','removed'=>false));
               foreach ($Assigned as $data) {
                $a[]= $data->getIdPatient();
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
                            $diagnostic = new Diagnostic();
                            if (isset($value)) {
                                if($typevalue == "double"){
                        
                            $diagnostic->setValue($value);
                            if ((2.0 < $value) && ($value > 3.0)){
                                $diagnostic->setIndication('anormal mesure');
                                    }
                                    else{
                                        $diagnostic->setIndication('normal mesure'); 
                                    }
                            }
                        
                                else{
                                    return View::create('value of INR must be double!', JsonResponse::HTTP_BAD_REQUEST, []);
                                }
                            } else{
                                return View::create('value INR is missing!', JsonResponse::HTTP_BAD_REQUEST, []);
                            }
                            $details= $request->request->get('details');
                            $typevalue= gettype($details);
                            if (isset($details)) {
                                if($typevalue == "string"){
                            $diagnostic->setDetails($details);
                                }
                                else{
                                    return View::create('details must be string!', JsonResponse::HTTP_BAD_REQUEST, []);
                                }
                            } 
                            $devicedate= $request->request->get('devicedate');
                            $typevalue= gettype($devicedate);
                            if (isset($devicedate)) {
                                if($typevalue == "string"){
                                    $diagnostic->setDeviceDate($devicedate);
                                }
                                else{
                                    return View::create(' device date must be string!', JsonResponse::HTTP_BAD_REQUEST, []);
                                }
                            }
                            $diagnostic->setCreatedBy($user);
                            $diagnostic->setCreatedAt(new \DateTime());
                            $entity ->persist($diagnostic);
                            $entity->flush();
                            return View::create($diagnostic, JsonResponse::HTTP_CREATED, []);        
                    }
                    else {
                        return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                    }
                    }catch (Exception $e){
                        return View::create($e->getMessage(), JsonResponse::HTTP_BAD_REQUEST, []);
                }  
            }

    /**
     * @Rest\Get("/api/MesureDetails", name ="mesure_detailss")
     * @Rest\View(serializerGroups={"users"})
     */
    public function countDiagnostic()
    {
        $user=$this->getUser();
        $data = array(
            'id' => $user->getId()
        );
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
            $Assigned = $repository->findBy(array('id_doctor'=>$user->getId(),'status'=>'Accepted','removed'=>false));
           foreach ($Assigned as $data) {
           $a[]= $data->getIdPatient();
           }
            if (!is_null($Assigned)) {
                $diagnosticrepository = $this->getDoctrine()->getRepository(Diagnostic::class);
                $diagnostic = $diagnosticrepository->findBy(array('created_by'=> $a));
                $nb=count($diagnostic);

                $normall='normal mesure';
                $diagnosticrepository = $this->getDoctrine()->getRepository(Diagnostic::class);
                $diagnosticNormal = $diagnosticrepository->findByINRMesureNormal($a,$normall);
                $diagnostictotal = $diagnosticrepository->findBy(array('created_by'=> $a));
                $normal=count($diagnosticNormal);
                $total=count($diagnostictotal);
                $nbnormal= strval(intval(round($normal*100/$total)))."%";
                $anormal='anormal mesure';
                $diagnosticrepository = $this->getDoctrine()->getRepository(Diagnostic::class);
                $diagnosticANormal = $diagnosticrepository->findByINRMesureINormal($a,$anormal);
                $diagnostictotal = $diagnosticrepository->findBy(array('created_by'=> $a));
                $normal=count($diagnosticANormal);
                $total=count($diagnostictotal);
                $nbAnormal= strval(intval(round($normal*100/$total)))."%";
                

                $response=array(
                    'ResultINR_Total'=>$nb,
                    'Normal_mesure'=>$nbnormal,
                    'Anormal_mesure'=>$nbAnormal,
                   
                );
                return View::create($response, JsonResponse::HTTP_OK, []);    
                    }
                }
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
                        $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
                        $Assigned = $repository->findBy(array('id_doctor'=>$doctors,'status'=>'Accepted','removed'=>false));
                foreach ($Assigned as $dataa) {
                $a[] = $dataa->getIdPatient()->getId();
                }
                    if (!is_null($Assigned)) {
                        /// normal mesure mesure ////
                        $normal='normal mesure';
                        $diagnosticrepository = $this->getDoctrine()->getRepository(Diagnostic::class);
                        $diagnosticNormal = $diagnosticrepository->findByINRMesureINormal($a,$normal);
                        $diagnostictotal = $diagnosticrepository->findBy(array('created_by'=> $a));
                        $anormal='anormal mesure';
                        $diagnosticrepository = $this->getDoctrine()->getRepository(Diagnostic::class);
                        $diagnosticANormal = $diagnosticrepository->findByINRMesureINormal($a,$anormal);
                        $anormal=count($diagnosticANormal);
                        $normal=count($diagnosticNormal);
                        $total=count($diagnostictotal);
                        $nbnormal= strval(intval(round($normal*100/$total)))."%";
                        $nbAnormal= strval(intval(round($anormal*100/$total)))."%";
                        $response=array(

                            'ResultINR_Anormal'=>$nbAnormal,
                            'ResultINR_Normal'=>$nbnormal,
                        );

                        return View::create($response, JsonResponse::HTTP_OK, []);
                    }
                }
                
                    if ($user->getUserType() === UserType::TYPE_PATIENT) {
                        $repository = $this->getDoctrine()->getRepository(Diagnostic::class);
                        $diagnostic = $repository->findBy(array('created_by' => $user));
                    
                        if (!is_null($diagnostic)) {
                        $total=count($diagnostic);
                        $normall='normal mesure';
                        $anormall='anormal mesure';
                        $diagnosticNormal = $repository->findBy(array('created_by' => $user,"indication"=>$normall));
                        $diagnosticANormal = $repository->findBy(array('created_by' => $user,"indication"=>$anormall));
                        $normal=count($diagnosticNormal);
                        $anormal=count($diagnosticANormal);
                        $nbdiagtotal=count($diagnostic);
                        $nbnormal=$normal*100/$total;
                        $nbanormal=$anormal*100/$total;
                        $normall=strval(intval(round($nbnormal)))."%";
                        $anormall=strval(intval(round($nbanormal)))."%";
                            $response=array(
                            'Total_INRmesure'=>$total,
                            'INR_Normal'=>$normall,
                            'INR_Anormal'=>$anormall,
                                );
                                    return View::create($response, JsonResponse::HTTP_OK, []);
                                
                                }
                                else{
                                    return View::create('No measurements found ', JsonResponse::HHTP_NOT_FOUND, []);
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


///////////get all mesure of INR By of one patient/////////////////////
     /**
     * @Rest\Get("/api/DiagnosticByPatient/{id}", name ="patient_diagnostic")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function ResultbyPatient($id)
    {
        $user=$this->getUser();
        $data = array(
            'id' => $user->getId()
        );
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {

            $repository = $this->getDoctrine()->getRepository(User::class);
            $patient = $repository->findOneBy(array('id' => $id));
            if (!is_null($patient)) {
                $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
                $doctorassignement = $repository->findOneBy(array('id_patient' => $patient->getId(),'id_doctor' => $user->getId(),'status'=> 'Accepted','removed'=> false));
                if (!is_null($doctorassignement)) {
                $repository = $this->getDoctrine()->getRepository(Diagnostic::class);
                $diagnostic = $repository->findBy(array('created_by' => $patient));
               return View::create($diagnostic, JsonResponse::HTTP_OK, []);
               
            }
            else{
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
            }
        }

    else{
        return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
    }
}
            else {
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
            }
         }
          /**
     * @Rest\Get("/api/doctor/UserDiagnostic/{id}", name ="user_diagnostic")
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
                if (!empty($diagnostic)){
                    return View::create($diagnostic, JsonResponse::HTTP_OK, []);
                }
                else {
                    return View::create('no diagnostic found', JsonResponse::HTTP_NOT_FOUND, []);
                }
               
               
            }
    
        else {
            return View::create('user not found', JsonResponse::HTTP_NOT_FOUND, []);
        }
    }
            else {
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
            }
         }

 



    /**
     * @Rest\Get("/api/doctor/PatientMesureDetails/{id}", name ="countt_indication")
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
               $normal=strval(intval(round($nbnormal*100/$nbdiagtotal)))."%";
               $annormal=strval(intval(round($nbanormal*100/$nbdiagtotal)))."%";
               $diagnosticrepository = $this->getDoctrine()->getRepository(Diagnostic::class);
               $latestdate = $diagnosticrepository->findBymaxDate($id);
              
                $response=array(
                    'Result_noraml'=>$normal,
                    'Result_anormal'=>$annormal,
                    'Total_mesure'=> $nbdiagtotal,
                    'Latest_result'=>$latestdate
                   
                );

                return View::create($response, JsonResponse::HTTP_OK, []);
        } 
    else {
        return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        } 
    }


    
/////////////il faut faire la correction ici/////////////

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
            $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
            $Assigned = $repository->findBy(array('id_doctor'=>$user->getId(),'status'=>'Accepted','removed'=>false));
            foreach ($Assigned as $dataa) {
           array_push($a,$dataa->getIdPatient());
            }
          
                $diagnosticrepository = $this->getDoctrine()->getRepository(Diagnostic::class);
                $latestdatee = $diagnosticrepository->findByMesuremaxDate($a);

             
                $response=array(
                    'Latest_INRresult'=>$latestdatee
                   
                );

                return View::create($response, JsonResponse::HTTP_OK, []);
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

            $patientrepository = $this->getDoctrine()->getRepository(Hospital::class);
            $hospital = $patientrepository->findOneBy(array('created_by'=> $user->getId()));
            
                $patientrepository = $this->getDoctrine()->getRepository(Doctor::class);
                $doctor = $patientrepository->findBy(array('hospital'=> $hospital->getId(),'affiliate'=>true));
                foreach ($doctor as $dataa) {
                    $doctors[] = $dataa->getCreatedBy()->getId();
                    }
                    $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
                    $Assigned = $repository->findBy(array('id_doctor'=>$doctors,'status'=>'Accepted','removed'=>false));
                $numberr=count($Assigned);
                $patientrepository = $this->getDoctrine()->getRepository(Hospital::class);
                $hospital = $patientrepository->findOneBy(array('created_by'=> $user->getId()));
                
                    $patientrepository = $this->getDoctrine()->getRepository(Doctor::class);
                    $doctor = $patientrepository->findBy(array('hospital'=> $hospital->getId(),'affiliate'=>true));
                    foreach ($doctor as $dataa) {
                        $doctors[] = $dataa->getCreatedBy()->getId();
                        }
                        $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
                        $Assigned = $repository->findBy(array('id_doctor'=>$doctors,'status'=>'Accepted','removed'=>false));
                   foreach ($Assigned as $dataa) {
                   $a[] = $dataa->getIdPatient();
                   }
                   
                    if (!is_null($Assigned)) {
                        $diagnosticrepository = $this->getDoctrine()->getRepository(Diagnostic::class);
                        $diagnostictotal = $diagnosticrepository->findBy(array('created_by'=> $a));
                        $total=count($diagnostictotal);


            $response=array(
                'DoctorNumber'=>$number,
                'PatientNumber'=>$numberr,
                'ResultINR_Total'=>$total,
               
            );
                return View::create($response, JsonResponse::HTTP_OK, []);
            }
        
        

    else {
        return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        } 
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
                $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
                $Assigned = $repository->findBy(array('id_doctor'=>$doctors,'status'=>'Accepted','removed'=>false));
            foreach ($Assigned as $dataa) {
            $a[] = $dataa->getIdPatient();
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
                            $purcentageMale=strval(intval(round($NBMale*100/$total)))."%"; 
                            $NBFemale=0;
                            foreach ($diagnostic as $dataa) {
                                if($dataa->getCreatedBy()->getGender()=="Female")
                                $NBFemale=$NBFemale+1;
                            }
                      
                            
                            $purcentaFemale=strval(intval(round($NBFemale*100/$total)))."%";
                       
                  
                            $response=array(
                                'Anormal_MaleMesure'=>$purcentageMale ,
                                'Anormal_FemaleMesure'=>$purcentaFemale  
                            );
                            return View::create($response, JsonResponse::HTTP_OK, []);
                        }
                       }
                }
                $indication='anormal mesure';
                $a=array();
                $user=$this->getUser();
                $data = array(
                    'id' => $user->getId()
                );
                if ($user->getUserType() === UserType::TYPE_DOCTOR) {
                    $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
                    $Assigned = $repository->findBy(array('id_doctor'=>$user->getId(),'status'=>'Accepted','removed'=>false));
                   foreach ($Assigned as $dataa) {
                   $a[]=$dataa->getIdPatient();
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
                                $purcentageMale=strval(intval(round($NBMale*100/$total)))."%";
                                $NBFemale=0;
                                foreach ($diagnostic as $dataa) {
                                    if($dataa->getCreatedBy()->getGender()=="Female")
                                    $NBFemale=$NBFemale+1;
                                }
                                $purcentaFemale=strval(intval(round($NBFemale*100/$total)))."%";
                                $response=array(
                                    'Anormal_MaleMesure'=>$purcentageMale ,
                                    'Anormal_FemaleMesure'=>$purcentaFemale  
                                );
                        return View::create($response, JsonResponse::HTTP_OK, []);
                    }
                   }
                
            }  else {
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                } 


    }
 
        /**
        * @Rest\Get("/api/DoctorActivity/{id}", name ="Doctor_Activity")
        * @Rest\View(serializerGroups={"doctors"})
        */
    public function doctorActivity($id){
        $a=array();
        $user=$this->getUser();
        $data = array(
            'id' => $user->getId()
        );
        if ($user->getUserType() === UserType::TYPE_HOSPITAL) {
            $doctorrepository = $this->getDoctrine()->getRepository(Doctor::class);
            $Doctor = $doctorrepository->findOneBy(array('id'=> $id));
            $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
            $Assigned = $repository->findBy(array('id_doctor'=>$id,'status'=>'Accepted','removed'=>false));
            if (!is_null($Assigned)) {
                foreach ($Assigned as $data){
                $a[]= $data->getIdPatient();
                }
            
            $patientrepository = $this->getDoctrine()->getRepository(User::class);
            $patient = $patientrepository->findBy(array('id'=>$a));
            
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
                'PatientNumber'=>$patientNumber,
                'ResultINRNumber'=>$diagnosticnumber,
                'MedicationNumber'=>$treatment,
                'MedicalReport'=>$reportNumber,
            
            );
                return View::create($response, JsonResponse::HTTP_OK, []);
            }
            else{
                return View::create('patient not found', JsonResponse::HTTP_NOT_FOUND, []); 
            }
        }
        

    else {
        return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        

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
                

    




  /**
        * @Rest\Get("/api/DoctorActivity", name ="DoctorUser_Activity")
        * @Rest\View(serializerGroups={"doctors"})
        */
        public function doctorActivityhistory(){
            $a=array();
            $user=$this->getUser();
            $data = array(
                'id' => $user->getId()
            );
            if ($user->getUserType() === UserType::TYPE_DOCTOR) {
                $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
                $Assigned = $repository->findBy(array('id_doctor'=>$user->getId(),'status'=>'Accepted','removed'=>false));
                if (!is_null($Assigned)) {
                    foreach ($Assigned as $data){
                    $a[]= $data->getIdPatient();
                    }
                
                $patientrepository = $this->getDoctrine()->getRepository(User::class);
                $patient = $patientrepository->findBy(array('id'=>$a));
                
                $diagnosticrepository = $this->getDoctrine()->getRepository(Diagnostic::class);
                $diagnostic = $diagnosticrepository->findBy(array('created_by'=>$a));
                $reportrepository = $this->getDoctrine()->getRepository(Note::class);
                $report = $reportrepository->findBy(array('created_by'=>$user->getId() ,'remove'=>false));
                $treatmentrepository = $this->getDoctrine()->getRepository(Treatment::class);
                $treatment = $treatmentrepository->findBy(array('created_by'=>$user->getId(),'remove'=>false));
    
                $patientNumber=count($patient);
                $diagnosticnumber=count($diagnostic);
                $reportNumber=count($report);
                $treatment=count($treatment);
    
                $response=array(
                    'PatientNumber'=>$patientNumber,
                    'ResultINRNumber'=>$diagnosticnumber,
                    'MedicationNumber'=>$treatment,
                    'MedicalReport'=>$reportNumber,
                
                );
                    return View::create($response, JsonResponse::HTTP_OK, []);
                }
                else{
                    return View::create('patient not found', JsonResponse::HTTP_NOT_FOUND, []); 
                }
            }
            
    
        else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
            
    
        }
    
        }


    }