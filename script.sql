CREATE TABLE tabDoklady 
(
  ID_dokladu      INTEGER       NOT NULL  AUTO_INCREMENT
, druh_pohybu     INTEGER       NOT NULL 
, zpusob_platby   INTEGER
, cena_konecna    DECIMAL(10,2) NOT NULL 
, datum           DATE          NOT NULL 
, datum_vytvoreni DATE          NOT NULL 
, aktivni         BOOLEAN       DEFAULT 1  
, CONSTRAINT PK_tabDoklady PRIMARY KEY ( ID_dokladu )
);

CREATE TABLE tabPohyby 
(
  ID_polozka      INTEGER       NOT NULL  AUTO_INCREMENT 
, mnozstvi        INTEGER
, mj              VARCHAR(20) 
, datum_vytvoreni DATE          NOT NULL 
, datum_zmeny     DATE          NOT NULL 
, doklad          INTEGER       NOT NULL
, skladKarta      INTEGER       NOT NULL
, aktivni         BOOLEAN       DEFAULT 1  
, CONSTRAINT PK_tabPohyby PRIMARY KEY ( ID_polozka ) 
);

CREATE TABLE tabSkladKarta 
(
  ID_karta        INTEGER       NOT NULL AUTO_INCREMENT
, cislo_skladu    INTEGER
, mnozstvi        INTEGER
, datum_vytvoreni DATE          NOT NULL 
, datum_zmeny     DATE          NOT NULL
, zbozi           INTEGER
, aktivni         BOOLEAN       DEFAULT 1
, CONSTRAINT PK_tabSkaldKarta PRIMARY KEY ( ID_karta ) 
);


CREATE TABLE tabSkupina 
(
  ID_skupina      INTEGER       NOT NULL 
, nazev           VARCHAR(50)
, datum_vytvoreni DATE          NOT NULL 
, datum_zmeny     DATE          NOT NULL
, aktivni      BOOLEAN       NOT NULL
, CONSTRAINT PK_tabSkupina PRIMARY KEY ( ID_skupina  ) 
);


CREATE TABLE tabZbozi 
(
  ID_polozka      INTEGER       NOT NULL    AUTO_INCREMENT
, nazev           VARCHAR(80)
, mnozstvi        INTEGER
, mj              VARCHAR(20) 
, popis           VARCHAR(80)
, cena_prodejni   DECIMAL(10,2)  
, datum_vytvoreni DATE          NOT NULL 
, datum_zmeny     DATE          NOT NULL
, vytvoril        CHAR(11)
, zmenil          CHAR(11)
, skupina         INTEGER
, aktivni         BOOLEAN       DEFAULT 1 
, CONSTRAINT PK_tabZbozi PRIMARY KEY ( ID_polozka ) 
);


CREATE TABLE tabReceptury 
(
  ID_receptury     INTEGER       NOT NULL  AUTO_INCREMENT
, mnozstvi         INTEGER
, cena             DECIMAL(10,2) NOT NULL 
, ID_zboziRecept   INTEGER       NOT NULL
, ID_zboziSurovina INTEGER       NOT NULL
, aktivni         BOOLEAN       DEFAULT 1
, CONSTRAINT PK_tabReceptury PRIMARY KEY ( ID_receptury ) 
);

CREATE TABLE tabLokace 
(
  ID_lokace        INTEGER      NOT NULL  AUTO_INCREMENT
, nazev            VARCHAR(50) NOT NULL
, pocet_stolu      INTEGER       NOT NULL 
, aktivni         BOOLEAN       DEFAULT 1 
, CONSTRAINT PK_tabLokace PRIMARY KEY (ID_lokace)
);

CREATE TABLE tabZamestnanec 
(
  ID_zamestnance   INTEGER      NOT NULL AUTO_INCREMENT
, jmeno            VARCHAR(50) NOT NULL 
, prijmeni         VARCHAR(50) NOT NULL
, rodne_cislo      VARCHAR(11) NOT null
, adresa           VARCHAR(100) NOT NULL 
, funkce           VARCHAR(40) NOT null
, telefon          VARCHAR(50) NOT NULL
, username         varchar(16)
, passwd           varchar(16) 
, aktivni         BOOLEAN       DEFAULT 1  
, CONSTRAINT PK_tabZamestnanec PRIMARY KEY (ID_zamestnance) 
);

