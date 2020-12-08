<?php

namespace App\Controller;

use App\Entity\Asset;
use App\Entity\Device;
use App\Entity\Measure;
use App\Entity\Doctor;
use App\Entity\DoctorAssignement;
use App\Entity\Hospital;
use App\Entity\Note;
use App\Entity\Patient;
use App\Entity\Session;
use App\Entity\Treatment;
use App\Entity\User;
use App\Entity\UserType;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Hoa\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class RestApiMeasureINRController extends FOSRestController
{
    /**
     * @Rest\Get("/api/measure", name ="api_Measure")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function index()
    {
        $user = $this->getUser();
        $data = array(
            'id' => $user->getId(),
        );
        if ($user->getUserType() === UserType::TYPE_PATIENT) {
            $repository = $this->getDoctrine()->getRepository(Measure::class);
            $Measure = $repository->findBy(array('created_by' => $data), array('id' => 'DESC'));
            if (!empty($Measure)) {
                return View::create($Measure, JsonResponse::HTTP_OK, []);
            }
            return View::create('No data found', JsonResponse::HTTP_OK, []);
        }
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
            $Assigned = $repository->findBy(array('id_doctor' => $user->getId(), 'status' => 'Accepted', 'removed' => false), array('id' => 'DESC'));
            foreach ($Assigned as $data) {
                $a[] = $data->getIdPatient();
            }
            if (!is_null($Assigned)) {
                $Measurerepository = $this->getDoctrine()->getRepository(Measure::class);
                $Measure = $Measurerepository->findBy(array('created_by' => $a));
                if (!empty($Measure)) {
                    return View::create($Measure, JsonResponse::HTTP_OK, []);
                }
                return View::create('No data found', JsonResponse::HTTP_OK, []);
            } else {
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }
    /**
     * @Rest\Get("/api/measure/{id}", name ="search_Measure")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function searchMeasure($id)
    {
        $user = $this->getUser();
        if ($user->getUserType() === UserType::TYPE_PATIENT) {
            $repository = $this->getDoctrine()->getRepository(Measure::class);
            $Measure = $repository->findOneBy(array('id' => $id, 'created_by' => $user->getId()));
            if (!is_null($Measure)) {
                return View::create($Measure, JsonResponse::HTTP_OK, []);
            } else {
                return View::create(' Not Found', JsonResponse::HTTP_NOT_FOUND);
            }
        }
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
            $Assigned = $repository->findBy(array('id_doctor' => $user->getId(), 'status' => 'Accepted', 'removed' => false));
            foreach ($Assigned as $data) {
                $a[] = $data->getIdPatient();
            }

            if (!is_null($Assigned)) {
                $Measurerepository = $this->getDoctrine()->getRepository(Measure::class);
                $Measure = $Measurerepository->findOneBy(array('id' => $id, 'created_by' => $a));
                if (!is_null($Measure)) {
                    return View::create($Measure, JsonResponse::HTTP_OK, []);
                } else {
                    return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                }
            } else {
                return View::create(' Not Found', JsonResponse::HTTP_NOT_FOUND);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }

    /**
     * @Rest\Post("/api/measure", name ="post_Measure")
     * @Rest\View(serializerGroups={"users"})
     */
    public function create(Request $request, EntityManagerInterface $entity)
    {
        $user = $this->getUser();

        try {
            if ($user->getUserType() === UserType::TYPE_PATIENT) {
                $value = $request->request->get('value');
                $typevalue = gettype($value);
                $Measure = new Measure();
                if (isset($value)) {
                    if ($typevalue == "string") {

                        $Measure->setValue((double)$value);
                        if ((2.0 < $value) && ($value > 3.0)) {
                            $Measure->setIndication('anormal mesure');
                        } else {
                            $Measure->setIndication('normal mesure');
                        }
                    } else {
                        return View::create('value of INR must be double!', JsonResponse::HTTP_BAD_REQUEST, []);
                    }
                } else {
                    return View::create('value INR is missing!', JsonResponse::HTTP_BAD_REQUEST, []);
                }
                $details = $request->request->get('details');
                $typevalue = gettype($details);
                if (isset($details)) {
                    if ($typevalue == "string") {
                        $Measure->setDetails($details);
                    } else {
                        return View::create('details must be string!', JsonResponse::HTTP_BAD_REQUEST, []);
                    }
                }
                $devicedate = $request->request->get('devicedate');
                $typevalue = gettype($devicedate);
                if (isset($devicedate)) {
                    if ($typevalue == "string") {
                        $Measure->setDeviceDate($devicedate);
                    } else {
                        return View::create(' device date must be string!', JsonResponse::HTTP_BAD_REQUEST, []);
                    }
                }
                $Measure->setReaded(false);
                $Measure->setCreatedBy($user);
                $Measure->setCreatedAt(new \DateTime());
                $entity->persist($Measure);
                $entity->flush();
                return View::create($Measure, JsonResponse::HTTP_CREATED, []);
            } else {
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
            }
        } catch (Exception $e) {
            return View::create($e->getMessage(), JsonResponse::HTTP_BAD_REQUEST, []);
        }
    }

    /**
     * @Rest\Get("/api/measureDetails", name ="mesure_detailss")
     * @Rest\View(serializerGroups={"users"})
     */
    public function countMeasure()
    {
        $user = $this->getUser();
        $data = array(
            'id' => $user->getId(),
        );
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
            $Assigned = $repository->findBy(array('id_doctor' => $user->getId(), 'status' => 'Accepted', 'removed' => false));
            foreach ($Assigned as $data) {
                $a[] = $data->getIdPatient();
            }
            if (!is_null($Assigned)) {
                $Measurerepository = $this->getDoctrine()->getRepository(Measure::class);
                $Measure = $Measurerepository->findBy(array('created_by' => $a));
                $nb = count($Measure);
                $normall = 'normal mesure';
                $Measurerepository = $this->getDoctrine()->getRepository(Measure::class);
                $MeasureNormal = $Measurerepository->findByINRMesureNormal($a, $normall);
                $Measuretotal = $Measurerepository->findBy(array('created_by' => $a));
                $normal = count($MeasureNormal);
                $total = count($Measuretotal);
                $nbnormal = strval(intval(round($normal * 100 / $total))) . "%";
                $anormal = 'anormal mesure';
                $Measurerepository = $this->getDoctrine()->getRepository(Measure::class);
                $MeasureANormal = $Measurerepository->findByINRMesureINormal($a, $anormal);
                $Measuretotal = $Measurerepository->findBy(array('created_by' => $a));
                $normal = count($MeasureANormal);
                $total = count($Measuretotal);
                $nbAnormal = strval(intval(round($normal * 100 / $total))) . "%";

                $response = array(
                    'ResultINR_Total' => $nb,
                    'Normal_mesure' => $nbnormal,
                    'Anormal_mesure' => $nbAnormal,

                );
                return View::create($response, JsonResponse::HTTP_OK, []);
            }
        }
        $a = array();
        $user = $this->getUser();
        $data = array(
            'id' => $user->getId(),
        );
        if ($user->getUserType() === UserType::TYPE_HOSPITAL) {
            $patientrepository = $this->getDoctrine()->getRepository(Hospital::class);
            $hospital = $patientrepository->findOneBy(array('created_by' => $user->getId()));

            $patientrepository = $this->getDoctrine()->getRepository(Doctor::class);
            $doctor = $patientrepository->findBy(array('hospital' => $hospital->getId(), 'affiliate' => true));
            foreach ($doctor as $dataa) {
                $doctors[] = $dataa->getCreatedBy()->getId();
            }
            $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
            $Assigned = $repository->findBy(array('id_doctor' => $doctors, 'status' => 'Accepted', 'removed' => false));
            foreach ($Assigned as $dataa) {
                $a[] = $dataa->getIdPatient()->getId();
            }
            if (!is_null($Assigned)) {
                /// normal mesure mesure ////
                $normal = 'normal mesure';
                $Measurerepository = $this->getDoctrine()->getRepository(Measure::class);
                $MeasureNormal = $Measurerepository->findByINRMesureINormal($a, $normal);
                $Measuretotal = $Measurerepository->findBy(array('created_by' => $a));
                $anormal = 'anormal mesure';
                $Measurerepository = $this->getDoctrine()->getRepository(Measure::class);
                $MeasureANormal = $Measurerepository->findByINRMesureINormal($a, $anormal);
                $anormal = count($MeasureANormal);
                $normal = count($MeasureNormal);
                $total = count($Measuretotal);
                $nbnormal = strval(intval(round($normal * 100 / $total))) . "%";
                $nbAnormal = strval(intval(round($anormal * 100 / $total))) . "%";
                $response = array(

                    'ResultINR_Anormal' => $nbAnormal,
                    'ResultINR_Normal' => $nbnormal,
                );

                return View::create($response, JsonResponse::HTTP_OK, []);
            }
        }

        if ($user->getUserType() === UserType::TYPE_PATIENT) {
            $repository = $this->getDoctrine()->getRepository(Measure::class);
            $Measure = $repository->findBy(array('created_by' => $user));

            if (!is_null($Measure)) {
                $total = count($Measure);
                $normall = 'normal mesure';
                $anormall = 'anormal mesure';
                $MeasureNormal = $repository->findBy(array('created_by' => $user, "indication" => $normall));
                $MeasureANormal = $repository->findBy(array('created_by' => $user, "indication" => $anormall));
                $normal = count($MeasureNormal);
                $anormal = count($MeasureANormal);
                $nbdiagtotal = count($Measure);
                if ($total == 0) {
                    $response = array(
                        'Total_INRmesure' => 0,
                        'INR_Normal' => "0%",
                        'INR_Anormal' => "0%",
                    );
                    return View::create($response, JsonResponse::HTTP_OK, []);
                } else {
                    if ($normal == 0) {
                        $nbnormal  == "0%";
                    } else {
                        $nbnormal = $normal * 100 / $total;
                        $normall = strval(intval(round($nbnormal))) . "%";
                    }
                    if ($anormal == 0) {
                        $nbanormal = "0%";
                    } else {
                        $nbanormal = $anormal * 100 / $total;
                        $anormall = strval(intval(round($nbanormal))) . "%";
                    }


                    $response = array(
                        'Total_INRmesure' => $total,
                        'INR_Normal' => $normall,
                        'INR_Anormal' => $anormall,
                    );
                    return View::create($response, JsonResponse::HTTP_OK, []);
                }
            } else {
                return View::create('No measurements found ', JsonResponse::HHTP_NOT_FOUND, []);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }
    /**
     * @Rest\Get("/api/patientMesureCount", name ="countt_Measure")
     * @Rest\View(serializerGroups={"users"})
     */
    public function CountpatientMeasure()
    {
        $a = array();
        $user = $this->getUser();
        $data = array(
            'id' => $user->getId(),
        );
        if ($user->getUserType() === UserType::TYPE_PATIENT) {
            $Measurerepository = $this->getDoctrine()->getRepository(Measure::class);
            $Measure = $Measurerepository->findby(array('created_by' => $user->getId()));
            $nbdiag = count($Measure);

            $response = array(
                'ResultINR_Total' => $nbdiag,

            );
            return View::create($response, JsonResponse::HTTP_OK, []);
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }

    ///////get all mesure of INR By of one patient///////
    /**
     * @Rest\Get("/api/measureByPatient/{id}", name ="patient_Measure")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function ResultbyPatient($id)
    {
        $user = $this->getUser();
        $data = array(
            'id' => $user->getId(),
        );
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {

            $repository = $this->getDoctrine()->getRepository(User::class);
            $patient = $repository->findOneBy(array('id' => $id));
            if (!is_null($patient)) {
                $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
                $doctorassignement = $repository->findOneBy(array('id_patient' => $patient->getId(), 'id_doctor' => $user->getId(), 'status' => 'Accepted', 'removed' => false));
                if (!is_null($doctorassignement)) {
                    $repository = $this->getDoctrine()->getRepository(Measure::class);
                    $Measure = $repository->findBy(array('created_by' => $patient));
                    return View::create($Measure, JsonResponse::HTTP_OK, []);
                } else {
                    return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                }
            } else {
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }
    /**
     * @Rest\Get("/api/doctor/userMeasure/{id}", name ="user_Measure")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function ResultbyUser($id)
    {
        $user = $this->getUser();
        $data = array(
            'id' => $user->getId(),
        );
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {

            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->findOneBy(array('id' => $id));
            if (!is_null($user)) {
                $repository = $this->getDoctrine()->getRepository(Measure::class);
                $Measure = $repository->findBy(array('created_by' => $user));
                if (!empty($Measure)) {
                    return View::create($Measure, JsonResponse::HTTP_OK, []);
                } else {
                    return View::create('no Measure found', JsonResponse::HTTP_NOT_FOUND, []);
                }
            } else {
                return View::create('user not found', JsonResponse::HTTP_NOT_FOUND, []);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }

    /**
     * @Rest\Get("/api/patientMesureDetails/{id}", name ="countt_indication")
     * @Rest\View(serializerGroups={"users"})
     */
    public function Countpatientmesure($id)
    {
        $a = array();
        $user = $this->getUser();
        $data = array(
            'id' => $user->getId(),
        );

        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $repository = $this->getDoctrine()->getRepository(User::class);
            $userverife = $repository->findBy(array('id' => $id));
            if (!is_null($userverife)) {
                $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
                $Assigned = $repository->findBy(array('id_doctor' => $user->getId(), 'id_patient' => $id, 'status' => 'Accepted', 'removed' => false));
                if (!is_null($Assigned)) {

                    foreach ($Assigned as $dataa) {
                        array_push($a, $dataa->getIdPatient());
                        $Measurerepository = $this->getDoctrine()->getRepository(Measure::class);
                        $Measure = $Measurerepository->findby(array('created_by' => $id, 'indication' => 'normal mesure'));
                        $Measureanormal = $Measurerepository->findby(array('created_by' => $id, 'indication' => 'anormal mesure'));
                        $Measuretotal = $Measurerepository->findBy(array('created_by' => $id));
                        $nbnormal = count($Measure);
                        $nbanormal = count($Measureanormal);
                        $nbdiagtotal = count($Measuretotal);
                        $normal = strval(intval(round($nbnormal * 100 / $nbdiagtotal))) . "%";
                        $annormal = strval(intval(round($nbanormal * 100 / $nbdiagtotal))) . "%";
                        $Measurerepository = $this->getDoctrine()->getRepository(Measure::class);
                        $latestdate = $Measurerepository->findBymaxDate($id);

                        $response = array(
                            'Result_noraml' => $normal,
                            'Result_anormal' => $annormal,
                            'Total_mesure' => $nbdiagtotal,
                            'Latest_result' => $latestdate,

                        );

                        return View::create($response, JsonResponse::HTTP_OK, []);
                    }
                }
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }

    /////////////il faut faire la correction ici/////////////

    /**
     * @Rest\Get("/api/latestPatientsResult", name ="latestpatient_result")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function getLatestPatientResult()
    {

        $a = array();
        $user = $this->getUser();
        $data = array(
            'id' => $user->getId(),
        );
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
            $Assigned = $repository->findBy(array('id_doctor' => $user->getId(), 'status' => 'Accepted', 'removed' => false));
            foreach ($Assigned as $dataa) {
                array_push($a, $dataa->getIdPatient());
            }

            $Measurerepository = $this->getDoctrine()->getRepository(Measure::class);
            $latestdatee = $Measurerepository->findByMesuremaxDate($a);

            $response = array(
                'Latest_INRresult' => $latestdatee,

            );

            return View::create($response, JsonResponse::HTTP_OK, []);
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }

    /**
     * @Rest\Get("/api/countDoctor", name ="doctorNumber")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function hospitalcountDoctor()
    {
        $a = array();
        $user = $this->getUser();
        $data = array(
            'id' => $user->getId(),
        );
        if ($user->getUserType() === UserType::TYPE_HOSPITAL) {
            $patientrepository = $this->getDoctrine()->getRepository(Hospital::class);
            $hospital = $patientrepository->findOneBy(array('created_by' => $user->getId()));

            $patientrepository = $this->getDoctrine()->getRepository(Doctor::class);
            $doctor = $patientrepository->findBy(array('hospital' => $hospital->getId(), 'affiliate' => true));

            $number = count($doctor);

            $patientrepository = $this->getDoctrine()->getRepository(Hospital::class);
            $hospital = $patientrepository->findOneBy(array('created_by' => $user->getId()));

            $patientrepository = $this->getDoctrine()->getRepository(Doctor::class);
            $doctor = $patientrepository->findBy(array('hospital' => $hospital->getId(), 'affiliate' => true));
            foreach ($doctor as $dataa) {
                $doctors[] = $dataa->getCreatedBy()->getId();
            }
            $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
            $Assigned = $repository->findBy(array('id_doctor' => $doctors, 'status' => 'Accepted', 'removed' => false));
            $numberr = count($Assigned);
            $patientrepository = $this->getDoctrine()->getRepository(Hospital::class);
            $hospital = $patientrepository->findOneBy(array('created_by' => $user->getId()));

            $patientrepository = $this->getDoctrine()->getRepository(Doctor::class);
            $doctor = $patientrepository->findBy(array('hospital' => $hospital->getId(), 'affiliate' => true));
            foreach ($doctor as $dataa) {
                $doctors[] = $dataa->getCreatedBy()->getId();
            }
            $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
            $Assigned = $repository->findBy(array('id_doctor' => $doctors, 'status' => 'Accepted', 'removed' => false));
            foreach ($Assigned as $dataa) {
                $a[] = $dataa->getIdPatient();
            }

            if (!is_null($Assigned)) {
                $Measurerepository = $this->getDoctrine()->getRepository(Measure::class);
                $Measuretotal = $Measurerepository->findBy(array('created_by' => $a));
                $total = count($Measuretotal);

                $response = array(
                    'DoctorNumber' => $number,
                    'PatientNumber' => $numberr,
                    'ResultINR_Total' => $total,

                );
                return View::create($response, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
            }
        }
    }

    /**
     * @Rest\Get("/api/mesureByGender", name ="hospital_mesureGender")
     * @Rest\View(serializerGroups={"users"})
     */
    public function hospitalMeasureBygender()
    {
        $indication = 'anormal mesure';
        $a = array();
        $user = $this->getUser();
        $data = array(
            'id' => $user->getId(),
        );
        if ($user->getUserType() === UserType::TYPE_HOSPITAL) {
            $patientrepository = $this->getDoctrine()->getRepository(Hospital::class);
            $hospital = $patientrepository->findOneBy(array('created_by' => $user->getId()));
            $patientrepository = $this->getDoctrine()->getRepository(Doctor::class);
            $doctor = $patientrepository->findBy(array('hospital' => $hospital->getId(), 'affiliate' => true));
            foreach ($doctor as $dataa) {
                $doctors[] = $dataa->getCreatedBy()->getId();
            }
            $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
            $Assigned = $repository->findBy(array('id_doctor' => $doctors, 'status' => 'Accepted', 'removed' => false));
            foreach ($Assigned as $dataa) {
                $a[] = $dataa->getIdPatient();
            }
            if (!is_null($Assigned)) {
                $Measurerepository = $this->getDoctrine()->getRepository(Measure::class);
                $Measure = $Measurerepository->getassigned($a, $indication);
                $Measurettotal = $Measurerepository->findBy(array('created_by' => $a));
                $total = count($Measure);
                $totale = count($Measurettotal);

                if (!is_null($Measure)) {
                    $NBMale = 0;
                    foreach ($Measure as $dataa) {
                        if ($dataa->getCreatedBy()->getGender() == "Male") {
                            $NBMale = $NBMale + 1;
                        }
                    }
                    if ($NBMale == 0) {
                        $purcentageMale = "0%";
                    } else {
                        $purcentageMale = strval(intval(round($NBMale * 100 / $total))) . "%";
                    }
                    $NBFemale = 0;
                    foreach ($Measure as $dataa) {
                        if ($dataa->getCreatedBy()->getGender() == "Female") {
                            $NBFemale = $NBFemale + 1;
                        }
                    }
                    if ($NBFemale == 0) {
                        $purcentaFemale = "0%";
                    } else {
                        $purcentaFemale = strval(intval(round($NBFemale * 100 / $total))) . "%";
                    }


                    $response = array(
                        'Anormal_MaleMesure' => $purcentageMale,
                        'Anormal_FemaleMesure' => $purcentaFemale,
                    );
                    return View::create($response, JsonResponse::HTTP_OK, []);
                }
            }
        }
        $indication = 'anormal mesure';
        $a = array();
        $user = $this->getUser();
        $data = array(
            'id' => $user->getId(),
        );
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
            $Assigned = $repository->findBy(array('id_doctor' => $user->getId(), 'status' => 'Accepted', 'removed' => false));
            foreach ($Assigned as $dataa) {
                $a[] = $dataa->getIdPatient();
            }
            if (!is_null($Assigned)) {
                $indication = 'anormal mesure';
                $Measurerepository = $this->getDoctrine()->getRepository(Measure::class);
                $Measure = $Measurerepository->getassigned($a, $indication);
                $Measurettotal = $Measurerepository->findBy(array('created_by' => $a));
                $total = count($Measure);
                $totale = count($Measurettotal);
                if (!is_null($Measure)) {
                    if ($total == 0) {
                        $response = array(
                            'Anormal_MaleMesure' => "0%",
                            'Anormal_FemaleMesure' => "0%",
                        );
                        return View::create($response, JsonResponse::HTTP_OK, []);
                    }
                    $NBMale = 0;
                    foreach ($Measure as $dataa) {
                        if ($dataa->getCreatedBy()->getGender() == "Male") {
                            $NBMale = $NBMale + 1;
                        }
                    }
                    if ($NBMale == 0) {
                        $purcentageMale = "0%";
                    } else {
                        $purcentageMale = strval(intval(round($NBMale * 100 / $total))) . "%";
                    }
                    $NBFemale = 0;
                    foreach ($Measure as $dataa) {
                        if ($dataa->getCreatedBy()->getGender() == "Female") {
                            $NBFemale = $NBFemale + 1;
                        }
                    }
                    if ($NBFemale == 0) {
                        $purcentaFemale = "0%";
                    } else {
                        $purcentaFemale = strval(intval(round($NBFemale * 100 / $total))) . "%";
                    }


                    $response = array(
                        'Anormal_MaleMesure' => $purcentageMale,
                        'Anormal_FemaleMesure' => $purcentaFemale,
                    );
                    return View::create($response, JsonResponse::HTTP_OK, []);
                }
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }

    /**
     * @Rest\Get("/api/doctorActivity/{id}", name ="Doctor_Activity")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function doctorActivity($id)
    {
        $a = array();
        $user = $this->getUser();
        $data = array(
            'id' => $user->getId(),
        );
        if ($user->getUserType() === UserType::TYPE_HOSPITAL) {
            $doctorrepository = $this->getDoctrine()->getRepository(Doctor::class);
            $Doctor = $doctorrepository->findOneBy(array('id' => $id));
            $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
            $Assigned = $repository->findBy(array('id_doctor' => $id, 'status' => 'Accepted', 'removed' => false));
            if (!is_null($Assigned)) {
                foreach ($Assigned as $data) {
                    $a[] = $data->getIdPatient();
                }
                $patientrepository = $this->getDoctrine()->getRepository(User::class);
                $patient = $patientrepository->findBy(array('id' => $a));
                $Measurerepository = $this->getDoctrine()->getRepository(Measure::class);
                $Measure = $Measurerepository->findBy(array('created_by' => $a));
                $reportrepository = $this->getDoctrine()->getRepository(Note::class);
                $report = $reportrepository->findBy(array('created_by' => $Doctor->getCreatedBy()->getId(), 'remove' => false));
                $treatmentrepository = $this->getDoctrine()->getRepository(Treatment::class);
                $treatment = $treatmentrepository->findBy(array('created_by' => $id, 'remove' => false));
                $patientNumber = count($patient);
                $Measurenumber = count($Measure);
                $reportNumber = count($report);
                $treatment = count($treatment);
                $response = array(
                    'PatientNumber' => $patientNumber,
                    'ResultINRNumber' => $Measurenumber,
                    'MedicationNumber' => $treatment,
                    'MedicalReport' => $reportNumber,

                );
                return View::create($response, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('patient not found', JsonResponse::HTTP_NOT_FOUND, []);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }

    /**
     * @Rest\Get("/api/count", name ="admin_statistique")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function Adminstatistics()
    {
        $user = $this->getUser();

        if ($user->getUserType() === UserType::TYPE_ADMIN) {
            $repository = $this->getDoctrine()->getRepository(Patient::class);
            $patient = $repository->findAll();
            $countpatient = count($patient);
            $repository = $this->getDoctrine()->getRepository(Doctor::class);
            $doctor = $repository->findAll();
            $countdoctor = count($doctor);
            $repository = $this->getDoctrine()->getRepository(Hospital::class);
            $hospital = $repository->findAll();
            $counthospital = count($hospital);
            $repository = $this->getDoctrine()->getRepository(Asset::class);
            $assets = $repository->findAll();
            $countassets = count($assets);
            $repository = $this->getDoctrine()->getRepository(Session::class);
            $session = $repository->findAll();
            $countsession = count($session);
            $repository = $this->getDoctrine()->getRepository(Device::class);
            $device = $repository->findAll();
            $countdevice = count($device);
            $response = array(
                'patient' => $countpatient,
                'doctor' => $countdoctor,
                'hospital' => $counthospital,
                'assets' => $countassets,
                'session' => $countsession,
                'device' => $countdevice,

            );
            return View::create($response, JsonResponse::HTTP_OK, []);
        } else {

            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }

    /**
     * @Rest\Get("/api/doctorActivity", name ="DoctorUser_Activity")
     * @Rest\View(serializerGroups={"doctors"})
     */
    public function doctorActivityhistory()
    {
        $a = array();
        $user = $this->getUser();
        $data = array(
            'id' => $user->getId(),
        );
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
            $Assigned = $repository->findBy(array('id_doctor' => $user->getId(), 'status' => 'Accepted', 'removed' => false));
            if (!is_null($Assigned)) {
                foreach ($Assigned as $data) {
                    $a[] = $data->getIdPatient();
                }
                $patientrepository = $this->getDoctrine()->getRepository(User::class);
                $patient = $patientrepository->findBy(array('id' => $a));

                $Measurerepository = $this->getDoctrine()->getRepository(Measure::class);
                $Measure = $Measurerepository->findBy(array('created_by' => $a));
                $reportrepository = $this->getDoctrine()->getRepository(Note::class);
                $report = $reportrepository->findBy(array('created_by' => $user->getId(), 'remove' => false));
                $treatmentrepository = $this->getDoctrine()->getRepository(Treatment::class);
                $treatment = $treatmentrepository->findBy(array('created_by' => $user->getId(), 'remove' => false));

                $patientNumber = count($patient);
                $Measurenumber = count($Measure);
                $reportNumber = count($report);
                $treatment = count($treatment);

                $response = array(
                    'PatientNumber' => $patientNumber,
                    'ResultINRNumber' => $Measurenumber,
                    'MedicationNumber' => $treatment,
                    'MedicalReport' => $reportNumber,

                );
                return View::create($response, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('patient not found', JsonResponse::HTTP_NOT_FOUND, []);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }

    /**
     * @Rest\Get("/api/latestMesureByPatient/{id}", name ="count_latestMesure")
     * @Rest\View(serializerGroups={"users"})
     */
    public function countMesureBypatient($id)
    {
        $a = array();
        $user = $this->getUser();
        $data = array(
            'id' => $user->getId(),
        );

        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $repository = $this->getDoctrine()->getRepository(User::class);
            $userverife = $repository->findBy(array('id' => $id));
            if (!empty($userverife)) {
                $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
                $Assigned = $repository->findBy(array(
                    'id_doctor' => $user->getId(), 'id_patient' => $id,
                    'status' => 'Accepted', 'removed' => false
                ));
                if (!empty($Assigned)) {
                    $Measurerepository = $this->getDoctrine()->getRepository(Measure::class);
                    $Measure = $Measurerepository->findByLatestMesureByPatient($id);
                    return View::create($Measure, JsonResponse::HTTP_OK, []);
                } else {
                    return View::create('you are not allowed', JsonResponse::HTTP_FORBIDDEN, []);
                }
            } else {
                return View::create('user not found', JsonResponse::HTTP_NOT_FOUND, []);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }
    /**
     * @Rest\Get("/api/latestMesureByPatient", name ="countUser_latestMesure")
     * @Rest\View(serializerGroups={"users"})
     */
    public function countMesureByuser()
    {
        $a = array();
        $user = $this->getUser();
        $data = array(
            'id' => $user->getId(),
        );
        if ($user->getUserType() === UserType::TYPE_PATIENT) {
            $Measurerepository = $this->getDoctrine()->getRepository(Measure::class);
            $Measure = $Measurerepository->findByLatestMesureByPatient($user->getId());
            if (!empty($Measure)) {
                return View::create($Measure, JsonResponse::HTTP_OK, []);
            } else {
                return View::create('there is no latest measure !', JsonResponse::HTTP_NOT_FOUND[]);
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }

    /**
     * @Rest\Get("/api/unreadMeasure/{id}", name ="count_readedMeasure")
     * @Rest\View(serializerGroups={"users"})
     */
    public function countReadedMesure($id)
    {
        $user = $this->getUser();
        $data = array(
            'id' => $user->getId(),
        );
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
                $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
                $doctorassignement = $repository->findBy(array('id_doctor' => $user->getId(),'id_patient'=>$id, 'status' => 'Accepted', 'removed' => false));
                if (!empty($doctorassignement)) {
                    $repository = $this->getDoctrine()->getRepository(Measure::class);
                    $Measure = $repository->findBy(array('created_by' => $id,'readed' => false));
                  
                    $count=count($Measure);
                    $response = array(
                        'unreaded_Measure' => $count,
                    );
                    return View::create($response, JsonResponse::HTTP_OK, []);
                   
                } else {
                    return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                }
            
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }
    /**
     * @Rest\Patch("/api/unreadMeasure/{id}", name ="update_readedMeasure")
     * @Rest\View(serializerGroups={"users"})
     */
    public function updateReadedMesure($id,Request $request)
    {
        $user = $this->getUser();
        $data = array(
            'id' => $user->getId(),
        );
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $readed = $request->request->get('readed');
            if(isset($readed)){
                if($readed === true){
            $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
                $doctorassignement = $repository->findBy(array('id_doctor' => $user->getId(), 'status' => 'Accepted', 'removed' => false));
                if (!empty($doctorassignement)) {
                    foreach ($doctorassignement as $data) {
                        $a[] = $data->getIdPatient();
                    }
                    $repository = $this->getDoctrine()->getRepository(Measure::class);
                    $Measure = $repository->findOneBy(array('id'=>$id,'created_by' => $a,'readed' => false));
                    if (!is_null($Measure)){
                        $Measure->setReaded(true); 
                        $em = $this->getDoctrine()->getManager();
                        $em->flush();
                        return View::create("Measure readed", JsonResponse::HTTP_OK, []);         
                    }
                } else {
                    return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
                }
            }
            else{
                return View::create('readed must be true!', JsonResponse::HTTP_BAD_REQUEST, []);
            }
            }else{
                return View::create('missing readed measure!', JsonResponse::HTTP_BAD_REQUEST, []);
            }
            
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }
    
    /**
     * @Rest\Get("/api/mesureByAge", name ="measure_age")
     * @Rest\View(serializerGroups={"users"})
     */
    public function MeasureByage()
    {
       
        $indication = 'anormal mesure';
        $a = array();
        $user = $this->getUser();
        $data = array(
            'id' => $user->getId(),
        );
      
    
        if ($user->getUserType() === UserType::TYPE_DOCTOR) {
            $repository = $this->getDoctrine()->getRepository(DoctorAssignement::class);
            $Assigned = $repository->findBy(array('id_doctor' => $user->getId(), 'status' => 'Accepted', 'removed' => false));
            foreach ($Assigned as $dataa) {
                $a[] = $dataa->getIdPatient();
            }
            if (!is_null($Assigned)) {
                $indication = 'anormal mesure';
                $Measurerepository = $this->getDoctrine()->getRepository(Measure::class);
                $Measure = $Measurerepository->getassigned($a, $indication);
          
                $Measurettotal = $Measurerepository->findBy(array('created_by' => $a));
                $total = count($Measure);
                 $totale = count($Measurettotal);
                if (!is_null($Measure)) {
                    if ($total == 0) {
                        $response = array(
                        '0-10' => "0%",
                        '10-20' => "0%",
                        '20-30' => "0%",
                        '30-40' => "0%",
                        '40-50' => "0%",
                        '50-60' => "0%",
                        '60-70' => "0%",
                        '70-80' => "0%",
                        '80-90' => "0%",
                        '90-100' => "0%",
                        );
                     
                        return View::create($response, JsonResponse::HTTP_OK, []);
                    }
                    $NB10 = 0;
                    $NB20 = 0;
                    $NB30 = 0;
                    $NB40 = 0;
                    $NB50 = 0;
                    $NB60 = 0;
                    $NB70 = 0;
                    $NB80 = 0;
                    $NB90 = 0;
                    $NB100 = 0;
                    foreach ($Measure as $dataa) {
                      $birth=  intval($dataa->getCreatedBy()->getBirthDate()->format("Y"));
                      $year=intval(date("Y"));
                      $age_patient=$year-$birth;
                        if ($age_patient >= 1 && $age_patient <= 10) {
                            $NB10 = $NB10 + 1;
                        }
                        if ($age_patient > 10 && $age_patient <= 20) {
                            $NB20 = $NB20 + 1;
                        }
                        
                        if ($age_patient > 20 && $age_patient <= 30) {
                            $NB30 = $NB30 + 1;
                        }
                       
                        if ($age_patient > 30 && $age_patient <= 40) {
                            $NB40 = $NB40 + 1;
                        }
                        if ($age_patient > 40 && $age_patient <= 50) {
                            $NB50 = $NB50 + 1;
                        }
                        if ($age_patient > 50 && $age_patient <=60) {
                            $NB60 = $NB60 + 1;
                        }
                        if ($age_patient > 60 && $age_patient <=70) {
                            $NB70 = $NB70 + 1;
                        }
                        if ($age_patient > 70 && $age_patient <= 80) {
                            $NB80 = $N80 + 1;
                        }
                        if ($age_patient > 80 && $age_patient <= 90) {
                            $NB90 = $NB90 + 1;
                        }
                        if ($age_patient > 90 && $age_patient <= 100) {
                            $NB100 = $NB100 + 1;
                        }
                    }
                
                    if ($NB10 == 0) {
                        $purcentage10= "0%";
                    } else {
                        $purcentage10 = strval(intval(round($NB10 * 100 / $total))) . "%";
                    }
                    if ($NB20 == 0) {
                        $purcentage20= "0%";
                    } else {
                        $purcentage20 = strval(intval(round($NB20 * 100 / $total))) . "%";
                    }
                    if ($NB30 == 0) {
                        $purcentage30= "0%";
                    } else {
                        $purcentage30 = strval(intval(round($NB30 * 100 / $total))) . "%";
                    }
                    if ($NB40 == 0) {
                        $purcentage40= "0%";
                    } else {
                        $purcentage40 = strval(intval(round($NB40 * 100 / $total))) . "%";
                    }
                    if ($NB50 == 0) {
                        $purcentage50= "0%";
                    } else {
                        $purcentage50 = strval(intval(round($NB50 * 100 / $total))) . "%";
                    }
                    if ($NB60 == 0) {
                        $purcentage60= "0%";
                    } else {
                        $purcentage60 = strval(intval(round($NB60 * 100 / $total))) . "%";
                    }
                    if ($NB70 == 0) {
                        $purcentage70= "0%";
                    } else {
                        $purcentage70 = strval(intval(round($NB70 * 100 / $total))) . "%";
                    }
                    if ($NB80 == 0) {
                        $purcentage80= "0%";
                    } else {
                        $purcentage80 = strval(intval(round($NB80 * 100 / $total))) . "%";
                    }
                    if ($NB90 == 0) {
                        $purcentage90= "0%";
                    } else {
                        $purcentage90 = strval(intval(round($NB90 * 100 / $total))) . "%";
                    }
                    if ($NB100 == 0) {
                        $purcentage100= "0%";
                    } else {
                        $purcentage100 = strval(intval(round($NB100 * 100 / $total))) . "%";
                    }
            
                    $response = array(
                        '0-10' => $purcentage10,
                        '10-20' => $purcentage20,
                        '20-30' => $purcentage30,
                        '30-40' => $purcentage40,
                        '40-50' => $purcentage50,
                        '50-60' => $purcentage60,
                        '60-70' => $purcentage70,
                        '70-80' => $purcentage80,
                        '80-90' => $purcentage90,
                        '90-100' => $purcentage100,
                    
                    );
                    return View::create($response, JsonResponse::HTTP_OK, []);
                }
            }
        } else {
            return View::create('Not Authorized', JsonResponse::HTTP_FORBIDDEN, []);
        }
    }
    

}