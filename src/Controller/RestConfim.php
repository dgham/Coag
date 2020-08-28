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
     * @Rest\Post("/auth/resetpassword", name ="Resettt_password")
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
                        ' <body>' .
                        '<center><img src="https://api.coagcare.continuousnet.com/profile/images/35b862f275f071b3d3465bbd845145d4.png" width="150px" height="150px">.<p> Dear ' . $name . ',<br><br> We got a request to reset
                   you CoagCare password .Just click the link below and you will be on your way <a href=`https://api.coagcare.continuousnet.com/Confirm/resetPassword/' . $token . '`' . ' height="42" width="150"> Reset password </a> . If you did not make this request, please ignore this email and thanks .
                   <br> If you need aditional assistance, or you did not make this change, please contact <a href=`mailto:CoagCareApp@gmail.com` style=`color:#ff6c37;text-decoration:unerline;font-weight:blod`>CoagCareApp@gmail.com</a>.<p> cheers, <br> the CoagCare App Team </p>
                   <p style="text-align:center;font-size:11px;color:#282828;padding:20px 0;padding-left:0px">
                   © 2020 CoagCare . All Rights Reserved. Continuous Net </p></center>' .
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
     * @Rest\Patch("/auth/RestConfirmation", name ="Resetconfirm_password")
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
     * @Rest\POST("/Acceptation", name ="invitation_accept")
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
