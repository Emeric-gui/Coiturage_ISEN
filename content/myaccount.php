<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>COVOIT'ISEN - Mon compte</title>
    <?php
    if(!isset($_SESSION['id'])){
        header('Location: ../');
    }else{
    include('head.php');?>
</head>
<body>
<?php
include('navbar.php');
?>
    <div id="page">
        <div class="container">
            <h1>Vos informations</h1>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <?php
                    try{
                        $db = new PDO('pgsql:host=localhost;port=5433;dbname=test_covoit_isen;', 'testapp', 'test');
                    }catch (Exception $e) {
                        die('Erreur : ' . $e->getMessage());
                    }

                    $id = $_SESSION['id'];
                    $req_data = $db->prepare('select * from userCov where id_user=:id_usr;');
                    $req_data->execute(array('id_usr'=>$id));

                    if(!empty($data = $req_data->fetch())) {

                        $mail = $data['mail'];
                        $num_tel = $data['num_tel'];
                        $promo = $data['promo'];
                        $name = $data['name'];
                        $fname = $data['fname'];
                        $conducteur = $data['conducteur'];



                        echo " <form method='POST' action=''>
                        <div class='form-group'>
                            <label for='name'>Nom</label>
                            <input type='text' minlength='3' maxlength='50' class='form-control' id='name' name='name' readonly value='" . $name . "'>
                        </div>
                        <div class='form-group'>
                            <label for='fname'>Prénom</label>
                            <input type='text' minlength='3' maxlength='50' class='form-control' id='fname' name='fname' readonly value='" . $fname . "'>
                        </div>
                        <div class='form-group'>
                            <label for='num_tel'>Numero de téléphone</label>
                            <input type='tel' size='14' class='form-control' id='num_tel' name='num_tel' readonly value='" . $num_tel . "'>
                        </div>
                        <div class='form-group'>
                            <label for='promo'>Promotion</label>
                                <input type='text' minlength='3' maxlength='50' class='form-control' id='promo' name='promo' readonly value='" . $promo . "'>
                        </div>
                        <div class='form-group'>
                            <label for='mail'>Mail</label>
                            <input type='email' minlength='3' maxlength='50' class='form-control' id='mail' name='mail' readonly value='" . $mail . "'>
                        </div>
                        <div class='form-group'>
                            <label for='conducteur'>Role</label>
                            <input type='text' minlength='3' maxlength='50' class='form-control' id='conducteur' name='conducteur' readonly value='";
                            if($conducteur == true){
                                echo "Conducteur";
                            }else{
                                echo "Passager";
                            }
                        echo"'>
                        </div>
                        <a href='modifAccount.php'><button type='button' class='btn btn-primary'>Modifier vos informations</button></a>
                    </form>";
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


