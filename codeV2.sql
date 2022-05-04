-------------------------------------------------------------
--        Script MySQL.
-------------------------------------------------------------


-------------------------------------------------------------
-- Table: userCov
-------------------------------------------------------------

CREATE TABLE userCov(
        id_user    SERIAL  NOT NULL ,
        mail       Varchar (255) NOT NULL ,
        name       Varchar (50) NOT NULL ,
        fname      Varchar (50) NOT NULL ,
        num_tel    Varchar (20) NOT NULL ,
        promo      Varchar (10) NOT NULL ,
        password   Varchar (255) NOT NULL ,
        conducteur Bool NOT NULL
	,CONSTRAINT userCov_PK PRIMARY KEY (id_user)
)WITHOUT OIDS ;


-------------------------------------------------------------
-- Table: Ville_dep
-------------------------------------------------------------

CREATE TABLE Ville_dep(
        id_ville_dep SERIAL  NOT NULL ,
        latitude_dep     FLOAT NOT NULL ,
        longitude_dep    FLOAT NOT NULL ,
        nom_dep      Varchar (50) NOT NULL
	,CONSTRAINT Ville_dep_PK PRIMARY KEY (id_ville_dep)
)WITHOUT OIDS;


-------------------------------------------------------------
-- Table: Ville_arr
-------------------------------------------------------------

CREATE TABLE Ville_arr(
        id_ville_arr FLOAT  NOT NULL ,
        latitude_arr     FLOAT NOT NULL ,
        longitude_arr    FLOAT NOT NULL ,
        nom_arr      Varchar (50) NOT NULL
	,CONSTRAINT Ville_arr_PK PRIMARY KEY (id_ville_arr)
)WITHOUT OIDS ;


-------------------------------------------------------------
-- Table: trajet
-------------------------------------------------------------

CREATE TABLE trajet(
        id_trajet       SERIAL NOT NULL ,
        villeDep        Varchar (255) NOT NULL ,
        villeArr        Varchar (255) NOT NULL ,
        dateDepart      Date NOT NULL ,
        heureDepart     Varchar (50) NOT NULL ,
        typeVoiture     Varchar (255) NOT NULL ,
        couleurVoiture  Varchar (50) NOT NULL ,
        plaqueImma      Varchar (50) NOT NULL ,
        nbPlace         Int NOT NULL ,
        nbBagage        Int NOT NULL ,
        prix            Int NOT NULL ,
        id_conducteur   Int NOT NULL ,
        suppression     Int NOT NULL ,
        descriptionsupp Varchar (255) NOT NULL ,
        id_ville_dep    Int NOT NULL ,
        id_ville_arr    Int NOT NULL
	,CONSTRAINT trajet_PK PRIMARY KEY (id_trajet)

	,CONSTRAINT trajet_Ville_dep_FK FOREIGN KEY (id_ville_dep) REFERENCES Ville_dep(id_ville_dep)
	,CONSTRAINT trajet_Ville_arr0_FK FOREIGN KEY (id_ville_arr) REFERENCES Ville_arr(id_ville_arr)
)WITHOUT OIDS ;


-------------------------------------------------------------
-- Table: passagerTrajet
-------------------------------------------------------------

CREATE TABLE passagerTrajet(
        id_trajet     Int NOT NULL ,
        id_user       Int NOT NULL ,
        validation    Int NOT NULL ,
        id_passTrajet Int NOT NULL
	,CONSTRAINT passagerTrajet_PK PRIMARY KEY (id_trajet,id_user)

	,CONSTRAINT passagerTrajet_trajet_FK FOREIGN KEY (id_trajet) REFERENCES trajet(id_trajet)
	,CONSTRAINT passagerTrajet_userCov0_FK FOREIGN KEY (id_user) REFERENCES userCov(id_user)
)WITHOUT OIDS;

