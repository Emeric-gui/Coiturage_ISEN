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

function verifHoraire($date){//date est en mode dateInterval
    if(($date->y > 0 || $date->m > 0 || $date->d > 0 || $date->h > 0 || $date->i > 0) && ($date->format("%R") == "-" )){
        return true;
    }
    return false;
}


?>
<!DOCTYPE html>
<html>
<head>
    <title>COVOIT'ISEN</title>
    <link rel="icon" href="ressources/logo_env-isen.svg"/>
    <meta charset = 'UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link href='https://fonts.googleapis.com/icon?family=Material+Icons' rel='stylesheet'>
    <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css' integrity='sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk' crossorigin='anonymous'>
    <link rel="stylesheet" href="style/style.css">
</head>

<body>
<nav id="navigation" class='navbar navbar-expand-lg navbar-light bg-nav'>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <a class='navbar-brand' href='index.php'> <img alt='logo' src='ressources/logo_env-isen.svg' width='25' id="logo"> </a>
    <div class='collapse navbar-collapse' id='navbarText'>
        <ul class='navbar-nav mr-auto'>
            <li class='nav-item'>
                <a class='nav-link' href='index.php'>Accueil </a>
            </li>
            <li class='nav-item'>
                <a class='nav-link' href='content/research_road.php'>Rechercher un trajet</a>
            </li>
            <?php
            if(isset($_SESSION['id'])){
                echo "<li class='nav-item'>
                <a class='nav-link' href='content/present_road.php'>Proposer un trajet</a>
            </li>
            <li class='nav-item'>
                        <a class='nav-link' href='content/myTrip.php'>Mes Trajets</a>
                      </li>";
            }
            ?>
        </ul>
        <!--                Pour avoir un dropdown a droite    -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <?php
                    if(isset($_SESSION['id']) && $_SESSION['conducteur'] == true){
                        echo "<span class='material-icons'>time_to_leave</span>";
                    }else{
                        echo "<span class='material-icons'>directions_walk</span>";
                    }
                    ?>

                </a>
                <div class=" connexionID dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown" style="background-color: whitesmoke;">
                    <?php
                    if(isset($_SESSION['id'])){
                        echo "<a class='dropdown-item' href='content/myaccount.php' style='color: black;'>Mon compte</a>";
                        echo "<div class='dropdown-divider'></div>";
                        echo "<button class='dropdown-item' data-toggle='modal' data-target='#modalDeconnexion' style='color: black;'>
                                            Deconnexion
                                        </button>";
                    }else{
                        echo"<a class='dropdown-item' href='content/login.php' style='color: black;'>Se connecter</a>";
                        echo "<div class='dropdown-divider'></div>";
                        echo"<a class='dropdown-item' href='content/register.php' style='color: black;'>Inscription</a>";
                    }
                    ?>

                </div>
            </li>
        </ul>
    </div>
</nav>

<!-- Modal -->
<div class="modal fade" id="modalDeconnexion" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Déconnexion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Etes-vous sur de vouloir vous déconnecter ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <a type="button" href="controller/controller.php?func=logout" class="btn btn-primary">Quitter</a>
            </div>
        </div>
    </div>
</div>
<!-- Fin Modal -->