CREATE TABLE tabStul 
(
  ID_stul          INTEGER      NOT NULL AUTO_INCREMENT
, pocet_mist       INTEGER       NOT NULL 
, rezervace        VARCHAR(150) 
, umisteni_lokace  INTEGER       NOT NULL
, obsluha          INTEGER
, cislo_v_lokaci   INTEGER
, aktivni         BOOLEAN       DEFAULT 1 
, CONSTRAINT tabStul_PK PRIMARY KEY (ID_stul)
, FOREIGN KEY (umisteni_lokace) REFERENCES tabLokace (ID_lokace)
, FOREIGN KEY (obsluha) REFERENCES tabZamestnanec (ID_zamestnance)
);


CREATE TABLE tabObjednavka 
(
  ID_objednavky    INTEGER      NOT NULL AUTO_INCREMENT
, datum            DATE         NOT NULL 
, zaplaceno        DECIMAL(10,2) NOT NULL 
, stul             INTEGER
, obsluha          INTEGER       NOT NULL
, aktivni         BOOLEAN       DEFAULT 1  
, CONSTRAINT tabObjednavka_PK PRIMARY KEY (ID_objednavky)
, FOREIGN KEY (stul) REFERENCES tabStul (ID_stul)
, FOREIGN KEY (obsluha) REFERENCES tabZamestnanec (ID_zamestnance)
);


CREATE TABLE tabJidelniListek 
(
  ID_listek        INTEGER      NOT NULL  AUTO_INCREMENT
, nazev            VARCHAR(150) NOT NULL 
, platnost_od      DATE 
, platnost_do      DATE
, aktivni         BOOLEAN       DEFAULT 1
, CONSTRAINT tabJidelniListek_PK PRIMARY KEY (ID_listek)
);

CREATE TABLE tabPolozkyJL 
(
  ID_polozky_jl    INTEGER      NOT NULL AUTO_INCREMENT 
, mnozstvi         FLOAT       NOT NULL
, mj               VARCHAR(3) 
, poznamka         VARCHAR(150) 
, jidelni_listek   INTEGER      NOT NULL
, zbozi            INTEGER      NOT NULL
, aktivni         BOOLEAN       DEFAULT 1
, CONSTRAINT tabPolozkyJL_PK PRIMARY KEY (ID_polozky_jl)
, FOREIGN KEY (jidelni_listek) REFERENCES tabJidelniListek (ID_listek)
, FOREIGN KEY (zbozi) REFERENCES tabZbozi (ID_polozka)
);

CREATE TABLE tabPolozkaObjednavka 
(
  ID_polozky_obj   INTEGER       NOT NULL AUTO_INCREMENT 
, mnozstvi         INTEGER       NOT NULL 
, mj               VARCHAR(10) 
, vydano           VARCHAR(3)  NOT NULL 
, cislo_objednavky INTEGER       NOT NULL
, polozka_jl       INTEGER       NOT NULL
, aktivni         BOOLEAN       DEFAULT 1  
, CONSTRAINT tabPolozkaObjednavka_PK PRIMARY KEY (ID_polozky_obj)
, FOREIGN KEY (cislo_objednavky) REFERENCES tabObjednavka (ID_objednavky)
, FOREIGN KEY (polozka_jl) REFERENCES tabPolozkyJL (ID_polozky_jl) 
);

CREATE TABLE tabZamestnanecLokace
(
  cislo_zamestnance INTEGER NOT NULL 
, cislo_lokace      INTEGER
, aktivni         BOOL          NOT NULL DEFAULT 1 
, PRIMARY KEY (cislo_zamestnance, cislo_lokace)
, FOREIGN KEY (cislo_zamestnance) REFERENCES tabZamestnanec (ID_zamestnance)
, FOREIGN KEY (cislo_lokace) REFERENCES tabLokace(ID_Lokace)
);

CREATE TABLE tabRezervace
(
  ID_rezervace  INTEGER      NOT NULL AUTO_INCREMENT 
, jmeno            VARCHAR(50)  NOT NULL
, rezervace_od     VARCHAR(20)         NOT NULL    
, rezervace_do     VARCHAR(20)         NOT NULL
, datum            DATE         NOT NULL
, id_restaurace    INTEGER
, id_mistnosti     INTEGER
, id_stul          INTEGER
, aktivni         BOOLEAN       DEFAULT 1 
, CONSTRAINT tabRezervace_PK PRIMARY KEY (ID_rezervace)
, FOREIGN KEY (id_mistnosti) REFERENCES tabLokace (ID_lokace)
, FOREIGN KEY (id_stul) REFERENCES tabStul (ID_stul)
);

