<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>COVOIT'ISEN - INSCRIPTION</title>
    <?php
    if (isset($_SESSION['id'])){
        header('Location: myaccount.php');
    }else if(isset($_COOKIE['mail']) && isset($_COOKIE['password'])){
        header('Location: ../controller/controller.php?func=login');
    }
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
        }
    }
    if(isset($_GET['mail'])) {
        if ($_GET['mail'] == 1) {
            echo "<div class='alert alert-danger' role='alert'>
					 Adresse mail invalide
					</div>";
        }
    }
    if(isset($_GET['tel'])){
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
                <h2>Création d'un compte</h2>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <form method='POST' action='../controller/controller.php?func=register'>
                    <div class='form-group'>
                        <label for='name'>Nom</label>
                        <input type='text' minlength='3' maxlength='50' class='form-control' id='name' name='name' required>
                    </div>
                    <div class='form-group'>
                        <label for='fname'>Prénom</label>
                        <input type='text' minlength='3' maxlength='50' class='form-control' id='fname' name='fname' required>
                    </div>

                    <div class="form-group">
                        <label for="promo">Promotion</label>
                        <div class="input-group mb-3" id="promo">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="promoSelect">Promotions Disponibles</label>
                            </div>
                            <select class="custom-select" id="promoSelect" name="promo" required>
                                <option value="CIR1">CIR 1</option>
                                <option value="CGSI1">CGSI 1</option>
                                <option value="CIR2">CIR 2</option>
                                <option value="CGSI2">CGSI 2</option>
                                <option value="CIR3">CIR 3</option>
                                <option value="CGSI3">CGSI 3</option>
                                <option value="M1">M1</option>
                                <option value="M2">M2</option>
                            </select>
                        </div>
                    </div>

                    <div class='form-group'>
                        <label for='num_tel'>Numero de Telephone</label>
                        <input type='tel' pattern="^0[1-9]-[0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}$" class='form-control'
                               size="14" id='num_tel' name='num_tel' placeholder="0x-xx-xx-xx-xx"
                               oninput="testNum()" required>
                    </div>

                    <div class='form-group'>
                        <label for='mail'>Mail (adresse ISEN)</label>
                        <input type='email' minlength='3' maxlength='50' class='form-control' id='mail' name='mail' required>
                    </div>
                    <div class='form-group'>
                        <label for='password'>Mot de passe</label>
                        <input type='password' minlength='3' maxlength='50' class='form-control' id='password' name='password' required>
                    </div>
                    <div class='form-group'>
                        <label for='passwordConf'>Confirmation mot de passe</label>
                        <input type='password' minlength='3' maxlength='50' class='form-control' id='passwordConf' name='passwordConf' required>
                    </div>
                    <div class="form-group">
                        <input type="checkbox" name="CGU" id="CGU" required>
                        <label for="CGU">
                                <a href="CGU-CovoitISEN.pdf" target="_blank">J'accepte les conditions générales d'utilisation</a>
                        </label>
                    </div>
                    <button type='submit' class='btn btn-success'>Créer mon compte</button>
                    <a href="login.php"><button type="button" class="btn btn-info">Connexion</button></a>
                </form>
            </div>
        </div>
    </div>
    <?php
    include('footer.php');
    ?>
</div>
<script src="../script/script_Num.js"></script>
</body>
</html>



