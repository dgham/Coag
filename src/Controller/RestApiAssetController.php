<?php

namespace App\Controller;
use DateTime;
use FFMpeg\FFProbe;
use App\Entity\Asset;
use App\Entity\UserType;
use FOS\RestBundle\View\View;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
class RestApiAssetController  extends FOSRestController
{
    /**
    * @Rest\Get("/api/asset", name ="api_asset")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function index()
    {
        $user = $this->getUser();
            if ($user->getUserType() === UserType::TYPE_ADMIN) {
                $repository = $this->getDoctrine()->getRepository(Asset::class);
                $asset = $repository->findBy(array('remove'=>false),array('id'=>'DESC'));
        if (!is_null($asset)) {
            return View::create($asset, JsonResponse::HTTP_OK, []);
        } else {
            return View::create('no assets Found', JsonResponse::HTTP_NOT_FOUND);  
                  } 
        
            } 
            else {
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                      }
        }
      /**
    * @Rest\Get("/api/asset/{id}", name ="search_asset")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function searchAsset($id)
    {
        $user=$this->getUser();
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $repository = $this->getDoctrine()->getRepository(Asset::class);
            $asset = $repository->findOneBy(array('id' => $id,'remove' => false));
            if (!is_null($asset)) {
                return View::create($asset, JsonResponse::HTTP_OK, []);
        } else {
            return View::create('asset not Found!', JsonResponse::HTTP_NOT_FOUND);  
                  } 
                
            } else {
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
           } 
    }  

     /**
     * @Rest\Post("/api/asset", name ="post_asset")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function create(Request $request,EntityManagerInterface $entity)
    {
        $user = $this->getUser();
            if ($user->getUserType() === UserType::TYPE_ADMIN) {
                $uploadedImage=$request->files->get('file');
              
                if (!is_null($uploadedImage)){
                    $asset = new Asset();
            /**
             * @var UploadedFile $file
             */
                $file=$uploadedImage;
              
                $exten=$file->getClientOriginalExtension();
                $namee=md5(uniqid()).'.'.$file->getClientOriginalExtension();
                $nameee=$file->getClientOriginalName();
                $size=$file->getSize();
                $path= $this->getParameter('video_directory');
                $path_uplaod='Assets/videos/';
                $videoName=$namee;
                if ($exten == "mp4"){
                    $file->move($path_uplaod,$videoName);
                    $asset->setTextValue($namee);
                    $access= $request->request->get('access');
                    if (isset($access)) {
                        if (($access  == 'public') || ($access == 'private')){
                            $asset->setAccess($access);
                        }
                    }else {
                        $asset->setAccess('public');  
                    }
                    $asset->setType('file');
                    $asset->setFilePath($path);
                    $asset->setFileSize($size);
                    $asset->setFileType($exten);
                    $asset->setCreatedBy($user);
                    $asset->setRemove(false);
                    $duration= $this->getAudioDuration($asset);
                    $asset->setDuration($duration);
                    $asset->setCreatedAt(new \DateTime());
                    $asset->setEnabled(true);
                    $entity->persist($asset);
                    $entity->flush();
                    return View::create($asset, JsonResponse::HTTP_CREATED, []);
                }
                $imageName=md5(uniqid()).'.'.$exten;
                $path= $this->getParameter('assetsImages_directory');
               $imagetype=$file->guessExtension();
               $type=$file->getType();
               $size = $file->getSize();
               $dim=$file->getClientMimeType();
               $info = getimagesize($file);
               list($x, $y) = $info;
               $resolution=$info[0].'*'.$info[1];
              $imagetype=$file->guessExtension();
               if ($imagetype == "jpeg" || $imagetype == "png" ){
                $path_uplaod='Assets/images/';
                    $file->move($path_uplaod,$imageName);
                    $asset->setTextValue($imageName);
                    $access= $request->request->get('access');
               
                    if (isset($access)) {
                        if (($access  == "public") || ($access == "private")){
                            $asset->setAccess($access);
                        }
                    }else {
                        $asset->setAccess('public');  
                    }
                    $asset->setType($type);
                    $asset->setFilePath($path);
                    $asset->setFileSize($size);
                    $asset->setRemove(false);
                    $asset->setFileType($imagetype);
                    $asset->setCreatedBy($user);
                    $asset->setResolution($resolution);
                    $asset->setCreatedAt(new \DateTime());
                    $asset->setEnabled(true);
                    $entity->persist($asset);
                    $entity->flush();
                    $response=array(
                        'message'=>'Asset created with success',
                        'result'=>$asset,
                       
                    );
                    return View::create($response, JsonResponse::HTTP_CREATED, []);
                } 
                $docName=md5(uniqid()).'.'.$exten;
                $path= $this->getParameter('document_directory');
               $doctype=$file->guessExtension();
               $type=$file->getType();
               $size = $file->getSize();
              $doctype=$file->guessExtension();
              if ($doctype == "txt" || $doctype == "pdf" ||  $doctype == "docx" || $doctype =="xlsx" || $doctype =="pptx"){
                $path_uplaod='Assets/documents/';
                $file->move($path_uplaod,$docName);
                $asset->setTextValue($docName);
                $access= $request->request->get('access');
                if (isset($access)) {
                    if (($access  == 'public') || ($access == 'private')){
                        $asset->setAccess($access);
                    }
                }else {
                    $asset->setAccess('public');  
                }
                $asset->setType($type);
                $asset->setFilePath($path);
                $asset->setFileSize($size);
                $asset->setFileType($imagetype);
                $asset->setCreatedBy($user);
                $asset->setRemove(false);
                $asset->setCreatedAt(new \DateTime());
                $asset->setEnabled(true);
                $entity->persist($asset);
                $entity->flush();
                $response=array(
                    'message'=>'Asset created with success',
                    'result'=>$asset,
                   
                );
                return View::create($response, JsonResponse::HTTP_CREATED, []);
            } 
                
