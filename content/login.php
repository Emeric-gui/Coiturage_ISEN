<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>COVOIT'ISEN - Connexion</title>
    <?php
    if(isset($_SESSION['id'])){
        header('Location: myaccount.php');
    }else if(isset($_COOKIE['mail']) && isset($_COOKIE['password'])){
            header('Location: ../controller/controller.php?func=login');
    }



    include('head.php');
    ?>
</head>
<body>
    <div id="page">
        <?php
        include('navbar.php');
            if(isset($_GET['err'])){
                if ($_GET['err'] == 0 && $_SESSION['mdpErrone'] < 4) {
                    echo"<div class='alert alert-danger' role='alert'>
              			Le mot de passe ou l'adresse mail est incorrect.
            			</div>";
                    $_SESSION['mdpErrone'] ++;
                    if($_SESSION['mdpErrone'] == 4){
                        $_SESSION['tempsBlocage'] = time();
                    }
                }
            }


        $newTime = time();
        if(($tempsRestant = $newTime - $_SESSION['tempsBlocage']) > 300){
            $_SESSION['mdpErrone'] = 0;
        }else{
            header('Location: ../index.php');
        }


        if($_SESSION['mdpErrone'] >= 4){
            $boutonEnvoi =  "<button class='btn btn-secondary' disabled>Connexion</button>";
            echo"<div class='alert alert-danger' role='alert'>
                    Vous avez depassés le nombres de tentatives de connexion possible.
            			</div>";
            $action = "";
        }else{
            $action = "../controller/controller.php?func=login";
            $boutonEnvoi =  "<button type='submit' class='btn btn-success'>Connexion</button>";
        }



        ?>
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h2>Se Connecter</h2>
                </div>
            </div>
            <hr>
            <form action="<?=$action?>" method="post">
                <div class="form-group">
                    <label for="mail">Adresse Mail</label>
                    <input type="text" class="form-control" id="mail" name="mail" required>

                    <label for="password">Mot de Passe</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <input type="checkbox" name="stayConnected" id="stayConnected">
                    <label for="stayConnected">Rester connecté</label>
                    <br>
                    <?=$boutonEnvoi?>
                    <a href="register.php"><button type="button" class="btn btn-info">Inscription</button></a>
                </div>
            </form>
        </div>
        <?php
            include('footer.php');
        ?>
    </div>
</body>
</html>


