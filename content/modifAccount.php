<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>COVOIT'ISEN - Modification d'informations personnelles</title>
    <?php
    if (!isset($_SESSION['id'])){
        header('Location: ../index.php');
    }else{
    include('head.php');?>
</head>
<body>
<div id="page">
    <?php
    include('navbar.php');
    if (isset($_GET['mdp'])) {
        if($_GET['mdp'] == 1){
            echo "<div class='alert alert-danger' role='alert'>
					Les mots de passe ne correspondent pas
					</div>";
        }else{
            echo "<div class='alert alert-danger' role='alert'>
					Votre ancien mot de passe est erroné
					</div>";
        }
    }else if (isset($_GET['mail'])) {
        if($_GET['mail'] == 1){
            echo "<div class='alert alert-danger' role='alert'>
					 Adresse mail invalide
					</div>";
        }
    }else if(isset($_GET['tel'])){
        if($_GET['tel'] == 1){
            echo "<div class='alert alert-danger' role='alert'>
					 Numero de téléphone incorrect
					</div>";
        }
    }
    ?>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Modifier vos informations personnelles</h2>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <form method='POST' action='../controller/controller.php?func=modifAccount'>
                    <div class="form-group">
                        <label for="promo">Promotion</label>
                        <div class="input-group mb-3" id="promo">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="promoSelect">Promotions Disponibles</label>
                            </div>
                            <select class="custom-select" id="promoSelect" name="promo" required>
                                <option value="cir1">CIR 1</option>
                                <option value="csi1">CGSI 1</option>
                                <option value="cir2">CIR 2</option>
                                <option value="csi2">CGSI 2</option>
                                <option value="cir3">CIR 3</option>
                                <option value="csi3">CGSI 3</option>
                                <option value="m1">M1</option>
                                <option value="m2">M2</option>
                            </select>
                        </div>
                    </div>

                    <div class='form-group'>
                        <label for='num_tel'>Numero de Telephone</label>
                        <input type='tel' pattern="^[0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}$" class='form-control' size="14" id='num_tel' name='num_tel' placeholder="xx-xx-xx-xx-xx" required>
                    </div>
                    <div class='form-group'>
                        <label for='AncPassword'>Ancien mot de passe</label>
                        <input type='password' minlength='3' maxlength='50' class='form-control' id='AncPassword' name='AncPassword' required>
                    </div>

                    <div class='form-group'>
                        <label for='NewPassword'>Nouveau mot de passe</label>
                        <input type='password' minlength='3' maxlength='50' class='form-control' id='NewPassword' name='NewPassword' required>
                    </div>
                    <div class='form-group'>
                        <label for='NewPasswordConf'>Confirmation du nouveau mot de passe</label>
                        <input type='password' minlength='3' maxlength='50' class='form-control' id='NewPasswordConf' name='NewPasswordConf' required>
                    </div>
                    <div class="form-group">
                        <input type="checkbox" name="CGU" id="CGU" required>
                        <label for="CGU"><a href="condition.php">J'accepte les conditions générales d'utilisation</a></label>
                    </div>
                    <button type='submit' class='btn btn-success'>Valider mes informations</button>
                </form>
            </div>
        </div>
    </div>
    <?php
    include('footer.php');
    }
    ?>
</div>
</body>
</html>