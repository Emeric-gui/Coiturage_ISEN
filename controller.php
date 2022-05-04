<?php
    session_start();
function dbConnect(){
    try{
        $db = new PDO('pgsql:host=localhost;port=5433;dbname=test_covoit_isen', 'testapp', 'test');
    }catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }
    return $db;
}

function checkPost($param){
    if($param == "register"){
        if (isset($_POST['name']) && isset($_POST['fname']) && isset($_POST['mail']) && isset($_POST['password']) && isset($_POST['passwordConf']) && isset($_POST['promo']) && isset($_POST['num_tel'])){
            return true;
        }
        return false;
    }else if ($param == "login"){
        if(isset($_POST['mail']) && isset($_POST['password'])){
            return true;
        }
        return false;
    }else if ($param == "suppr"){
        if(isset($_POST['id_suppr'])){
            return true;
        }
    }else if($param =="modifAccount"){
        if(isset($_POST['num_tel']) && isset($_POST['promo']) && isset($_POST['AncPassword'])  && isset($_POST['NewPassword']) && isset($_POST['NewPasswordConf'])){
            return true;
        }
    }else if($param == "submitTrip"){
        if(isset($_POST['villeDep']) && isset($_POST['villeArr']) && isset($_POST['dateDepart']) && isset($_POST['heureDepart']) && isset($_POST['typeVoiture'])
            && isset($_POST['couleurVoiture']) && isset($_POST['plaqueImma']) && isset($_POST['nbPlace']) && isset($_POST['nbBagage'])
            && isset($_POST['prix']) && $_POST['confirmationPermis'] && isset($_POST['descriptionsupp'])){
            return true;
        }
    }else if($param == "validResearchTrip"){
        if(isset($_GET["id_trajet"]) && isset($_SESSION['id'])){
            return true;
        }
    }
    return false;
}

function verifHoraire($date){//date est en mode dateInterval
    if(($date->y > 0 || $date->m > 0 || $date->d > 0 || $date->h > 0 || $date->i > 0) && ($date->format("%R") == "-" )){
        return true;
    }
    return false;
}

function getTime(){
//    $time = mktime(20,00,00,5,28,2021);//heure de fin enregistrement
//
//    $dateConcours = date_create(date('d-m-Y H:i:s', $time));
//
//    $dataToday = date('d-m-Y H:i:s', time());
//    $dateToday = date_create($dataToday);
//
//    $date = date_diff($dateConcours, $dateToday);
//
//    $days = $date->days;//days pour le nombre de jours restant et d pour afficher par rapport au mois
//    $hours = $date->h;
//    $minutes = $date->i;
//    $seconds = $date->s;
//    $dateRetour = $date->days.':'.$date->h.':'.$date->i.':'.$date->s;
//    $array = array('date'=>$dateRetour);
//    $json = json_encode($array);
//    print_r($json);
}


//Pour enregistrer un nouvel utilisateur
function register(){

    $texteReturn = "";
    if(checkPost("register")){
        $db = dbConnect();

        $errTel = 0;
        $errPass = 0;
        $errMail = 0;
        $value = 0;

        $mail = htmlspecialchars($_POST['mail']);
        $name = htmlspecialchars($_POST['name']);
        $fname = htmlspecialchars($_POST['fname']);
        $num_tel = htmlspecialchars($_POST['num_tel']);
        $promo = htmlspecialchars($_POST['promo']);
        $pass1 = htmlspecialchars($_POST['password']);
        $pass1Conf = htmlspecialchars($_POST['passwordConf']);


        //verif num de telephone
        $telRegex = '#^0[1-9]-[0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}$#';

        if(preg_match($telRegex, $num_tel) ===0){//erreur login 2
            //si la value ne respecte pas la regex
            $errTel = 1;
            $value =1;
        }

        //verif adresse mail si err -> 0
        if(preg_match("#^[a-z0-9_.-]+.[a-z0-9_.-]+@isen-ouest.yncrea.fr$#", $mail) === 0){
            $value = 1;
            $errMail = 1;
        }else{
            $req_pseudo = $db->prepare('select * from usercov where mail = :mail');
            $req_pseudo->execute(array('mail'=>$mail));
            //verif disponibilite pseudo
            if(!empty($donnes = $req_pseudo->fetch())){//erreur login 1
                $req_pseudo ->closeCursor();
                $errMail = 1;
                $value = 1;
            }
        }

        //verif password
        if($pass1 != $pass1Conf){
            $value = 1;
            $errPass = 1;
        }

        if ($value ==1){
          $texteReturn = "?mdp=".$errPass."&mail=".$errMail."&tel=".$errTel;
          header('Location: ../content/register.php'.$texteReturn.'');
        }

        if($value == 0){
            //hachage du mot de passe
            $pass_hache = password_hash($pass1, PASSWORD_DEFAULT);

            $reqInsertion = $db->prepare('insert into usercov (name, fname, mail, password, num_tel, promo, conducteur) values (:name, :fname, :mail, :password, :num_tel, :promo, false)');
            $reqInsertion->execute(array(
                'name'=> $name,
                'fname'=>$fname,
                'mail'=>$mail,
                'password'=>$pass_hache,
                'num_tel'=>$num_tel,
                'promo'=>$promo
            ));
            header('Location: ../index.php');
        }
    }else{
        header("Location: ../content/register.php");
    }
}

