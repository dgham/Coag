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
        if ($request->isMethod('POST')) {
            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->findOneBy(array('confirmationToken' => $tokenn));
        if ($user === null) {
                $this->addFlash('danger', 'sorry! your session expired ');
                return $this->redirectToRoute('reset_pages');
            }
                $hash = $encoder->encodePassword($user, $request->request->get('password'));
                $user->setPassword($hash);
                $user->setConfirmationToken(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                $this->addFlash('notice', 'your password updated!');
                return $this->redirectToRoute('reset_pages');
        }else {
 
            return $this->render('security/resetPassword.html.twig', ['token' => $token]);
        }
   
}
}




    
    
   
