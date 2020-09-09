<?php

namespace App\Controller;

use App\Entity\DoctorAssignement;
use App\Entity\User;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;

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

    public function resetpassword(Request $request, UserPasswordEncoderInterface $encoder, SerializerInterface $serializer)
    {
        $session = new Session(new PhpBridgeSessionStorage());
        $session->start();
        if ($request->getMethod() === 'POST') {

            $token = $_POST['token'];
            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->findOneBy(array('confirmationToken' => $token));
            if (!is_null($user)) {
                $password = $_POST['password'];
                $confirmpassword = $_POST['confirm_Password'];
                $hash = $encoder->encodePassword($user, $password);
                $user->setPassword($hash);
                $user->setUpdatedAt(new \DateTime());
                $user->setConfirmationToken(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                $token = $_POST['token'];
                $this->addFlash('success', 'your password updated , Sign In again !');
                return $this->render('web_pages/resetPassword.html.twig', [
                    'token' => $token,
                ]);
            } else {
                $token = $_POST['token'];
                $this->addFlash('danger', 'Unable to request password! your session expired ');
                return $this->render('web_pages/resetPassword.html.twig', [
                    'token' => $token,
                ]);

            }

        } else {
            $token = $request->query->get('token');
            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->findOneBy(array('confirmationToken' => $token));
            if (is_null($user)) {
                $token = $request->query->get('token');
                $this->addFlash('danger', 'Unable to request password! your session expired ');
                return $this->render('web_pages/resetPassword.html.twig', [
                    'token' => $token,
                ]);
            } else {
                $token = $request->query->get('token');
                return $this->render('web_pages/resetPassword.html.twig', [
                    'token' => $token,
                ]);
            }
        }
    }

    /**
     * @Route("/Confirm/response", name="resett_pages")
     */

    public function resetpasswordd(Request $request, UserPasswordEncoderInterface $encoder, SerializerInterface $serializer)
    {
        $token = $_POST['token'];
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findOneBy(array('confirmationToken' => $token));
        if (!is_null($user)) {
            $password = $_POST['password'];
            $confirmpassword = $_POST['confirm_Password'];
            $hash = $encoder->encodePassword($user, $password);
            $user->setPassword($hash);
            $user->setUpdatedAt(new \DateTime());
            $user->setConfirmationToken(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $token = $_POST['token'];
            $this->addFlash('success', 'your password updated!');
            return $this->render('web_pages/resetPassword.html.twig', [
                'token' => $token,
            ]);
        } else {
            $token = $_POST['token'];
            return $this->render('web_pages/resetError.html.twig');
        }
    }
    /**
     * @Route("/InvitationResponse", name="invitation_page")
     */

    public function invitationResponse(Request $request, UserPasswordEncoderInterface $encoder, SerializerInterface $serializer)
    {
        $token = $request->query->get('token');
        $id = $request->query->get('id');
        $response = $request->query->get('response');
        $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
        $invitationValidation = $repository->findOneBy(array('invitation_token' => $token, 'created_by' => $id, 'status' => "Pending", 'removed' => false));  
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findOneBy(array('id' => $id));
        $name= $user->getUsername();
        if (isset($response)) {
            if (!is_null($invitationValidation)) {
                if ($response === "Accept invitation") {
                    $idpatient= $invitationValidation->getIdPatient()->getId();
                    $idDoctor= $invitationValidation->getIdDoctor()->getId();
                    if ( $idpatient !== $id){
                    $repository = $this->getDoctrine()->getRepository(User::class);
                    $patient = $repository->findOneBy(array('id' => $idpatient));
                    $patientName= $patient->getUsername();
                    $repository = $this->getDoctrine()->getRepository(User::class);
                    $userr = $repository->findOneBy(array('id' => $id));
                    $name= $userr->getUsername();
                    $email= $userr->getEmail();
                    $invitationValidation->setStatus("Accepted");
                    $invitationValidation->setInvitationToken(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
                    $invitationValidation->setUpdatedBy($user);
                    $invitationValidation->setUpdatedAt(new \DateTime());
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($invitationValidation);
                    $em->flush();
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
                            </style></head>' .
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
                                            <h1 style="text-align:center;font-weight:600;margin:30px 0 50px 0">YOUR COAGCARE PATIENT WAS UPDATED</h1>
                                            <p>Dear ' . $name . ',</p>
                                              <p>The patient '. $patientName.' has accepted your invitation to join your Coagcare patient member. </p>
                                              </td>
                                              </tr>
                                              <tr>
                                                 
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
                    $this->addFlash('success', 'Welcome to CoagCare app ! Now you can join your coagcare health community');
                    return $this->render('web_pages/invitationResponse.html.twig', [
                        'token' => $token,
                        'id' => $id,
                    ]);
                    }

                    $idpatient= $invitationValidation->getIdPatient()->getId();
                    $idDoctor= $invitationValidation->getIdDoctor()->getId();
                    if ( $idDoctor !== $id){
                    $repository = $this->getDoctrine()->getRepository(User::class);
                    $patient = $repository->findOneBy(array('id' => $idDoctor));
                    $doctorName= $patient->getUsername();
                    $repository = $this->getDoctrine()->getRepository(User::class);
                    $userr = $repository->findOneBy(array('id' => $id));
                    $name= $userr->getUsername();
                    $email= $userr->getEmail();
                    $invitationValidation->setStatus("Accepted");
                    $invitationValidation->setInvitationToken(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
                    $invitationValidation->setUpdatedBy($user);
                    $invitationValidation->setUpdatedAt(new \DateTime());
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($invitationValidation);
                    $em->flush();
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
                            background-color: #56c596;
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
                            </style></head>' .
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
                                            <h1 style="text-align:center;font-weight:600;margin:30px 0 50px 0">YOUR COAGCARE Doctor WAS UPDATED</h1>
                                            <p>Dear ' . $name . ',</p>
                                              <p>The doctor '. $doctorName.' has accepted your invitation to join your Coagcare doctor member. </p>
                                              </td>
                                              </tr>
                                              <tr>
                                                 
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
                    $this->addFlash('success', 'Welcome to CoagCare app ! Now you can join your coagcare health community');
                    return $this->render('web_pages/invitationResponse.html.twig', [
                        'token' => $token,
                        'id' => $id,
                    ]);
                    }

                }
                if ($response === "Refuse invitation") {
                    
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
                        'token' => $token,
                        'id' => $id,
                    ]);
                }

            } else {
                $this->addFlash('danger', 'You are already take an action to this request');
                return $this->render('web_pages/invitationResponse.html.twig', [
                    'token' => $token,
                    'id' => $id,
                ]);
            }
        } else {
            return $this->render('web_pages/invitationResponse.html.twig', [
                'token' => $token,
                'id' => $id,
            ]);
        }

    }
}
