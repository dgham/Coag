<?php

namespace App\Controller;

use App\Entity\User;
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
     * @Route("/Confirm/resetPassword/{token}", name="reset_pages")
     */

    public function resetpassword(Request $request,string $token,UserPasswordEncoderInterface $encoder, SerializerInterface $serializer)
    {
        $session = new Session(new PhpBridgeSessionStorage());
        $session->start();
            return $this->render('web_pages/resetPassword.html.twig',['token'=>$token]);
    }
/**
     * @Route("/Confirm/response", name="resett_pages")
     */

    public function resetpasswordsuccess(Request $request,UserPasswordEncoderInterface $encoder, SerializerInterface $serializer)
    {
       $token= $request->request->get('token');
       $password=  $request->request->get('password');
       dump($token);
       dump($password);
       die;
      
            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->findOneBy(array('confirmationToken' => $token));
            dump($user);
            die;
        if ($user === null) {
               
                return $this->render('web_pages/resetPassword.html.twig',['token'=>$token]);
                $this->addFlash('danger', 'sorry! your session expired ');
            }
                $hash = $encoder->encodePassword($user,$password);
                $user->setPassword($hash);
                $user->setConfirmationToken(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                
                return $this->render('web_pages/resetPassword.html.twig',['token'=>$token]);
                $this->addFlash('success', 'your password updated!');
      
    }
}




    
    
   
