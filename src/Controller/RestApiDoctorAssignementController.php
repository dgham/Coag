<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use ReflectionClass;
use App\Entity\Doctor;
use App\Entity\Patient;
use App\Entity\Hospital;
use App\Entity\UserType;
use App\Entity\Matricule;
use App\Entity\Diagnostic;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\Form;
use App\Entity\DoctorAssignement;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\Mime\Email;
use FOS\UserBundle\Event\FormEvent;
use App\Repository\DoctorRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use FOS\UserBundle\Event\GetResponseUserEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use FOS\UserBundle\Event\GetResponseNullableUserEvent;
use Symfony\Component\Security\Core\User\UserInterface;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RestApiDoctorAssignementController extends AbstractController
{

     /**
     * @Rest\Get("/api/showAssigned", name ="show_assigned")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function showAssigned()
    {
        $user= $this->getUser();
        $data = array(
            'id' => $user->getId()
        );
            if ($user->getUserType() === UserType::TYPE_DOCTOR) {
                $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
                $doctorassignement = $repository->findBy(array('id_doctor'=>$user->getId(),'status'=>'Accepted','removed'=>false),array('id'=>'DESC'));
               if (empty($doctorassignement)){
                return View::create('no data found' , JsonResponse::HTTP_OK, []);
               }
               else{
                return View::create($doctorassignement, JsonResponse::HTTP_OK, []);
            }
        }
            if ($user->getUserType() === UserType::TYPE_PATIENT) {
                $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
                $patientassignement = $repository->findBy(array('id_patient'=>$user->getId(),'status'=>'Accepted','removed'=>false));
                if (empty($patientassignement)){
                    return View::create('no data found' , JsonResponse::HTTP_OK, []);
                   }
                   else{
                return View::create($patientassignement, JsonResponse::HTTP_OK, []);
            }
        }
            
            else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
            }
    }
    /**
     * @Rest\Get("/api/history/showAssigned/{id}", name ="history_assigned")
     * @Rest\View(serializerGroups={"doctors"})
     */

    public function showHistoryAssigned($id)
    {
        $user= $this->getUser();
        $data = array(
            'id' => $user->getId()
        );
            if ($user->getUserType() === UserType::TYPE_DOCTOR) {
                $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
                $doctorassignement = $repository->findBy(array('id_doctor'=>$user->getId(),'removed'=>false));
               if (empty($doctorassignement)){
                return View::create('no data found' , JsonResponse::HTTP_OK, []);
               }
                return View::create($doctorassignement, JsonResponse::HTTP_OK, []);
            }
            if ($user->getUserType() === UserType::TYPE_PATIENT) {
                $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
                $patientassignement = $repository->findBy(array('id_patient'=>$user->getId(),'removed'=>false));
                if (empty($patientassignement)){
                    return View::create('no data found' , JsonResponse::HTTP_OK, []);
                   }
                return View::create($patientassignement, JsonResponse::HTTP_OK, []);
            }
            else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
            }
    }



 /**
     * @Rest\Get("/api/showAssigned/{id}", name ="show_assignedID")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function showAssignedById($id)
    {
        $user= $this->getUser();
        $data = array(
            'id' => $user->getId()
        );
            if ($user->getUserType() === UserType::TYPE_DOCTOR) {
                $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
                $doctorassignement = $repository->findOneBy(array('id_doctor'=>$user->getId(),'id_patient'=>$id,'status'=>'Accepted','removed'=>false),array('id'=>'DESC'));
               if (empty($doctorassignement)){
                return View::create('no data found' , JsonResponse::HTTP_OK, []);
               }
               else{

                return View::create($doctorassignement, JsonResponse::HTTP_OK, []);
            }
        }
            if ($user->getUserType() === UserType::TYPE_PATIENT) {
                $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
                $patientassignement = $repository->findOneBy(array('id_patient'=>$user->getId(),'id_doctor'=>$id,'status'=>'Accepted','removed'=>false));
                if (empty($patientassignement)){
                    return View::create('no data found' , JsonResponse::HTTP_OK, []);
                   }
                   else{
                return View::create($patientassignement, JsonResponse::HTTP_OK, []);
            }
        }
            
            else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
            }
    }

    


    

     /**
     * send patient invitation
     *
     * @Rest\Post("/api/sendInvitation", name ="sendinvitaion")
     */
    public function sendInvitation(Request $request,\Swift_Mailer $mailer,EntityManagerInterface $entity)
    {
        $user= $this->getUser();
        $data = array(
            'id' => $user->getId()
        );
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
        $email= $request->request->get('email');
        $doctor= $user->getUsername();
        $idDoctor= $user->getId();
        $username=$user->getUsername();
        $emaill= $user->getEmail();
        $id= $user->getId();
        if (isset($email)) {
            $repository = $this->getDoctrine()->getRepository(User::class);
            $patientValidation = $repository->findOneBy(array('email'=>$email,'userType'=>'patient'));
            $repository = $this->getDoctrine()->getRepository(User::class);
            $doctorvalidation = $repository->findOneBy(array('username'=>$doctor));
            if (!is_null($patientValidation)) {
                $idpatient= $patientValidation->getId();
                $name=$patientValidation->getUsername();
                $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
                $Assignementvalidation = $repository->findOneBy(array('id_doctor'=>$idDoctor,'id_patient'=>$idpatient,'created_by'=>$user->getId(),'status'=>'Pending','removed'=>false));
                $AssignementRefusedvalidation = $repository->findOneBy(array('id_doctor'=>$idDoctor,'id_patient'=>$idpatient,'created_by'=>$user->getId(),'status'=>'Refused','removed'=>false));
                $Assignementacceptedvalidation = $repository->findOneBy(array('id_doctor'=>$idDoctor,'id_patient'=>$idpatient,'status'=>'Accepted','removed'=>false));
                $Assignementacceptedremoved = $repository->findOneBy(array('id_doctor'=>$idDoctor,'id_patient'=>$idpatient,'status'=>'Accepted','removed'=>true));
                if (!empty($Assignementacceptedvalidation)){
                    return View::create('you are already accepted by this patient ', JsonResponse::HTTP_FORBIDDEN, []);
                }
            if (!empty($Assignementvalidation)){
               $token= $Assignementvalidation->getInvitationToken();
               if($token !=null){
                $Assignementvalidation->setRequestDate(new \DateTime());
                $Assignementvalidation->setUpdatedBy($user);
                $Assignementvalidation->setUpdatedAt(new \DateTime());
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                try {
                    $transport = (new \Swift_SmtpTransport('mail.dreamhost.com', 587, 'tls'))
                        ->setUsername('amira.dgham@intern.continuousnet.com')
                        ->setPassword('?qS^3igZ')
                        ->setStreamOptions(array('ssl' => array('allow_self_signed' => false, 'verify_peer' => false)));
                    $mailer = new \Swift_Mailer($transport);
            $message = (new \Swift_Message('CoagCare message'))
            ->setFrom('amira.dgham@intern.continuousnet.com')
            ->setTo($email)
            ->setBody(
                '<html>' .
                '<head>
                <style>
            .button {
            background-color: #56c596; /* Green */
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            width: 150px;
            text-align: center;
            color: white;
            }
            .btn {
                border: 2px solid #56c596;
                padding: 15px 32px;
                text-align: center;
                text-decoration: none;
                display: inline-block;
                font-size: 16px;
                margin: 4px 2px;
                cursor: pointer;
                width: 150px;
                text-align: center;
            }
            .button3 {background-color: #6ccda4;} /* Red */ 
            #container{
                text-align: center;
            }
            </style></head>'.

            ' <body>' .
            ' <div marginwidth="0" marginheight="0" style="width:100%;background-color:#ffffff;margin:0;padding:0;">

            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="m_-3655858657801354087container" style="border-collapse:collapse;width:100%;min-width:100%;height:auto">
              <tbody><tr>
                <td width="100%" valign="top" bgcolor="#ffffff" style="padding-top:20px">


                  <table width="580" class="m_-3655858657801354087deviceWidth" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" style="border-collapse:collapse;margin:0 auto">
                    <tbody><tr>
                      <td valign="top" align="center" style="padding:0" bgcolor="#ffffff">
                          <img src="https://api.coagcare.continuousnet.com/Assets/images/4448b54b5015cf433e02a578933a9925.png" alt="" border="0" width="125" style="display:block">
                      </td>
                    </tr>
                    <tr>
                      <td style="font-size:13px;color:#282828;font-weight:normal;text-align:left;line-height:24px;vertical-align:top;padding:15px 8px 10px 8px" bgcolor="#ffffff">
                        <h2 style="text-align:center;font-weight:600;margin:30px 0 50px 0">  you have been invited to join <br> doctor '.$username.'</h2>
                        <br><p> Dear '. $name .',<br><br> We got a request invitation from CoagCare Doctor '. $username .' 
                       .Just click the link below and 
                        you will be on your way. If you did not want to allow Dr '. $username .'to assigned you, please ignore this email by clicking on refuse button and thanks . </p>
                        <p> If you need aditional information about the doctor, or you did not make this change, please contact <a href=`mailto:'. $emaill .'` style=`color:#56c596;text-decoration:unerline;font-weight:blod`>'. $emaill .'</a>. <br>
                          </td>
                          </tr>
                          <tr>
                              <td style="padding-bottom:30px">
       <div class="container"> <center><a href=`https://api.coagcare.continuousnet.com/InvitationResponse?token='.$token.'&id='.$id.' class="button button3" style=`color:#fffff;` ><font color="FFFFF"> Accept invitation</font> </a> <a href=`https://api.coagcare.continuousnet.com/InvitationResponse?token='.$token.'&id='.$id.' class="btn" style=`color:#fffff;` > <font color="56c596"> Refuse invitation </font></a></center></div>
                            </td>
                          </tr>
                          <tr>
                              <td style="font-size:13px;padding:0px 10px 0px 10px;text-align:left">
                                <p>If you need additional assistance, or you did not make this change, please contact <a href="mailto:amira.dgham@intern.continuousnet.com" style="color:#56c596;;text-decoration:underline;font-weight:bold" target="_blank">amira.dgham@intern.continuousnet.com</a>.</p>
                                <p>Cheers,<br>The coagcare Team</p>
                            </td>
                        </tr>
                  </tbody></table>
                </td>
              </tr>
            </tbody></table>
            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse;margin:0 auto">   <tbody>     <tr>       <td bgcolor="#ffffff" style="line-height:150%;padding-top:10px;padding-left:10px;padding-right:18px;padding-bottom:30px;text-align:left;border-bottom:0;font-size:10px;border-top:0">         <table width="100%" cellpadding="0" cellspacing="0" border="0" align="left" style="border-collapse:collapse">           <tbody>             <tr>               <td valign="top" style="text-align:center;font-size:11px;color:#282828;padding:20px 0;padding-left:0px">This email was sent to <a style="color:#56c596;text-decoration:none;font-weight:600">' . $email . '</a>, which is associated with a CoagCare App account.  <br> <br>   © 2020 CoagCare App., All Rights Reserved                 <br> ContinuousNet., Residence ElAhmed 2nd Street Yesser Arafet, sahloul 4054                 <br>                 &nbsp;               </td>             </tr>           </tbody>         </table>       </td>     </tr>   </tbody> </table>

            <div style="display:none;white-space:nowrap;font:15px courier;color:#ffffff">
              - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            </div>
            </div>' .
            ' </body>' .
                            '</html>',
                            'text/html' 
                        );
        $mailer->send($message);
    } catch (\Exception $ex) {

        return View::create($ex->getMessage(), Response::HTTP_BAD_REQUEST, []);
    }

        $response=array(
            'message'=>'success',
            'result'=>'Email was send successfuly, check your email to reset your password'
        );
    return View::create($response, JsonResponse::HTTP_OK, []); 
               }
            }
             if(!empty($Assignementacceptedremoved)){
                $token= $Assignementacceptedremoved->getInvitationToken();
                if($token !=null){
                $token= $Assignementacceptedremoved->getInvitationToken();
                $Assignementacceptedremoved->setRequestDate(new \DateTime());
                $Assignementacceptedremoved->setUpdatedBy($user);
                $Assignementacceptedremoved->setUpdatedAt(new \DateTime());
                $Assignementacceptedremoved->setStatus('Pending');
                $Assignementacceptedremoved->setRemoved(false);
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                try {
                    $transport = (new \Swift_SmtpTransport('mail.dreamhost.com', 587, 'tls'))
                        ->setUsername('amira.dgham@intern.continuousnet.com')
                        ->setPassword('?qS^3igZ')
                        ->setStreamOptions(array('ssl' => array('allow_self_signed' => false, 'verify_peer' => false)));
                    $mailer = new \Swift_Mailer($transport);
            $message = (new \Swift_Message('CoagCare message'))
            ->setFrom('amira.dgham@intern.continuousnet.com')
            ->setTo($email)
            ->setBody(
                '<html>' .
                '<head>
                <style>
            .button {
            background-color: #56c596; /* Green */
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            width: 150px;

            text-align: center;
            color: white;
            
            }
            .btn {
                border: 2px solid #56c596;
                padding: 15px 32px;
                text-align: center;
                text-decoration: none;
                display: inline-block;
                font-size: 16px;
                margin: 4px 2px;
                cursor: pointer;
                width: 150px;
            
                text-align: center;
            }
            
            
            .button3 {background-color: #6ccda4;} /* Red */ 
            #container{
                text-align: center;
            }
            </style></head>'.
            ' <body>' .
            ' <div marginwidth="0" marginheight="0" style="width:100%;background-color:#ffffff;margin:0;padding:0;">

            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="m_-3655858657801354087container" style="border-collapse:collapse;width:100%;min-width:100%;height:auto">
              <tbody><tr>
                <td width="100%" valign="top" bgcolor="#ffffff" style="padding-top:20px">


                  <table width="580" class="m_-3655858657801354087deviceWidth" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" style="border-collapse:collapse;margin:0 auto">
                    <tbody><tr>
                      <td valign="top" align="center" style="padding:0" bgcolor="#ffffff">
                          <img src="https://api.coagcare.continuousnet.com/Assets/images/4448b54b5015cf433e02a578933a9925.png" alt="" border="0" width="125" style="display:block">
                      </td>
                    </tr>
                    <tr>
                      <td style="font-size:13px;color:#282828;font-weight:normal;text-align:left;line-height:24px;vertical-align:top;padding:15px 8px 10px 8px" bgcolor="#ffffff">
                      <h2 style="text-align:center;font-weight:600;margin:30px 0 50px 0">  you have been invited to join <br> doctor '.$username.'</h2>
                        <br><p> Dear '. $name .',<br><br> We got a request invitation from CoagCare Doctor '. $username .' 
                         .Just click the link below and
                        you will be on your way. If you did not want to allow Dr '. $username .'to assigned you, please ignore this email by clicking on refuse button and thanks . </p>
                        <p> If you need aditional information about the doctor, or you did not make this change, please contact <a href=`mailto:'. $emaill .'` style=`color:#56c596;text-decoration:unerline;font-weight:blod`>'. $emaill .'</a>. <br>
                          </td>
                          </tr>
                          <tr>
                              <td style="padding-bottom:30px">
       <div class="container"> <center><a href=`https://api.coagcare.continuousnet.com/InvitationResponse?token='.$token.'&id='.$id.' class="button button3" style=`color:#fffff;` ><font color="FFFFF"> Accept invitation</font> </a> <a href=`https://api.coagcare.continuousnet.com/InvitationResponse?token='.$token.'&id='.$id.' class="btn" style=`color:#fffff;` > <font color="56c596"> Refuse invitation </font></a></center></div>
                            </td>
                          </tr>
                          <tr>
                              <td style="font-size:13px;padding:0px 10px 0px 10px;text-align:left">
                                <p>If you need additional assistance, or you did not make this change, please contact <a href="mailto:amira.dgham@intern.continuousnet.com" style="color:#56c596;;text-decoration:underline;font-weight:bold" target="_blank">amira.dgham@intern.continuousnet.com</a>.</p>
                                <p>Cheers,<br>The coagcare Team</p>
                            </td>
                        </tr>
                  </tbody></table>
                </td>
              </tr>
            </tbody></table>
            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse;margin:0 auto">   <tbody>     <tr>       <td bgcolor="#ffffff" style="line-height:150%;padding-top:10px;padding-left:10px;padding-right:18px;padding-bottom:30px;text-align:left;border-bottom:0;font-size:10px;border-top:0">         <table width="100%" cellpadding="0" cellspacing="0" border="0" align="left" style="border-collapse:collapse">           <tbody>             <tr>               <td valign="top" style="text-align:center;font-size:11px;color:#282828;padding:20px 0;padding-left:0px">This email was sent to <a style="color:#56c596;text-decoration:none;font-weight:600">' . $email . '</a>, which is associated with a CoagCare App account.  <br> <br>   © 2020 CoagCare App., All Rights Reserved                 <br> ContinuousNet., Residence ElAhmed 2nd Street Yesser Arafet, sahloul 4054                 <br>                 &nbsp;               </td>             </tr>           </tbody>         </table>       </td>     </tr>   </tbody> </table>

            <div style="display:none;white-space:nowrap;font:15px courier;color:#ffffff">
              - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            </div>
            </div>' .
            ' </body>' .
                            '</html>',
                            'text/html' 
                        );
        $mailer->send($message);
    } catch (\Exception $ex) {

        return View::create($ex->getMessage(), Response::HTTP_BAD_REQUEST, []);
    }
        $response=array(
            'message'=>'success',
            'result'=>'Email was send successfuly, check your email to reset your password'
        );
    return View::create($response, JsonResponse::HTTP_OK, []); 
                } 
            }
             





                 if ((!empty($AssignementRefusedvalidation))){
                        $token= $AssignementRefusedvalidation->getInvitationToken();
                        if($token !=null){
                        $token= $AssignementRefusedvalidation->getInvitationToken();
                        $AssignementRefusedvalidation->setRequestDate(new \DateTime());
                        $AssignementRefusedvalidation->setUpdatedBy($user);
                        $AssignementRefusedvalidation->setUpdatedAt(new \DateTime());
                        $AssignementRefusedvalidation->setStatus('Pending');
                        $em = $this->getDoctrine()->getManager();
                        $em->flush();
                        try {
                            $transport = (new \Swift_SmtpTransport('mail.dreamhost.com', 587, 'tls'))
                                ->setUsername('amira.dgham@intern.continuousnet.com')
                                ->setPassword('?qS^3igZ')
                                ->setStreamOptions(array('ssl' => array('allow_self_signed' => false, 'verify_peer' => false)));
                            $mailer = new \Swift_Mailer($transport);
                    $message = (new \Swift_Message('CoagCare message'))
                    ->setFrom('amira.dgham@intern.continuousnet.com')
                    ->setTo($email)
                    ->setBody(
                        '<html>' .
                        '<head>
                        <style>
                    .button {
                    background-color: #56c596; /* Green */
                    border: none;
                    color: white;
                    padding: 15px 32px;
                    text-align: center;
                    text-decoration: none;
                    display: inline-block;
                    font-size: 16px;
                    margin: 4px 2px;
                    cursor: pointer;
                    width: 150px;
        
                    text-align: center;
                    color: white;
                    
                    }
                    .btn {
                        border: 2px solid #56c596;
                        padding: 15px 32px;
                        text-align: center;
                        text-decoration: none;
                        display: inline-block;
                        font-size: 16px;
                        margin: 4px 2px;
                        cursor: pointer;
                        width: 150px;
                    
                        text-align: center;
                    }
                    
                    
                    .button3 {background-color: #6ccda4;} /* Red */ 
                    #container{
                        text-align: center;
                    }
                    </style></head>'.
                    ' <body>' .
                    ' <div marginwidth="0" marginheight="0" style="width:100%;background-color:#ffffff;margin:0;padding:0;">
        
                    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="m_-3655858657801354087container" style="border-collapse:collapse;width:100%;min-width:100%;height:auto">
                      <tbody><tr>
                        <td width="100%" valign="top" bgcolor="#ffffff" style="padding-top:20px">
        
        
                          <table width="580" class="m_-3655858657801354087deviceWidth" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" style="border-collapse:collapse;margin:0 auto">
                            <tbody><tr>
                              <td valign="top" align="center" style="padding:0" bgcolor="#ffffff">
                                  <img src="https://api.coagcare.continuousnet.com/Assets/images/4448b54b5015cf433e02a578933a9925.png" alt="" border="0" width="125" style="display:block">
                              </td>
                            </tr>
                            <tr>
                              <td style="font-size:13px;color:#282828;font-weight:normal;text-align:left;line-height:24px;vertical-align:top;padding:15px 8px 10px 8px" bgcolor="#ffffff">
                              <h2 style="text-align:center;font-weight:600;margin:30px 0 50px 0">  you have been invited to join <br> doctor '.$username.'</h2>
                                <br><p> Dear '. $name .',<br><br> We got a request invitation from CoagCare Doctor '. $username .' 
                                .Just click the link below and
                                you will be on your way. If you did not want to allow Dr '. $username .'to assigned you, please ignore this email by clicking on refuse button and thanks . </p>
                                <p> If you need aditional information about the doctor, or you did not make this change, please contact <a href=`mailto:'. $emaill .'` style=`color:#56c596;text-decoration:unerline;font-weight:blod`>'. $emaill .'</a>. <br>
                                  </td>
                                  </tr>
                                  <tr>
                                      <td style="padding-bottom:30px">
               <div class="container"> <center><a href=`https://api.coagcare.continuousnet.com/InvitationResponse?token='.$token.'&id='.$id.' class="button button3" style=`color:#fffff;` ><font color="FFFFF"> Accept invitation</font> </a> <a href=`https://api.coagcare.continuousnet.com/InvitationResponse?token='.$token.'&id='.$id.' class="btn" style=`color:#fffff;` > <font color="56c596"> Refuse invitation </font></a></center></div>
                                    </td>
                                  </tr>
                                  <tr>
                                      <td style="font-size:13px;padding:0px 10px 0px 10px;text-align:left">
                                        <p>If you need additional assistance, or you did not make this change, please contact <a href="mailto:amira.dgham@intern.continuousnet.com" style="color:#56c596;;text-decoration:underline;font-weight:bold" target="_blank">amira.dgham@intern.continuousnet.com</a>.</p>
                                        <p>Cheers,<br>The coagcare Team</p>
                                    </td>
                                </tr>
                          </tbody></table>
                        </td>
                      </tr>
                    </tbody></table>
                    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse;margin:0 auto">   <tbody>     <tr>       <td bgcolor="#ffffff" style="line-height:150%;padding-top:10px;padding-left:10px;padding-right:18px;padding-bottom:30px;text-align:left;border-bottom:0;font-size:10px;border-top:0">         <table width="100%" cellpadding="0" cellspacing="0" border="0" align="left" style="border-collapse:collapse">           <tbody>             <tr>               <td valign="top" style="text-align:center;font-size:11px;color:#282828;padding:20px 0;padding-left:0px">This email was sent to <a style="color:#56c596;text-decoration:none;font-weight:600">' . $email . '</a>, which is associated with a CoagCare App account.  <br> <br>   © 2020 CoagCare App., All Rights Reserved                 <br> ContinuousNet., Residence ElAhmed 2nd Street Yesser Arafet, sahloul 4054                 <br>                 &nbsp;               </td>             </tr>           </tbody>         </table>       </td>     </tr>   </tbody> </table>
        
                    <div style="display:none;white-space:nowrap;font:15px courier;color:#ffffff">
                      - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
                    </div>
                    </div>' .
                    ' </body>' .
                                    '</html>',
                                    'text/html' 
                                );
                $mailer->send($message);
            } catch (\Exception $ex) {

                return View::create($ex->getMessage(), Response::HTTP_BAD_REQUEST, []);
            }
                $response=array(
                    'message'=>'success',
                    'result'=>'Email was send successfuly, check your email to reset your password'
                );
            return View::create($response, JsonResponse::HTTP_OK, []); 
                        } 
                    }
                    else{
                        $patient_id= $patientValidation->getId();
                        $doctor_id= $user->getId();
                        $doctorAssignment=new DoctorAssignement();
                        $doctorAssignment->setIdPatient($patientValidation);
                        $doctorAssignment->setIdDoctor($user);
                        $doctorAssignment->setRequestDate(new \DateTime());
                        $doctorAssignment->setStatus("Pending");
                        $doctorAssignment->setCreatedBy($user);
                        $doctorAssignment->setEnabled(true);
                        $doctorAssignment->setRemoved(false);
                        $doctorAssignment->setCreatedAt(new \DateTime());
                        $doctorAssignment->setInvitationToken(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
                        $entity ->persist($doctorAssignment);
                        $entity->flush();
                        try {
                            $token = $doctorAssignment->getInvitationToken();
                            $transport = (new \Swift_SmtpTransport('mail.dreamhost.com', 587, 'tls'))
                                ->setUsername('amira.dgham@intern.continuousnet.com')
                                ->setPassword('?qS^3igZ')
                                ->setStreamOptions(array('ssl' => array('allow_self_signed' => false, 'verify_peer' => false)));
                            $mailer = new \Swift_Mailer($transport);
                        $message = (new \Swift_Message('CoagCare message'))
            ->setFrom('amira.dgham@intern.continuousnet.com')
            ->setTo($email)
            ->setBody(
                '<html>' .
                '<head>
                <style>
            .button {
            background-color: #56c596; /* Green */
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            width: 150px;

            text-align: center;
            color: white;
            
            }
            .btn {
                border: 2px solid #56c596;
                padding: 15px 32px;
                text-align: center;
                text-decoration: none;
                display: inline-block;
                font-size: 16px;
                margin: 4px 2px;
                cursor: pointer;
                width: 150px;
            
                text-align: center;
            }
            
            
            .button3 {background-color: #6ccda4;} /* Red */ 
            #container{
                text-align: center;
            }
            </style></head>'.
            ' <body>' .
            ' <div marginwidth="0" marginheight="0" style="width:100%;background-color:#ffffff;margin:0;padding:0;">

            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="m_-3655858657801354087container" style="border-collapse:collapse;width:100%;min-width:100%;height:auto">
              <tbody><tr>
                <td width="100%" valign="top" bgcolor="#ffffff" style="padding-top:20px">


                  <table width="580" class="m_-3655858657801354087deviceWidth" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" style="border-collapse:collapse;margin:0 auto">
                    <tbody><tr>
                      <td valign="top" align="center" style="padding:0" bgcolor="#ffffff">
                          <img src="https://api.coagcare.continuousnet.com/Assets/images/4448b54b5015cf433e02a578933a9925.png" alt="" border="0" width="125" style="display:block">
                      </td>
                    </tr>
                    <tr>
                      <td style="font-size:13px;color:#282828;font-weight:normal;text-align:left;line-height:24px;vertical-align:top;padding:15px 8px 10px 8px" bgcolor="#ffffff">
                      <h2 style="text-align:center;font-weight:600;margin:30px 0 50px 0">  you have been invited to join <br> doctor '.$username.'</h2>
                       <br><p> Dear '. $name .',<br><br> We got a request invitation from CoagCare Doctor '. $username .' 
                        .Just click the link below and
                        you will be on your way. If you did not want to allow Dr '. $username .'to assigned you, please ignore this email by clicking on refuse button and thanks . </p>
                        <p> If you need aditional information about the doctor, or you did not make this change, please contact <a href=`mailto:'. $emaill .'` style=`color:#56c596;text-decoration:unerline;font-weight:blod`>'. $emaill .'</a>. <br>
                          </td>
                          </tr>
                          <tr>
                              <td style="padding-bottom:30px">
       <div class="container"> <center><a href=`https://api.coagcare.continuousnet.com/InvitationResponse?token='.$token.'&id='.$id.' class="button button3" style=`color:#fffff;` ><font color="FFFFF"> Accept invitation</font> </a> <a href=`https://api.coagcare.continuousnet.com/InvitationResponse?token='.$token.'&id='.$id.' class="btn" style=`color:#fffff;` > <font color="56c596"> Refuse invitation </font></a></center></div>
                            </td>
                          </tr>
                          <tr>
                              <td style="font-size:13px;padding:0px 10px 0px 10px;text-align:left">
                                <p>If you need additional assistance, or you did not make this change, please contact <a href="mailto:amira.dgham@intern.continuousnet.com" style="color:#56c596;;text-decoration:underline;font-weight:bold" target="_blank">amira.dgham@intern.continuousnet.com</a>.</p>
                                <p>Cheers,<br>The coagcare Team</p>
                            </td>
                        </tr>
                  </tbody></table>
                </td>
              </tr>
            </tbody></table>
            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse;margin:0 auto">   <tbody>     <tr>       <td bgcolor="#ffffff" style="line-height:150%;padding-top:10px;padding-left:10px;padding-right:18px;padding-bottom:30px;text-align:left;border-bottom:0;font-size:10px;border-top:0">         <table width="100%" cellpadding="0" cellspacing="0" border="0" align="left" style="border-collapse:collapse">           <tbody>             <tr>               <td valign="top" style="text-align:center;font-size:11px;color:#282828;padding:20px 0;padding-left:0px">This email was sent to <a style="color:#56c596;text-decoration:none;font-weight:600">' . $email . '</a>, which is associated with a CoagCare App account.  <br> <br>   © 2020 CoagCare App., All Rights Reserved                 <br> ContinuousNet., Residence ElAhmed 2nd Street Yesser Arafet, sahloul 4054                 <br>                 &nbsp;               </td>             </tr>           </tbody>         </table>       </td>     </tr>   </tbody> </table>

            <div style="display:none;white-space:nowrap;font:15px courier;color:#ffffff">
              - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            </div>
            </div>' .
            ' </body>' .
                            '</html>',
                            'text/html' 
                        );
        $mailer->send($message);
    } catch (\Exception $ex) {

        return View::create($ex->getMessage(), Response::HTTP_BAD_REQUEST, []);
    }
                        $response=array(
                                'message'=>'success',
                                'result'=>'Email was send successfuly, check your email to reset your password'
                            );
                        return View::create($response, JsonResponse::HTTP_OK, []);  
                    }
      
            }
            else{
                $response=array(
                    'message'=>'failure',
                    'result'=>'this email is not patient email! try another!'
                );
                return View::create($response, Response::HTTP_NOT_FOUND,[]);
                }
          }
          else{
            $response=array(
                'message'=>'failure',
                'result'=>' missing patient email!'
            );
            return View::create($response, Response::HTTP_BAD_REQUEST,[]);

            }
        }
        if ($user->getUserType() === UserType::TYPE_PATIENT) {

            $email= $request->request->get('email');
            $patient= $user->getUsername();
            $idpatient= $user->getId();
            $username=$user->getUsername();
            $emaill= $user->getEmail();
            $id= $user->getId();
            if (isset($email)) {
                $repository = $this->getDoctrine()->getRepository(User::class);
                $doctorValidation = $repository->findOneBy(array('email'=>$email,'userType'=>'doctor'));
           
                $repository = $this->getDoctrine()->getRepository(User::class);
                $patientvalidation = $repository->findOneBy(array('username'=>$patient));
              
                if (!is_null($doctorValidation)) {
                    $iddoctor= $doctorValidation->getId();
                    $name=$doctorValidation->getUsername();
                    $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
                    $Assignementvalidation = $repository->findOneBy(array('id_doctor'=>$iddoctor,'id_patient'=>$idpatient,'created_by'=>$user->getId(),'status'=>'Pending','removed'=>false));
                    $AssignementRefusedvalidation = $repository->findOneBy(array('id_doctor'=>$iddoctor,'id_patient'=>$idpatient,'created_by'=>$user->getId(),'status'=>'Refused','removed'=>false));
                    $Assignementacceptedvalidation = $repository->findOneBy(array('id_doctor'=>$iddoctor,'id_patient'=>$idpatient,'status'=>'Accepted','removed'=>false));
                    $Assignementacceptedromoved = $repository->findOneBy(array('id_doctor'=>$iddoctor,'id_patient'=>$idpatient,'status'=>'Accepted','removed'=>true));
                    if (!empty($Assignementacceptedvalidation)){
                        return View::create('you are already accepted by this doctor ', JsonResponse::HTTP_FORBIDDEN, []);
                    }
                    if (!empty($Assignementvalidation)){
                   $token= $Assignementvalidation->getInvitationToken();
                   if($token !=null){
                    $token= $Assignementvalidation->getInvitationToken();
                    $Assignementvalidation->setRequestDate(new \DateTime());
                    $Assignementvalidation->setUpdatedBy($user);
                    $Assignementvalidation->setUpdatedAt(new \DateTime());
                    $em = $this->getDoctrine()->getManager();
                    $em->flush(); 
                   }
                   else{
                    $Assignementvalidation->setInvitationToken(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
                    $Assignementvalidation->setRequestDate(new \DateTime());
                    $Assignementvalidation->setUpdatedBy($user);
                    $Assignementvalidation->setUpdatedAt(new \DateTime());
                    $em = $this->getDoctrine()->getManager();
                    $em->flush(); 
                      $token= $Assignementvalidation->getInvitationToken();
                      try {
                        $transport = (new \Swift_SmtpTransport('mail.dreamhost.com', 587, 'tls'))
                            ->setUsername('amira.dgham@intern.continuousnet.com')
                            ->setPassword('?qS^3igZ')
                            ->setStreamOptions(array('tls' => array('allow_self_signed' => false, 'verify_peer' => false)));
                        $mailer = new \Swift_Mailer($transport);
                $message = (new \Swift_Message('CoagCare message'))
                ->setFrom('amira.dgham@intern.continuousnet.com')
                ->setTo($email)
                ->setBody(
                    '<html>' .
                    '<head>
                    <style>
                .button {
                background-color: #56c596; /* Green */
                border: none;
                color: white;
                padding: 15px 32px;
                text-align: center;
                text-decoration: none;
                display: inline-block;
                font-size: 16px;
                margin: 4px 2px;
                cursor: pointer;
                width: 150px;
    
                text-align: center;
                color: white;
                
                }
                .btn {
                    border: 2px solid #56c596;
                    padding: 15px 32px;
                    text-align: center;
                    text-decoration: none;
                    display: inline-block;
                    font-size: 16px;
                    margin: 4px 2px;
                    cursor: pointer;
                    width: 150px;
                
                    text-align: center;
                }
                
                
                .button3 {background-color: #6ccda4;} /* Red */ 
                #container{
                    text-align: center;
                }
                </style></head>'.


                ' <body>' .
                ' <div marginwidth="0" marginheight="0" style="width:100%;background-color:#ffffff;margin:0;padding:0;">
    
                <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="m_-3655858657801354087container" style="border-collapse:collapse;width:100%;min-width:100%;height:auto">
                  <tbody><tr>
                    <td width="100%" valign="top" bgcolor="#ffffff" style="padding-top:20px">
    
    
                      <table width="580" class="m_-3655858657801354087deviceWidth" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" style="border-collapse:collapse;margin:0 auto">
                        <tbody><tr>
                          <td valign="top" align="center" style="padding:0" bgcolor="#ffffff">
                              <img src="https://api.coagcare.continuousnet.com/Assets/images/4448b54b5015cf433e02a578933a9925.png" alt="" border="0" width="125" style="display:block">
                          </td>
                        </tr>
                        <tr>
                          <td style="font-size:13px;color:#282828;font-weight:normal;text-align:left;line-height:24px;vertical-align:top;padding:15px 8px 10px 8px" bgcolor="#ffffff">
                          <h2 style="text-align:center;font-weight:600;margin:30px 0 50px 0">  you have been invited to join <br> doctor '.$username.'</h2>
                        <br><p> Dear Dr '. $name .',<br><br> We got a request invitation from CoagCare application from the patient '. $username .' 
                        .Just click the button Accept below and
                          you will be on your way. If you did not want to follow INR measurements of the patient '. $username .', please ignore this email by clicking on refuse button and thanks . </p>
                          <p> If you need aditional information about the doctor, or you did not make this change, please contact <a href=`mailto:'. $emaill .'` style=`color:#56c596;text-decoration:unerline;font-weight:blod`>'. $emaill .'</a>. <br>
                              </td>
                              </tr>
                              <tr>
                                  <td style="padding-bottom:30px">
           <div class="container"> <center><a href=`https://api.coagcare.continuousnet.com/InvitationResponse?token='.$token.'&id='.$id.' class="button button3" style=`color:#fffff;` ><font color="FFFFF"> Accept invitation</font> </a> <a href=`https://api.coagcare.continuousnet.com/InvitationResponse?token='.$token.'&id='.$id.' class="btn" style=`color:#fffff;` > <font color="56c596"> Refuse invitation </font></a>
                                </td>
                              </tr>
                              <tr>
                                  <td style="font-size:13px;padding:0px 10px 0px 10px;text-align:left">
                                    <p>If you need additional assistance, or you did not make this change, please contact <a href="mailto:amira.dgham@intern.continuousnet.com" style="color:#56c596;;text-decoration:underline;font-weight:bold" target="_blank">amira.dgham@intern.continuousnet.com</a>.</p>
                                    <p>Cheers,<br>The coagcare Team</p>
                                </td>
                            </tr>
                      </tbody></table>
                    </td>
                  </tr>
                </tbody></table>
                <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse;margin:0 auto">   <tbody>     <tr>       <td bgcolor="#ffffff" style="line-height:150%;padding-top:10px;padding-left:10px;padding-right:18px;padding-bottom:30px;text-align:left;border-bottom:0;font-size:10px;border-top:0">         <table width="100%" cellpadding="0" cellspacing="0" border="0" align="left" style="border-collapse:collapse">           <tbody>             <tr>               <td valign="top" style="text-align:center;font-size:11px;color:#282828;padding:20px 0;padding-left:0px">This email was sent to <a style="color:#56c596;text-decoration:none;font-weight:600">' . $email . '</a>, which is associated with a CoagCare App account.  <br> <br>   © 2020 CoagCare App., All Rights Reserved                 <br> ContinuousNet., Residence ElAhmed 2nd Street Yesser Arafet, sahloul 4054                 <br>                 &nbsp;               </td>             </tr>           </tbody>         </table>       </td>     </tr>   </tbody> </table>
    
                <div style="display:none;white-space:nowrap;font:15px courier;color:#ffffff">
                  - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
                </div>
                </div>' .
                ' </body>' .

                                
                                '</html>',
                                'text/html' 
                            );
            $mailer->send($message);
        } catch (\Exception $ex) {

            return View::create($ex->getMessage(), Response::HTTP_BAD_REQUEST, []);
        }
            $response=array(
                'message'=>'success',
                'result'=>'Email was send successfuly, check your email to reset your password'
            );
        return View::create($response, JsonResponse::HTTP_OK, []); 
                    }
                  
                        }
    if(!empty($Assignementacceptedromoved)){

        $token= $Assignementacceptedromoved->getInvitationToken();
        if($token !=null){
        $token= $Assignementacceptedromoved->getInvitationToken();
        $Assignementacceptedromoved->setRequestDate(new \DateTime());
        $Assignementacceptedromoved->setUpdatedBy($user);
        $Assignementacceptedromoved->setUpdatedAt(new \DateTime());
        $Assignementacceptedromoved->setStatus('Pending');
        $Assignementacceptedromoved->setRemoved(false);
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        try {
            $transport = (new \Swift_SmtpTransport('mail.dreamhost.com', 587, 'tls'))
                ->setUsername('amira.dgham@intern.continuousnet.com')
                ->setPassword('?qS^3igZ')
                ->setStreamOptions(array('tls' => array('allow_self_signed' => false, 'verify_peer' => false)));
            $mailer = new \Swift_Mailer($transport);
    $message = (new \Swift_Message('CoagCare message'))
    ->setFrom('amira.dgham@intern.continuousnet.com')
    ->setTo($email)
    ->setBody(
        '<html>' .
        '<head>
        <style>
    .button {
    background-color: #56c596; /* Green */
    border: none;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    width: 150px;

    text-align: center;
    color: white;
    
    }
    .btn {
        border: 2px solid #56c596;
        padding: 15px 32px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        cursor: pointer;
        width: 150px;
    
        text-align: center;
    }
    
    
    .button3 {background-color: #6ccda4;} /* Red */ 
    #container{
        text-align: center;
    }
    </style></head>'.
    ' <body>' .
    ' <div marginwidth="0" marginheight="0" style="width:100%;background-color:#ffffff;margin:0;padding:0;">

    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="m_-3655858657801354087container" style="border-collapse:collapse;width:100%;min-width:100%;height:auto">
      <tbody><tr>
        <td width="100%" valign="top" bgcolor="#ffffff" style="padding-top:20px">


          <table width="580" class="m_-3655858657801354087deviceWidth" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" style="border-collapse:collapse;margin:0 auto">
            <tbody><tr>
              <td valign="top" align="center" style="padding:0" bgcolor="#ffffff">
                  <img src="https://api.coagcare.continuousnet.com/Assets/images/4448b54b5015cf433e02a578933a9925.png" alt="" border="0" width="125" style="display:block">
              </td>
            </tr>
            <tr>
              <td style="font-size:13px;color:#282828;font-weight:normal;text-align:left;line-height:24px;vertical-align:top;padding:15px 8px 10px 8px" bgcolor="#ffffff">
              <h2 style="text-align:center;font-weight:600;margin:30px 0 50px 0">  you have been invited to join <br> doctor '.$username.'</h2>
             <br><p> Dear Dr '. $name .',<br><br> We got a request invitation from CoagCare application from the patient '. $username .' 
              .Just click the button Accept below and 
              you will be on your way. If you did not want to follow INR measurements of the patient '. $username .', please ignore this email by clicking on refuse button and thanks . </p>
              <p> If you need aditional information about the doctor, or you did not make this change, please contact <a href=`mailto:'. $emaill .'` style=`color:#56c596;text-decoration:unerline;font-weight:blod`>'. $emaill .'</a>. <br>
                  </td>
                  </tr>
                  <tr>
                      <td style="padding-bottom:30px">
<div class="container"> <center><a href=`https://api.coagcare.continuousnet.com/InvitationResponse?token='.$token.'&id='.$id.' class="button button3" style=`color:#fffff;` ><font color="FFFFF"> Accept invitation</font> </a> <a href=`https://api.coagcare.continuousnet.com/InvitationResponse?token='.$token.'&id='.$id.' class="btn" style=`color:#fffff;` > <font color="56c596"> Refuse invitation </font></a>
                    </td>
                  </tr>
                  <tr>
                      <td style="font-size:13px;padding:0px 10px 0px 10px;text-align:left">
                        <p>If you need additional assistance, or you did not make this change, please contact <a href="mailto:amira.dgham@intern.continuousnet.com" style="color:#56c596;;text-decoration:underline;font-weight:bold" target="_blank">amira.dgham@intern.continuousnet.com</a>.</p>
                        <p>Cheers,<br>The coagcare Team</p>
                    </td>
                </tr>
          </tbody></table>
        </td>
      </tr>
    </tbody></table>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse;margin:0 auto">   <tbody>     <tr>       <td bgcolor="#ffffff" style="line-height:150%;padding-top:10px;padding-left:10px;padding-right:18px;padding-bottom:30px;text-align:left;border-bottom:0;font-size:10px;border-top:0">         <table width="100%" cellpadding="0" cellspacing="0" border="0" align="left" style="border-collapse:collapse">           <tbody>             <tr>               <td valign="top" style="text-align:center;font-size:11px;color:#282828;padding:20px 0;padding-left:0px">This email was sent to <a style="color:#56c596;text-decoration:none;font-weight:600">' . $email . '</a>, which is associated with a CoagCare App account.  <br> <br>   © 2020 CoagCare App., All Rights Reserved                 <br> ContinuousNet., Residence ElAhmed 2nd Street Yesser Arafet, sahloul 4054                 <br>                 &nbsp;               </td>             </tr>           </tbody>         </table>       </td>     </tr>   </tbody> </table>

    <div style="display:none;white-space:nowrap;font:15px courier;color:#ffffff">
      - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    </div>
    </div>' .
    ' </body>' .

                    '</html>',
                    'text/html' 
                );
$mailer->send($message);
} catch (\Exception $ex) {

return View::create($ex->getMessage(), Response::HTTP_BAD_REQUEST, []);
}
$response=array(
    'message'=>'success',
    'result'=>'Email was send successfuly, check your email to reset your password'
);
return View::create($response, JsonResponse::HTTP_OK, []); 
        } 


    }
                     if (!empty($AssignementRefusedvalidation)){
                            $token= $AssignementRefusedvalidation->getInvitationToken();
                            if($token !=null){
                            $token= $AssignementRefusedvalidation->getInvitationToken();
                            $AssignementRefusedvalidation->setRequestDate(new \DateTime());
                            $AssignementRefusedvalidation->setUpdatedBy($user);
                            $AssignementRefusedvalidation->setUpdatedAt(new \DateTime());
                            $AssignementRefusedvalidation->setStatus('Pending');
                            $em = $this->getDoctrine()->getManager();
                            $em->flush();
                            try {
                                $transport = (new \Swift_SmtpTransport('mail.dreamhost.com', 587, 'tls'))
                                    ->setUsername('amira.dgham@intern.continuousnet.com')
                                    ->setPassword('?qS^3igZ')
                                    ->setStreamOptions(array('tls' => array('allow_self_signed' => false, 'verify_peer' => false)));
                                $mailer = new \Swift_Mailer($transport);
                        $message = (new \Swift_Message('CoagCare message'))
                        ->setFrom('amira.dgham@intern.continuousnet.com')
                        ->setTo($email)
                        ->setBody(
                            '<html>' .
                            '<head>
                            <style>
                        .button {
                        background-color: #56c596; /* Green */
                        border: none;
                        color: white;
                        padding: 15px 32px;
                        text-align: center;
                        text-decoration: none;
                        display: inline-block;
                        font-size: 16px;
                        margin: 4px 2px;
                        cursor: pointer;
                        width: 150px;
            
                        text-align: center;
                        color: white;
                        
                        }
                        .btn {
                            border: 2px solid #56c596;
                            padding: 15px 32px;
                            text-align: center;
                            text-decoration: none;
                            display: inline-block;
                            font-size: 16px;
                            margin: 4px 2px;
                            cursor: pointer;
                            width: 150px;
                        
                            text-align: center;
                        }
                        
                        
                        .button3 {background-color: #6ccda4;} /* Red */ 
                        #container{
                            text-align: center;
                        }
                        </style></head>'.
                        ' <body>' .
                        ' <div marginwidth="0" marginheight="0" style="width:100%;background-color:#ffffff;margin:0;padding:0;">
            
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="m_-3655858657801354087container" style="border-collapse:collapse;width:100%;min-width:100%;height:auto">
                          <tbody><tr>
                            <td width="100%" valign="top" bgcolor="#ffffff" style="padding-top:20px">
            
            
                              <table width="580" class="m_-3655858657801354087deviceWidth" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" style="border-collapse:collapse;margin:0 auto">
                                <tbody><tr>
                                  <td valign="top" align="center" style="padding:0" bgcolor="#ffffff">
                                      <img src="https://api.coagcare.continuousnet.com/Assets/images/4448b54b5015cf433e02a578933a9925.png" alt="" border="0" width="125" style="display:block">
                                  </td>
                                </tr>
                                <tr>
                                  <td style="font-size:13px;color:#282828;font-weight:normal;text-align:left;line-height:24px;vertical-align:top;padding:15px 8px 10px 8px" bgcolor="#ffffff">
                                  <h2 style="text-align:center;font-weight:600;margin:30px 0 50px 0">  you have been invited to join <br> doctor '.$username.'</h2>
                                <br><p> Dear Dr '. $name .',<br><br> We got a request invitation from CoagCare application from the patient '. $username .' 
                                 .Just click the button Accept below and
                                  you will be on your way. If you did not want to follow INR measurements of the patient '. $username .', please ignore this email by clicking on refuse button and thanks . </p>
                                  <p> If you need aditional information about the doctor, or you did not make this change, please contact <a href=`mailto:'. $emaill .'` style=`color:#56c596;text-decoration:unerline;font-weight:blod`>'. $emaill .'</a>. <br>
                                      </td>
                                      </tr>
                                      <tr>
                                          <td style="padding-bottom:30px">
                   <div class="container"> <center><a href=`https://api.coagcare.continuousnet.com/InvitationResponse?token='.$token.'&id='.$id.' class="button button3" style=`color:#fffff;` ><font color="FFFFF"> Accept invitation</font> </a> <a href=`https://api.coagcare.continuousnet.com/InvitationResponse?token='.$token.'&id='.$id.' class="btn" style=`color:#fffff;` > <font color="56c596"> Refuse invitation </font></a>
                                        </td>
                                      </tr>
                                      <tr>
                                          <td style="font-size:13px;padding:0px 10px 0px 10px;text-align:left">
                                            <p>If you need additional assistance, or you did not make this change, please contact <a href="mailto:amira.dgham@intern.continuousnet.com" style="color:#56c596;;text-decoration:underline;font-weight:bold" target="_blank">amira.dgham@intern.continuousnet.com</a>.</p>
                                            <p>Cheers,<br>The coagcare Team</p>
                                        </td>
                                    </tr>
                              </tbody></table>
                            </td>
                          </tr>
                        </tbody></table>
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse;margin:0 auto">   <tbody>     <tr>       <td bgcolor="#ffffff" style="line-height:150%;padding-top:10px;padding-left:10px;padding-right:18px;padding-bottom:30px;text-align:left;border-bottom:0;font-size:10px;border-top:0">         <table width="100%" cellpadding="0" cellspacing="0" border="0" align="left" style="border-collapse:collapse">           <tbody>             <tr>               <td valign="top" style="text-align:center;font-size:11px;color:#282828;padding:20px 0;padding-left:0px">This email was sent to <a style="color:#56c596;text-decoration:none;font-weight:600">' . $email . '</a>, which is associated with a CoagCare App account.  <br> <br>   © 2020 CoagCare App., All Rights Reserved                 <br> ContinuousNet., Residence ElAhmed 2nd Street Yesser Arafet, sahloul 4054                 <br>                 &nbsp;               </td>             </tr>           </tbody>         </table>       </td>     </tr>   </tbody> </table>
            
                        <div style="display:none;white-space:nowrap;font:15px courier;color:#ffffff">
                          - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
                        </div>
                        </div>' .
                        ' </body>' .
        
                                        '</html>',
                                        'text/html' 
                                    );
                    $mailer->send($message);
                } catch (\Exception $ex) {
        
                    return View::create($ex->getMessage(), Response::HTTP_BAD_REQUEST, []);
                }
                    $response=array(
                        'message'=>'success',
                        'result'=>'Email was send successfuly, check your email to reset your password'
                    );
                return View::create($response, JsonResponse::HTTP_OK, []); 
                            } 
                            else{
                                $AssignementRefusedvalidation->setInvitationToken(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
                                $AssignementRefusedvalidation->setRequestDate(new \DateTime());
                                $AssignementRefusedvalidation->setUpdatedBy($user);
                                $AssignementRefusedvalidation->setUpdatedAt(new \DateTime());
                                $em = $this->getDoctrine()->getManager();
                                $em->flush(); 
                                  $token= $AssignementRefusedvalidation->getInvitationToken();
                                }
                                try {
                                    $transport = (new \Swift_SmtpTransport('mail.dreamhost.com', 587, 'tls'))
                                        ->setUsername('amira.dgham@intern.continuousnet.com')
                                        ->setPassword('?qS^3igZ')
                                        ->setStreamOptions(array('tls' => array('allow_self_signed' => false, 'verify_peer' => false)));
                                    $mailer = new \Swift_Mailer($transport);
                            $message = (new \Swift_Message('CoagCare message'))
                            ->setFrom('amira.dgham@intern.continuousnet.com')
                            ->setTo($email)
                            ->setBody(
                                '<html>' .
                                '<head>
                                <style>
                            .button {
                            background-color: #56c596; /* Green */
                            border: none;
                            color: white;
                            padding: 15px 32px;
                            text-align: center;
                            text-decoration: none;
                            display: inline-block;
                            font-size: 16px;
                            margin: 4px 2px;
                            cursor: pointer;
                            width: 150px;
                
                            text-align: center;
                            color: white;
                            
                            }
                            .btn {
                                border: 2px solid #56c596;
                                padding: 15px 32px;
                                text-align: center;
                                text-decoration: none;
                                display: inline-block;
                                font-size: 16px;
                                margin: 4px 2px;
                                cursor: pointer;
                                width: 150px;
                            
                                text-align: center;
                            }
                            
                            
                            .button3 {background-color: #6ccda4;} /* Red */ 
                            #container{
                                text-align: center;
                            }
                            </style></head>'.
                            ' <body>' .
                            ' <div marginwidth="0" marginheight="0" style="width:100%;background-color:#ffffff;margin:0;padding:0;">
                
                            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="m_-3655858657801354087container" style="border-collapse:collapse;width:100%;min-width:100%;height:auto">
                              <tbody><tr>
                                <td width="100%" valign="top" bgcolor="#ffffff" style="padding-top:20px">
                
                
                                  <table width="580" class="m_-3655858657801354087deviceWidth" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" style="border-collapse:collapse;margin:0 auto">
                                    <tbody><tr>
                                      <td valign="top" align="center" style="padding:0" bgcolor="#ffffff">
                                          <img src="https://api.coagcare.continuousnet.com/Assets/images/4448b54b5015cf433e02a578933a9925.png" alt="" border="0" width="125" style="display:block">
                                      </td>
                                    </tr>
                                    <tr>
                                      <td style="font-size:13px;color:#282828;font-weight:normal;text-align:left;line-height:24px;vertical-align:top;padding:15px 8px 10px 8px" bgcolor="#ffffff">
                                      <h2 style="text-align:center;font-weight:600;margin:30px 0 50px 0">  you have been invited to join <br> doctor '.$username.'</h2>
                                    <br><p> Dear Dr '. $name .',<br><br> We got a request invitation from CoagCare application from the patient '. $username .' 
                                     .Just click the button Accept below and
                                      you will be on your way. If you did not want to follow INR measurements of the patient '. $username .', please ignore this email by clicking on refuse button and thanks . </p>
                                      <p> If you need aditional information about the doctor, or you did not make this change, please contact <a href=`mailto:'. $emaill .'` style=`color:#56c596;text-decoration:unerline;font-weight:blod`>'. $emaill .'</a>. <br>
                                          </td>
                                          </tr>
                                          <tr>
                                              <td style="padding-bottom:30px">
                       <div class="container"> <center><a href=`https://api.coagcare.continuousnet.com/InvitationResponse?token='.$token.'&id='.$id.' class="button button3" style=`color:#fffff;` ><font color="FFFFF"> Accept invitation</font> </a> <a href=`https://api.coagcare.continuousnet.com/InvitationResponse?token='.$token.'&id='.$id.' class="btn" style=`color:#fffff;` > <font color="56c596"> Refuse invitation </font></a>
                                            </td>
                                          </tr>
                                          <tr>
                                              <td style="font-size:13px;padding:0px 10px 0px 10px;text-align:left">
                                                <p>If you need additional assistance, or you did not make this change, please contact <a href="mailto:amira.dgham@intern.continuousnet.com" style="color:#56c596;;text-decoration:underline;font-weight:bold" target="_blank">amira.dgham@intern.continuousnet.com</a>.</p>
                                                <p>Cheers,<br>The coagcare Team</p>
                                            </td>
                                        </tr>
                                  </tbody></table>
                                </td>
                              </tr>
                            </tbody></table>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse;margin:0 auto">   <tbody>     <tr>       <td bgcolor="#ffffff" style="line-height:150%;padding-top:10px;padding-left:10px;padding-right:18px;padding-bottom:30px;text-align:left;border-bottom:0;font-size:10px;border-top:0">         <table width="100%" cellpadding="0" cellspacing="0" border="0" align="left" style="border-collapse:collapse">           <tbody>             <tr>               <td valign="top" style="text-align:center;font-size:11px;color:#282828;padding:20px 0;padding-left:0px">This email was sent to <a style="color:#56c596;text-decoration:none;font-weight:600">' . $email . '</a>, which is associated with a CoagCare App account.  <br> <br>   © 2020 CoagCare App., All Rights Reserved                 <br> ContinuousNet., Residence ElAhmed 2nd Street Yesser Arafet, sahloul 4054                 <br>                 &nbsp;               </td>             </tr>           </tbody>         </table>       </td>     </tr>   </tbody> </table>
                
                            <div style="display:none;white-space:nowrap;font:15px courier;color:#ffffff">
                              - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
                            </div>
                            </div>' .
                            ' </body>' .
            
                                            '</html>',
                                            'text/html' 
                                        );
                        $mailer->send($message);
                    } catch (\Exception $ex) {

                        return View::create($ex->getMessage(), Response::HTTP_BAD_REQUEST, []);
                    }
                        $response=array(
                            'message'=>'success',
                            'result'=>'Email was send successfuly, check your email to reset your password'
                        );
                    return View::create($response, JsonResponse::HTTP_OK, []); 
                         }
                        else{
                            $doctor_id= $doctorValidation->getId();
                            $patient_id= $user->getId();
                            $doctorAssignment=new DoctorAssignement();
                            $doctorAssignment->setIdPatient($user);
                            $doctorAssignment->setIdDoctor($doctorValidation);
                            $doctorAssignment->setRequestDate(new \DateTime());
                            $doctorAssignment->setStatus("Pending");
                            $doctorAssignment->setCreatedBy($user);
                            $doctorAssignment->setEnabled(true);
                            $doctorAssignment->setRemoved(false);
                            $doctorAssignment->setCreatedAt(new \DateTime());
                            $doctorAssignment->setInvitationToken(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
                            $entity ->persist($doctorAssignment);
                            $entity->flush();
                            $token=  $doctorAssignment->getInvitationToken();
                            try {
                                $transport = (new \Swift_SmtpTransport('mail.dreamhost.com', 587, 'tls'))
                                    ->setUsername('amira.dgham@intern.continuousnet.com')
                                    ->setPassword('?qS^3igZ')
                                    ->setStreamOptions(array('tls' => array('allow_self_signed' => false, 'verify_peer' => false)));
                                $mailer = new \Swift_Mailer($transport);
                            $message = (new \Swift_Message('CoagCare message'))
                ->setFrom('amira.dgham@intern.continuousnet.com')
                ->setTo($email)
                ->setBody(
                    '<html>' .
                    '<head>
                    <style>
                .button {
                background-color: #56c596; /* Green */
                border: none;
                color: white;
                padding: 15px 32px;
                text-align: center;
                text-decoration: none;
                display: inline-block;
                font-size: 16px;
                margin: 4px 2px;
                cursor: pointer;
                width: 150px;
                text-align: center;
                color: white;
                }
                .btn {
                    border: 2px solid #56c596;
                    padding: 15px 32px;
                    text-align: center;
                    text-decoration: none;
                    display: inline-block;
                    font-size: 16px;
                    margin: 4px 2px;
                    cursor: pointer;
                    width: 150px;
                    text-align: center;
                }
                .button3 {background-color: #6ccda4;} /* Red */ 
                #container{
                    text-align: center;
                }
                </style></head>'.
                ' <body>' .
                ' <div marginwidth="0" marginheight="0" style="width:100%;background-color:#ffffff;margin:0;padding:0;">
    
                <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="m_-3655858657801354087container" style="border-collapse:collapse;width:100%;min-width:100%;height:auto">
                  <tbody><tr>
                    <td width="100%" valign="top" bgcolor="#ffffff" style="padding-top:20px">
    
    
                      <table width="580" class="m_-3655858657801354087deviceWidth" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" style="border-collapse:collapse;margin:0 auto">
                        <tbody><tr>
                          <td valign="top" align="center" style="padding:0" bgcolor="#ffffff">
                              <img src="https://api.coagcare.continuousnet.com/Assets/images/4448b54b5015cf433e02a578933a9925.png" alt="" border="0" width="125" style="display:block">
                          </td>
                        </tr>
                        <tr>
                          <td style="font-size:13px;color:#282828;font-weight:normal;text-align:left;line-height:24px;vertical-align:top;padding:15px 8px 10px 8px" bgcolor="#ffffff">
                          <h2 style="text-align:center;font-weight:600;margin:30px 0 50px 0">  you have been invited to join <br> doctor '.$username.'</h2>
                        <br><p> Dear Dr '. $name .',<br><br> We got a request invitation from CoagCare application from the patient '. $username .' 
                         .Just click the button Accept below and
                          you will be on your way. If you did not want to follow INR measurements of the patient '. $username .', please ignore this email by clicking on refuse button and thanks . </p>
                          <p> If you need aditional information about the doctor, or you did not make this change, please contact <a href=`mailto:'. $emaill .'` style=`color:#56c596;text-decoration:unerline;font-weight:blod`>'. $emaill .'</a>. <br>
                              </td>
                              </tr>
                              <tr>
                                  <td style="padding-bottom:30px">
           <div class="container"> <center><a href=`https://api.coagcare.continuousnet.com/InvitationResponse?token='.$token.'&id='.$id.' class="button button3" style=`color:#fffff;` ><font color="FFFFF"> Accept invitation</font> </a> <a href=`https://api.coagcare.continuousnet.com/InvitationResponse?token='.$token.'&id='.$id.' class="btn" style=`color:#fffff;` > <font color="56c596"> Refuse invitation </font></a>
                                </td>
                              </tr>
                              <tr>
                                  <td style="font-size:13px;padding:0px 10px 0px 10px;text-align:left">
                                    <p>If you need additional assistance, or you did not make this change, please contact <a href="mailto:amira.dgham@intern.continuousnet.com" style="color:#56c596;;text-decoration:underline;font-weight:bold" target="_blank">amira.dgham@intern.continuousnet.com</a>.</p>
                                    <p>Cheers,<br>The coagcare Team</p>
                                </td>
                            </tr>
                      </tbody></table>
                    </td>
                  </tr>
                </tbody></table>
                <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse;margin:0 auto">   <tbody>     <tr>       <td bgcolor="#ffffff" style="line-height:150%;padding-top:10px;padding-left:10px;padding-right:18px;padding-bottom:30px;text-align:left;border-bottom:0;font-size:10px;border-top:0">         <table width="100%" cellpadding="0" cellspacing="0" border="0" align="left" style="border-collapse:collapse">           <tbody>             <tr>               <td valign="top" style="text-align:center;font-size:11px;color:#282828;padding:20px 0;padding-left:0px">This email was sent to <a style="color:#56c596;text-decoration:none;font-weight:600">' . $email . '</a>, which is associated with a CoagCare App account.  <br> <br>   © 2020 CoagCare App., All Rights Reserved                 <br> ContinuousNet., Residence ElAhmed 2nd Street Yesser Arafet, sahloul 4054                 <br>                 &nbsp;               </td>             </tr>           </tbody>         </table>       </td>     </tr>   </tbody> </table>
    
                <div style="display:none;white-space:nowrap;font:15px courier;color:#ffffff">
                  - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
                </div>
                </div>' .
                ' </body>' .

                                '</html>',
                                'text/html' 
                            );
            $mailer->send($message);
        } catch (\Exception $ex) {

            return View::create($ex->getMessage(), Response::HTTP_BAD_REQUEST, []);
        }
                            $response=array(
                                    'message'=>'success',
                                    'result'=>'Email was send successfuly, check your email to reset your password'
                                );
                            return View::create($response, JsonResponse::HTTP_OK, []);
                             
                        }
                }
        
                else{
                    $response=array(
                        'message'=>'failure',
                        'result'=>'this email is not doctor email! try another!'
                    );
                    return View::create($response, Response::HTTP_NOT_FOUND,[]);
                    }
              }
              else{
                $response=array(
                    'message'=>'failure',
                    'result'=>' missing doctor email!'
                );
                return View::create($response, Response::HTTP_BAD_REQUEST,[]);
                }
        }
        else{
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }    
     /**
     * @Rest\POST("/invitationResponse", name ="invitation_response")
     */
    public function invtationResponse(Request $request,SerializerInterface $serializer)
    {
         $token= $request->request->get('token');
         $id= $request->request->get('id');
         $response=$request->request->get('response');
         $tokentype= gettype($token);
         $idtype= gettype($id);
         $responsetype= gettype($response);
        if (isset($token)) {
            if (isset($id)){
                if (isset($response)){
                    if($tokentype == "string"){
                        if($idtype == "integer"){
                            if($responsetype == "string"){
                $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
                $invitationValidation = $repository->findOneBy(array('invitation_token'=>$token,'created_by'=>$id,'status'=>'Pending'));
                $invitationValidation2 = $repository->findOneBy(array('invitation_token'=>$token,'created_by'=>$id));
                if(empty($invitationValidation2)){
                    return View::create('data failed, check your request information' , Response::HTTP_FORBIDDEN,[]); 
                }

                $Userrepository = $this->getDoctrine()->getRepository(User::class);
                $user = $Userrepository->findOneBy(array('id'=>$id));
                if (!empty($invitationValidation)){
                if ($response=="Accepted"){
                    $invitationValidation->setStatus($response);
                    $invitationValidation->setupdatedBy($user);
                    $invitationValidation->setupdatedAt(new \DateTime());
                    $invitationValidation->setInvitationToken(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
                    $em = $this->getDoctrine()->getManager();
                    $em->flush();
                    return View::create('invitation accepted successfully :)', JsonResponse::HTTP_OK, []);  
                }
                if ($response=="Refused"){
                    $invitationValidation->setStatus($response);
                    $invitationValidation->setupdatedBy($user);
                    $invitationValidation->setupdatedAt(new \DateTime());
                    $invitationValidation->setInvitationToken(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
                    $em = $this->getDoctrine()->getManager();
                    $em->flush();
                    return View::create('invitation refused :(', JsonResponse::HTTP_OK, []);  
                }
                else{
                    return View::create('bad request response it should be accepted or refused ', Response::HTTP_BAD_REQUEST,[]);
                }
            }
            else{
                return View::create('session expired! :) ,you are already take an action' , Response::HTTP_FORBIDDEN,[]); 
            }
            }
               else{
                return View::create('bad request response should be a string', Response::HTTP_BAD_REQUEST,[]);
            }

               }

               else{
                return View::create('bad request id request should be an integer', Response::HTTP_BAD_REQUEST,[]);
            }
        }
           else{
            return View::create('bad  request token should be a string', Response::HTTP_BAD_REQUEST,[]);
        }
          
        }
        else{
            return View::create('missing response', Response::HTTP_BAD_REQUEST,[]);
        }
    }
    else{
        return View::create('missing id ', Response::HTTP_BAD_REQUEST,[]);
    }  
        }
        else{
            return View::create('missing token ', Response::HTTP_BAD_REQUEST,[]);
        }               
}
          /**
          * @Rest\Delete("/api/DeleteAssigned/{id}", name ="patient_removeassigned")
          * @Rest\View(serializerGroups={"doctors","hospitals"})
          */
          public function delete($id){
            $user = $this->getUser();
            if ($user->getUserType() === UserType::TYPE_DOCTOR) {
                if ($id == 132){
                    return View::create('Error !you cannot delete the default patient John Doe try another', JsonResponse::HTTP_FORBIDDEN, []);
                }
                else{
                $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
                $assignement = $repository->findOneBy(array('id_patient'=>$id,'id_doctor'=>$user->getId(),'status'=>'Accepted','removed'=>false));
                $assignementDeleted = $repository->findOneBy(array('id_patient'=>$id,'id_doctor'=>$user->getId(),'status'=>'Accepted','removed'=>true));
            
            
                if(!is_null($assignementDeleted)){
                    return View::create('sorry ! you are already remove this doctor from your list', JsonResponse::HTTP_FORBIDDEN, []);
                   }
                if (!is_null($assignement)) {      
                    $assignement->setRemovedBy($user);
                    $assignement->setRemoved(true);
                    $assignement->setRemovedAt(new \DateTime());
                    $em = $this->getDoctrine()->getManager();
                    $em->flush();
                    return View::create('you delete the assignement, you are not allowed to see the medical information about this patient', JsonResponse::HTTP_OK, []);
                }
                }
            }
                if ($user->getUserType() === UserType::TYPE_PATIENT) {
                    $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
                    $assignement = $repository->findOneBy(array('id_doctor'=>$id,'id_patient'=>$user->getId(),'status'=>'Accepted','removed'=>false));
                    $assignementDeleted = $repository->findOneBy(array('id_doctor'=>$id,'id_patient'=>$user->getId(),'status'=>'Accepted','removed'=>true));
                       if(!is_null($assignementDeleted)){
                        return View::create('sorry ! you are already remove this doctor from your list', JsonResponse::HTTP_FORBIDDEN, []);
                       }
                    if (!is_null($assignement)) {      
                        $assignement->setRemovedBy($user);
                        $assignement->setRemoved(true);
                        $assignement->setRemovedAt(new \DateTime());
                        $em = $this->getDoctrine()->getManager();
                        $em->flush();
                        return View::create('you delete the assignement, you are not allowed to see the medical information about this patient', JsonResponse::HTTP_OK, []);
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
     * @Rest\Post("/api/QrCodeValidation", name ="QR_Validation")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function QrCodeValidation(Request $request,EntityManagerInterface $entity)
    {
        $user= $this->getUser();
        $data = array(
            'id' => $user->getId()
        );
        $qr_code=$request->request->get('qr_code');

            if ($user->getUserType() === UserType::TYPE_DOCTOR) {
                if (isset($qr_code)){
                $repository = $this->getDoctrine()->getRepository(User::class);
                $qr_validation = $repository->findOneBy(array('QR_code'=>$qr_code,'userType'=>'patient'));
               if (empty($qr_validation)){
                return View::create('QrCode not valid' , JsonResponse::HTTP_BAD_REQUEST, []);
               }
               else{
                  $idpatient= $qr_validation->getId();
                  $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
                  $assignement = $repository->findOneBy(array('id_doctor'=>$user->getId(),'id_patient'=>$idpatient,'status'=>'Accepted','removed'=>false));
                  $assignementpending = $repository->findOneBy(array('id_doctor'=>$user->getId(),'id_patient'=>$idpatient,'status'=>'Pending','removed'=>false));
                  $assignementrefused = $repository->findOneBy(array('id_doctor'=>$user->getId(),'id_patient'=>$idpatient,'status'=>'Refused','removed'=>false));
                  if(!empty($assignement)){
                    return View::create('you are already the doctor of this patient, try another!' , JsonResponse::HTTP_BAD_REQUEST, []);
                  }  
                  if(!empty($assignementpending)){
                    $assignementpending->setStatus("Accepted");
                    $assignementpending->setUpdatedBy($user);
                    $assignementpending->setUpdatedAt(new \DateTime());
                    $em = $this->getDoctrine()->getManager();
                    $em->flush(); 
                    return View::create('congratulation, you are the doctor of this patient' , JsonResponse::HTTP_OK, []);
                  } 
                  if (!empty($assignementrefused)){
                    $assignementrefused->setStatus("Accepted");
                    $assignementrefused->setUpdatedBy($user->getId());
                    $assignementrefused->setUpdatedAt(new \DateTime());
                    $em = $this->getDoctrine()->getManager();
                    $em->flush(); 
                    return View::create('congratulation, you are the doctor of this patient' , JsonResponse::HTTP_OK, []);
                  }
                  else{
                    $doctorAssignment=new DoctorAssignement();
                    $doctorAssignment->setIdPatient($qr_validation);
                    $doctorAssignment->setIdDoctor($user);
                    $doctorAssignment->setRequestDate(new \DateTime());
                    $doctorAssignment->setStatus("Accepted");
                    $doctorAssignment->setCreatedBy($user);
                    $doctorAssignment->setEnabled(true);
                    $doctorAssignment->setRemoved(false);
                    $doctorAssignment->setCreatedAt(new \DateTime());
                    $entity ->persist($doctorAssignment);
                    $entity->flush();
                     return View::create('congratulation, you are the doctor of this patient' , JsonResponse::HTTP_OK, []);
                  }
                }
                }else{
                    return View::create('missing QR_code!' , JsonResponse::HTTP_BAD_REQUEST, []);  
                }

            }
            if ($user->getUserType() === UserType::TYPE_PATIENT) {
                if (isset($qr_code)){
                    $repository = $this->getDoctrine()->getRepository(User::class);
                    $qr_validation = $repository->findOneBy(array('QR_code'=>$qr_code,'userType'=>'doctor'));
                   if (empty($qr_validation)){
                    return View::create('QrCode not valid' , JsonResponse::HTTP_BAD_REQUEST, []);
                   }
                   else{
                      $iddoctor= $qr_validation->getId();
                      $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
                      $assignement = $repository->findOneBy(array('id_doctor'=>$iddoctor,'id_patient'=>$user->getId(),'status'=>'Accepted','removed'=>false));
                      $assignementpending = $repository->findOneBy(array('id_doctor'=>$iddoctor,'id_patient'=>$user->getId(),'status'=>'Pending','removed'=>false));
                      $assignementrefused = $repository->findOneBy(array('id_doctor'=>$iddoctor,'id_patient'=>$user->getId(),'status'=>'Refused','removed'=>false));
                      if(!empty($assignement)){
                        return View::create('you are already the patient of this doctor, try another!' , JsonResponse::HTTP_BAD_REQUEST, []);
                      }  
                      if(!empty($assignementpending)){
                        $assignementpending->setStatus("Accepted");
                        $assignementpending->setUpdatedBy($user);
                        $assignementpending->setUpdatedAt(new \DateTime());
                        $em = $this->getDoctrine()->getManager();
                        $em->flush(); 
                        return View::create('QrCode valid with success.Now, you are the doctor of this patient' , JsonResponse::HTTP_OK, []);
                      } 
                      if (!empty($assignementrefused)){
                        $assignementrefused->setStatus("Accepted");
                        $assignementrefused->setUpdatedBy($user);
                        $assignementrefused->setUpdatedAt(new \DateTime());
                        $em = $this->getDoctrine()->getManager();
                        $em->flush(); 
                        return View::create('congratulation, you are the doctor of this patient' , JsonResponse::HTTP_OK, []);
                      }
                      else{
                        $doctorAssignment=new DoctorAssignement();
                        $doctorAssignment->setIdPatient($user);
                        $doctorAssignment->setIdDoctor($qr_validation);
                        $doctorAssignment->setRequestDate(new \DateTime());
                        $doctorAssignment->setStatus("Accepted");
                        $doctorAssignment->setCreatedBy($user);
                        $doctorAssignment->setEnabled(true);
                        $doctorAssignment->setRemoved(false);
                        $doctorAssignment->setCreatedAt(new \DateTime());
                        $entity ->persist($doctorAssignment);
                        $entity->flush();
                         return View::create('congratulation, you are the doctor of this patient' , JsonResponse::HTTP_OK, []);
                      }
                    }
                    }else{
                        return View::create('missing QR_code!' , JsonResponse::HTTP_BAD_REQUEST, []);  
                    }
                }
    
    }

   
}