<div id="page">
    <div class="container">
        <h1 style="text-align: center">COVOIT'ISEN</h1><hr>
        <p class="subtitle">Le site de covoiturage pour ISEN Nantes</p>
        <img src="ressources/logo_env-isen.png" class="img-fluid imgLogo" alt="Responsive image" width="150">
        <div class="row">
            <div class="col-md-12">
                <h3 style="text-align: center">Derniers covoiturages ajoutées</h3><hr class='h1hr'>
            </div>
        </div>
        <?php
        $counter = 0;
        $villeDeps = array();
        $villeArrs = array();
        $heureDeparts = array();
        $dateDeparts = array();
        $prixs = array();
        $id_trajetss = array();
        $db = dbConnect();


        //checker heure et date car si pourri, devrait pas s'afficher --> Patcher au niveau de la recherche mais pas au niveau de l'index
        /**
         * Verif heure et date
         *
         */
        if(isset($_SESSION['id'])){
            //Faire requete pour tous les éléments
            //Puis faire tri des dates erronnées
            $ide = $_SESSION['id'];
            //--------------
            $reqLastCovoit = $db->prepare('select t.*, vd.nom_dep, va.nom_arr from trajet t, ville_dep vd, ville_arr va where  t.id_ville_dep=vd.id_ville_dep and t.id_ville_arr=va.id_ville_arr and t.id_conducteur!=:id_user order by t.datedepart desc, t.heuredepart desc limit 5; ');
            //select t.*, vd.nom_dep, va.nom_arr from trajet t, ville_dep vd, ville_arr va where  t.id_ville_dep=vd.id_ville_dep and t.id_ville_arr=va.id_ville_arr and t.id_conducteur!=2 order by t.datedepart desc, t.heuredepart desc limit 5;
            //--------------

            $reqLastCovoit->execute(array('id_user'=>$ide));
        }else{

            //-----------
            $reqLastCovoit = $db->query('select t.*, vd.nom_dep, va.nom_arr from trajet t, ville_dep vd, ville_arr va where (t.id_ville_dep = vd.id_ville_dep and t.id_ville_arr=va.id_ville_arr) order by t.datedepart desc, t.heuredepart desc limit 5;');
            //-----------

        }


        while($dataLastCovoit = $reqLastCovoit->fetch()){

            //Check date ici

            $dateDeDepart = $dataLastCovoit['datedepart'];
            $heureDeDepart = $dataLastCovoit['heuredepart'];

            $date_courante = date_create(date('d-m-Y H:i',time() +120)); // strtotime("now")
            $dateDonnee = explode('-', $dateDeDepart);
            $jour = $dateDonnee[2];
            $mois = $dateDonnee[1];
            $annee = $dateDonnee[0];
            //mettre les deux dates avec les meme champs

            $heureDonnee = explode(":", $heureDeDepart);
            $heure = $heureDonnee[0];
            $minutes = $heureDonnee[1];

            $dateTrip = date_create(date('d-m-Y H:i', mktime($heure, $minutes, 0, $mois, $jour, $annee)));
            $dateVerif = date_diff($dateTrip, $date_courante);

            if (verifHoraire($dateVerif)){  //si l'horaire est respectée
                array_push($villeDeps, $dataLastCovoit['nom_dep']);
                array_push($villeArrs, $dataLastCovoit['nom_arr']);
                array_push($dateDeparts, $dataLastCovoit['datedepart']);
                array_push($heureDeparts, $dataLastCovoit['heuredepart']);
                array_push($prixs, $dataLastCovoit['prix']);
                array_push($id_trajetss, $dataLastCovoit['id_trajet']);
                $counter++;
            }
        }

        $reqLastCovoit->closeCursor();

        if($counter == 5){
            echo "<div class='row'>";
            echo "<div class='col-md-2'></div>";
            for($i = 0;$i<3;$i++){
                echo "<div class='card col-md-2 cardAffTrajet'>
                    <div class='person'>
                        <span class='material-icons person_circle'>account_circle</span>
                    </div>";

                echo "  <div class='card-body'>
                        <div class='informationBody'>
                            <h5 class='card-title'>De ".$villeDeps[$i]." vers ".$villeArrs[$i]."</h5>
                            <p class='card-text'>
                                <span class='material-icons'>schedule</span>
                                ".implode('-', array_reverse(explode('-',$dateDeparts[$i])))."   |   ".$heureDeparts[$i]."
                            </p>
                            <p class='card-text'>
                                <span class='material-icons'>euro</span>
                                ".$prixs[$i]." €
                            </p>
                        </div>
                        <br>
                        <a href='content/research_road.php?id_trajet=".$id_trajetss[$i]."' class='btn btn-dark'>Plus d'informations</a>
                    </div>
                </div>";
                if ($i < 2){
                    echo "<div class='col-md-1'></div>";
                }else{
                    echo "<div class='col-md-2'></div>";
                }
            }
            echo "</div><br>
                        <div class='row'>";
            for($i = 3;$i<5;$i++){
                echo "<div class='col-md-2'></div>
                <div class='card col-md-3 cardAffTrajet'>
                <div class='person'>
                    <span class='material-icons person_circle'>account_circle</span>
                </div>
                 ";

                echo "
                    <div class='card-body'>
                        <div class='informationBody'>
                            <h5 class='card-title'>De ".$villeDeps[$i]." vers ".$villeArrs[$i]."</h5>
                            <p class='card-text'>
                                <span class='material-icons'>schedule</span>
                                ".implode('-', array_reverse(explode('-',$dateDeparts[$i])))."   |   ".$heureDeparts[$i]."
                            </p>
                            <p class='card-text'>
                                <span class='material-icons'>euro</span>
                                ".$prixs[$i]." €
                            </p>
                        </div>
                        <br>
                        <a href='content/research_road.php?id_trajet=".$id_trajetss[$i]."' class='btn btn-dark'>Plus d'informations</a>
                    </div>
                </div>";
            }
            echo "<div class='col-md-2'></div>
        </div>
        <br>
    </div>";
        }else if($counter == 4){
            $indice = 0;

            for($i = 0;$i<2;$i++){
                echo "<div class='row'>";
                for($j = 0;$j<2;$j++){
                    echo "<div class='col-md-2'></div>
                <div class='card col-md-3 cardAffTrajet'>
                <div class='person'>
                    <span class='material-icons person_circle'>account_circle</span>
                </div>
                ";
                    if($i == 0 && $j == 0){
                        $indice = 0;
                    }else if($i == 0 && $j == 1){
                        $indice = 1;
                    }else if($i == 1 && $j == 0){
                        $indice = 2;
                    }else if ($i == 1 && $j == 1) {
                        $indice = 3;
                    }
                    echo "
                    <div class='card-body'>
                        <div class='informationBody'>
                            <h5 class='card-title'>De ".$villeDeps[$indice]." vers ".$villeArrs[$indice]."</h5>
                            <p class='card-text'>
                                <span class='material-icons'>schedule</span>
                                ".implode('-', array_reverse(explode('-',$dateDeparts[$indice])))."   |   ".$heureDeparts[$indice]."
                            </p>
                            <p class='card-text'>
                                <span class='material-icons'>euro</span>
                                ".$prixs[$indice]." €
                            </p>
                        </div>
                        <br>
                        <a href='content/research_road.php?id_trajet=".$id_trajetss[$indice]."' class='btn btn-dark'>Plus d'informations</a>
                    </div>
                </div>";
                }
                echo "<div class='col-md-2'></div>
            </div><br>";
            }

        }else if($counter == 3){
            $indice = 0;
            echo "<div class='row'>";
            for($j = 0;$j<2;$j++){
                $indice = $j;
                echo "<div class='col-md-2'></div>
                <div class='card col-md-3 cardAffTrajet'>
                <div class='person'>
                    <span class='material-icons person_circle'>account_circle</span>
                </div>
                ";

                echo "
                    <div class='card-body'>
                        <div class='informationBody'>
                            <h5 class='card-title'>De ".$villeDeps[$indice]." vers ".$villeArrs[$indice]."</h5>
                            <p class='card-text'>
                                <span class='material-icons'>schedule</span>
                                ".implode('-', array_reverse(explode('-',$dateDeparts[$indice])))."   |   ".$heureDeparts[$indice]."
                            </p>
                            <p class='card-text'>
                                <span class='material-icons'>euro</span>
                                ".$prixs[$indice]." €
                            </p>
                        </div>
                        <br>
                        <a href='content/research_road.php?id_trajet=".$id_trajetss[$indice]."' class='btn btn-dark'>Plus d'informations</a>
                    </div>
                </div>";
            }
            echo "<div class='col-md-2'></div>       
        </div>
        <br>";
            $indice = 2;
            echo "<div class='row'>
                        <div class='col-md-4'></div>
                            <div class='card col-md-4 cardAffTrajet'>
                                <div class='person'>
                                    <span class='material-icons person_circle'>account_circle</span>
                                </div>
                                <div class='card-body'>
                                    <div class='informationBody'>
                                        <h5 class='card-title'>De ".$villeDeps[$indice]." vers ".$villeArrs[$indice]."</h5>
                                        <p class='card-text'>
                                            <span class='material-icons'>schedule</span>
                                            ".implode('-', array_reverse(explode('-',$dateDeparts[$indice])))."   |   ".$heureDeparts[$indice]."
                                        </p>
                                        <p class='card-text'>
                                             <span class='material-icons'>euro</span>
                                            ".$prixs[$indice]." €
                                        </p>
                                    </div> 
                                    <br>
                                     <a href='content/research_road.php?id_trajet=".$id_trajetss[$indice]."' class='btn btn-dark'>Plus d'informations</a>
                                 </div>
                            </div>
                        <div class='col-md-4'></div>   
                        <br>
                    </div>";




        }else if($counter == 2){
            $indice = 0;
            echo "<div class='row'>";
            for($j = 0;$j<2;$j++){
                $indice = $j;
                echo "<div class='col-md-2'></div>
                            <div class='card col-md-3 cardAffTrajet'>
                            <div class='person'>
                                <span class='material-icons person_circle'>account_circle</span>
                            </div>
                    <div class='card-body'>
                        <div class='informationBody'>
                            <h5 class='card-title'>De ".$villeDeps[$indice]." vers ".$villeArrs[$indice]."</h5>
                            <p class='card-text'>
                                <span class='material-icons'>schedule</span>
                                ".implode('-', array_reverse(explode('-',$dateDeparts[$indice])))."   |   ".$heureDeparts[$indice]."
                            </p>
                            <p class='card-text'>
                                <span class='material-icons'>euro</span>
                                ".$prixs[$indice]." €
                            </p>
                        </div>
                        <br>
                        <a href='content/research_road.php?id_trajet=".$id_trajetss[$indice]."' class='btn btn-dark'>Plus d'informations</a>
                    </div>
                </div>";
            }
            echo "<div class='col-md-2'></div>
        </div>
        <br>";
        }else if($counter == 1){
            $indice = 0;
            echo "<div class='row'>
                        <div class='col-md-4'></div> 
                            <div class='card col-md-4 cardAffTrajet'>
                                <div class='person'>
                                    <span class='material-icons person_circle'>account_circle</span>
                                </div>
                                <div class='card-body'>
                                    <div class='informationBody'>
                                        <h5 class='card-title'>De ".$villeDeps[$indice]." vers ".$villeArrs[$indice]."</h5>
                                        <p class='card-text'>
                                             <span class='material-icons'>schedule</span>
                                            ".implode('-', array_reverse(explode('-',$dateDeparts[$indice])))."   |   ".$heureDeparts[$indice]."
                                        </p>
                                        <p class='card-text'>
                                             <span class='material-icons'>euro</span>
                                            ".$prixs[$indice]." €
                                        </p>
                                    </div>
                                    <br>
                                     <a href='content/research_road.php?id_trajet=".$id_trajetss[$indice]."' class='btn btn-dark'>Plus d'informations</a>
                                 </div>
                            </div>
                        <div class='col-md-3'></div>   
                    </div><br>";
        }
        ?>
        <br>
    </div>
    <br>
</div>
<footer id='footer' class="colorFooter">
    <p> &copy;
        <?=date('Y') ?> COVOIT'ISEN</p>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
</body>
</html>