//Connexion au serveur
function login(){


    if((checkPost('login') || (isset($_COOKIE['mail']) && isset($_COOKIE['password']))) && $_SESSION['mdpErrone'] < 4){
        $db = dbConnect();

        $mail = '';
        $password = '';
        $cookie = false;
        if(isset($_COOKIE['mail']) && isset($_COOKIE['password'])){
            $mail = htmlspecialchars($_COOKIE['mail']);
            $password = htmlspecialchars($_COOKIE['password']);
            $cookie = true;
        }else{
            $mail = htmlspecialchars($_POST['mail']);
            $password = htmlspecialchars($_POST['password']);
        }


        $req_password = $db->prepare('select * from usercov where mail=:mail;');
        $req_password->execute(array('mail'=>$mail));

        $dataUser = $req_password->fetch();
        if(!empty($dataUser)){
            $passwordRequested = $dataUser['password'];
            $isValid = false;

            if($cookie){
                if($password == $passwordRequested){//les 2 mdp sont chiffres
                    $isValid = true;
                }
            }else{//1 des 2 mdp est chiffre
                $isValid = password_verify($password, $passwordRequested);
            }


            if($isValid){
                $_SESSION['id'] = $dataUser['id_user'];
                $_SESSION['mail'] = $dataUser['mail'];
                $_SESSION['conducteur'] = $dataUser['conducteur'];
                $_SESSION['mdpErrone'] = 0;

                if(isset($_POST['stayConnected'])){
                    //faire un cookie avec username et mot de passe
                    setcookie('mail', $mail, time() + 365*24*3600, '/', null, false, true);
                    setcookie('password', $passwordRequested, time() + 365*24*3600, '/', null, false, true);//le mdp est haché et valable 1 year
                }
                header('Location: ../content/myaccount.php');
            }else{//erreur mdp renvoi m erreur pr mdp et pseudo
                header('Location: ../content/login.php?err=0');
            }
        }else{//erreur pseudo
            header('Location: ../content/login.php?err=0');
        }

    }else{//si probleme dans les champs entrees
        header('Location: ../content/login.php');
    }
}

//Deconnexion du serveur / navigateur (cache / cookies)
function logout(){//popup deconnexion
    $_SESSION = array();
    session_destroy();
    setcookie('mail', '', time() -3600, '/', null, false, true);
    setcookie('password', '', time() -3600, '/', null, false, true);
    header('Location: ../index.php');
}


