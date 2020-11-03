<?php

namespace App\Controller;

use App\Entity\Patient;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;

class RestConfim extends FOSRestController
{

    /**
     * Reset user password
     *
     * @Rest\Post("/authResetPassword", name ="Resettt_password")
     */
    public function resetPasswordRequestAction(Request $request, \Swift_Mailer $mailer, UrlGeneratorInterface $router)
    {
        $email = $request->request->get('email');
        if (isset($email)) {
            $user = $this->get("fos_user.user_manager")->findUserByEmail($email);
            if (null === $user) {
                return View::create("user email not valid", Response::HTTP_BAD_REQUEST, []);
            }
            if ($user->isPasswordRequestNonExpired($this->container->getParameter("fos_user.resetting.token_ttl"))) {
                return View::create("Password already requested", Response::HTTP_BAD_REQUEST, []);
            }
            if (null === $user->getConfirmationToken()) {
                /** @var $tokenGenerator FOSUserBundleUtilTokenGeneratorInterface */
                $tokenGenerator = $this->get("fos_user.util.token_generator");
                $user->setConfirmationToken($tokenGenerator->generateToken());
            }
            $user->setPasswordRequestedAt(new DateTime());
            $this->get("fos_user.user_manager")->updateUser($user);
            $token = $user->getConfirmationToken();
            $name = $user->getUsername();
            try {
                $transport = (new \Swift_SmtpTransport('mail.continuousnet.com', 587, 'tls'))
                ->setUsername('amira.dgham@continuousnet.com')
                ->setPassword('aSspjguK')
                ->setStreamOptions(array('ssl' => array('allow_self_signed' => true, 'verify_peer' => false, 'verify_peer_name' => false)));
                $mailer = new \Swift_Mailer($transport);
                $message = (new \Swift_Message('CoagCare message'))
                    ->setFrom('amira.dgham@continuousnet.com')
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
                                    <h1 style="text-align:center;font-weight:600;margin:30px 0 50px 0">PASSWORD RESET REQUEST</h1>
                                    <p>Dear ' . $name . ',</p>
                                      <p>We have received your request to reset your password. Please click the link below to complete the reset:</p>
                                      </td>
                                      </tr>
                                      <tr>
                                          <td style="padding-bottom:30px">
                   <div class="container"> <center><a href=`https://api.coagcare.continuousnet.com/confirm/resetPassword?token=' . $token . ' "  class="button button3" style=`color:#fffff;` ><font color="FFFFF"> Reset Password</font> </a></center></div>
                                        </td>
                                      </tr>
                                      <tr>
                                          <td style="font-size:13px;padding:0px 10px 0px 10px;text-align:left">
                                            <p>If you need additional assistance, or you did not make this change, please contact <a href="mailto:amira.dgham@continuousnet.com" style="color:#56c596;;text-decoration:underline;font-weight:bold" target="_blank">amira.dgham@continuousnet.com</a>.</p>
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

            return View::create("email send", Response::HTTP_OK, []);
        } else {
            return View::create("missing email !", Response::HTTP_BAD_REQUEST, []);
        }
    }

    /**
     * Reset user password
     *
     * @Rest\Patch("/authResetConfirmation", name ="Resetconfirm_password")
     */
    public function resetConfirmation(Request $request, UserPasswordEncoderInterface $encoder, SerializerInterface $serializer)
    {
        $token = $request->request->get('token');

        if (isset($token)) {
            $password = $request->request->get('password');
            $confirmpassword = $request->request->get('confirm_Password');
            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->findOneBy(array('confirmationToken' => $token));

            if (!is_null($user)) {
                if (isset($password)) {
                    if (isset($confirmpassword)) {
                        $hash = $encoder->encodePassword($user, $password);
                        $user->setPassword($hash);
                        $user->setConfirmationToken(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
                        $em = $this->getDoctrine()->getManager();
                        $em->flush();
                        $response = array(
                            'message' => 'success',
                            'result' => 'Reset Password successfully',
                        );
                        return View::create($response, JsonResponse::HTTP_OK, []);
                    } else {
                        $response = array(
                            'message' => 'failure',
                            'result' => 'missing confirm_Password',
                        );
                        return View::create($response, Response::HTTP_BAD_REQUEST, []);
                    }
                } else {
                    $response = array(
                        'message' => 'failure',
                        'result' => 'missing password',
                    );
                    return View::create($response, Response::HTTP_BAD_REQUEST, []);
                }
            } else {

                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
            }
        } else {
            $response = array(
                'message' => 'failure',
                'result' => 'missing token',
            );
            return View::create($response, Response::HTTP_BAD_REQUEST, []);
        }
    }

    /**
     * send patient invitation
     *
     * @Rest\Post("/sendInvitation", name ="send_invitaion")
     */
    public function sendInvitation(Request $request, \Swift_Mailer $mailer, EntityManagerInterface $entity)
    {
        $email = $request->request->get('email');
        $doctor = $request->request->get('username');
        if (isset($email)) {
            $repository = $this->getDoctrine()->getRepository(User::class);
            $patientValidation = $repository->findOneBy(array('email' => $email, 'userType' => 'patient'));
            if (!is_null($patientValidation)) {
                $token = $patientValidation->getConfirmationToken();
                if ($token != null) {
                    $token = $patientValidation->getConfirmationToken();
                } else {
                    $patientValidation->setConfirmationToken(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
                    $em = $this->getDoctrine()->getManager();
                    $em->flush();
                    $token = $patientValidation->getConfirmationToken();
                }
                if (isset($doctor)) {
                    $repository = $this->getDoctrine()->getRepository(User::class);
                    $doctorvalidation = $repository->findOneBy(array('username' => $doctor));
                    $username = $doctorvalidation->getUsername();
                    $emaill = $doctorvalidation->getEmail();
                    $id = $doctorvalidation->getId();
                    $name = $patientValidation->getUsername();

                    if (!is_null($patientValidation)) {
                        try {
                            $transport = (new \Swift_SmtpTransport('mail.continuousnet.com', 587, 'tls'))
                            ->setUsername('amira.dgham@continuousnet.com')
                            ->setPassword('aSspjguK')
                            ->setStreamOptions(array('ssl' => array('allow_self_signed' => true, 'verify_peer' => false, 'verify_peer_name' => false)));
                            $mailer = new \Swift_Mailer($transport);
                            $message = (new \Swift_Message('CoagCare message'))
                                ->setFrom('amira.dgham@continuousnet.com')
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
               <center><img src="https://dewey.tailorbrands.com/production/brand_version_mockup_image/268/3222788268_9c1f3fd7-2d2f-4ed7-abd5-0381dd4740c0.png?cb=1594582078"></center>.<p> Dear ' . $name . ',<br><br> <center><h2 style="color:#282828" > you have been invited to be assigned <br> by doctor ' . $username . '</h2></center> <br>We got a request invitation from CoagCare Doctor ' . $username . '
               that wants to assigned you .Just click the link below and you
               you will be on your way <center> <button   type="button" class="btn btn-info" onclick= "location.href=`http://localhost:4200/Accept-Invitation?token=' . $token . '&id=' . $id . ' \"  style="display:block" > Accept invitation </button><button  type="button" class="btn btn-info" onclick= \"location.href=`http://localhost:4200/Accept-Invitation?token=' . $token . '&id=' . $id . ' \"  style="display:block" > refuse invitation </button>
               <br> <p> If you need aditional information about the doctor, or you did not make this change, please contact <a href=`mailto:' . $emaill . '` style=`color:#ff6c37;text-decoration:unerline;font-weight:blod`>' . $emaill . '</a>.<p> cheers, <br> the CoagCare App Team </p>
               <p style="text-align:center;font-size:11px;color:#282828;padding:20px 0;padding-left:0px">
               © 2020 CoagCare . All Rights Reserved. Continuous Net </p>' .
                                        ' </body>' .
                                        '</html>',
                                    'text/html'
                                );

                            $mailer->send($message);
                        } catch (\Exception $ex) {

                            return View::create($ex->getMessage(), Response::HTTP_BAD_REQUEST, []);
                        }
                        $patientid = $patientValidation->getId();
                       
                        $response = array(
                            'message' => 'success',
                            'result' => 'Email was send successfuly, check your email to reset your password',
                        );
                        return View::create($response, JsonResponse::HTTP_OK, []);
                    } else {
                        $response = array(
                            'message' => 'failure',
                            'result' => 'this email not exist try again!',
                        );
                        return View::create($response, Response::HTTP_NOT_FOUND, []);
                    }
                } else {
                    $response = array(
                        'message' => 'failure',
                        'result' => 'doctor username missing!',
                    );
                    return View::create($response, Response::HTTP_BAD_REQUEST, []);
                }
            } else {
                $response = array(
                    'message' => 'failure',
                    'result' => 'this email is not patient email! try another',
                );
                return View::create($response, Response::HTTP_BAD_REQUEST, []);
            }
        } else {
            $response = array(
                'message' => 'failure',
                'result' => 'email patient missing!',
            );
            return View::create($response, Response::HTTP_BAD_REQUEST, []);
        }
    }

    /**
     *
     * @Rest\POST("/acceptation", name ="invitation_accept")
     */
    public function PatientAcceptation(Request $request, UserPasswordEncoderInterface $encoder, SerializerInterface $serializer)
    {
        $token = $request->request->get('token');

        if (isset($token)) {
            $id = $request->request->get('id');
            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->findOneBy(array('confirmationToken' => $token));
            if (!is_null($user)) {
                $userid = $user->getId();
                if (!is_null($user)) {
                    if (isset($id)) {
                        $repository = $this->getDoctrine()->getRepository(Patient::class);
                        $patient = $repository->findOneBy(array('created_by' => $userid));
                        if (!is_null($patient)) {
                            $repository = $this->getDoctrine()->getRepository(User::class);
                            $doctor = $repository->findOneBy(array('id' => $id));
                            if (!is_null($doctor)) {
                                $patient->setAssignedBy($doctor);
                                $patient->setUpdatedBy($doctor);
                                $em = $this->getDoctrine()->getManager();
                                $em->flush();
                                $user->setConfirmationToken(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
                                $em = $this->getDoctrine()->getManager();
                                $em->flush();
                                $response = array(
                                    'message' => 'success',
                                    'result' => 'patient accept request invitation',
                                );
                                return View::create($response, JsonResponse::HTTP_OK, []);
                            } else {
                                $response = array(
                                    'message' => 'failure',
                                    'result' => 'doctor not found',
                                );
                                return View::create($response, Response::HTTP_NOT_FOUND, []);
                            }
                        } else {
                            $response = array(
                                'message' => 'failure',
                                'result' => 'patient not found password',
                            );
                            return View::create($response, Response::HTTP_NOT_FOUND, []);
                        }
                    } else {
                        $response = array(
                            'message' => 'failure',
                            'result' => 'missing id doctor',
                        );
                        return View::create($response, Response::HTTP_BAD_REQUEST, []);
                    }
                } else {
                    $response = array(
                        'message' => 'failure',
                        'result' => 'user not found',
                    );
                    return View::create($response, Response::HTTP_NOT_FOUND, []);
                }
            } else {
                $response = array(
                    'message' => 'failure',
                    'result' => 'token not found',
                );
                return View::create($response, Response::HTTP_NOT_FOUND, []);
            }
        } else {
            $response = array(
                'message' => 'failure',
                'result' => 'token missing',
            );
            return View::create($response, Response::HTTP_BAD_REQUEST, []);
        }
    }
}