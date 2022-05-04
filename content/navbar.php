<!--
Navbar

Barre de navigation générique
-->

<nav id="navigation" class='navbar navbar-expand-lg navbar-light bg-nav'>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <a class='navbar-brand' href='../index.php'> <img alt='logo' class="imgLogo" src='../ressources/logo_env-isen.svg' width='25' id="logo"> </a>
    <div class='collapse navbar-collapse' id='navbarText'>
        <ul class='navbar-nav mr-auto'>
            <li class='nav-item'>
                <a class='nav-link' href='../index.php'>Accueil </a>
            </li>
            <li class='nav-item'>
                <a class='nav-link' href='research_road.php'>Rechercher un trajet</a>
            </li>
            <?php
            if(isset($_SESSION['id'])){
                echo "<li class='nav-item'>
                        <a class='nav-link' href='present_road.php'>Proposer un trajet</a>
                        </li>
                      <li class='nav-item'>
                        <a class='nav-link' href='myTrip.php'>Mes Trajets</a>
                      </li>";
            }
            ?>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?php
                    if(isset($_SESSION['id']) && $_SESSION['conducteur'] == true){
                        echo "<span class='material-icons'>time_to_leave</span>";
                    }else{
                        echo "<span class='material-icons'>directions_walk</span>";
                    }
                    ?>
                </a>
                <div class="connexionID dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown" style="background-color: whitesmoke;">

                    <?php
                    if(isset($_SESSION['id'])){
                        echo "<a class='dropdown-item' href='myaccount.php' style='color: black;'>Mon compte</a>";
                        echo "<div class='dropdown-divider'></div>";
                        echo "<button class='dropdown-item' data-toggle='modal' data-target='#modalDeconnexion' style='color: black;'>
                                            Deconnexion
                                        </button>";
                    }else{
                        echo"<a class='dropdown-item' href='login.php' style='color: black;'>Se connecter</a>";
                        echo "<div class='dropdown-divider'></div>";
                        echo"<a class='dropdown-item' href='register.php' style='color: black;'>Inscription</a>";
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
                <a type="button" href="../controller/controller.php?func=logout" class="btn btn-primary">Quitter</a>
            </div>
        </div>
    </div>
</div>
<!-- Fin Modal -->