//POur des tests
function insertTest(){//insertion via admin

    $texteReturn = "";
    if(checkPost("register")){
        $db = dbConnect();

        $errPass = 0;
        $errTel = 0;
        $errMail = 0;
        $value = 0;

        $mail = htmlspecialchars($_POST['mail']);
        $name = htmlspecialchars($_POST['name']);
        $fname = htmlspecialchars($_POST['fname']);
        $pass1 = htmlspecialchars($_POST['password']);
        $pass1Conf = htmlspecialchars($_POST['passwordConf']);
        $num_tel = htmlspecialchars($_POST['num_tel']);
        $promo = htmlspecialchars($_POST['promo']);

        //verif num de telephone
        $telRegex = '#^0[1-9]-[0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}$#';

        if(preg_match($telRegex, $num_tel) ===0){//erreur login 2
            //si la value ne respecte pas la regex
            $errTel = 1;
            $value =1;
        }

        $req_mail = $db->prepare('select * from usercov where mail = :mail;');
        $req_mail->execute(array('mail'=>$mail));


        //verif adresse mail si err -> 0
        if(preg_match("#^[a-z0-9_.-]+.[a-z0-9_.-]+@isen-ouest.yncrea.fr$#", $mail) === 0){
            $value = 1;
            $errMail = 1;
        }else if(!empty($donnes = $req_mail->fetch())){
            $req_mail ->closeCursor();
            $errMail = 1;
            $value = 1;
        }

        //verif password
        if($pass1 != $pass1Conf){
            $value = 1;
            $errPass = 1;
        }

        if ($value ==1){
            $texteReturn = "?mdp=".$errPass."&mail=".$errMail."&tel=".$errTel;
            header('Location: ../admin/testBDD.php'.$texteReturn.'');
        }

        if($value == 0){
            //hachage du mot de passe
            $pass_hache = password_hash($pass1, PASSWORD_DEFAULT);
            // il faut creer un objet competition avant

            $reqInsertion = $db->prepare('insert into usercov (name, fname, mail, password, num_tel, promo, conducteur) values (:name, :fname, :mail, :password, :num_tel, :promo, :conducteur)');
            $reqInsertion->execute(array(
                'name'=> $name,
                'fname'=>$fname,
                'mail'=>$mail,
                'password'=>$pass_hache,
                'num_tel'=>$num_tel,
                'promo'=>$promo,
                'conducteur'=>false
            ));

            header('Location: ../admin/testBDD.php?ok=1');
        }
    }else{
        header("Location: ../admin/testBDD.php?errChamp=1");
    }
}

function supprTest(){
    if(checkPost("suppr")){
        $db = dbConnect();

        $id_usr = htmlspecialchars($_POST['id_suppr']);

        $req_id = $db->prepare('select * from usercov where id_user = :id_user;');
        $req_id->execute(array('id_user'=>$id_usr));

        //verif existance compte
        if(empty($donnes = $req_id->fetch())){
            $req_id ->closeCursor();
            $errSuppr = 1;
            $value = 1;
        }

        if ($value ==1){
            $texteReturn = "?errSuppr=".$errSuppr;
            header('Location: ../admin/testBDD.php'.$texteReturn.'');
        }

        if ($value == 0){

            $req_supp_id = $db->prepare('delete from usercov where id_user = :id_user;');
            $req_supp_id->execute(array('id_user'=>$id_usr));

            header('Location: ../admin/testBDD.php');
        }
    }else{
        header('Location: ../admin/testBDD.php?errChamp=1');
    }

}
//FIn pour tests


function verifNameCity($ville): string
{
    $villeRetour = "";
    //Si pas espace --> Tout ok

    $tmpLowerCity = strtolower($ville);

    //check si espace dans le nom, si oui, on récupère l'indice de l'espace pour mettre une majuscule a la lettre d'après
    $villeAvecEspace = explode(" ", $tmpLowerCity);

    if(sizeof($villeAvecEspace) >=2){
        for($i = 0; $i<sizeof($villeAvecEspace);$i++){
            $partOfCity = $villeAvecEspace[$i];
            $firstLetterPart = substr($partOfCity, 0, 1);
            $lastLettersPart = substr($partOfCity, 1);
            $firstLetterPart = strtoupper($firstLetterPart);
            if($i> 0){
                $villeRetour = $villeRetour." ".$firstLetterPart.$lastLettersPart;
            }else{
                $villeRetour = $villeRetour.$firstLetterPart.$lastLettersPart;
            }

        }
    }else{
        $firstLetterCity = substr($tmpLowerCity, 0, 1);
        $lastLettersCity = substr($tmpLowerCity, 1);
        $firstLetterCity = strtoupper($firstLetterCity);
        $villeRetour = $firstLetterCity.$lastLettersCity;
    }
    return $villeRetour;
}

