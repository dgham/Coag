<?php
//getting json data specialitys from https://www.tobba.tn///
$url = 'https://api.keeplyna.com/api/tc_auth/specialities/';
echo'
https://api.keeplyna.com/api/tc_auth/specialities/...';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
$html = curl_exec($ch);
curl_close($ch);
$specialitys = json_decode(($html), true);
for ($i = 0; $i < 28; $i++) {
    $speciality_fr = mb_strtolower($specialitys[$i]['name_l1']);
    $speciality_fr=str_replace(",", " ", $speciality_fr);
    $speciality_fr=str_replace("'", "` ", $speciality_fr);
    $servername = "mysql.coagcare.continuousnet.com";
    $username = "coagcare";
    $password = "RLD8MIy1jNl";
    $dbname = "coagcare";
    try {
        $arr = [];
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = $conn->prepare("SELECT * from speciality where speciality_name LIKE '%$speciality_fr%'");
        $sql->execute();
        if ($sql->rowCount() > 0) {
            echo '';
        } else {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=UTF8", $username, $password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "INSERT INTO wp_speciality (id,created_by, speciality_name,created_at,removed)
                    VALUES (0,21,'$speciality_fr','2018-12-05 12:39:16',0";
            // use exec() because no results are returned
            echo '#';
            $conn->exec($sql);
        }
        $conn = null;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    $conn = null;
}
echo'
Done.
';
?>