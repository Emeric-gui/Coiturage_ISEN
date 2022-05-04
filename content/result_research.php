<?php session_start();
function dbConnect(){
    try{
        $db = new PDO('pgsql:host=localhost;port=5433;dbname=test_covoit_isen', 'testapp', 'test');
    }catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }
    return $db;
}
function verifHoraire($date){//date est en mode dateInterval
    if(($date->y > 0 || $date->m > 0 || $date->d > 0 || $date->h > 0 || $date->i > 0) && ($date->format("%R") == "-" )){
        return true;
    }
    return false;
}

function verifNameCity($ville){
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

    include('head.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<title>COVOIT'ISEN - resultats de la recherche</title>
</head>
<body>
<div id="page">
    <?php
    include ('navbar.php');
    if (isset($_GET['errTrajet'])) {
        if($_GET['errTrajet'] == 1){
            echo "<div class='alert alert-danger' role='alert'>
					Le trajet spécifié n'existe pas
					</div>";
        }
    }else if (isset($_GET['errInfo'])) {
        if($_GET['errInfo'] == 1){
            echo "<div class='alert alert-danger' role='alert'>
					Une erreur est survenue lors de la réservation, réessayez
					</div>";
        }
    }


    if(!(isset($_POST['villeDep']) && isset($_POST['villeArr']) && isset($_POST['dateDepart']))){
        header('Location: research_road.php');
    }else{
        $villeDep = $_POST['villeDep'];
        $villeArr = $_POST['villeArr'];
        $dateChoix = $_POST['dateDepart'];
        $value = 0;
        $errDate = 0;
        $errVille = 0;

        $date_courante = date_create(date('d-m-Y H:i',time() +120)); // strtotime("now")
        $dateDonnee = explode('-', $dateChoix);
        $jour = $dateDonnee[2];
        $mois = $dateDonnee[1];
        $annee = $dateDonnee[0];
        //mettre les deux dates avec les meme champs

        $dateTrip = date_create(date('d-m-Y H:i', mktime(0, 0, 0, $mois, $jour, $annee)));
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


        if($value == 1){
            $texteReturn = "?date=".$errDate."&ville".$errVille;
            header('Location: research_road.php'.$texteReturn.'');
        }else{
            $db = dbConnect();

            $villeDep = verifNameCity($villeDep);
            $villeArr = verifNameCity($villeArr);

            //----------
            $reqTrajetCount = $db->prepare('select count(t.*) as nbresult from trajet t, ville_dep vd, ville_arr va where (t.datedepart>=:dateDepart and (t.id_ville_dep= vd.id_ville_dep and t.id_ville_arr=va.id_ville_arr) and vd.nom_dep=:villeDep and va.nom_arr=:villeArr) and t.nbplace > 0 group by t.datedepart; ');
            //select count(t.*) as nbresult from trajet t, ville_dep vd, ville_arr va where (t.datedepart>=:dateDepart and (t.id_ville_dep= vd.id_ville_dep and t.id_ville_arr=va.id_ville_arr) and vd.nom_dep='Guerande' and va.nom_arr='Nantes') and t.nbplace > 0 group by t.datedepart;

            //----------
            $reqTrajetCount->execute(array('dateDepart'=>$dateChoix, 'villeDep'=>$villeDep, 'villeArr'=>$villeArr));

            if(empty($test = $reqTrajetCount->fetch())){
                header('Location: research_road.php?trajet=1');
            }else{

                //-----------

                $reqTrajet = $db->prepare('select t.*, vd.nom_dep, va.nom_arr from trajet t, ville_dep vd, ville_arr va where datedepart>=:dateDepart and (t.id_ville_dep= vd.id_ville_dep and t.id_ville_arr=va.id_ville_arr) and vd.nom_dep=:villeDep and va.nom_arr=:villeArr and t.nbplace>0 order by t.datedepart;');
                //select t.* vd.nom_dep, va.nom_arr from trajet t, ville_dep vd, ville_arr va where datedepart>='2022-04-02' and (t.id_ville_dep= vd.id_ville_dep and t.id_ville_arr=va.id_ville_arr) and vd.nom_dep='Guerande' and va.nom_arr='Nantes' and t.nbplace>0 order by t.datedepart;

                //----------
                $reqTrajet->execute(array('dateDepart'=>$dateChoix, 'villeDep'=>$villeDep, 'villeArr'=>$villeArr));

                ?>
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <h2>Resultats de votre recherche</h2>
                        </div>
                    </div>
                    <hr>

                        <table class="table table-striped col-md-12">
                            <thead>
                                <tr>
                                    <th scope="col">Date</th>
                                    <th scope="col">Heure</th>
                                    <th scope="col">Conducteur</th>
                                    <th scope="col">Voiture</th>
                                    <th scope="col">Places Restantes</th>
                                    <th scope="col">Prix (€)</th>
                                    <th scope="col">Réservation</th>
                                </tr>
                            </thead>
                            <tbody>

                <?php
                $cpt = 1;
                while($trajet = $reqTrajet->fetch()){
                    $reqNumTel = $db->prepare('select num_tel, name, fname from usercov where id_user=:id_conducteur;');
                    $reqNumTel->execute(array('id_conducteur'=>$trajet['id_conducteur']));

                    $data = $reqNumTel->fetch();
                    $tel = $data['num_tel'];
                    $name = $data['name'];
                    $fname = $data['fname'];
                    $reqNumTel->closeCursor();

                    echo "<tr>
                            <td>".implode('-', array_reverse(explode('-',$trajet['datedepart'])))."</td>
                            <td>".$trajet['heuredepart']."</td>
                            <td>".$fname." ".$name."</td>
                            <td>".$trajet['typevoiture']."</td>
                            <td>".$trajet['nbplace']."</td>
                            <td>".$trajet['prix']." €</td>
                            <td><button type='button' class='btn btn-primary' data-toggle='modal' data-target='#ModalLong".$cpt."'>Valider</button></td></tr>
                            ";

                    if($trajet['descriptionsupp'] == 'NULL'){
                        $trajet['descriptionsupp'] = 'Pas de description supplémentaire';
                    }
                    $disa = "";
                    $boutonClose = "secondary";
                    $boutonReser = "primary";
                    if(!isset($_SESSION['id'])){
                        $disa = "disabled";
                        $boutonClose = "primary";
                        $boutonReser = "secondary";
                    }

                    echo "<!-- Modal -->
<div class='modal fade' id='ModalLong".$cpt."' tabindex='-1' role='dialog' aria-labelledby='ModalLongTitle".$cpt."' aria-hidden='true'>
  <div class='modal-dialog modal-lg' role='document'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title' id='ModalLongTitle".$cpt."'>Trajet entre ".$villeDep." et ".$villeArr." avec ".$fname."</h5>
        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
          <span aria-hidden='true'>&times;</span>
        </button>
      </div>
      <div class='modal-body'>
        <h5>Récapitulatif</h5>
        <span class='material-icons'>explore</span>  De ".$villeDep." à ".$villeArr."
        <br>
      <span class='material-icons'>schedule</span>    ".implode('-', array_reverse(explode('-',$trajet['datedepart'])))."  |  ".$trajet['heuredepart']."
      <hr>
      <h5>Informations supplémentaires</h5>
      ".$trajet['descriptionsupp']."
      
      </div>
      <div class='modal-footer'>
        <button type='button' class='btn btn-".$boutonClose."' data-dismiss='modal'>Fermer</button>
        <a href='../controller/controller.php?func=validResearchTrip&id_trajet=".$trajet['id_trajet']."'><button type='button' data-toggle='tooltip' data-placement='top' title='Inscrivez ou connectez vous pour réserver' class='btn btn-".$boutonReser."'".$disa.">Réserver</button></a>
      </div>
    </div>
  </div>
</div>";
                    $cpt++;
                }
                ?>
                            </tbody>
                        </table>
                </div>
                <?php
            }
            $reqTrajetCount->closeCursor();
            $reqTrajet->closeCursor();
        }
    }
    ?>
</div>
<?php include("footer.php")?>
</body>
</html>




