<?php
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationFailureListener
{
    /**
     * @param AuthenticationFailureEvent $event
     */
    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event)
    {
        $data = [
            'status'  => 'error',
            'code' => Response::HTTP_UNAUTHORIZED,
            'message' => 'Bad credentials, please verify that your email/password are correctly set',
        ];

        $response = new JsonResponse($data, Response::HTTP_UNAUTHORIZED, ['WWW-Authenticate' => 'Bearer']);

        $event->setResponse($response);
    }
}
?>