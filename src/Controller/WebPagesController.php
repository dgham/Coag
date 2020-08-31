<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\DoctorAssignement;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;

class WebPagesController extends AbstractController
{
    /**
     * @Route("/", name="web_pages")
     */
    public function index()
    {
        return $this->render('web_pages/index.html.twig', [
            'controller_name' => 'WebPagesController',
        ]);
    }


   /**
     * @Route("/Confirm/resetPassword", name="reset_pages")
     */

    public function resetpassword(Request $request,UserPasswordEncoderInterface $encoder, SerializerInterface $serializer)
    {
        $session = new Session(new PhpBridgeSessionStorage());
        $session->start();
        if ($request->getMethod()==='POST'){

            $token= $_POST['token'];
            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->findOneBy(array('confirmationToken' => $token));  
            if (!is_null($user)) {
            $password = $_POST['password'];
            $confirmpassword = $_POST['confirm_Password'];
            $hash = $encoder->encodePassword($user,$password);
            $user->setPassword($hash);
            $user->setUpdatedAt(new \DateTime());
            $user->setConfirmationToken(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $token= $_POST['token'];
            $this->addFlash('success', 'your password updated , Sign In again !'); 
            return $this->render('web_pages/resetPassword.html.twig', [
                'token' =>  $token,
            ]);
            }
             else{
                $token= $_POST['token'];
                $this->addFlash('danger', 'Unable to request password! your session expired ');
                return $this->render('web_pages/resetPassword.html.twig', [
                    'token' =>  $token,
                ]);
               
               
            }

        }
        else{
        $token= $request->query->get('token');
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findOneBy(array('confirmationToken' => $token));  
        if (is_null($user)) {
            $token= $request->query->get('token');
            $this->addFlash('danger', 'Unable to request password! your session expired ');
            return $this->render('web_pages/resetPassword.html.twig', [
                'token' =>  $token,
            ]);
            }else{
                   $token= $request->query->get('token');
                return $this->render('web_pages/resetPassword.html.twig', [
                    'token' =>  $token,
                ]);
                }
            }
        }
 
    /**
     * @Route("/Confirm/response", name="resett_pages")
     */

    public function resetpasswordd(Request $request,UserPasswordEncoderInterface $encoder, SerializerInterface $serializer)
    {
            $token= $_POST['token'];
            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->findOneBy(array('confirmationToken' => $token));  
            if (!is_null($user)) {
            $password = $_POST['password'];
            $confirmpassword = $_POST['confirm_Password'];
            $hash = $encoder->encodePassword($user,$password);
            $user->setPassword($hash);
            $user->setUpdatedAt(new \DateTime());
            $user->setConfirmationToken(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $token= $_POST['token'];
            $this->addFlash('success', 'your password updated!'); 
            return $this->render('web_pages/resetPassword.html.twig', [
                'token' =>  $token,
            ]);
            }
             else{
                $token= $_POST['token'];
                return $this->render('web_pages/resetError.html.twig');  
            }  
    }
         /**
     * @Route("/InvitationResponse", name="invitation_page")
     */

    public function resetpassworddError(Request $request,UserPasswordEncoderInterface $encoder, SerializerInterface $serializer)
    {
        $token=$request->query->get('token');
        $id=$request->query->get('id');
        $response=$request->query->get('response');
        $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
        $invitationValidation = $repository->findOneBy(array('invitation_token' => $token,'created_by'=> $id, 'status'=> "Pending",'removed'=>false));  
        if(isset($response)){
        if (!is_null($invitationValidation)) {
           if ($response === "Accept invitation"){
            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->findOneBy(array('id' => $id));  
            $invitationValidation->setStatus("Accepted");
            $invitationValidation->setInvitationToken(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
            $invitationValidation->setUpdatedBy($user);
            $invitationValidation->setUpdatedAt(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($invitationValidation);
            $em->flush();
            $this->addFlash('success', 'Welcome to CoagCare app ! Now you can join your coagcare health community'); 
            return $this->render('web_pages/invitationResponse.html.twig', [
                'token' =>  $token,
                'id' => $id
            ]);
           }
           if ($response === "Refuse invitation"){
            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->findOneBy(array('id' => $id));  
            $invitationValidation->setStatus("Refused");
            $invitationValidation->setInvitationToken(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
            $invitationValidation->setUpdatedBy($user);
            $invitationValidation->setUpdatedAt(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($invitationValidation);
            $em->flush();
            $this->addFlash('success', 'Unfortunately ! you are descline one of coagcare health community. Maybe next time '); 
            return $this->render('web_pages/invitationResponse.html.twig', [
                'token' =>  $token,
                'id' => $id
            ]);
           }


        }
        else{
            $this->addFlash('danger', 'You are already take an action to this request'); 
            return $this->render('web_pages/invitationResponse.html.twig', [
                'token' =>  $token,
                'id' => $id
            ]); 
        }
        }
        else{
            return $this->render('web_pages/invitationResponse.html.twig', [
                'token' =>  $token,
                'id' => $id
            ]);  
        }
       
    }
}
 




    
   
