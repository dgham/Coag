<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function resetpassword()
    {
        
// legacy application configures session

session_start();

// Get Symfony to interface with this existing session
$session = new Session(new PhpBridgeSessionStorage());
$session->start();

        return $this -> render('web_pages/resetPassword.html.twig');
     
    
    }
    /**
     * @Rest\POST("/ResetResponse", name ="Confirmation_resetting")
     */
    public function ConfirmReset()
    {

        $password = $request->query->get("password");
        $confirm_password = $request->query->get("confirm_Password");
        $token = $this->container->getParameter('token');
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findOneBy(array('confirmationToken' => $token));

        if (!is_null($user)) {
            $hash = $encoder->encodePassword($user, $password);
            $user->setPassword($hash);
            $user->setConfirmationToken(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success',' Your password has been reset!');
             return $this->redirectToRoute('reset_pages');
        } else {

           $this->addFlash('error',' sorry!, your session expired');
           return $this->redirectToRoute('reset_pages');
        }

    }
}
