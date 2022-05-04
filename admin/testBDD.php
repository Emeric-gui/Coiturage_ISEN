<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>COV ISEN-testInsert/suppr</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link href='https://fonts.googleapis.com/icon?family=Material+Icons' rel='stylesheet'>
    <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css' integrity='sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk' crossorigin='anonymous'>
</head>
<body>
<?php

if (isset($_GET['mdp'])) {
    if($_GET['mdp'] == 1){
        echo "<div class='alert alert-danger' role='alert'>
					Erreur mot de passe
					</div>";
    }
}else if (isset($_GET['mail'])) {
    if($_GET['mail'] == 1){
        echo "<div class='alert alert-danger' role='alert'>
					erreur mail
					</div>";
    }
}else if (isset($_GET['errChamp'])) {
    if($_GET['errChamp'] == 1){
        echo "<div class='alert alert-danger' role='alert'>
					erreur champ
					</div>";
    }
}else if (isset($_GET['errSuppr'])) {
    if($_GET['errSuppr'] == 1){
        echo "<div class='alert alert-danger' role='alert'>
					probleme de suppression
					</div>";
    }
}else if(isset($_GET['tel'])){
    if($_GET['tel'] == 1){
        echo "<div class='alert alert-danger' role='alert'>
					 Numero de téléphone incorrect
					</div>";
    }
}else if (isset($_GET['ok'])) {
    if($_GET['ok'] == 1){
        echo "<div class='alert alert-success' role='alert'>
					ok insert
					</div>";
    }
}
?>

<div class="container">
    <h1 style="text-align: center">Test admin</h1>
    <hr>
    <h2>Liste</h2>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th scope="col">id_user</th>
                    <th scope="col">name</th>
                    <th scope="col">first name</th>
                    <th scope="col">mail</th>
                    <th scope="col">mot de passe</th>
                </tr>
                </thead>
                <tbody>
                <?php
                try{
                    $db = new PDO('pgsql:host=localhost;port=5433;dbname=cov_isen;', 'testapp', 'test');
                }catch (Exception $e) {
                    die('Erreur : ' . $e->getMessage());
                }

                $req = $db->query('select * from usercov;');


                while ($data = $req->fetch()){

                    $mdpTotal = $data['password'];
                    $mdplength = strlen($mdpTotal);

                    $mdp = "";

                    if($mdplength >=10){
                        for ($i=0;$i<9;$i++){
                            $mdp = $mdp.$mdpTotal[$i];
                        }
                    }else{
                        $mdp = $mdpTotal;
                    }

                    echo "<tr><td>".$data['id_user']."</td><td>".$data['name']."</td><td>".$data['fname']."</td>
<td>".$data['mail']."</td><td>
<button class='btn btn-secondary' data-toggle='tooltip' data-placement='right' title data-original-title='$mdpTotal'>
  ".$mdp."
</button></td></tr>";
                }

                ?>
                </tbody>
            </table>
        </div>
    </div>

<hr>


    <h2>Insertion User</h2>
    <hr>
    <div class="row">
<!--        form insertion-->
        <div class="col-md-12">
            <form method='POST' action='../controller/controller.php?func=insertTest'>
                <div class='form-group'>
                    <label for='name'>Nom</label>
                    <input type='text' minlength='3' maxlength='50' class='form-control' id='name' name='name' required>
                </div>
                <div class='form-group'>
                    <label for='fname'>Prénom</label>
                    <input type='text' minlength='3' maxlength='50' class='form-control' id='fname' name='fname' required>
                </div>
                <div class='form-group'>
                    <label for='mail'>Mail</label>
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
                <button type='submit' class='btn btn-primary'>Créer un user</button>
            </form>

        </div>
    </div>
    <br>
    <hr>
    <h2>Suppression</h2>
    <hr>
    <form method='POST' action='../controller/controller.php?func=supprTest'>
        <div class='form-group'>
            <label for='id_suppr'>id_user</label>
            <input type="number" class='form-control' id='id_suppr' name='id_suppr' min="1" required>
        </div>
        <button type='submit' class='btn btn-primary' >Supprimer un user</button>
    </form>
    <br>

</div>

<footer id='footer' class="colorBackG">
    <p> &copy;<?=date('Y') ?> COV ISEN</p>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
</body>
</html>