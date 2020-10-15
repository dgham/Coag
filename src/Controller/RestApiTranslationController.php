<?php

namespace App\Controller;

use DateTime;
use App\Entity\UserType;
use App\Entity\Translation;
use FOS\RestBundle\View\View;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RestApiTranslationController extends FOSRestController
{
    /**
     * @Rest\Get("/api/translation", name ="api_translation")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function index()
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $repository = $this->getDoctrine()->getRepository(Translation::class);
            $translation = $repository->findBy(array('remove' => false));
            return View::create($translation, JsonResponse::HTTP_OK, []);
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }
    /**
     * @Rest\Get("/api/translation/{id}", name ="search_translation")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function searchtranslation($id)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $repository = $this->getDoctrine()->getRepository(Translation::class);
            $translation = $repository->findOneBy(array('id' => $id, 'remove' => false));
            if (!is_null($translation)) {
                return View::create($translation, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('translation not Found', JsonResponse::HTTP_NOT_FOUND);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }

    /**
     * @Rest\Post("/api/translation", name ="post_translation")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function create(Request $request, EntityManagerInterface $entity)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $word = $request->request->get('word');
            $translation = new Translation();
            if (isset($word)) {
                $translation->setWord($word);
            }
            $en = $request->request->get('en');
            if (isset($en)) {
                $translation->setEn($en);
            }
            $fr = $request->request->get('fr');
            if (isset($fr)) {
                $translation->setFr($fr);
            }
            $translation->setCreatedBy($user);
            $translation->setCreatedAt(new \DateTime());
            $translation->setEnabled(true);
            $translation->setRemove(false);
            $entity->persist($translation);
            $entity->flush();
            return View::create($translation, JsonResponse::HTTP_CREATED, []);
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }


    /**
     * @param Request $request
     * @Rest\Patch("/api/translation/{id}", name ="patch_translation")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function patchAction(Request $request, $id)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $repository = $this->getDoctrine()->getRepository(Translation::class);
            $translation = $repository->findOneBy(array('id' => $id, 'created_by' => $user->getId(), 'remove' => false));

            if (!is_null($translation)) {
                $word = $request->request->get('word');
                if (isset($word)) {
                    $translation->setWord($word);
                }
                $en = $request->request->get('en');
                if (isset($en)) {
                    $translation->setEn($en);
                }
                $fr = $request->request->get('fr');
                if (isset($fr)) {
                    $translation->setFr($fr);
                }
                $enabled = $request->request->get('enabled');
                if (isset($enabled)) {
                    if (($enabled == true) || ($enabled == false)) {
                        $translation->setEnabled($enabled);
                    }
                }

                $translation->setUpdatedBy($user);
                $translation->setUpdatedAt(new \DateTime());
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                return View::create('translation updated', JsonResponse::HTTP_OK, []);
            } else {
                return View::create('translation not Found', JsonResponse::HTTP_NOT_FOUND);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }



    /**
     * @Rest\Delete("/api/translation/{id}", name ="delete_translation")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function delete($id)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $repository = $this->getDoctrine()->getRepository(Translation::class);
            $translation = $repository->findOneBy(array('id' => $id, 'created_by' => $user->getId(), 'remove' => false));
            if (!is_null($translation)) {
                $translation->setRemove(true);
                $translation->setRemovedBy($user);
                $translation->setRemovedAt(new \DateTime());
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                return View::create('translation deleted', JsonResponse::HTTP_OK, []);
            } else {
                return View::create('translation not Found', JsonResponse::HTTP_NOT_FOUND);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }
}