//QUand on veut partager un trajet
function submitTrip(){
    //si un utilisateur propose un trajet --> on regarde s'il en a deja fait --> sinon change un bool pour dire qu'il est maintenant conducteur
    if(!checkPost("submitTrip")){
        header('Location: ../content/present_road.php');
    }else{
       $villeDep = htmlspecialchars($_POST['villeDep']);
       $villeArr = htmlspecialchars($_POST['villeArr']);
       $dateDepart = htmlspecialchars($_POST['dateDepart']);
       $heureDepart = htmlspecialchars($_POST['heureDepart']);
       $typeVoiture = htmlspecialchars($_POST['typeVoiture']);
       $couleurVoiture = htmlspecialchars($_POST['couleurVoiture']);
       $plaqueImma = htmlspecialchars($_POST['plaqueImma']);
       $nbPlace = htmlspecialchars($_POST['nbPlace']);
       $nbBagage = htmlspecialchars($_POST['nbBagage']);
       $prix = htmlspecialchars($_POST['prix']);
       $description = htmlspecialchars($_POST['descriptionsupp']);
       if(empty($description)){
           $description = 'NULL';
       }

       $value = 0;
       $errDate = 0;
       $errPlaque = 0;
       $errVille = 0;
       $errAutresChamps = 0;

       $regexPlaque = "#^[A-Z]{2}-[0-9]{3}-[A-Z]{2}$#";

        if(preg_match($regexPlaque, $plaqueImma) ===0){//erreur login 2
            //si la value ne respecte pas la regex
            $regex_vielle_plaque = "#^[0-9]{3} [A-Z]{3} [0-9]{2}$#";
            if(preg_match($regex_vielle_plaque, $plaqueImma) === 0){
                $errPlaque = 1;
                $value = 1;
            }
        }

        //verif heure + date
        $date_courante = date_create(date('d-m-Y H:i',time() +120)); // strtotime("now")

        $dateDonnee = explode('-', $dateDepart);
        $heureDonnee = explode(':', $heureDepart);
        $minute = $heureDonnee[1];
        $heure = $heureDonnee[0];

        $jour = $dateDonnee[2];
        $mois = $dateDonnee[1];
        $annee = $dateDonnee[0];


        $dateTrip = date_create(date('d-m-Y H:i', mktime($heure, $minute, 0, $mois, $jour, $annee)));
        $dateVerif = date_diff($dateTrip, $date_courante);


        if (!verifHoraire($dateVerif)){//si le nombre de minutes restantes est inférieure a 0
            $errDate = 1;
            $value = 1;
        }

        $regexVille = "#^[A-Za-z ]{2,}$#";
        //verif regex ville et si elles sont correctes
        if(preg_match($regexVille, $villeDep) == 0){
            $errVille = 1;
            $value = 1;
        }
        if(preg_match($regexVille, $villeArr) == 0){
            $errVille = 1;
            $value = 1;
        }

        if ($value ==1){
            $texteReturn = "?date=".$errDate."&plaque=".$errPlaque."&ville=".$errVille."&champs=".$errAutresChamps;
            header('Location: ../content/present_road.php'.$texteReturn.'');
        }else{
            $db = dbConnect();
            //si tout est ok au niveau des valeurs entrées
            $id_user = $_SESSION['id'];

            //regarde si user est deja conducteur ou pas
            $req_conducteurBool = $db->prepare('update usercov set conducteur = true where id_user=:id_user;');
            $req_conducteurBool->execute(array('id_user'=>$id_user));

            //ajout d'un trajet

            //-----------
                //On ajoute d'abord les villes de départ et d'arrivée

                /*  Verifie l'écriture des villes
                    On a verifié si ce sont des lettres avec les regexs
                    Il faut maintenant vérifier la casse
                */
            $villeDep = verifNameCity($villeDep);
            $villeArr = verifNameCity($villeArr);

            /**
             *  TODO --> Utilisation Google Maps API ou autre pour déterminer si ville existe & leur longitude et latitude
             */

            $checkVilleDep = $db->prepare('select count(*) as numberVillesDep from ville_dep where nom_dep=:villeDep');
            $checkVilleDep->execute(array('villeDep'=>$villeDep));
            $recuDep = $checkVilleDep->fetch();
            $checkVilleDep->closeCursor();

            if($recuDep['numberVilleDep'] == 0){
                $reqVilleDep = $db->prepare('insert into ville_dep(nom_dep, latitude_dep, longitude_dep) values(:villeDep, 0, 0);');
                $reqVilleDep->execute(array('villeDep'=>$villeDep));
            }



            $checkVilleArr = $db->prepare('select count(*) as numberVilleArr from ville_arr where nom_arr=:villeArr');
            $checkVilleArr->execute(array('villeArr'=>$villeArr));
            $recuArr =  $checkVilleArr->fetch();

            if($recuArr['numberVilleArr'] == 0){
                $reqVilleArr = $db->prepare('insert into ville_arr(nom_arr, latitude_arr, longitude_arr) values(:villeArr, 0, 0);');
                $reqVilleArr->execute(array('villeArr'=>$villeArr));
            }
            $checkVilleArr->closeCursor();

            //Recupérer maintenant les id de ville des 2 villes pour la bdd

            $reqRecupIdVilles = $db->prepare('select vd.id_ville_dep, va.id_ville_arr from ville_dep vd, ville_arr va where vd.nom_dep=:villeDep and va.nom_arr=:villeArr; ');
            $reqRecupIdVilles->execute(array('villeDep'=>$villeDep, 'villeArr'=>$villeArr));

            $donnesRecu = $reqRecupIdVilles->fetch();
            $id_ville_dep = $donnesRecu['id_ville_dep'];
            $id_ville_arr = $donnesRecu['id_ville_arr'];

            $reqRecupIdVilles->closeCursor();

            $req_trajet = $db->prepare('insert into trajet(id_conducteur, datedepart, heuredepart, typevoiture, couleurvoiture, plaqueimma, nbplace, nbbagage, prix, descriptionsupp, suppression, id_ville_dep, id_ville_arr)
        values(:id_conducteur, :dateDepart, :heureDepart, :typeVoiture, :couleurVoiture, :plaqueImma, :nombrePlace, :nombreBagage, :prix, :description, :suppression, :id_ville_dep, :id_ville_arr);');

        $req_trajet->execute(array('id_conducteur'=>$id_user,
            'dateDepart'=>$dateDepart,
            'heureDepart'=>$heureDepart,
            'typeVoiture'=>$typeVoiture,
            'couleurVoiture'=>$couleurVoiture,
            'plaqueImma'=>$plaqueImma,
            'nombrePlace'=>$nbPlace,
            'nombreBagage'=>$nbBagage,
            'prix'=>$prix,
            'description'=>$description,
            'suppression'=>0,
            'id_ville_dep'=>$id_ville_dep,
            'id_ville_arr'=>$id_ville_arr));

            //----------
            //l'ajout de l'id de trajet se faut automatiquement --> integer
            $_SESSION['conducteur'] = true;
            header('Location: ../content/myTrip.php');
        }
    }
}

//QUand un utilisateur valide sa place pour un trajet
function validResearchTrip(){
    if(!checkPost("validResearchTrip")){
        header('Location: ../content/result_research.php?errInfo=1');
    }else{
        $db = dbConnect();
        $i = 1;
        $recupNombrePlace = $db->prepare('select nbplace from trajet where id_trajet=:id_trajet');
        $recupNombrePlace->execute(array('id_trajet'=>$_GET['id_trajet']));
        $dataNbPlace = $recupNombrePlace->fetch();
        if(empty($dataNbPlace)){
            header('Location: ../content/result_research.php?errTrajet=1');
        }else{

            $nbPlace = $dataNbPlace['nbplace'];
            $reqUpdateTrajet = $db->prepare('update trajet set nbplace=:nbPlace where id_trajet=:id_trajet;');
            $reqUpdateTrajet->execute(array('nbPlace'=>$nbPlace-$i,
                                            'id_trajet'=>$_GET['id_trajet']));

            $reqPassager = $db->query('select count(*) as nbpassagerbdd from passagertrajet');
            $donnee = $reqPassager->fetch();
            if(!empty($donnee)){
                $reqAjoutConducteur = $db->prepare('insert into passagertrajet(id_trajet, id_user, id_passtrajet, validation) values(:id_trajet, :id_user, :id_passTrajet, :valid)');
                $reqAjoutConducteur->execute(array('id_trajet'=>$_GET['id_trajet'],
                    'id_user'=>$_SESSION['id'],
                    'id_passTrajet'=>$donnee['nbpassagerbdd'] +1,
                    'valid'=>0));

                header('Location: ../content/myTrip.php');
            }else{
                header('Location: ../content/result_research.php?errTrajet=1');
            }
            $reqPassager->closeCursor();
        }
        $recupNombrePlace->closeCursor();
    }
}


//Conducteur qui refuse un passager
function refuTrip(){
    $id_user = $_GET['id_passager'];
    $tripAnnul = $_GET['id_trajet'];

    $db = dbConnect();

    $reqCheckIfTrue = $db->prepare('select * from passagertrajet where id_trajet=:id_trajet and id_user=:id_user;');
    $reqCheckIfTrue->execute(array('id_trajet'=>$tripAnnul,
        'id_user'=>$id_user));

    if(empty($reqCheckIfTrue->fetch())){
        header('Location: ../content/myTrip.php?err=1');
    }else{
        $recupNbPlace = $db->prepare('select nbplace from trajet where id_trajet=:id_trajet;');
        $recupNbPlace->execute(array('id_trajet'=>$tripAnnul));

        $dataNbPlace = $recupNbPlace->fetch();
        $place = $dataNbPlace['nbplace'];
        $recupNbPlace->closeCursor();
        $reqUpdatePlace = $db->prepare('update trajet set nbplace=:newNbPlace where id_trajet=:id_trajet;');
        $reqUpdatePlace->execute(array('newNbPlace'=>$place+1, 'id_trajet'=>$tripAnnul));

        if(isset($_GET['valid']) && $_GET['valid']== 1){
            $reqSupprPassager = $db->prepare('update passagertrajet set validation=2 where id_trajet=:id_trajet and id_user=:id_user;');
            $reqSupprPassager->execute(array('id_trajet'=>$tripAnnul, 'id_user'=>$id_user));
        }

        header('Location: ../content/myTrip.php');
    }
    $reqCheckIfTrue->closeCursor();
}

//Passager annule sa réservation
function annulTrip(){
    $id_user = $_SESSION['id'];
    $tripAnnul = $_GET['id_trajet'];

    $db = dbConnect();

    $reqCheckIfTrue = $db->prepare('select * from passagertrajet where id_trajet=:id_trajet and id_user=:id_user;');
    $reqCheckIfTrue->execute(array('id_trajet'=>$tripAnnul,
                                    'id_user'=>$id_user));

    if(empty($reqCheckIfTrue->fetch())){
        header('Location: ../content/myTrip.php?err=1');
    }else{
        $recupNbPlace = $db->prepare('select nbplace from trajet where id_trajet=:id_trajet;');
        $recupNbPlace->execute(array('id_trajet'=>$tripAnnul));

        $dataNbPlace = $recupNbPlace->fetch();
        $place = $dataNbPlace['nbplace'];
        $recupNbPlace->closeCursor();
        $reqUpdatePlace = $db->prepare('update trajet set nbplace=:newNbPlace where id_trajet=:id_trajet;');
        $reqUpdatePlace->execute(array('newNbPlace'=>$place+1, 'id_trajet'=>$tripAnnul));

        $reqSupprPassager = $db->prepare('delete from passagertrajet where id_trajet=:id_trajet and id_user=:id_user;');
        $reqSupprPassager->execute(array('id_trajet'=>$tripAnnul, 'id_user'=>$id_user));

        header('Location: ../content/myTrip.php');
    }
    $reqCheckIfTrue->closeCursor();
}


//modification des informations personnelles d'un compte
function modifAccount(){
    if(checkPost("modifAccount")){
        $db = dbConnect();

        $errTel = 0;
        $errPass = 0;
        $value = 0;

        $mail = htmlspecialchars($_SESSION['mail']);
        $num_tel = htmlspecialchars($_POST['num_tel']);
        $promo = htmlspecialchars($_POST['promo']);
        $ancPass = htmlspecialchars($_POST['AncPassword']);
        $pass1 = htmlspecialchars($_POST['NewPassword']);
        $pass1Conf = htmlspecialchars($_POST['NewPasswordConf']);


        //verif num de telephone
        $telRegex = '#^[0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}$#';

        if(preg_match($telRegex, $num_tel) ===0){//erreur login 2
            //si la value ne respecte pas la regex
            $errTel = 1;
            $value =1;
        }


        //verif password
        if($pass1 != $pass1Conf){
            $value = 1;
            $errPass = 1;
        }else{
            $req_password = $db->prepare('select * from usercov where mail=:mail;');
            $req_password->execute(array('mail'=>$mail));

            $dataUser = $req_password->fetch();
            if(!empty($dataUser)) {
                $passwordRequested = $dataUser['password'];
                $req_password->closeCursor();
                $isValid = password_verify($ancPass, $passwordRequested);
                if(!$isValid){
                    $value = 1;
                    $errPass = 2;
                }
            }
        }

        if ($value ==1){
            $texteReturn = "?mdp=".$errPass."&tel=".$errTel;
            header('Location: ../content/modifAccount.php'.$texteReturn.'');
        }

        if($value == 0){
            //hachage du mot de passe
            $pass_hache = password_hash($pass1, PASSWORD_DEFAULT);

            $reqInsertion = $db->prepare(' update usercov set password = :password, num_tel = :num_tel, promo = :promo where id_user = :id_user;');
            $reqInsertion->execute(array(
                'password'=>$pass_hache,
                'num_tel'=>$num_tel,
                'promo'=>$promo,
                'id_user'=>$_SESSION['id']
            ));

            //modif password cookie
            if(isset($_COOKIE['password'])){
                //faire un cookie avec username et mot de passe
                setcookie('mail', $mail, time() + 365*24*3600, '/', null, false, true);
                setcookie('password', $pass_hache, time() + 365*24*3600, '/', null, false, true);//le mdp est haché et valable 1 year
            }

            header('Location: ../index.php');
        }
    }else{
        header("Location: ../content/myaccount.php");
    }
}


//Trajet annulé par le conducteur
function annulTripConducteur(){
    $id_trajet = htmlspecialchars($_GET['id_trajet']);
    $db = dbConnect();
    $reqExiste = $db->prepare('select * from trajet where id_trajet=:id_trajet;');
    $reqExiste->execute(array('id_trajet'=>$id_trajet));
    if(empty($reqExiste->fetch())){
        header('Location: ../content/myTrip.php?errSuppr=1');
    }else{

        //regarde si on a des passagers, sinon on passe directment a la suppression

        $reqCheckIfpassengers = $db->prepare('select count(*) as nbPassengers from passagertrajet where id_trajet=:id_trajet;');
        $reqCheckIfpassengers->execute(array('id_trajet'=>$id_trajet));
        $resultPassenger = $reqCheckIfpassengers->fetch();
        $reqCheckIfpassengers->closeCursor();
        if($resultPassenger['nbPassengers'] == 0){
            supprTripConducteur();
        }else{
            //On ne supprime pas les passagers, on leur indique que le trajet a été annulé
            $reqUpdatePassagers = $db->prepare('update passagertrajet set validation=3 where id_trajet=:id_trajet;');
            $reqUpdatePassagers->execute(array('id_trajet'=>$id_trajet));

            $reqUpdateMessage = $db->prepare('update trajet set  suppression=1 where id_trajet=:id_trajet;');
            $reqUpdateMessage->execute(array('id_trajet'=>$id_trajet));

            header('Location: ../content/myTrip.php');
        }
    }
    $reqExiste->closeCursor();
}

//Trajet supprimé par le conducteur
function supprTripConducteur(){
    $id_trajet = htmlspecialchars($_GET['id_trajet']);
    $db = dbConnect();
    $reqExist = $db->prepare('select * from trajet where id_trajet=:id_trajet;');
    $reqExist->execute(array('id_trajet'=>$id_trajet));
    if(empty($reqExist->fetch())){
        header('Location: ../content/myTrip.php?errSuppr=1');
    }else{
        //Effectué seulement si plus de passager Trajet
        //suppression trajet
        $reqSupprTrajet = $db->prepare('delete from trajet where id_trajet =:id_trajet;');
        $reqSupprTrajet->execute(array('id_trajet'=>$id_trajet));
        header('Location: ../content/myTrip.php');
    }
    $reqExist->closeCursor();
}

//Pour supprimer la réservation qui a été refusé par le conducteur
function annulTripValid(){
    $id_user = $_GET['id_user'];
    $tripAnnul = $_GET['id_trajet'];
    $db = dbConnect();

    $reqCheckIfTrue = $db->prepare('select * from passagertrajet where id_trajet=:id_trajet and id_user=:id_user;');
    $reqCheckIfTrue->execute(array('id_trajet'=>$tripAnnul,
        'id_user'=>$id_user));

    if(empty($reqCheckIfTrue->fetch())){
        header('Location: ../content/myTrip.php?err=1');
    }else{

        $reqSupprPassager = $db->prepare('delete from passagertrajet where id_trajet=:id_trajet and id_user=:id_user;');
        $reqSupprPassager->execute(array('id_trajet'=>$tripAnnul, 'id_user'=>$id_user));

        $reqUpdateSuppression = $db->prepare('update trajet set suppression=:suppression where id_trajet=:id_trajet;');

        if(checkIfNoMorePassenger($tripAnnul)){
            $reqUpdateSuppression->execute(array('suppression'=>2, 'id_trajet'=>$tripAnnul));
        }else{
            $reqUpdateSuppression->execute(array('suppression'=>1, 'id_trajet'=>$tripAnnul));
        }

        header('Location: ../content/myTrip.php');
    }
    $reqCheckIfTrue->closeCursor();
}

//A combiner avec annulTripValid pour vérifier s'il reste des passagers pour le trajet
function checkIfNoMorePassenger($id_trajet){
    $isNoPassenger = false;
    $db = dbConnect();
    $requestCheckPassenger = $db->prepare('select * from passagertrajet where id_trajet=:id_trajet;');
    $requestCheckPassenger->execute(array('id_trajet'=>$id_trajet));

    if(empty($requestCheckPassenger->fetch())){
        $isNoPassenger = true;
    }
    $requestCheckPassenger->closeCursor();
    return $isNoPassenger;
}

//valider un passager pour le trajet --> validé par le conducteur
function validation_passager(){
    $id_user = $_GET['id_passager'];
    $tripAnnul = $_GET['id_trajet'];
    $db = dbConnect();

    $reqCheckIfTrue = $db->prepare('select * from passagertrajet where id_trajet=:id_trajet and id_user=:id_user;');
    $reqCheckIfTrue->execute(array('id_trajet'=>$tripAnnul,
                                    'id_user'=>$id_user));

    if(empty($reqCheckIfTrue->fetch())){
        header('Location: ../content/myTrip.php?err=1');
    }else{
        $reqUpdateValid = $db->prepare('update passagertrajet set validation=1 where id_trajet=:id_trajet and id_user=:id_user');
        $reqUpdateValid->execute(array('id_trajet'=>$tripAnnul,
                                        'id_user'=>$id_user));
        header('Location: ../content/myTrip.php');
    }
}

if ($_GET['func'] == 'register') {
    register();
} else if ($_GET['func'] == 'login') {
    login();
} else if ($_GET['func'] == 'logout') {
    logout();
}else if($_GET['func'] == 'modifAccount'){
    modifAccount();
}else if($_GET['func'] == 'getTime'){
    getTime();
}else if($_GET['func'] == 'insertTest'){
    insertTest();
}else if($_GET['func'] == 'supprTest'){
    supprTest();
}else if($_GET['func'] == 'submitTrip') {
    submitTrip();
}else if($_GET['func']== "validResearchTrip"){
    validResearchTrip();
}else if($_GET['func']== "refuTrip"){
    refuTrip();
}else if($_GET['func']== "annulTrip"){
    annulTrip();
}else if($_GET['func']== "annulTripConducteur"){
    annulTripConducteur();
}else if($_GET['func'] == "supprTripConducteur"){
    supprTripConducteur();
}else if($_GET['func']== "annulTripValid"){
    annulTripValid();
}else if($_GET['func']== "validation"){
    validation_passager();
}else{//redirection classique pour eviter l'access a des pages non autorisées
    header('Location: ../index.php');
}
?>