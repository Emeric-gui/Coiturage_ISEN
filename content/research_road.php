<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
        <title>COVOIT'ISEN - Recherche de trajets</title>
        <?php include('head.php');?>
</head>
<body>
    <div id="page">
        <?php
        include('navbar.php');
        $id_trajet = 0;
        $trajetByMain = false;
        $valid = false;

        function dbConnect(){
            try{
                $db = new PDO('pgsql:host=localhost;port=5433;dbname=test_covoit_isen', 'testapp', 'test');
            }catch (Exception $e) {
                die('Erreur : ' . $e->getMessage());
            }
            return $db;
        }

        if (isset($_GET['date'])) {
            if($_GET['date'] == 1){
                echo "<div class='alert alert-danger' role='alert'>
					Erreur dans le choix de la date
					</div>";
            }
        }

        if (isset($_GET['ville'])) {
            if($_GET['ville'] == 1){
                echo "<div class='alert alert-danger' role='alert'>
					Le nom de la ville est invalide
					</div>";
            }
        }





        if(isset($_GET['id_trajet'])){
            $trajetByMain = true;
            $id_trajet = htmlspecialchars($_GET['id_trajet']);
        }
        if (isset($_GET['trajet'])) {
            if($_GET['trajet'] == 1){
                echo "<div class='alert alert-danger' role='alert'>
					Aucun trajet disponible a cette date ou ultérieurement
					</div>";
            }
        }


        $villeDep = "";
        $villeArr = "";
        $dateDep = "";
        if($trajetByMain){
            $db = dbConnect();

            //-------------
            //select t.*, vd.nom_dep, va.nom_arr, e.fname, e.name, e.num_tel from trajet t, ville_dep vd, ville_arr va, usercov e where t.id_trajet=:id_trajet and e.id_user = :id_user and (t.id_ville_dep=vd.id_ville_dep and t.id_ville_arr=va.id_ville_arr);
            $reqCheckExistance = $db->prepare('select t.*, vd.nom_dep, va.nom_arr from trajet t, ville_dep vd, ville_arr va where t.id_trajet=:id_trajet and (t.id_ville_dep=vd.id_ville_dep and t.id_ville_arr=va.id_ville_arr);');
            //-------------

            /**
             * TODO --> Changer champs de requete et donnees en sortie
             *
             */

            $reqCheckExistance->execute(array('id_trajet'=>$id_trajet));

            $checkExistance = $reqCheckExistance->fetch();
            if(!empty($checkExistance)){
                $villeDep = $checkExistance['nom_dep'];
                $villeArr = $checkExistance['nom_arr'];
                $dateDep = $checkExistance['datedepart'];
                $valid = true;
            }
            $reqCheckExistance->closeCursor();
        }

        ?>

        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2 style="text-align: center;">Rechercher un Covoiturage</h2>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <form method="post" action="result_research.php">
                        <div class="form-group setVille">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="villeDep">Ville de Départ</label>
                                </div>
                                <div class="col-md-7"></div>
                                <div class="col-md-2">
                                    <label for="rayonVilleDep">Marge (km)</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-10">
                                    <input type="text" minlength="3" maxlength="50" class="form-control" id="villeDep" name="villeDep"
                                        <?php
                                        if($valid){
                                            echo "value='".$villeDep."' ";
                                        }

                                        ?>
                                           required>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" min="0" id="rayonVilleDep" name="rayonVilleDep" class="form-control" value="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group setVille">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="villeArr">Ville d'arrivée</label>
                                </div>
                                <div class="col-md-7"></div>
                                <div class="col-md-2">
                                    <label for="rayonVilleArr">Marge (km)</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-10">
                                    <input type="text" minlength="3" maxlength="50" class="form-control" id="villeArr" name="villeArr"
                                        <?php
                                        if($valid){
                                            echo "value='".$villeArr."' ";
                                        }

                                        ?>
                                           required>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" min="0" id="rayonVilleArr" name="rayonVilleArr" class="form-control" value="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="dateDepart">Date</label>
                            <input type="date" class="form-control" id="dateDepart" name="dateDepart"
                                <?php
                                if($valid){
                                    echo "value='".$dateDep."' ";
                                }

                                ?>
                                   required>
                        </div>
                        <button type="submit" class="btn btn-success">Rechercher</button>
                    </form>
                </div>
            </div>
        </div>

        <?php
        include('footer.php');
        ?>

    </div>
</body>
</html>


