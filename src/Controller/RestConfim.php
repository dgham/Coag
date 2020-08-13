<?php
namespace App\Controller;

use App\Entity\User;
use App\Entity\Patient;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\Form;
use App\Entity\DoctorAssignement;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\Mime\Email;
use FOS\UserBundle\Event\FormEvent;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use FOS\UserBundle\Event\GetResponseUserEvent;
use Symfony\Component\HttpFoundation\Response;
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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RestConfim extends FOSRestController
{
     /**
     * Reset user password
     *
     * @Rest\Post("/ResetPassword", name ="Reset_password")
     */
    public function ResetPassword(Request $request,\Swift_Mailer $mailer)
    {
        $email= $request->request->get('email');
        if (isset($email)) {
            $repository = $this->getDoctrine()->getRepository(User::class);
            $emailValidation = $repository->findOneBy(array('email'=>$email));
           $token= $emailValidation->getConfirmationToken();
           if ($token != null){
           $token= $emailValidation->getConfirmationToken();
           }
           else{
           $emailValidation->setConfirmationToken(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
            $em = $this->getDoctrine()->getManager();
             $em->flush(); 
             $token=$emailValidation->getConfirmationToken();
           }
        
           $name= $emailValidation->getUsername();
         
            if (!is_null($emailValidation)) {
            $message = (new \Swift_Message('CoagCare message'))
            ->setFrom('amiradgham15@gmail.com')
            ->setTo($email)
            ->setBody(
                '<html>' .
                ' <body>' .
               '<center><img src=`https://www.groupe-grim.com/bmw/wp-content/uploads/2019/10/groupe-personnel-medical-portant-icones-liees-sante_53876-43071.jpg`></center>.<p> Dear '. $name .',<br><br> We got a request to reset 
               you CoagCare password .Just click the link below and you
               you will be on your way <a href=`http://localhost:4200/reset-password?token='.$token.'`'.' style="display:block" height="42" width="150"> Reset password </a> . If you did not make this request, please ignore this email and thanks . </p>
               <br> <p> If you need aditional assistance, or you did not make this change, please contact <a href=`mailto:CoagCareApp@gmail.com` style=`color:#ff6c37;text-decoration:unerline;font-weight:blod`>CoagCareApp@gmail.com</a>.<p> cheers, <br> the CoagCare App Team </p>
               <p style="text-align:center;font-size:11px;color:#282828;padding:20px 0;padding-left:0px">
               © 2020 CoagCare . All Rights Reserved. Continuous Net </p>'.
                ' </body>' .
                '</html>',
                  'text/html' 
            );
        $mailer->send($message);
            $response=array(
                'message'=>'success',
                'result'=>'Email was send successfuly, check your email to reset your password'
            );
        return View::create($response, JsonResponse::HTTP_OK, []);
            }
            else{
                $response=array(
                    'message'=>'failure',
                    'result'=>'this email not exist try again!'
                );
                return View::create($response, Response::HTTP_NOT_FOUND,[]);

                }
        
          }
          else{
           
            return View::create("missing email", Response::HTTP_BAD_REQUEST,[]);

            }
    
        }
      /**
     * Reset user password
     *
     * @Rest\Patch("/RestConfirmation", name ="Resetconfirm_password")
     */
    public function resetConfirmation(Request $request,UserPasswordEncoderInterface $encoder,SerializerInterface $serializer)
    {
         $token= $request->request->get('token');
    
        if (isset($token)) {
            $password= $request->request->get('password');
            $confirmpassword= $request->request->get('confirmPassword');
            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->findOneBy(array('confirmationToken'=>$token));
            
            if(!is_null($user)){
        if (isset($password)) {
            if(isset($confirmpassword)){
            $hash = $encoder->encodePassword($user,$password);
            $user->setPassword($hash);
            $user->setConfirmationToken(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $response=array(
                'message'=>'success',
                'result'=>'Reset Password successfully'
            );
        return View::create($response, JsonResponse::HTTP_OK, []);
            }
            else{
                $response=array(
                    'message'=>'failure',
                    'result'=>'missing confirmpassword'
                );
                return View::create($response, Response::HTTP_BAD_REQUEST,[]);

                }
            }
            else{
                $response=array(
                    'message'=>'failure',
                    'result'=>'missing password'
                );
                return View::create($response, Response::HTTP_BAD_REQUEST,[]);

                }
            } else{
              
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);

                }
    }
    else{
        $response=array(
            'message'=>'failure',
            'result'=>'missing token'
        );
        return View::create($response, Response::HTTP_BAD_REQUEST,[]);

        }
}

/**
     * send patient invitation
     *
     * @Rest\Post("/sendInvitation", name ="send_invitaion")
     */
    public function sendInvitation(Request $request,\Swift_Mailer $mailer,EntityManagerInterface $entity)
    {
        $email= $request->request->get('email');
        $doctor= $request->request->get('username');
        if (isset($email)) {
            $repository = $this->getDoctrine()->getRepository(User::class);
            $patientValidation = $repository->findOneBy(array('email'=>$email,'userType'=>'patient'));
            if (!is_null($patientValidation)) {
           $token= $patientValidation->getConfirmationToken();
           if ($token != null){
           $token= $patientValidation->getConfirmationToken();
           }
           else{
           $patientValidation->setConfirmationToken(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
            $em = $this->getDoctrine()->getManager();
             $em->flush(); 
             $token=$patientValidation->getConfirmationToken();
           }
           if (isset($doctor)) {
            $repository = $this->getDoctrine()->getRepository(User::class);
            $doctorvalidation = $repository->findOneBy(array('username'=>$doctor));
            $username= $doctorvalidation->getUsername();
            $emaill=$doctorvalidation->getEmail();
            $id=$doctorvalidation->getId();
           $name= $patientValidation->getUsername();
         
            if (!is_null($patientValidation)) {
            $message = (new \Swift_Message('CoagCare message'))
            ->setFrom('coagcare@gmail.com')
            ->setTo($email)
            ->setBody(
                '<html>' .
                '<head>
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
                <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
                </head>
                <body>
               <center><img src="https://dewey.tailorbrands.com/production/brand_version_mockup_image/268/3222788268_9c1f3fd7-2d2f-4ed7-abd5-0381dd4740c0.png?cb=1594582078"></center>.<p> Dear '. $name .',<br><br> <center><h2 style="color:#282828" > you have been invited to be assigned <br> by doctor '.$username.'</h2></center> <br>We got a request invitation from CoagCare Doctor '. $username .' 
               that wants to assigned you .Just click the link below and you
               you will be on your way <center> <button   type="button" class="btn btn-info" onclick= "location.href=`http://localhost:4200/Accept-Invitation?token='.$token.'&id='.$id.' \"  style="display:block" > Accept invitation </button><button  type="button" class="btn btn-info" onclick= \"location.href=`http://localhost:4200/Accept-Invitation?token='.$token.'&id='.$id.' \"  style="display:block" > refuse invitation </button>
               <br> <p> If you need aditional information about the doctor, or you did not make this change, please contact <a href=`mailto:'. $emaill .'` style=`color:#ff6c37;text-decoration:unerline;font-weight:blod`>'. $emaill .'</a>.<p> cheers, <br> the CoagCare App Team </p>
               <p style="text-align:center;font-size:11px;color:#282828;padding:20px 0;padding-left:0px">
               © 2020 CoagCare . All Rights Reserved. Continuous Net </p>'.
                ' </body>' .
                '</html>',
                  'text/html' 
            );
  
        $mailer->send($message);
        $patientid= $patientValidation->getId();
        
        //$doctor_id= $doctorvalidation->getId();
        //$doctorassignment=new DoctorAssignement();
        //$doctorassignment->setIdPatient($patientValidation);
        //$doctorassignment->setIdDoctor($doctorvalidation);
        //$doctorassignment->setRequestDate(new \DateTime());
        //$doctorassignment->setStatus("pending");
        //$doctorassignment->setEnabled(false);
        //$doctorassignment->setRemoved(false);
        //$entity ->persist($doctorassignment);
        //$entity->flush();
        $response=array(
                'message'=>'success',
                'result'=>'Email was send successfuly, check your email to reset your password'
            );
        return View::create($response, JsonResponse::HTTP_OK, []);
            }
            else{
                $response=array(
                    'message'=>'failure',
                    'result'=>'this email not exist try again!'
                );
                return View::create($response, Response::HTTP_NOT_FOUND,[]);
                }
          }
          else{
            $response=array(
                'message'=>'failure',
                'result'=>'doctor username missing!'
            );
            return View::create($response, Response::HTTP_BAD_REQUEST,[]);

            }
        }
        else{
            $response=array(
                'message'=>'failure',
                'result'=>'this email is not patient email! try another'
            );
            return View::create($response, Response::HTTP_BAD_REQUEST,[]);

            }
    
        }
        else{
            $response=array(
                'message'=>'failure',
                'result'=>'email patient missing!'
            );
            return View::create($response, Response::HTTP_BAD_REQUEST,[]);

            }

        }


     /**
     *
     * @Rest\POST("/Acceptation", name ="invitation_accept")
     */
    public function PatientAcceptation(Request $request,UserPasswordEncoderInterface $encoder,SerializerInterface $serializer)
    {
         $token= $request->request->get('token');
    
        if (isset($token)) {
            $id= $request->request->get('id');
            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->findOneBy(array('confirmationToken'=>$token));
            if(!is_null($user)){
            $userid= $user->getId();
            if(!is_null($user)){
        if (isset($id)) {
            $repository = $this->getDoctrine()->getRepository(Patient::class);
            $patient = $repository->findOneBy(array('created_by'=>$userid));
            if(!is_null($patient)){
                $repository = $this->getDoctrine()->getRepository(User::class);
                $doctor = $repository->findOneBy(array('id'=>$id));
                if(!is_null($doctor)){
                $patient->setAssignedBy($doctor);
                $patient->setUpdatedBy($doctor);
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                $user->setConfirmationToken(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                $response=array(
                    'message'=>'success',
                    'result'=>'patient accept request invitation'
                );
            return View::create($response, JsonResponse::HTTP_OK, []);
                }
            else{
                $response=array(
                    'message'=>'failure',
                    'result'=>'doctor not found'
                );
                return View::create($response, Response::HTTP_NOT_FOUND,[]);

                }
            }
            else{
                $response=array(
                    'message'=>'failure',
                    'result'=>'patient not found password'
                );
                return View::create($response, Response::HTTP_NOT_FOUND,[]);

                }

            }
            else{
                $response=array(
                    'message'=>'failure',
                    'result'=>'missing id doctor'
                );
                return View::create($response, Response::HTTP_BAD_REQUEST,[]);

                }
            }
            else{
                $response=array(
                    'message'=>'failure',
                    'result'=>'user not found'
                );
                return View::create($response, Response::HTTP_NOT_FOUND,[]);

                }
            }
            else{
                $response=array(
                    'message'=>'failure',
                    'result'=>'token not found'
                );
                return View::create($response, Response::HTTP_NOT_FOUND,[]);

                }

            }
            else{
                $response=array(
                    'message'=>'failure',
                    'result'=>'token missing'
                );
                return View::create($response, Response::HTTP_BAD_REQUEST,[]);

                }
        
    
}


}
        
    