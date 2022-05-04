<?php session_start();

function dbConnect(){
    try{
        $db = new PDO('pgsql:host=localhost;port=5433;dbname=test_covoit_isen', 'testapp', 'test');
    }catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }
    return $db;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>COVOIT'ISEN - Mes Trajets</title>
    <?php
    if(!isset($_SESSION['id'])){
        header('Location: ../');
    }else{
        $db = dbConnect();

    include('head.php');?>
</head>
<body>
<?php
include ('navbar.php');
if (isset($_GET['errSuppr'])) {
    if($_GET['errSuppr'] == 1){
        echo "<div class='alert alert-danger' role='alert'>
					 Erreur lors de la suppression
			  </div>";
    }
}
?>
    <div class="container">
        <h1>Vos prochains covoiturages</h1>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <div id="trip">
                    <h3>Passager</h3>
                    <?php
                    //id_user = id du passager et id_passtrajet = id du conducteur
                        $reqRechTrajet = $db->prepare('select * from passagertrajet where id_user=:id_user;');
                        $reqRechTrajet->execute(array('id_user'=>$_SESSION['id']));
                        $tabIdTrajets = array();
                        $tabIdConducteur = array();
                        $tabValidPassager = array();
                        while($resultRechTrajet = $reqRechTrajet->fetch()) {
                            $validPass = $resultRechTrajet['validation'];
                            array_push($tabValidPassager, $validPass);
                            $id_trajet = $resultRechTrajet['id_trajet'];
                            array_push($tabIdTrajets, $id_trajet);
                            $id_conducteur = $resultRechTrajet['id_passtrajet'];
                            array_push($tabIdConducteur, $id_conducteur);

                        }
                        $reqRechTrajet->closeCursor();

                        if(count($tabIdTrajets) < 1){//modifier la police
                            echo "<p style='text-align: center'>Aucun trajet à venir</p>";
                        }
                        for ($i = 1;$i<=count($tabIdTrajets);$i++) {
                            //-------------
                            $reqTrajet = $db->prepare('select t.*, vd.nom_dep, va.nom_arr, e.fname, e.name, e.num_tel from trajet t, ville_dep vd, ville_arr va, usercov e where t.id_trajet=:id_trajet and (t.id_ville_dep=vd.id_ville_dep and t.id_ville_arr=va.id_ville_arr) and e.id_user= t.id_conducteur order by t.datedepart, t.heuredepart ');
                            //select t.*, vd.nom_dep, va.nom_arr, e.fname, e.name, e.num_tel from trajet t, ville_dep vd, ville_arr va, usercov e where t.id_trajet=25 and e.id_user = 3 and (t.id_ville_dep=vd.id_ville_dep and t.id_ville_arr=va.id_ville_arr);
                            //-------------
                            $reqTrajet->execute(array('id_trajet' => $tabIdTrajets[$i - 1]));
                                                        //'id_conducteur'=>$tabIdConducteur[$i -1]));

                            $donneePassager = $reqTrajet->fetch();
                            $trajetAttente = false;
                            echo "
                    <div class='card'>
                        <div class='card-header' id='heading" . $i . "'>
                            <h5 class='mb-0'>
                                <button class='btn btn-link' data-toggle='collapse' data-target='#collapse" . $i . "' aria-expanded='true' aria-controls='collapse" . $i . "'>
                                " . $donneePassager['nom_dep'] . " - " . $donneePassager['nom_arr'] . " [" .implode('-', array_reverse(explode('-',$donneePassager['datedepart']))). "]
                                </button>
                            </h5>
                             <div class='ml-auto' id='infoTrajValid' >";
                            if($tabValidPassager[$i -1] == 0){
                                $trajetAttente = true;
                                echo "<p style='color: #ffa340'> Validation en attente</p>";
                            }else if($tabValidPassager[$i -1] == 1){
                                echo "<p style='color: limegreen'> Trajet confirmé</p>";
                            }else if($tabValidPassager[$i -1] == 2){
                                $trajetAttente = true;
                                echo "<p style='color: red'> Trajet refusé</p>";
                            }else if($tabValidPassager[$i -1] == 3){
                                $trajetAttente = true;
                                echo "<p style='color: red'> Trajet annulé</p>";
                            }
                            echo" 
                            </div>
                        </div>
                        <div id='collapse" . $i . "' class='collapse show' aria-labelledby='heading" . $i . "' data-parent='#trip'>
                            <div class='card-body'>
                                <div class='infoContact'>
                                    <div class='form-group row'>
                                        <label for='staticDate" . $i . "' class='col-sm-2 col-form-label'><span class='material-icons'>schedule</span></label>
                                        <div class='col-md-10'>
                                            <input type='text' readonly class='form-control-plaintext' id='staticDate" . $i . "' value='" .implode('-', array_reverse(explode('-',$donneePassager['datedepart']))). "  |   " . $donneePassager['heuredepart'] . "'>
                                        </div>
                                    </div>
                                    <div class='form-group row'>
                                        <label for='staticStatut" . $i . "' class='col-sm-2 col-form-label'><span class='material-icons'>person</span></label>
                                        <div class='col-md-10'>
                                            <input type='text' readonly class='form-control-plaintext' id='staticStatut1" . $i . "' value='" . $donneePassager['fname'] . " " . $donneePassager['name'] . "'>
                                        </div>
                                    </div>";
                            if(!$trajetAttente){
                                echo"
                                    <div class='form-group row'>
                                        <label for='staticTel" . $i . "' class='col-sm-2 col-form-label'><span class='material-icons'>call</span></label>
                                        <div class='col-md-10'>
                                            <input type='text' readonly class='form-control-plaintext' id='staticTel1" . $i . "' value='" . $donneePassager['num_tel'] . "'>
                                        </div>
                                    </div>";
                            }

                            echo "
                                    <div class='form-group row'>
                                        <label for='staticPrix" . $i . "' class='col-sm-2 col-form-label'><span class='material-icons'>euro_symbol</span></label>
                                        <div class='col-md-10'>
                                            <input type='text' readonly class='form-control-plaintext' id='staticPrix" . $i . "' value='" . $donneePassager['prix'] . "€'>
                                        </div>
                                    </div>

                                    <div class='form-group row'>
                                        <label for='staticVoiture" . $i . "' class='col-sm-2 col-form-label'>
                                            <span class='material-icons'>time_to_leave</span>
                                        </label>
                                        <div class='col-md-10'>
                                            <input type='text' readonly class='form-control-plaintext' id='staticVoiture" . $i . "' value='" . $donneePassager['typevoiture'] . " - " . $donneePassager['couleurvoiture'] . "'>
                                        </div>
                                    </div>
                                    <div class='form-group row'>
                                        <label for='staticBagage" . $i . "' class='col-sm-2 col-form-label'>
                                            <span class='material-icons'>luggage</span>
                                        </label>
                                        <div class='col-md-10'>
                                            <input type='text' readonly class='form-control-plaintext' id='staticBagage" . $i . "' value='" . $donneePassager['nbbagage'] . " bagage(s) autorisé(s)'>
                                        </div>
                                    </div>
                                    <div class='form-group row'>
                                        <label for='InfoSupp" . $i . "' class='col-sm-2 col-form-label'>
                                            <span class='material-icons'>info</span>
                                        </label>
                                        <div class='col-md-10'>
                                            <input type='text' readonly class='form-control' placeholder='Pas de description particulière' id='InfoSupp" . $i . "' value='";

                            if($donneePassager['descriptionsupp'] == 'NULL'){
                                echo '';
                            }else{
                                echo $donneePassager['descriptionsupp'];
                            }

                            echo "'>
                                        </div>
                                    </div>  
                                </div>";
                            if($tabValidPassager[$i -1] == 2 || $tabValidPassager[$i -1] == 3){
                                echo "<a href='../controller/controller.php?func=annulTripValid&id_trajet=" . $tabIdTrajets[$i - 1] . "&id_user=" . $_SESSION['id'] . "'><button type='button' class='btn btn-danger'>Supprimer</button></a>";

                            }else{
                                echo "<a href='../controller/controller.php?func=annulTrip&id_trajet=" . $tabIdTrajets[$i - 1] . "'><button type='button' class='btn btn-warning'>Annuler</button></a>";
                            }
                            echo "
                            </div>
                        </div>
                    </div>";
                            $reqTrajet->closeCursor();
                        }
                    ?>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <div id="trip_conducteur">
                    <h3>Conducteur</h3>
                    <?php
                        $reqAllTrajetConducteur = $db->prepare('select id_trajet from trajet where id_conducteur =:id_conducteur order by datedepart, heuredepart');
                        $reqAllTrajetConducteur->execute(array('id_conducteur'=>$_SESSION['id']));

                        $tabIdTrajetsConducteur = array();

                        while($resultRechTrajet = $reqAllTrajetConducteur->fetch()) {
                            $id_trajet = $resultRechTrajet['id_trajet'];
                            array_push($tabIdTrajetsConducteur, $id_trajet);
                        }
                        $reqRechTrajet->closeCursor();


                    if(count($tabIdTrajetsConducteur) < 1){//modifier la police
                        echo "<p style='text-align: center'>Aucun trajet en conducteur à venir</p>";
                    }
                    for ($i = 1;$i<=count($tabIdTrajetsConducteur);$i++) {

                        //-----------
                        $reqTrajetConduc = $db->prepare('select t.*, vd.nom_dep, va.nom_arr  from trajet t, ville_dep vd, ville_arr va where t.id_trajet=:id_trajet and (t.id_ville_dep = vd.id_ville_dep and t.id_ville_arr= va.id_ville_arr)');
                        //-----------

                        //$reqTrajetConduc = $db->prepare('select * from trajet where id_trajet=:id_trajet order by datedepart;');
                        $reqTrajetConduc->execute(array('id_trajet' => $tabIdTrajetsConducteur[$i - 1]));

                        $donnee = $reqTrajetConduc->fetch();
                        $suppression = $donnee['suppression'];

                        echo "
                    <div class='card'>
                        <div class='card-header' id='heading" . $i . "'>
                            <h5 class='mb-0'>
                                <button class='btn btn-link' data-toggle='collapse' data-target='#collapseConducteur" . $i . "' aria-expanded='true' aria-controls='collapse" . $i . "'>
                                " . $donnee['nom_dep'] . " - " . $donnee['nom_arr'] . " [" .implode('-', array_reverse(explode('-',$donnee['datedepart']))). "]
                                </button>
                            </h5>
                            <div class='ml-auto' id='infoAnnulation'>";
                        if($suppression == 1){
                            echo "<p style='color: #ffa340'> En attente de l'annulation des clients</p>";
                        }else if($suppression == 2){
                            echo "<p style='color: #ffa340'> Tout les clients ont annulé, vous pouvez maintenant supprimer ce trajet</p>";
                        }
                        echo"</div>
                        </div>
                        <div id='collapseConducteur" . $i . "' class='collapse show' aria-labelledby='heading" . $i . "' data-parent='#trip'>
                            <div class='card-body'>
                                <div class='infoContact'>
                                    <div class='form-group row'>
                                        <label for='staticDatei" . $i . "' class='col-sm-2 col-form-label'><span class='material-icons'>schedule</span></label>
                                        <div class='col-md-10'>
                                            <input type='text' readonly class='form-control-plaintext' id='staticDatei" . $i . "' value='" .implode('-', array_reverse(explode('-',$donnee['datedepart']))). "  |   " . $donnee['heuredepart'] . "'>
                                        </div>
                                    </div>
                                    <div class='form-group row'>
                                        <label for='staticPrixi" . $i . "' class='col-sm-2 col-form-label'><span class='material-icons'>euro_symbol</span></label>
                                        <div class='col-md-10'>
                                            <input type='text' readonly class='form-control-plaintext' id='staticPrixi" . $i . "' value='" . $donnee['prix'] . "€'>
                                        </div>
                                    </div>

                                    <div class='form-group row'>
                                        <label for='staticVoiturei" . $i . "' class='col-sm-2 col-form-label'>
                                            <span class='material-icons'>time_to_leave</span>
                                        </label>
                                        <div class='col-md-10'>
                                            <input type='text' readonly class='form-control-plaintext' id='staticVoiturei" . $i . "' value='" . $donnee['typevoiture'] . " - " . $donnee['couleurvoiture'] . "'>
                                        </div>
                                    </div>
                                    <div class='form-group row'>
                                        <label for='staticBagagei" . $i . "' class='col-sm-2 col-form-label'>
                                            <span class='material-icons'>luggage</span>
                                        </label>
                                        <div class='col-md-10'>
                                            <input type='text' readonly class='form-control-plaintext' id='staticBagagei" . $i . "' value='" . $donnee['nbbagage'] . " bagage(s) autorisé(s)'>
                                        </div>
                                    </div>
                                    <div class='form-group row'>
                                        <label for='staticPlacei" . $i . "' class='col-sm-2 col-form-label'>
                                            <span class='material-icons'>chair</span>
                                        </label>
                                        <div class='col-md-10'>
                                            <input type='text' readonly class='form-control-plaintext' id='staticPlacei" . $i . "' value='" . $donnee['nbplace'] . " place(s) restante(s)'>
                                        </div>
                                    </div>
                                    <div class='form-group row'>
                                        <label for='InfoSuppi" . $i . "' class='col-sm-2 col-form-label'>
                                            <span class='material-icons'>info</span>
                                        </label>
                                        <div class='col-md-10'>
                                            <input type='text' readonly class='form-control' placeholder='Pas de description particulière' id='InfoSuppi" . $i . "' value='";

                            if($donnee['descriptionsupp'] == 'NULL'){
                                echo '';
                            }else{
                                echo $donnee['descriptionsupp'];
                            }

                            echo "'>
                                        </div>
                                    </div>
                                    <hr>

                                    ";
                                    $reqPassagerID = $db->prepare('select id_user, validation from passagertrajet where id_trajet=:id_trajet and validation !=2;');
                                    $reqPassagerID->execute(array('id_trajet'=>$donnee['id_trajet']));

                                    $tabIdPassager = array();
                                    $tabPourValid = array();

                                    while($reponsePassager = $reqPassagerID->fetch()){
                                        $validPass = $reponsePassager['validation'];
                                        array_push($tabPourValid, $validPass);

                                        $passagerID = $reponsePassager['id_user'];
                                        array_push($tabIdPassager, $passagerID);
                                    }

                                    $reqPassagerID->closeCursor();
                                    if(count($tabIdPassager) < 1){//modifier la police

                                        echo "<div class='form-group row'>
                                                    <label for='staticStatuti" . $i . "' class='col-sm-2 col-form-label'><span class='material-icons'>person</span></label>
                                                    <div class='col-md-10'>
                                                        <input type='text' readonly class='form-control-plaintext' id='staticStatuti" . $i . "' value='Pas de passager'>
                                                    </div>
                                              </div>";
                                    }else{

                                         for($h = 1; $h<= count($tabIdPassager);$h++){

                                             $reqEachPassenger = $db->prepare('select name, fname, num_tel from usercov where id_user = :id_user;');
                                             $reqEachPassenger->execute(array('id_user'=>$tabIdPassager[$h -1]));
                                             if(empty($datas = $reqEachPassenger->fetch())){
                                                 echo "error";
                                             }
                                             echo "<div class='form-group row'>
                                                        <label for='staticStatut" .$h.$i . "' class='col-md-2 col-form-label'><span class='material-icons'>person</span></label>
                                                        <div class='col-md-3'>
                                                            <input type='text' readonly class='form-control-plaintext' id='staticStatuti" . $h.$i . "' value='" . $datas['fname'] . " " . $datas['name'] . "'>
                                                        </div>                
                                                        <label for='staticTel" . $h.$i . "' class='col-sm-2 col-form-label'><span class='material-icons'>call</span></label>
                                                        <div class='col-md-3'>
                                                            <input type='text' readonly class='form-control-plaintext' id='staticTeli" . $h.$i . "' value='" . $datas['num_tel'] . "'>
                                                        </div>";
                                                    if ($tabPourValid[$h -1] == 0) {
                                                        echo "  <a href='../controller/controller.php?func=refuTrip&id_trajet=" . $tabIdTrajetsConducteur[$i - 1] . "&valid=1&id_passager=" . $tabIdPassager[$h - 1] . "'><button style='margin-right: 15px; margin-left: -15px;' type='button' class='btn btn-danger'>Refuser</button></a>
                                                       <a href='../controller/controller.php?func=validation&id_trajet=" . $tabIdTrajetsConducteur[$i - 1] . "&id_passager=" . $tabIdPassager[$h - 1] . "'><button type='button' class='btn btn-success'>Accepter</button></a>";

                                                    }
                                             echo "</div>";
                                             $reqEachPassenger->closeCursor();
                                         }
                                     }
                            echo"</div>";
                        if($suppression == 0){
                            echo "<a href='../controller/controller.php?func=annulTripConducteur&id_trajet=" . $tabIdTrajetsConducteur[$i - 1] . "'><button type='button' class='btn btn-warning'>Annuler</button></a>";
                        }else if($suppression == 1){//si suppr = 1 alors, c'est qu'on est en attente de l'annulation de la part des clients
                            echo "<button class='btn btn-secondary' disabled >Supprimer</button>";
                        }else {// suppr = 2
                            echo "<a href='../controller/controller.php?func=supprTripConducteur&id_trajet=" . $tabIdTrajetsConducteur[$i - 1] . "'><button type='button' class='btn btn-danger'>Supprimer</button></a>";
                        }
                        echo "</div>
                        </div>
                    </div>";
                        $reqTrajetConduc->closeCursor();
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
<?php
include('footer.php');
}
    ?>
</body>
</html>
