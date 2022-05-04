------------------------------------------------------------
--        Script Postgre
------------------------------------------------------------



------------------------------------------------------------
-- Table: userCov
------------------------------------------------------------
CREATE TABLE public.userCov(
	id_user      SERIAL NOT NULL ,
	mail         VARCHAR (255) NOT NULL ,
	name         VARCHAR (50) NOT NULL ,
	fname        VARCHAR (50) NOT NULL ,
	num_tel      VARCHAR (20) NOT NULL ,
	promo        VARCHAR (10) NOT NULL ,
	password     VARCHAR (255) NOT NULL ,
	conducteur   BOOL  NOT NULL  ,
	CONSTRAINT userCov_PK PRIMARY KEY (id_user)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: trajet
------------------------------------------------------------
CREATE TABLE public.trajet(
	id_trajet        SERIAL NOT NULL ,
	villeDep         VARCHAR (255) NOT NULL ,
	villeArr         VARCHAR (255) NOT NULL ,
	dateDepart       DATE  NOT NULL ,
	heureDepart      VARCHAR (50) NOT NULL ,
	typeVoiture      VARCHAR (255) NOT NULL ,
	couleurVoiture   VARCHAR (50) NOT NULL ,
	plaqueImma       VARCHAR (50) NOT NULL ,
	nbPlace          INT  NOT NULL ,
	nbBagage         INT  NOT NULL ,
	prix             INT  NOT NULL ,
	id_conducteur    INT  NOT NULL  ,
	descriptionsupp      VARCHAR (255) NOT NULL,
	suppression     INT NOT NULL,
	CONSTRAINT trajet_PK PRIMARY KEY (id_trajet)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: passagerTrajet
------------------------------------------------------------
CREATE TABLE public.passagerTrajet(
	id_trajet       INT  NOT NULL ,
	id_user         INT  NOT NULL ,
	id_passTrajet   INT  NOT NULL  ,
	validation      INT  NOT NULL,
	CONSTRAINT passagerTrajet_PK PRIMARY KEY (id_trajet,id_user)

	,CONSTRAINT passagerTrajet_trajet_FK FOREIGN KEY (id_trajet) REFERENCES public.trajet(id_trajet)
	,CONSTRAINT passagerTrajet_userCov0_FK FOREIGN KEY (id_user) REFERENCES public.userCov(id_user)
)WITHOUT OIDS;




