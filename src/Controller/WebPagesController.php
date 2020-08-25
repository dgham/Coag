<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
        return $this->render('web_pages/resetPassword.html.twig', [
            'controller_name' => 'WebPagesController',
        ]);
    }
    /**
     * @Route("/ConfirmReset, name="Confirmation_resetting")
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