INSERT INTO tabSkupina VALUES(101, 'Koření', '2015-12-07', '2015-12-07', true);
INSERT INTO tabSkupina VALUES(102, 'Zelenina', '2015-12-07', '2015-12-07', true);
INSERT INTO tabSkupina VALUES(103, 'Maso', '2015-12-07', '2015-12-07', true);
INSERT INTO tabSkupina VALUES(201, 'Alkoholické nápoje', '2015-12-07', '2015-12-07', true);
INSERT INTO tabSkupina VALUES(202, 'Nealkoholické nápoje', '2015-12-07', '2015-12-07', true);
INSERT INTO tabSkupina VALUES(301, 'Polévky', '2015-12-07', '2015-12-07', true);
INSERT INTO tabSkupina VALUES(302, 'Hotová jídla', '2015-12-07', '2015-12-07', true);
INSERT INTO tabSkupina VALUES(303, 'Jídla z vepřového masa', '2015-12-07', '2015-12-07', true);
INSERT INTO tabSkupina VALUES(304, 'Přílohy', '2015-12-07', '2015-12-07', true);


INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Bazalka',2000,'g', '', 0, '2015-12-07', '2015-12-07', 1, 1, 101);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Badyán celý',2000,'g', '', 0, '2015-12-07', '2015-12-07', 1, 1, 101);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Bobkový list celý',2000,'g', '', 0, '2015-12-07', '2015-12-07', 1, 1, 101);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Chilli mleté',2000,'g', '', 0, '2015-12-07', '2015-12-07', 1, 1, 101);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Fenykl',2000,'g', '', 0, '2015-12-07', '2015-12-07', 1, 1, 101);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Brambory',200,'kg', '', 0, '2015-12-07', '2015-12-07', 1, 1, 102);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Brokolice',12,'kg', '', 0, '2015-12-07', '2015-12-07', 1, 1, 102);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Celer',15,'kg', '', 0, '2015-12-07', '2015-12-07', 1, 1, 102);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Cibule',20,'kg', '', 0, '2015-12-07', '2015-12-07', 1, 1, 102);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Cuketa',5,'kg', '', 0, '2015-12-07', '2015-12-07', 1, 1, 102);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Česnek',20,'kg', '', 0, '2015-12-07', '2015-12-07', 1, 1, 102);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Vepřové',60,'kg', '', 0,'2015-12-07', '2015-12-07', 1, 1, 103);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Hovězí',60,'kg', '', 0, '2015-12-07', '2015-12-07', 1, 1, 103);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Kachní',20,'kg', '', 0, '2015-12-07', '2015-12-07', 1, 1, 103);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Tuňák',10,'kg', '', 0, '2015-12-07', '2015-12-07', 1, 1, 103);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Kuřecí',60,'kg', '', 0, '2015-12-07', '2015-12-07', 1, 1, 103);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Pivo světlé 10',300,'L', '', 0, '2015-12-07', '2015-12-07', 1, 1, 201);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Pivo světlé 12',300,'L', '', 0, '2015-12-07', '2015-12-07', 1, 1, 201);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Pivo tmavé 10',150,'L', '', 0, '2015-12-07', '2015-12-07', 1, 1, 201);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Rum',50,'L', '', 0, '2015-12-07', '2015-12-07', 1, 1, 201);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Vodka',50,'L', '', 0, '2015-12-07', '2015-12-07', 1, 1, 201);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Coca Cola',60,'L', '', 0, '2015-12-07', '2015-12-07', 1, 1, 202);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Kofola',60,'L', '', 0, '2015-12-07', '2015-12-07', 1, 1, 202);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Minerálka',60,'L', '', 0, '2015-12-07', '2015-12-07', 1, 1, 202);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Voda',60,'L', '', 0, '2015-12-07', '2015-12-07', 1, 1, 202);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Česnečka se sýrem',7,'L', '', 35, '2015-12-07', '2015-12-07', 1, 1, 301);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Zelňačka s klobásou',5,'L', '', 42, '2015-12-07', '2015-12-07', 1, 1, 301);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Pravá jihočeská kulajda',5,'L', '', 44, '2015-12-07', '2015-12-07', 1, 1, 301);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Babiččina dršťková polévka',4,'L', '', 38, '2015-12-07', '2015-12-07', 1, 1, 301);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Zvěřinový guláš s křenem',7,'Kg', '', 95, '2015-12-07', '2015-12-07', 1, 1, 302);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Hovězí ragú na zelenině',7,'Kg', '', 89, '2015-12-07', '2015-12-07', 1, 1, 302);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Vepřové rizoto se sýrem',10,'Kg', '', 78, '2015-12-07', '2015-12-07', 1, 1, 302);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Vepřová kýtave smetanové omáčce',0,'Kg', '', 75, '2015-12-07', '2015-12-07', 1, 1, 303);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Vepřový guláš s mletým masem',0,'Kg', '', 81, '2015-12-07', '2015-12-07', 1, 1, 303);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Smažená krkovička s třepanými brambory',0,'Kg', '', 92, '2015-12-07', '2015-12-07', 1, 1, 303);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Vepřové kostky na celeru a smetaně',0,'Kg', '', 69, '2015-12-07', '2015-12-07', 1, 1, 303);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Bramborový salát',0,'Kg', '', 19, '2015-12-07', '2015-12-07', 1, 1, 304);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Hranolky',0,'Kg', '', 25, '2015-12-07', '2015-12-07', 1, 1, 304);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Bramborová kaše',0,'Kg', '', 23, '2015-12-07', '2015-12-07', 1, 1, 304);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Krokety',0,'Kg', '', 26, '2015-12-07', '2015-12-07', 1, 1, 304);
INSERT INTO tabZbozi (nazev,mnozstvi,mj,popis,cena_prodejni,datum_vytvoreni,datum_zmeny,vytvoril,zmenil,skupina)
VALUES('Americké brambory',0,'Kg', '', 28, '2015-12-07', '2015-12-07', 1, 1, 304);

