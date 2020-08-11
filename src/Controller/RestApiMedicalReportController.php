<?php

namespace App\Controller;

use DateTime;
use Exception;
use App\Entity\Note;
use App\Entity\User;
use App\Entity\Patient;
use App\Entity\UserType;
use FOS\RestBundle\View\View;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RestApiMedicalReportController extends FOSRestController
{

     /**
    * @Rest\Get("/api/MedicalReport", name ="api_notes")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function index()
    {
        $user=$this->getUser();
        $data = array(
            'id' => $user->getId()
        );
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $notesrepository = $this->getDoctrine()->getRepository(Note::class);
            $notes = $notesrepository->findBy(array('created_by'=> $data,'remove' => false),array('created_at'=>'DESC'));
            if (!is_null($notes)) {
                return View::create($notes, JsonResponse::HTTP_OK, []);
         }

        }

    if ($user->getUserType() === UserType::TYPE_PATIENT) {
        $notesrepository = $this->getDoctrine()->getRepository(Note::class);
        $notes = $notesrepository->findBy(array('patient_id'=> $data,'remove' => false));
        if (!is_null($notes)) {
            return View::create($notes, JsonResponse::HTTP_OK, []);
        }
      
    }
        else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }

     /**
     * @Rest\Get("/api/MedicalReport/{id}", name ="serch_notes")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function searchNote($id){
        $user=$this->getUser();
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $repository = $this->getDoctrine()->getRepository(Note::class);
            $note = $repository->findOneBy(array('id' => $id,'created_by' => $user->getId(),'remove' => false));
            if (!is_null($note)) {
                return View::create($note, JsonResponse::HTTP_OK, []);
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                  } 
                }

        if ($user->getUserType() === UserType::TYPE_PATIENT) {
            $repository = $this->getDoctrine()->getRepository(Note::class);
            $note = $repository->findOneBy(array('id' => $id,'patient_id' => $user->getId(),'remove' => false));
            if (!is_null($note)) {
            return View::create($note, JsonResponse::HTTP_OK, []);
        } else {
            return View::create(' Not Found,try again!', JsonResponse::HTTP_NOT_FOUND);
        }
            }
            else {
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
            }
        }

    /**
   * @Rest\Post("/api/MedicalReport", name ="post_notes")
   * @Rest\View(serializerGroups={"doctors"})
     */
        public function create(Request $request,EntityManagerInterface $entity){
            $user = $this->getUser();
          
                if ($user->getUserType() === UserType::TYPE_DOCTOR) {
                    $comment= $request->request->get('comment');
                    $typecomment= gettype($comment);
                    if (isset($comment)) {
                    if($typecomment == "string"){
                    $note = new Note();
                   $note->setComment(preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); },$comment));
                   $patientid= $request->request->get('patient_id');
                   if (isset($patientid)){
                       //   Get user if exist or not   //
                    $userrepository = $this->getDoctrine()->getRepository(User::class);
                    $iduser = $userrepository->findOneBy(array('id' => $patientid));
                    //    Get patient if doctor is assigned by or not //
                    $patientrepository = $this->getDoctrine()->getRepository(Patient::class);
                    $idpatient= $patientrepository->findOneBy(array('created_by' => $patientid,'assignedBy'=> $user->getId()));
                    if(!is_null($iduser)){
                        if(!is_null($idpatient)){
                            $note->setPatientId($iduser);
                            $note->setCreatedBy($user);
                            $note->setCreatedAt(new \DateTime());
                            $note->setRemove(false);
                            $entity ->persist($note);
                            $entity->flush();
                            $response=array(
                                'message'=>'Medical repport created',
                                'result'=>$note,
                               
                            );
                            return View::create($response, Response::HTTP_CREATED, []);
                    
                        } else {
                         return View::create('not assigned to this patient!!', JsonResponse::HTTP_BAD_REQUEST, []);
                        }
                         }else {
                        return View::create('sorry this patient not exist!!', JsonResponse::HTTP_BAD_REQUEST, []);
                        }
                        }else {
                        return View::create('you should add patient that u assigned !', JsonResponse::HTTP_BAD_REQUEST, []);
                        }
                    } else {
                        return View::create('comment should be a string', JsonResponse::HTTP_BAD_REQUEST, []);
                        }
                 
                        } else {
                        return View::create('comment is required to add note for your patient', JsonResponse::HTTP_BAD_REQUEST, []);
                        }
                       

                 } else {
                    return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                }   
    }
    
     /**
     * @param Request $request
     *
      * @Rest\Patch("/api/MedicalReport/{id}", name ="patch_notes")
      * @Rest\View(serializerGroups={"doctors"})
     */
    public function patchAction(Request $request,$id)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $repository = $this->getDoctrine()->getRepository(Note::class);
            $notes = $repository->findOneBy(array('id' => $id,'created_by' => $user->getId(),'remove' => false));
                 if (!is_null($notes)) {
                    $comment= $request->request->get('comment');
                     if (isset($comment)) {
                    $notes->setComment(preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); },$comment));
                     }
                     $patientid= $request->request->get('patient_id');
                     if (isset($patientid)){
                         //   Get user if exist or not   //
                      $userrepository = $this->getDoctrine()->getRepository(User::class);
                      $iduser = $userrepository->findOneBy(array('id' => $patientid));
                      //    Get patient if doctor is assigned by or not //
                      $patientrepository = $this->getDoctrine()->getRepository(Patient::class);
                      $idpatient= $patientrepository->findOneBy(array('created_by' => $patientid,'assignedBy'=> $user->getId()));
                      if(!is_null($iduser)){
                          if(!is_null($idpatient)){
                    $notes->setPatientId($iduser);
                        } else {
                            return View::create('sorry but you are not assigned to this patient!', JsonResponse::HTTP_BAD_REQUEST, []);
                           
                        }
                    }else {
                        return View::create('sorry ,this patient not exist!!', JsonResponse::HTTP_BAD_REQUEST, []);
                        
                    }
                }
        
                    $notes->setUpadatedBy($user);
                    $notes->setUpadtedAt(new \DateTime());
                    $em = $this->getDoctrine()->getManager();
                    $em->flush();
                    $response=array(
                        'message'=>'Medical report updated',
                        'result'=>$notes,
                       
                    );
                    return View::create($response, JsonResponse::HTTP_OK, []);
                     }    
                     else {
                        return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                    
                 }
            }
        }
    

    
   /**
     * @Rest\Delete("/api/MedicalReport/{id}", name ="delete_notes")
     */
    public function delete($id){
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $repository = $this->getDoctrine()->getRepository(Note::class);
            $notes = $repository->findOneBy(array('id' => $id,'created_by' => $user->getId(),'remove' => false));
            if (!is_null($notes)) {
                    $notes->setRemove(true);
                    $notes->setRemovedBy($user);
                    $notes->setRemovedAt(new \DateTime());
                    $em = $this->getDoctrine()->getManager();
                    $em->flush();
                    return View::create('note are deleted', JsonResponse::HTTP_OK, []);
                 } 
                
                return View::create(' Not Found!', JsonResponse::HTTP_NOT_FOUND);
        }
            
                 else {
                    return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                
        
            
           
        }    
}


 /**
    * @Rest\Get("/api/PatientMedicalReport/{id}", name ="patient_report")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function ReportOfPatient($id)
    {
        $user=$this->getUser();
        $data = array(
            'id' => $user->getId()
        );
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $notesrepository = $this->getDoctrine()->getRepository(Note::class);
            $notes = $notesrepository->findBy(array('created_by'=> $data,'remove' => false,'patient_id'=>$id));
            if (!is_null($notes)) {
                return View::create($notes, JsonResponse::HTTP_OK, []);
         }

    
        else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }
}
}