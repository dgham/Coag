<?php

namespace App\Controller;

use DateTime;
use App\Entity\Asset;
use App\Entity\Access;
use App\Entity\Country;
use App\Entity\UserType;
use FOS\RestBundle\View\View;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RestApiCountryController extends FOSRestController
{
    /**
     * @Rest\Get("/api/country", name ="api_country")
     * @Rest\View(serializerGroups={"users"})
     */
    public function index()
    {
        $user = $this->getUser();
    
            $repository = $this->getDoctrine()->getRepository(Country::class);
            $country = $repository->findBy(array('remove' => false), array('id' => 'DESC'));
            if (!empty($country)) {
                return View::create($country, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('no country found', JsonResponse::HTTP_OK);
            }
     
    }

    /**
     * @Rest\Get("/api/country/{id}", name ="search_country")
     * @Rest\View(serializerGroups={"users"})
     */
    public function searchCountry($id)
    {
        $user = $this->getUser();
            $repository = $this->getDoctrine()->getRepository(Country::class);
            $country = $repository->findOneBy(array('id' => $id, 'created_by' => $user->getId(), 'remove' => false));
            if (!is_null($country)) {
                return View::create($country, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('country not Found', JsonResponse::HTTP_NOT_FOUND);
            }
    }


    /**
     * @Rest\Post("/api/country", name ="post_country")
     * @Rest\View(serializerGroups={"users"})
     */
    public function create(Request $request, EntityManagerInterface $entity)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $name = $request->request->get('name');
            $typename = gettype($name);
            $country = new Country();
            if (isset($name)) {
                if ($typename == "string") {
                    $country->setName($name);
                } else {
                    return View::create('country name must be a string', JsonResponse::HTTP_BAD_REQUEST);
                }
            } else {
                return View::create('missing name of country !!', JsonResponse::HTTP_BAD_REQUEST);
            }
            $code = $request->request->get('code');
            $typecode = gettype($code);
            if (isset($code)) {
                if ($typename == "string") {
                    $country->setCode($code);
                } else {
                    return View::create('country code must be a string', JsonResponse::HTTP_BAD_REQUEST);
                }
            } else {
                return View::create('you should add the code to the country', JsonResponse::HTTP_BAD_REQUEST);
            }
            $longcode = $request->request->get('long_code');
            $typelongcode = gettype($longcode);
            if (isset($longcode)) {
                if ($typelongcode == "string") {
                    $country->setLongCode($longcode);
                } else {
                    return View::create('country long_code must be a string', JsonResponse::HTTP_BAD_REQUEST);
                }
            }
            $prefix = $request->request->get('prefix');
            $typeprefix = gettype($prefix);
            if (isset($prefix)) {
                if ($typeprefix == "string") {
                    $country->setPrefix($prefix);
                } else {
                    return View::create('country prefix must be a string', JsonResponse::HTTP_BAD_REQUEST);
                }
            }
            $uploadedImage = $request->files->get('picture');
            if (!is_null($uploadedImage)) {
                /**
                 * @var UploadedFile $image
                 */
                $image = $uploadedImage;

                $imageName = md5(uniqid()) . '.' . $image->guessExtension();
                $type = $image->getType();
                $size = $image->getSize();
                $imagetype = $image->guessExtension();
                $path = $this->getParameter('image_directory');
                $serveur_ip = gethostbyname(gethostname());
                $path_uplaod = 'country/images/';

                if ($imagetype == "jpeg" || $imagetype == "png" || $imagetype == "svg") {
                    $image->move($path_uplaod, $imageName);
                    $image_url = $path_uplaod . $imageName;
                    $country->setPicture($image_url);
                } else {
                    return View::create('there is something wrong with this file!,select picture!', JsonResponse::HTTP_BAD_REQUEST, []);
                }
            }
            $country->setCreatedBy($user);
            $country->setCreatedAt(new \DateTime());
            $country->setEnabled(true);
            $country->setRemove(false);
            $entity->persist($country);
            $entity->flush();
            $response = array(
                'message' => 'country created',
                'result' => $country,

            );
            return View::create($response, JsonResponse::HTTP_CREATED, []);
        } else {

            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }

    /**
     * @param Request $request
     *
     * @Rest\POST("/api/country/{id}", name ="patch_country")
     * @Rest\View(serializerGroups={"users"})
     */
    public function patchAction(Request $request, $id)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $repository = $this->getDoctrine()->getRepository(Country::class);
            $country = $repository->findOneBy(array('id' => $id, 'created_by' => $user->getId(), 'remove' => false));

            if (!is_null($country)) {

                $name = $request->request->get('name');
                $typename = gettype($name);
                if (isset($name)) {
                    if ($typename == "string") {
                        $country->setName($name);
                    } else {
                        return View::create('country name must be a string', JsonResponse::HTTP_BAD_REQUEST);
                    }
                }

                $code = $request->request->get('code');
                $typecode = gettype($code);
                if (isset($code)) {
                    if ($typename == "string") {
                        $country->setCode($code);
                    } else {
                        return View::create('country code must be a string', JsonResponse::HTTP_BAD_REQUEST);
                    }
                }
                $longcode = $request->request->get('long_code');
                $typelongcode = gettype($longcode);
                if (isset($longcode)) {
                    if ($typelongcode == "string") {
                        $country->setLongCode($longcode);
                    } else {
                        return View::create('country long_code must be a string', JsonResponse::HTTP_BAD_REQUEST);
                    }
                }
                $prefix = $request->request->get('prefix');
                $typeprefix = gettype($prefix);
                if (isset($prefix)) {
                    if ($typeprefix == "string") {
                        $country->setPrefix($prefix);
                    } else {
                        return View::create('country prefix must be a string', JsonResponse::HTTP_BAD_REQUEST);
                    }
                }


                $uploadedImage = $request->files->get('picture');
                if (isset($uploadedImage)) {

                    /**
                     * @var UploadedFile $image
                     */
                    $image = $uploadedImage;
                    $imageName = md5(uniqid()) . '.' . $image->guessExtension();
                    $imagetype = $image->guessExtension();
                    $path = $this->getParameter('image_directory');
                    $serveur_ip = gethostbyname(gethostname());
                    $path_uplaod = 'country/images/';
                    if ($imagetype == "jpeg" || $imagetype == "png" || $imagetype =="svg") {
                        $image->move($path_uplaod, $imageName);
                        $image_url = $path_uplaod . $imageName;
                        $country->setPicture($image_url);
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($country);
                        $em->flush();
                    } else {
                        return View::create('there is something wrong with this file!,select picture!', JsonResponse::HTTP_BAD_REQUEST, []);
                    }
                }
                $country->setUpdatedBy($user);
                $country->setUpdatedAt(new \DateTime());
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                $response = array(
                    'message' => 'country updated',
                    'result' => $country,

                );
                return View::create($response, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('not Found', JsonResponse::HTTP_NOT_FOUND);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Rest\Post("/api/country/picture/{id}", name ="uplpoad_country")
     * @Rest\View(serializerGroups={"users"})
     */
    public function uploadImage($id, Request $request)
    {

        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $repository = $this->getDoctrine()->getRepository(Country::class);
            $country = $repository->findOneBy(array('id' => $id, 'created_by' => $user->getId(), 'remove' => false));

            if (!is_null($country)) {
                $uploadedImage = $request->files->get('picture');
                if (!is_null($uploadedImage)) {


                    /**
                     * @var UploadedFile $image
                     */
                    $image = $uploadedImage;

                    $imageName = md5(uniqid()) . '.' . $image->guessExtension();
                    $type = $image->getType();
                    $size = $image->getSize();
                    $imagetype = $image->guessExtension();
                    $path = $this->getParameter('image_directory');
                    $serveur_ip = gethostbyname(gethostname());
                    $path_uplaod = 'country/images/';

                    if ($imagetype == "jpeg" || $imagetype == "png") {
                        $image->move($path_uplaod, $imageName);
                        $image_url = $path_uplaod . $imageName;
                        $country->setPicture($image_url);
                        $country->setUpdatedBy($user);
                        $country->setUpdatedAt(new \DateTime());
                        $em = $this->getDoctrine()->getManager();
                        $em->flush();
                        $response = array(
                            'message' => 'country updated',
                            'result' => $country,

                        );
                        return View::create($response, JsonResponse::HTTP_OK, []);
                    } else {
                        return View::create('there is something wrong with this file!,select picture!', JsonResponse::HTTP_BAD_REQUEST, []);
                    }
                } else {
                    return View::create('picture is missing!', JsonResponse::HTTP_BAD_REQUEST, []);
                }
            } else {
                return View::create('country not Found', JsonResponse::HTTP_NOT_FOUND);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }

    /**
     * @Rest\Delete("/api/country/{id}", name ="delete_country")
     */
    public function delete($id)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $repository = $this->getDoctrine()->getRepository(Country::class);
            $country = $repository->findOneBy(array('id' => $id, 'created_by' => $user->getId(), 'remove' => false));
            if (!is_null($country)) {
                $country->setRemove(true);
                $country->setRemovedBy($user);
                $country->setRemovedAt(new \DateTime());
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                return View::create('country deleted', JsonResponse::HTTP_OK, []);
            } else {
                return View::create('country not Found', JsonResponse::HTTP_NOT_FOUND);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }
}