INSERT INTO tabReceptury(mnozstvi,cena,ID_zboziRecept,ID_zboziSurovina) 
VALUES(1.5,  10, 138, 129);  -- voda = 129
INSERT INTO tabReceptury(mnozstvi,cena,ID_zboziRecept,ID_zboziSurovina)
VALUES(1,    10, 138, 103);  -- cibule = 103
INSERT INTO tabReceptury (mnozstvi,cena,ID_zboziRecept,ID_zboziSurovina)
VALUES(100,  10, 138, 125);  -- houby = 125
INSERT INTO tabReceptury(mnozstvi,cena,ID_zboziRecept,ID_zboziSurovina)
VALUES( 0.5,  10, 138, 100);  -- brambory = 100
INSERT INTO tabReceptury(mnozstvi,cena,ID_zboziRecept,ID_zboziSurovina) 
VALUES(0.05, 10, 138, 130);  -- sul = 130
INSERT INTO tabReceptury (mnozstvi,cena,ID_zboziRecept,ID_zboziSurovina)
VALUES(50, 10, 138, 150);  -- kmin = 150

INSERT INTO tabSkladKarta(cislo_skladu,mnozstvi,datum_vytvoreni,datum_zmeny,zbozi) 
VALUES(0,  5, '2015-12-07', '2015-12-07', 103);
INSERT INTO tabSkladKarta (cislo_skladu,mnozstvi,datum_vytvoreni,datum_zmeny,zbozi) 
VALUES(0,  4, '2015-12-07', '2015-12-07', 130);
INSERT INTO tabSkladKarta (cislo_skladu,mnozstvi,datum_vytvoreni,datum_zmeny,zbozi) 
VALUES(0,  25, '2015-12-07', '2015-12-07', 100);
INSERT INTO tabSkladKarta (cislo_skladu,mnozstvi,datum_vytvoreni,datum_zmeny,zbozi) 
VALUES(0,  500, '2015-12-07', '2015-12-07', 125);
INSERT INTO tabSkladKarta (cislo_skladu,mnozstvi,datum_vytvoreni,datum_zmeny,zbozi) 
VALUES(0,  0.1, '2015-12-07', '2015-12-07', 150);