                else {
                    return View::create('this type of file is not accepted ,try another !', JsonResponse::HTTP_BAD_REQUEST, []);
                            }
            }
            else {
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                      }
                    }
}

      /**
     * @return integer
     */
    private function getAudioDuration(Asset $asset)
    {
        $path = $this->getParameter('public_directory') . $asset->getFilePath() . $asset->getTextValue() ;
        $ffprobe = FFProbe::create([
            'ffmpeg.binaries'  => $this->getParameter('ffmpeg_binaries'),
            'ffprobe.binaries' => $this->getParameter('ffprobe_binaries'),
            'timeout' => $this->getParameter('ffmpeg_timeout'),
            'ffmpeg.threads' => $this->getParameter('ffmpeg_threads')
        ]);
        $duration = $ffprobe
            ->streams($path)   // extracts streams informations
            ->audios()         // filters audio streams
            ->first()          // returns the first audio stream
            ->get('duration'); // returns the duration
        return $duration;
    }
  

     /**
     * @param Request $request
     * @Rest\Post("/api/asset/{id}", name ="update_asset")
     * @Rest\View(serializerGroups={"admin"})
     */
    public function upadateAction(Request $request,$id)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $repository = $this->getDoctrine()->getRepository(Asset::class);
            $asset = $repository->findOneBy(array('id' => $id,'remove' => false));
                 if (!is_null($asset)) {
                        $uploadedfile=$request->files->get('file');
                    if (!is_null($uploadedfile)){
                         /**
                         * @var UploadedFile $file
                         */
                        $file=$uploadedfile;
                    
                        $exten=$file->getClientOriginalExtension();
                        $namee=md5(uniqid()).'.'.$file->getClientOriginalExtension();
                        $nameee=$file->getClientOriginalName();
                        $size=$file->getSize();
                        $path= $this->getParameter('video_directory');
                        $videoName=$namee;
                            if ($exten == "mp4"){
                            $path_uplaod='Assets/videos/';
                            $file->move($path_uplaod,$videoName);
                            $asset->setTextValue($namee);
                                if (isset($access)) {
                                    if (($access  == "public") || ($access == "private")){
                                        $asset->setAccess($access);
                                    }
                                }else {
                                    $asset->setAccess('public');  
                                }
                            $asset->setType('file');
                            $asset->setFilePath($path);
                            $asset->setFileSize($size);
                            $asset->setFileType($exten);
                            $asset->setCreatedBy($user);
                            $ass= $asset->getFilePath();
                            $duration= $this->getAudioDuration($asset);
                            $asset->setDuration($duration);
                            $asset->setUpdatedBy($user);
                            $asset->setUpdatedAt(new \DateTime());
                            $em = $this->getDoctrine()->getManager();
                              $em->flush();
                           
                            $response=array(
                                'message'=>'Asset updated',
                                'result'=> $asset
                               
                            );
                            return View::create($response, JsonResponse::HTTP_OK, []);
                            }

                            $imageName=md5(uniqid()).'.'.$exten;
                            $path= $this->getParameter('assetsImages_directory');
                            $imagetype=$file->guessExtension();
                            $type=$file->getType();
                            $size = $file->getSize();
                            $dim=$file->getClientMimeType();
                            $info = getimagesize($file);
                            list($x, $y) = $info;
                            $resolution=$info[0].'*'.$info[1];
                            $imagetype=$file->guessExtension();
                                if ($imagetype == "jpeg" || $imagetype == "png" ){
                                    $path_uplaod='Assets/images/';
                                    $file->move($path_uplaod,$imageName);
                                $asset->setTextValue($imageName);
                                    if (isset($access)) {
                                        if (($access  == "public") || ($access == "private")){
                                            $asset->setAccess($access);
                                        }
                                    }else {
                                        $asset->setAccess('public');  
                                    }
                                $asset->setType($type);
                                $asset->setFilePath($path);
                                $asset->setFileSize($size);
                                $asset->setFileType($imagetype);
                                $asset->setCreatedBy($user);
                                $asset->setResolution($resolution);
                                $asset->setUpdatedBy($user);
                                $asset->setUpdatedAt(new \DateTime());
                                $em = $this->getDoctrine()->getManager();
                                $em->flush();
                                $response=array(
                                    'message'=>'Asset updated',
                                    'result'=> $asset
                                   
                                );
                                return View::create($response, JsonResponse::HTTP_OK, []);
                               }
                  
                    
                                $docName=md5(uniqid()).'.'.$exten;
                                $path= $this->getParameter('document_directory');
                                $doctype=$file->guessExtension();
                                $type=$file->getType();
                                $size = $file->getSize();
                                $doctype=$file->guessExtension();
                                if ($doctype == "txt" || $doctype == "pdf" ||   $doctype == "docx" || $doctype =="xlsx" || $doctype =="pptx"){
                                $path_uplaod='Assets/documents/';
                                $file->move($path_uplaod,$docName);
                                $asset->setTextValue($docName);
                                        if (isset($access)) {
                                            if (($access  == "public") || ($access == "private")){
                                                $asset->setAccess($access);
                                            }
                                        }else {
                                            $asset->setAccess('public');  
                                        }
                                $asset->setType($type);
                                $asset->setFilePath($path);
                                $asset->setFileSize($size);
                                $asset->setFileType($imagetype);
                                $asset->setUpdatedBy($user);
                                $asset->setUpdatedAt(new \DateTime());
                                $em = $this->getDoctrine()->getManager();
                                $em->flush();
                                $response=array(
                                    'message'=>'Asset updated',
                                    'result'=> $asset                                
                                );
                                return View::create($response, JsonResponse::HTTP_OK, []);
                                }
                                else {
                                    return View::create('this type of file is not accepted ,try another !!', JsonResponse::HTTP_BAD_REQUEST, []);
                                
                                    }
                            }
                                else{
                                    return View::create('file missing!', JsonResponse::HTTP_BAD_REQUEST, []);
                    
                                }
                        }    
                            else {
                                return View::create('asset not Found', JsonResponse::HTTP_NOT_FOUND);  
                                    
                                
                            }
                    
                        }
                            else {
                                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                    
                            }
     }
     /**
      * @Rest\Delete("/api/asset/{id}", name ="selete_asset")
     */
    public function delete($id){
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $repository = $this->getDoctrine()->getRepository(Asset::class);
            $asset = $repository->findOneBy(array('id' => $id,'created_by' => $user->getId(),'remove' => false));
            if (!is_null($asset)) {
                    $asset->setRemove(true);
                    $asset->setRemovedBy($user);
                    $asset->setRemovedAt(new \DateTime());
                    $em = $this->getDoctrine()->getManager();
                    $em->flush();
                    return View::create('asset deleted with success!', JsonResponse::HTTP_OK, []);
                 } 
                
                else {

                    return View::create('not Found', JsonResponse::HTTP_NOT_FOUND);  
               
       
           }
        }
                 else {
                    return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                
        
            
           
        }    
}
                

}