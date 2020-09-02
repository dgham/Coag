<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Patient;
use App\Entity\UserType;
use App\Entity\Treatment;
use FOS\RestBundle\View\View;
use App\Entity\MedicationType;
use App\Entity\DoctorAssignement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RestApiTreatmentController extends FOSRestController
{
   /**
    * @Rest\Get("/api/medication", name ="api_treatment")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function index()
    {
        $user=$this->getUser();
        $data = array(
            'id' => $user->getId()
        );
            if ($user->getUserType() === UserType::TYPE_DOCTOR) {
                $repository = $this->getDoctrine()->getRepository(Treatment::class);
                $treatment = $repository->findBy(array('created_by'=> $data,'remove' => false),array('id'=>'DESC'));
                if (!empty($treatment)) {
                return View::create($treatment, JsonResponse::HTTP_OK, []);  
        } 
        else{
            return View::create('no treatment found', JsonResponse::HTTP_OK, []);
        }
            }  
              if ($user->getUserType() === UserType::TYPE_PATIENT) {
                $repository = $this->getDoctrine()->getRepository(Treatment::class);
                $treatment = $repository->findBy(array('patient'=> $data,'remove' => false,array('id'=>'DESC')));
                if (!empty($treatment)) {
                    return View::create($treatment, JsonResponse::HTTP_OK, []);
        
                } 
                else{
                    return View::create('no treatment found', JsonResponse::HTTP_OK, []);
                }

                        } 

                else {
                    return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                        }
            }
     /**
    * @Rest\Get("/api/medication/{id}", name ="search_treatment")
     * @Rest\View(serializerGroups={"users"})
     */
    public function searchTreatment($id)
    {
        $user=$this->getUser();
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $repository = $this->getDoctrine()->getRepository(Treatment::class);
            $treatment = $repository->findOneBy(array('id' => $id,'created_by' => $user->getId(),'remove' => false));
            if (!is_null($treatment)) {
                return View::create($treatment, JsonResponse::HTTP_OK, []);
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                  } 
                }
                  if ($user->getUserType() === UserType::TYPE_PATIENT) {
                    $repository = $this->getDoctrine()->getRepository(Treatment::class);
                    $treatment = $repository->findOneBy(array('id' => $id,'patient' => $user->getId(),'remove' => false));
                    if (!is_null($treatment)) {
                        return View::create($treatment, JsonResponse::HTTP_OK, []);
                } 
                else {
                    return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                           } 
            } else {
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []); 
        }
    }
    /**
    * @Rest\Post("/api/medication", name ="create_treatment")
    * @Rest\View(serializerGroups={"users"})
    */
    public function create(Request $request,EntityManagerInterface $entity){
        $user = $this->getUser();
            if ($user->getUserType() === UserType::TYPE_DOCTOR) {
                $treatment = new Treatment();
                $name= $request->request->get('name');
                $typname= gettype($name);
                if (isset($name)) {
                if($typname == "string"){
               $treatment->setName($name);
            } else {
                return View::create(' name of treatment should be string!', JsonResponse::HTTP_BAD_REQUEST, []);
            }
        }
        else {
            return View::create('missing name of treatment!!', JsonResponse::HTTP_BAD_REQUEST, []);
        }        
                $type= $request->request->get('type');
                $typetype= gettype($type);
                if (isset($type)) {
                    if($typetype == "string"){
                         $treatment->setMedicationType($type);
                            }
                            else{
                                return View::create('type of treatment must be integer!', JsonResponse::HTTP_BAD_REQUEST, []);
                            }
                            }
                            else {
                                return View::create('missing type of treatment!!', JsonResponse::HTTP_BAD_REQUEST, []);
                            }        
                                    $dosage= $request->request->get('dosage');
                                  
                                    $typedosage= gettype($dosage);
                                    if (isset($dosage)) {
                                        if($typedosage == "string"){
                                    $treatment->setDosage($dosage);
                                } else {
                                    return View::create(' dosage of treatment should be string!', JsonResponse::HTTP_BAD_REQUEST, []);
                                }
                            }
                            else {
                                return View::create('missing dosage of the treatment!!', JsonResponse::HTTP_BAD_REQUEST, []);
                            }
                                    $periode= $request->request->get('periode');
                                    $periodef= $request->request->get('periodeof');
                                    $typeperiode= gettype($periode);
                                    if (isset($periode)) {
                                        $periodfinal=$periode.' '.$periodef;
                                        if($typeperiode == "string"){
                                    $treatment->setPeriode($periodfinal);
                                } else {
                                    return View::create(' periode of treatment should be string!', JsonResponse::HTTP_BAD_REQUEST, []);
                                }
                            }
                            else {
                                return View::create('missing periode!!', JsonResponse::HTTP_BAD_REQUEST, []);
                            }
                                    
                                $patientid= $request->request->get('patient_id');
                                if (isset($patientid)){
                                    //   Get user if exist or not   //
                                    $userrepository = $this->getDoctrine()->getRepository(User::class);
                                    $iduser = $userrepository->findOneBy(array('id' => $patientid));
                                  
                                    //    Get patient if doctor is assigned by or not //
                                    $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
                                    $idpatient = $repository->findOneBy(array('id_doctor'=>$user->getId(),'id_patient'=>$patientid,'status'=>'Accepted','removed'=>false));
                                    if(!is_null($iduser)){
                                        if(!is_null($idpatient)){
                                            $treatment->setPatient($iduser);
                                            $treatment->setCreatedBy($user);
                                            $treatment->setCreatedAt(new \DateTime());
                                            $treatment->setRemove(false);
                                            $entity ->persist($treatment);
                                            $entity->flush();
                                            $response=array(
                                                'message'=>'treatment created',
                                                'result'=>$treatment,
                                            
                                            );
                                            return View::create($response, Response::HTTP_CREATED, []);
                                            
                                    
                                        } else {
                                            return View::create('this doctor is not assigned to this patient!!', JsonResponse::HTTP_BAD_REQUEST, []);
                                        }
                                    }else {
                                        return View::create('sorry, you are not the doctor of this patient!', JsonResponse::HTTP_BAD_REQUEST, []);
                                        
                                    }
                                
                                
                                }else {
                                    return View::create('you should add patient to add his treatment !', JsonResponse::HTTP_BAD_REQUEST, []);
                                }                    
                    } 
                        else {
                            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                        } 
                    }
      
     /**
     * @param Request $request
     * @Rest\Patch("/api/medication/{id}", name ="patch_treatment")
     * @Rest\View(serializerGroups={"users"})
     */
    public function patchAction(Request $request,$id)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $repository = $this->getDoctrine()->getRepository(Treatment::class);
            $treatment = $repository->findOneBy(array('id' => $id,'created_by' => $user->getId(),'remove' => false));
                 if (!is_null($treatment)) {
                    $name= $request->request->get('name');
                    $typname= gettype($name);
                if (isset($name)) {
                    if($typname == "string"){
               $treatment->setName($name);
            } else {
                return View::create(' name of treatment should be string!', JsonResponse::HTTP_BAD_REQUEST, []);
            }
        }
                    $type= $request->request->get('type');
                    $typetype= gettype($type);
                    if (isset($type)) {
                        if($typetype == "string"){
                            $treatment->setMedicationType($type);
                        } else {
                            return View::create(' type of treatment should be string!', JsonResponse::HTTP_BAD_REQUEST, []);
                        }
                    }
                    $dosage= $request->request->get('dosage');
                    $typedosage= gettype($dosage);
                    if (isset($dosage)) {
                        if($typedosage == "string"){
                            $treatment->setDosage($dosage);
                        } else {
                            return View::create(' dosage of treatment should be string!', JsonResponse::HTTP_BAD_REQUEST, []);
                        }
                    }
                    $periode= $request->request->get('periode');
                    $typeperiode= gettype($periode);
                    if (isset($periode)) {
                        if($typeperiode == "string"){
                    $treatment->setPeriode($periode);
                } else {
                    return View::create(' periode of treatment should be string!', JsonResponse::HTTP_BAD_REQUEST, []);
                }
            }
                    $patientid= $request->request->get('patient_id');
                    if (isset($patientid)){
                        //   Get user if exist or not   //
                     $userrepository = $this->getDoctrine()->getRepository(User::class);
                     $iduser = $userrepository->findOneBy(array('id' => $patientid));
                     //    Get patient if doctor is assigned by or not //

                     $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
                     $idpatient = $repository->findOneBy(array('id_doctor'=>$user->getId() ,'id_patient'=>$patientid ,'status'=>'Accepted','removed'=>false));
                     if(!is_null($iduser)){
                         if(!is_null($idpatient)){
                             $treatment->setPatient($iduser);
                        }    
                            else {
                             return View::create('this patient not assingned ', JsonResponse::HTTP_BAD_REQUEST, []);
                           
                            }  
                    }

                           else {
                            return View::create('patient Not Found', JsonResponse::HTTP_NOT_FOUND);
                            }
                }
                             $treatment->setUpdatedBy($user);
                             $treatment->setUpdatedAt(new \DateTime());
                             $em = $this->getDoctrine()->getManager();
                             $em->flush();
                             $response=array(
                                'message'=>'treatment updated',
                                'result'=>$treatment,
                               
                            );
                             return View::create($response, JsonResponse::HTTP_OK, []);
                           
                             }

                        else {
                        return View::create('treatment Not Found', JsonResponse::HTTP_NOT_FOUND);   
                    }
                
                }
                else {
                    return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                    }
    }
       
    /**
    * @Rest\Delete("/api/medication/{id}", name ="delete_treatment")
    * @Rest\View(serializerGroups={"users"})
    */
    public function delete($id){
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $repository = $this->getDoctrine()->getRepository(Treatment::class);
            $treatment = $repository->findOneBy(array('id' => $id,'created_by' => $user->getId(),'remove' => false));
            if (!is_null($treatment)) {
                    $treatment->setRemove(true);
                    $treatment->setRemovedBy($user);
                    $treatment->setRemovedAt(new \DateTime());
                    $em = $this->getDoctrine()->getManager();
                    $em->flush();
                    return View::create('treatment deleted', JsonResponse::HTTP_OK, []);
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
     * @param Request $request
     * @return JsonResponse
     * @Rest\Post("api/medication/picture/{id}", name ="treatment_image")
     * @Rest\View(serializerGroups={"users"})
     */
    public function uploadImage($id,Request $request)
    {
              
               $user = $this->getUser();
               if ($user->getUserType() === UserType::TYPE_DOCTOR) {
                $repository = $this->getDoctrine()->getRepository(Treatment::class);
                $treatment = $repository->findOneBy(array('id' => $id,'created_by' => $user->getId(),'remove' => false));
              
                if (!is_null($treatment)) {
               $uploadedImage=$request->files->get('picture');
               if (!is_null($uploadedImage)){

           
           /**
            * @var UploadedFile $image
            */
               $image=$uploadedImage;
           
               $imageName=md5(uniqid()).'.'.$image->guessExtension();
              $type=$image->getType();
              $size = $image->getSize();
               $imagetype=$image->guessExtension();
              $path= $this->getParameter('treatment_directory'); 
              $path_uplaod='Assets/Treatments/';
              if ($imagetype == "jpeg" || $imagetype == "png" ){
                   $image->move($path_uplaod,$imageName);
                   $image_url=$path_uplaod.$imageName;
                   $treatment->setPicture($image_url);   
                   $treatment->setUpdatedBy($user);
                   $treatment->setUpdatedAt(new \DateTime());
                   $em = $this->getDoctrine()->getManager();
                   $em->flush();
                   return View::create('picture of treatment updated', JsonResponse::HTTP_OK, []);
              }
              else {
                return View::create('there is something wrong with this file!,select picture!', JsonResponse::HTTP_BAD_REQUEST, []);
               }
            }    
            else {
                return View::create('picture is missing!', JsonResponse::HTTP_BAD_REQUEST, []);
           
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

                //get treatment by patient//
    /**
    * @Rest\Get("/api/medicationByUser/{id}", name ="user_treatment")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function findTreatmentByUser($id)
    {
        $user=$this->getUser();
        $data = array(
            'id' => $user->getId()
        );
            if ($user->getUserType() === UserType::TYPE_DOCTOR) {
                $repository = $this->getDoctrine()->getRepository(Treatment::class);
                $treatment = $repository->findBy(array('created_by'=> $data,'remove' => false,'patient'=>$id));
                if (!empty($treatment)) {
            
                return View::create($treatment, JsonResponse::HTTP_OK, []);       
                }
                else{
                    return View::create('No treatment found for this user ', JsonResponse::HTTP_OK, []);
                }
            }  
             
            else {
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                      }
        }           
}

