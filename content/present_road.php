<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>COVOIT'ISEN - Proposer un trajet</title>
    <?php
    if (!isset($_SESSION['id'])){
        header('Location: ../index.php');
    }else{
    include('head.php');?>
</head>
<body>
<?php
    include('navbar.php');
if (isset($_GET['date'])) {
    if($_GET['date'] == 1){
        echo "<div class='alert alert-danger' role='alert'>
					Le date ou l'heure est incorrecte
					</div>";
    }
}
if (isset($_GET['plaque'])) {
    if($_GET['plaque'] == 1){
        echo "<div class='alert alert-danger' role='alert'>
					La plaque n'est pas valide
					</div>";
    }
}
if (isset($_GET['ville'])) {
    if($_GET['ville'] == 1){
        echo "<div class='alert alert-danger' role='alert'>
					Le nom de ville est invalide
					</div>";
    }
}
if (isset($_GET['champs'])) {
    if($_GET['champs'] == 1){
        echo "<div class='alert alert-danger' role='alert'>
					Des informations fournies sont incorrectes
					</div>";
    }
}

    ?>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 style="text-align: center">Proposer un Covoiturage</h2>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <form method="post" action="../controller/controller.php?func=submitTrip">
                    <div class="form-group">
                        <label for="villeDep">Ville de Départ</label>
                        <input type="text" minlength="3" maxlength="50" class="form-control" id="villeDep" name="villeDep" required>
                    </div>

                    <div class="form-group">
                        <label for="villeArr">Ville d'Arrivée</label>
                        <input type="text" minlength="3" maxlength="50" class="form-control" id="villeArr" name="villeArr" required>
                    </div>
                    <div class="form-group">
                        <label for="dateDepart">Date de départ</label>
                        <input type="date" class="form-control" id="dateDepart" name="dateDepart" required>
                    </div>
                    <div class="form-group">
                        <label for="heureDepart">Heure de départ</label>
                        <input type="time" class="form-control" id="heureDepart" name="heureDepart" required>
                    </div>

                    <hr>
                    <div class="form-group">
                        <label for="typeVoiture">Voiture</label>
                        <input type="text" class="form-control" id="typeVoiture" name="typeVoiture" required>
                        <label for="couleurVoiture">Couleur de la voiture</label>
                        <input type="text" class="form-control" id="couleurVoiture" name="couleurVoiture" required>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="plaqueImma">Plaque d'immatriculation</label>
                            </div>
                            <div class="col-md-7"></div>
                            <div class="col-md-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="typePlaque" checked onchange="testCheckPlaque()">
                                    <label class="form-check-label" for="typePlaque">Nouvelle plaque</label>
                                </div>
                            </div>
                        </div>

                        <input type="text" class="form-control" placeholder="XX-123-XX"
                               oninput="testPlaque()" size="10" id="plaqueImma" name="plaqueImma" required>
                    </div>
                    <div class="form-group">
                        <label for="nbPlace">Nombre de places</label>
                        <input type="number" min="0" class="form-control" id="nbPlace" name="nbPlace" required>

                        <label for="nbBagage">Nombre de bagages autorisé par personne</label>
                        <input type="number" min="0" class="form-control" id="nbBagage" name="nbBagage" required>

                        <label for="prix">Prix (€)</label>
                        <input type="number" min="0" class="form-control" id="prix" name="prix" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description supplémentaire</label>
                        <input type="text" class="form-control" id="description" name="descriptionsupp">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="confirmationPermis" id="confirmationPermis" required>
                        <label for="confirmationPermis" class="form-check-label">  Je certifie être en possession du permis B et d'avoir une assurance sur ma voiture</label>
                    </div>
                    <button type="submit" class="btn btn-success">Proposer</button>
                </form>
            </div>
        </div>
    </div>

<?php
    include ('footer.php');
}
?>
<script src="../script/scriptPlaque.js"></script>
</body>
</html>
