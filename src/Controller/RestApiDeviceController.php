<?php

namespace App\Controller;

use App\Entity\Device;
use App\Entity\UserType;
use DateTime;
use Exception;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RestApiDeviceController extends FOSRestController
{

    /**
     * @Rest\Get("/api/device", name ="api_device")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function index()
    {
        $user = $this->getUser();
        $data = array(
            'id' => $user->getId(),
        );
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $repository = $this->getDoctrine()->getRepository(Device::class);
            $device = $repository->findBy(array('removed' => false), array('id' => 'DESC'));
            if (!empty($device)) {
                return View::create($device, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('no device found', JsonResponse::HTTP_OK);
            }
        }
            if (($user->getUserType() === UserType::TYPE_PATIENT) || ($user->getUserType() === UserType::TYPE_DOCTOR)) {
                $repository = $this->getDoctrine()->getRepository(Device::class);
                $device = $repository->findOneBy(array('created_by'=>$user->getId(),'removed' => false));
                if (!empty($device)) {
                    return View::create($device, JsonResponse::HTTP_OK, []);
                } else {
                    return View::create('no device found', JsonResponse::HTTP_OK);
                }
                
            
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }

    /**
     * @Rest\Get("/api/device/{id}", name ="search_device")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function searchDevice($id)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $repository = $this->getDoctrine()->getRepository(Device::class);
            $device = $repository->findOneBy(array('id' => $id, 'removed' => false));
            if (!is_null($device)) {
                return View::create($device, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('device not Found', JsonResponse::HTTP_NOT_FOUND);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }

    /**
     * @Rest\Post("/api/device", name ="post_device")
     * @Rest\View(serializerGroups={"admin"})
     * @return array
     */
    public function postPushDeviceAction(Request $request)
    {
        $add = false;
        $user = $this->getUser();
        if (($user->getUserType() === UserType::TYPE_ADMIN) || ($user->getUserType() === UserType::TYPE_DOCTOR) ||($user->getUserType() === UserType::TYPE_PATIENT) || ($user->getUserType() === UserType::TYPE_HOSPITAL)) {
            try {
                $data = $request->request->all();
                $repository = $this->getDoctrine()->getRepository(Device::class);
                $pushDevice = $repository->findOneBy(array('created_by' => $this->getUser()->getId()));
                if (is_null($pushDevice)) {
                    $pushDevice = new Device();
                    $add = true;
                    $typetoken = gettype($data['token']);
                    if (isset($data['token'])) {
                        if ($typetoken == "string") {
                            $pushDevice->setToken($data['token']);
                        } else {
                            return View::create('token should be type string', JsonResponse::HTTP_BAD_REQUEST);
                        }
                    } else {
                        return View::create('missing token !!', JsonResponse::HTTP_BAD_REQUEST);
                    }
                    $typeos = gettype($data['os']);
                    if (isset($data['os'])) {
                        if ($typeos == "string") {
                            $pushDevice->setOs($data['os']);
                        } else {
                            return View::create('os should be type string', JsonResponse::HTTP_BAD_REQUEST);
                        }
                    } else {
                        return View::create('missing os !!', JsonResponse::HTTP_BAD_REQUEST);
                    }
                    $typeversion = gettype($data['version']);
                    if (isset($data['version'])) {
                        if ($typeversion == "string") {
                            $pushDevice->setVersion($data['version']);
                        } else {
                            return View::create('version should be type string', JsonResponse::HTTP_BAD_REQUEST);
                        }
                    } else {
                        return View::create('missing version !!', JsonResponse::HTTP_BAD_REQUEST);
                    }
                    $typemodele = gettype($data['modele']);
                    if (isset($data['modele'])) {
                        if ($typemodele == "string") {
                            $pushDevice->setModele($data['modele']);
                        } else {
                            return View::create('modele should be type string', JsonResponse::HTTP_BAD_REQUEST);
                        }
                    } else {
                        return View::create('missing modele !!', JsonResponse::HTTP_BAD_REQUEST);
                    }
                    $typeuuid = gettype($data['uuid']);
                    if (isset($data['uuid'])) {
                        if ($typeuuid == "string") {
                            $pushDevice->setUuid($data['uuid']);
                        } else {
                            return View::create('modele should be type string', JsonResponse::HTTP_BAD_REQUEST);
                        }
                    } else {
                        return View::create('missing modele !!', JsonResponse::HTTP_BAD_REQUEST);
                    }
                    if (isset($data['position'])) {
                        $pushDevice->setPosition($data['position']);
                    }
                    $pushDevice->setEnabled(true);
                    $pushDevice->setRemoved(false);
                    $pushDevice->setCreatedBy($user);
                    $pushDevice->setCreatedAt(new \DateTime());
                    $em = $this->getDoctrine()->getManager();
                    if ($add) {
                        $em->persist($pushDevice);
                    }
                    $em->flush();
                    $response = array(
                        'message' => 'device created',
                        'result' => $pushDevice,

                    );
                    return View::create($response, Response::HTTP_CREATED, []);
                } else {

                    $typetoken = gettype($data['token']);
                    if (isset($data['token'])) {
                        if ($typetoken == "string") {
                            $pushDevice->setToken($data['token']);
                        } else {
                            return View::create('token should be type string', JsonResponse::HTTP_BAD_REQUEST);
                        }
                    } else {
                        return View::create('missing token !!', JsonResponse::HTTP_BAD_REQUEST);
                    }
                    $typeos = gettype($data['os']);
                    if (isset($data['os'])) {
                        if ($typeos == "string") {
                            $pushDevice->setOs($data['os']);
                        } else {
                            return View::create('os should be type string', JsonResponse::HTTP_BAD_REQUEST);
                        }
                    } else {
                        return View::create('missing os !!', JsonResponse::HTTP_BAD_REQUEST);
                    }
                    $typeversion = gettype($data['version']);
                    if (isset($data['version'])) {
                        if ($typeversion == "string") {
                            $pushDevice->setVersion($data['version']);
                        } else {
                            return View::create('version should be type string', JsonResponse::HTTP_BAD_REQUEST);
                        }
                    } else {
                        return View::create('missing version !!', JsonResponse::HTTP_BAD_REQUEST);
                    }
                    $typemodele = gettype($data['modele']);
                    if (isset($data['modele'])) {
                        if ($typemodele == "string") {
                            $pushDevice->setModele($data['modele']);
                        } else {
                            return View::create('modele should be type string', JsonResponse::HTTP_BAD_REQUEST);
                        }
                    } else {
                        return View::create('missing modele !!', JsonResponse::HTTP_BAD_REQUEST);
                    }
                    $typeuuid = gettype($data['uuid']);
                    if (isset($data['uuid'])) {
                        if ($typeuuid == "string") {
                            $pushDevice->setUuid($data['uuid']);
                        } else {
                            return View::create('modele should be type string', JsonResponse::HTTP_BAD_REQUEST);
                        }
                    } else {
                        return View::create('missing modele !!', JsonResponse::HTTP_BAD_REQUEST);
                    }
                    if (isset($data['position'])) {
                        $pushDevice->setPosition($data['position']);
                    }
                    $pushDevice->setUpdatedBy($user);
                    $pushDevice->setUpdatedAt(new \DateTime());
                    $em = $this->getDoctrine()->getManager();
                    $em->flush();
                    $response = array(
                        'message' => 'device exist and updated',
                        'result' => $pushDevice,

                    );
                    return View::create($response, JsonResponse::HTTP_OK, []);
                }
            } catch (\Exception $ex) {
                return new JsonResponse($ex->getMessage(), JsonResponse::HTTP_BAD_REQUEST, []);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }

    /**
     *
     * @Rest\Patch("/api/device/{id}", name ="patch_device")
     * @Rest\View(serializerGroups={"admin"})
     *
     * @return array
     */
    public function patchPushDeviceAction(Request $request, $id)
    {
        $user = $this->getUser();
        if (($user->getUserType() === UserType::TYPE_ADMIN) || ($user->getUserType() === UserType::TYPE_DOCTOR) || ($user->getUserType() === UserType::TYPE_HOSPITAL)) {
            try {
                $data = $request->request->all();
                $repository = $this->getDoctrine()->getRepository(Device::class);
                $pushDevice = $repository->findOneBy(array('id' => $id, 'created_by' => $this->getUser()->getId(), 'removed' => false));
                if (is_null($pushDevice)) {
                    return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                }
                $typetoken = gettype($data['token']);
                if (isset($data['token'])) {
                    if ($typetoken == "string") {
                        $pushDevice->setToken($data['token']);
                    } else {
                        return View::create('token should be type string', JsonResponse::HTTP_BAD_REQUEST);
                    }
                } else {
                    return View::create('missing token !!', JsonResponse::HTTP_BAD_REQUEST);
                }
                $typeos = gettype($data['os']);
                if (isset($data['os'])) {
                    if ($typeos == "string") {
                        $pushDevice->setOs($data['os']);
                    } else {
                        return View::create('os should be type string', JsonResponse::HTTP_BAD_REQUEST);
                    }
                } else {
                    return View::create('missing os !!', JsonResponse::HTTP_BAD_REQUEST);
                }
                $typeversion = gettype($data['version']);
                if (isset($data['version'])) {
                    if ($typeversion == "string") {
                        $pushDevice->setVersion($data['version']);
                    } else {
                        return View::create('version should be type string', JsonResponse::HTTP_BAD_REQUEST);
                    }
                } else {
                    return View::create('missing version !!', JsonResponse::HTTP_BAD_REQUEST);
                }
                $typemodele = gettype($data['modele']);
                if (isset($data['modele'])) {
                    if ($typemodele == "string") {
                        $pushDevice->setModele($data['modele']);
                    } else {
                        return View::create('modele should be type string', JsonResponse::HTTP_BAD_REQUEST);
                    }
                } else {
                    return View::create('missing modele !!', JsonResponse::HTTP_BAD_REQUEST);
                }
                $typeuuid = gettype($data['uuid']);
                if (isset($data['uuid'])) {
                    if ($typeuuid == "string") {
                        $pushDevice->setUuid($data['uuid']);
                    } else {
                        return View::create('modele should be type string', JsonResponse::HTTP_BAD_REQUEST);
                    }
                } else {
                    return View::create('missing modele !!', JsonResponse::HTTP_BAD_REQUEST);
                }
                if (isset($data['position'])) {
                    $pushDevice->setPosition($data['position']);
                }
                $pushDevice->setUpdatedBy($user);
                $pushDevice->setUpdatedAt(new \DateTime());
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                $response = array(
                    'message' => 'device updated',
                    'result' => $pushDevice,

                );
                return View::create($response, JsonResponse::HTTP_OK, []);
            } catch (\Exception $ex) {
                return View::create($ex->getMessage(), JsonResponse::HTTP_BAD_REQUEST, []);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }
}