

DROP DATABASE IF EXISTS c216_devoir2;

CREATE DATABASE c216_devoir2;

USE c216_devoir2;

CREATE TABLE `articles` (
   `reference` INT NOT NULL AUTO_INCREMENT ,
   `nom` VARCHAR( 50 ) NOT NULL ,
   `description` VARCHAR( 200 ) ,
   `prix` FLOAT NOT NULL ,
   PRIMARY KEY ( `reference` )
) ENGINE=INNODB;

CREATE TABLE `clients` (
   `numero` INT NOT NULL AUTO_INCREMENT ,
   `nom` VARCHAR( 100 ) NOT NULL ,
   `prenom` VARCHAR( 100 ) NOT NULL ,
   `adresse` VARCHAR( 200 ) NOT NULL ,
   `codepostal` INT UNSIGNED NOT NULL ,
   `ville` VARCHAR( 100 ) NOT NULL ,
   `pays` VARCHAR( 50 ) DEFAULT 'France' NOT NULL ,
   `telephone` VARCHAR( 50 ) ,
   PRIMARY KEY ( `numero` )
) ENGINE=INNODB;

CREATE TABLE `achats` (
   `id_achat` INT NOT NULL AUTO_INCREMENT ,
   `id_client` INT NOT NULL ,
   `id_article` INT NOT NULL ,
   `quantite` INT NOT NULL ,
   `date` DATE NOT NULL ,
   PRIMARY KEY ( `id_achat` ),
   INDEX (`id_article`),
   FOREIGN KEY (`id_article`)
   REFERENCES articles(`reference`)
   ON UPDATE CASCADE ON DELETE RESTRICT,
   INDEX (`id_client`),
   FOREIGN KEY (`id_client`)
   REFERENCES clients(`numero`)
) ENGINE=INNODB;


ALTER TABLE `articles` ADD COLUMN stock_actuel INT;

CREATE USER 'vendeur'@'localhost' IDENTIFIED BY 'vendeur';

GRANT USAGE ON *.* TO 'vendeur'@'localhost' REQUIRE NONE 
   WITH GRANT OPTION MAX_QUERIES_PER_HOUR 0 
         MAX_CONNECTIONS_PER_HOUR 0 
         MAX_UPDATES_PER_HOUR 0 
         MAX_USER_CONNECTIONS 0;

GRANT SELECT, INSERT, UPDATE ON `c216_devoir2`.`achats`   TO 'vendeur'@'localhost';

GRANT SELECT, INSERT, UPDATE ON `c216_devoir2`.`clients`  TO 'vendeur'@'localhost';

GRANT SELECT (`reference`, `nom`, `description`, `prix`, `stock_actuel`), 
      UPDATE (`stock_actuel`), 
      REFERENCES (`reference`) 
ON `c216_devoir2`.`articles` TO 'vendeur'@'localhost';

CREATE USER 'comptable'@'localhost' IDENTIFIED BY 'comptable';

GRANT USAGE ON *.* TO 'comptable'@'localhost' REQUIRE NONE 
   WITH GRANT OPTION MAX_QUERIES_PER_HOUR 0 
         MAX_CONNECTIONS_PER_HOUR 0 
         MAX_UPDATES_PER_HOUR 0 
         MAX_USER_CONNECTIONS 0;

GRANT SELECT ON `c216_devoir2`.`achats`   TO 'comptable'@'localhost';

GRANT SELECT ON `c216_devoir2`.`clients`  TO 'comptable'@'localhost';

GRANT SELECT ON `c216_devoir2`.`articles` TO 'comptable'@'localhost';

INSERT INTO articles (nom, description, prix) VALUES 
   ('Article 1', 'Un super premier article !', '1.1'),
   ('Article 2', 'Un super deuxieme article !', '2.2'),
   ('Article 3', 'Un super troisieme article !', '3.3'),
   ('Article 4', 'Un super quatrieme article !', '4.4'),
   ('Article 5', 'Un super cinquieme article !', '5.5'),
   ('Article 6', 'Un super sixieme article !', '6.6');

FLUSH PRIVILEGES;