INSERT INTO tabDoklady(druh_pohybu,zpusob_platby,cena_konecna,datum,datum_vytvoreni) 
VALUES(0, 0, 600, '2015-12-07', '2015-12-07');
INSERT INTO tabDoklady(druh_pohybu,zpusob_platby,cena_konecna,datum,datum_vytvoreni) 
VALUES(0, 0, 1500, '2015-12-07', '2015-12-07');
INSERT INTO tabDoklady(druh_pohybu,zpusob_platby,cena_konecna,datum,datum_vytvoreni) 
VALUES(2, 0, 200, '2015-12-07', '2015-12-07');
INSERT INTO tabDoklady(druh_pohybu,zpusob_platby,cena_konecna,datum,datum_vytvoreni)
VALUES(0, 0, 456, '2015-12-07', '2015-12-07');
INSERT INTO tabDoklady(druh_pohybu,zpusob_platby,cena_konecna,datum,datum_vytvoreni) 
VALUES(0, 0, 987, '2015-12-07', '2015-12-07');

INSERT INTO tabPohyby(mnozstvi,mj,datum_vytvoreni,datum_zmeny,doklad,skladKarta)
VALUES(25,  'kg', '2015-12-07', '2015-12-07', 1, 3);
INSERT INTO tabPohyby(mnozstvi,mj,datum_vytvoreni,datum_zmeny,doklad,skladKarta) 
VALUES(4,  'kg', '2015-12-07', '2015-12-07', 1, 2);
INSERT INTO tabPohyby(mnozstvi,mj,datum_vytvoreni,datum_zmeny,doklad,skladKarta) 
VALUES(500,  'g', '2015-12-07', '2015-12-07', 2, 5);
INSERT INTO tabPohyby(mnozstvi,mj,datum_vytvoreni,datum_zmeny,doklad,skladKarta) 
VALUES(-6,  'kg', '2015-12-07', '2015-12-07', 3, 3);
INSERT INTO tabPohyby(mnozstvi,mj,datum_vytvoreni,datum_zmeny,doklad,skladKarta) 
VALUES(900,  'g', '2015-12-07', '2015-12-07', 5, 4);

INSERT INTO tabLokace (nazev,pocet_stolu)
VALUES ('Zahrada - prední', 2);
INSERT INTO tabLokace (nazev,pocet_stolu)
VALUES ('Zahrada - zadní', 3);
INSERT INTO tabLokace (nazev,pocet_stolu)
VALUES ('Terasa', 2);
INSERT INTO tabLokace (nazev,pocet_stolu)
VALUES ('Bar', 2);
INSERT INTO tabLokace (nazev,pocet_stolu)
VALUES ('Interiér - nekurácký', 3);

INSERT INTO tabZamestnanec (jmeno,prijmeni,rodne_cislo,adresa,funkce,telefon,username,passwd)
VALUES ('Pavel', 'Nový', '465632/0253', 'U Bárty 12, Brno 230 23', 'Vedouci', '789 456 123','PavelN','123');
INSERT INTO tabZamestnanec (jmeno,prijmeni,rodne_cislo,adresa,funkce,telefon,username,passwd) 
VALUES ('Mike', 'Revell', '741258/0963', 'U Bárty 12, Praha 896 52', 'Majitel', '629 474 123','MikeR','123');
INSERT INTO tabZamestnanec (jmeno,prijmeni,rodne_cislo,adresa,funkce,telefon,username,passwd) 
VALUES ('Milada', 'Horáková', '369852/0782', 'U Bárty 12, Sahaje 752 85', 'Obsluha', '548 658 321','MiladaH','123');
INSERT INTO tabZamestnanec (jmeno,prijmeni,rodne_cislo,adresa,funkce,telefon,username,passwd) 
VALUES ('Martina', 'Horáková', '369852/0752', 'U Bárty 13, Sahaje 752 85', 'Obsluha', '548 685 321','MiladaM','123');

INSERT INTO tabStul(pocet_mist,umisteni_lokace,obsluha,cislo_v_lokaci)
VALUES (4, 1, 3, 1);
INSERT INTO tabStul(pocet_mist,umisteni_lokace,obsluha,cislo_v_lokaci)
VALUES (4, 1, 3, 2);
INSERT INTO tabStul(pocet_mist,umisteni_lokace,obsluha,cislo_v_lokaci)
VALUES (4, 2, 4, 1);
INSERT INTO tabStul(pocet_mist,umisteni_lokace,obsluha,cislo_v_lokaci)
VALUES (4, 2, 4, 2);
INSERT INTO tabStul(pocet_mist,umisteni_lokace,obsluha,cislo_v_lokaci)
VALUES (4, 2, 4, 3);
INSERT INTO tabStul(pocet_mist,umisteni_lokace,obsluha,cislo_v_lokaci)
VALUES (2, 3, 3, 1);
INSERT INTO tabStul(pocet_mist,umisteni_lokace,obsluha,cislo_v_lokaci)
VALUES (2, 3, 3, 2);
INSERT INTO tabStul(pocet_mist,umisteni_lokace,obsluha,cislo_v_lokaci)
VALUES (2, 4, 3, 1);
INSERT INTO tabStul(pocet_mist,umisteni_lokace,obsluha,cislo_v_lokaci)
VALUES (3, 4, 3, 2);
INSERT INTO tabStul(pocet_mist,umisteni_lokace,obsluha,cislo_v_lokaci)
VALUES (4, 5, 3, 1);INSERT INTO tabStul(pocet_mist,umisteni_lokace,obsluha,cislo_v_lokaci)
VALUES (3, 5, 3, 2);

INSERT INTO tabObjednavka (datum,zaplaceno,stul,obsluha)
VALUES ('2015-12-07', 0, 1, 3);
INSERT INTO tabObjednavka (datum,zaplaceno,stul,obsluha)
VALUES ('2015-12-07', 0, 2, 3);
INSERT INTO tabObjednavka (datum,zaplaceno,stul,obsluha)
VALUES ('2015-12-08', 0, 2, 3);

INSERT INTO tabJidelniListek (nazev,platnost_od, platnost_do)
VALUES ('Nápojový lístek', null, null);
INSERT INTO tabJidelniListek (nazev,platnost_od, platnost_do) 
VALUES ('Hotové jídla', null, null);
INSERT INTO tabJidelniListek (nazev,platnost_od, platnost_do) 
VALUES ('Jídelní lístek', null, null);
INSERT INTO tabJidelniListek (nazev,platnost_od, platnost_do) 
VALUES ('Přílohy', null, null);
INSERT INTO tabJidelniListek (nazev,platnost_od, platnost_do)
VALUES ('Polévky', '2015-12-08', '2015-12-30');

INSERT INTO tabPolozkyJL (mnozstvi ,mj ,poznamka,jidelni_listek,zbozi) 
VALUES (0.5,'L', null, 1, 18);
INSERT INTO tabPolozkyJL (mnozstvi ,mj ,poznamka,jidelni_listek,zbozi) 
VALUES (0.5,'L', null, 1, 19);
INSERT INTO tabPolozkyJL (mnozstvi ,mj ,poznamka,jidelni_listek,zbozi) 
VALUES (4,'dl', null, 1, 20);
INSERT INTO tabPolozkyJL (mnozstvi ,mj ,poznamka,jidelni_listek,zbozi) 
VALUES (4,'dl', null, 1, 21);
INSERT INTO tabPolozkyJL (mnozstvi ,mj ,poznamka,jidelni_listek,zbozi) 
VALUES (0.4,'L', null, 1, 22);
INSERT INTO tabPolozkyJL (mnozstvi ,mj ,poznamka,jidelni_listek,zbozi) 
VALUES (0.4,'L', null, 1, 23);
INSERT INTO tabPolozkyJL (mnozstvi ,mj ,poznamka,jidelni_listek,zbozi) 
VALUES (0.4,'L', null, 1, 24);   
INSERT INTO tabPolozkyJL (mnozstvi ,mj ,poznamka,jidelni_listek,zbozi) 
VALUES (0.4,'L', null, 1, 25);  
INSERT INTO tabPolozkyJL (mnozstvi ,mj ,poznamka,jidelni_listek,zbozi) 
VALUES (0.33,'L', null, 5, 26);
INSERT INTO tabPolozkyJL (mnozstvi ,mj ,poznamka,jidelni_listek,zbozi) 
VALUES (0.33,'L', null, 5, 27);
INSERT INTO tabPolozkyJL (mnozstvi ,mj ,poznamka,jidelni_listek,zbozi) 
VALUES (0.33,'L', null, 5, 28);
INSERT INTO tabPolozkyJL (mnozstvi ,mj ,poznamka,jidelni_listek,zbozi) 
VALUES (0.33,'L', null, 5, 29); 
INSERT INTO tabPolozkyJL (mnozstvi ,mj ,poznamka,jidelni_listek,zbozi) 
VALUES (180,'g', null, 2, 30);
INSERT INTO tabPolozkyJL (mnozstvi ,mj ,poznamka,jidelni_listek,zbozi) 
VALUES (150,'g', null, 2, 31); 
INSERT INTO tabPolozkyJL (mnozstvi ,mj ,poznamka,jidelni_listek,zbozi) 
VALUES (180,'g', null, 3, 32);  
INSERT INTO tabPolozkyJL (mnozstvi ,mj ,poznamka,jidelni_listek,zbozi) 
VALUES (190,'g', null, 3, 33);
INSERT INTO tabPolozkyJL (mnozstvi ,mj ,poznamka,jidelni_listek,zbozi) 
VALUES (180,'g', null, 3, 34);
INSERT INTO tabPolozkyJL (mnozstvi ,mj ,poznamka,jidelni_listek,zbozi) 
VALUES (160,'g', null, 3, 35);
INSERT INTO tabPolozkyJL (mnozstvi ,mj ,poznamka,jidelni_listek,zbozi) 
VALUES (220,'g', null, 3, 36);  
INSERT INTO tabPolozkyJL (mnozstvi ,mj ,poznamka,jidelni_listek,zbozi) 
VALUES (50,'g', null, 4, 37);
INSERT INTO tabPolozkyJL (mnozstvi ,mj ,poznamka,jidelni_listek,zbozi) 
VALUES (50,'g', null, 4, 38);
INSERT INTO tabPolozkyJL (mnozstvi ,mj ,poznamka,jidelni_listek,zbozi) 
VALUES (60,'g', null, 4, 39);
INSERT INTO tabPolozkyJL (mnozstvi ,mj ,poznamka,jidelni_listek,zbozi) 
VALUES (50,'g', null, 4, 40);

INSERT INTO tabPolozkaObjednavka (mnozstvi,vydano,cislo_objednavky,polozka_jl)
VALUES (2, 'ne', 1, 1);
INSERT INTO tabPolozkaObjednavka (mnozstvi,vydano,cislo_objednavky,polozka_jl)
VALUES (1, 'ne', 1, 17);  
INSERT INTO tabPolozkaObjednavka (mnozstvi,vydano,cislo_objednavky,polozka_jl)
VALUES (1, 'ne', 1, 21);  
INSERT INTO tabPolozkaObjednavka (mnozstvi,vydano,cislo_objednavky,polozka_jl)
VALUES (3, 'ne', 2, 2);
INSERT INTO tabPolozkaObjednavka (mnozstvi,vydano,cislo_objednavky,polozka_jl)
VALUES (1, 'ne', 2, 4); 
INSERT INTO tabPolozkaObjednavka (mnozstvi,vydano,cislo_objednavky,polozka_jl)
VALUES (1, 'ne', 2, 8); 
INSERT INTO tabPolozkaObjednavka (mnozstvi,vydano,cislo_objednavky,polozka_jl)
VALUES (2, 'ne', 3, 1);
INSERT INTO tabPolozkaObjednavka (mnozstvi,vydano,cislo_objednavky,polozka_jl)
VALUES (14, 'ne', 3, 2);

INSERT INTO tabZamestnanecLokace(cislo_zamestnance,cislo_lokace)
VALUES (3, 3);
INSERT INTO tabZamestnanecLokace(cislo_zamestnance,cislo_lokace) 
VALUES (3, 5);
INSERT INTO tabZamestnanecLokace(cislo_zamestnance,cislo_lokace) 
VALUES (3, 1);
INSERT INTO tabZamestnanecLokace(cislo_zamestnance,cislo_lokace) 
VALUES (1, 4);

INSERT INTO tabRezervace (jmeno, rezervace_od, rezervace_do, datum, id_restaurace, id_mistnosti, id_stul)
VALUES('Pavel Nový', '14:00:00', '15:00:00', '2015-12-12', 0, 1, 4);
INSERT INTO tabRezervace (jmeno, rezervace_od, rezervace_do, datum, id_restaurace, id_mistnosti, id_stul)
VALUES('Pavel Nový', '15:00:00', '16:00:00', '2015-12-12', 0, 1, 3);
INSERT INTO tabRezervace (jmeno, rezervace_od, rezervace_do, datum, id_restaurace, id_mistnosti, id_stul)
VALUES('Pavel Nový', '14:00:00', '15:00:00', '2015-12-13', 0, 2, null);
INSERT INTO tabRezervace (jmeno, rezervace_od, rezervace_do, datum, id_restaurace, id_mistnosti, id_stul)
VALUES('Pavel Nový', '15:00:00', '16:00:00', '2015-12-14', 0, 3, null);