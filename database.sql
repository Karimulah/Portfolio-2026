CREATE DATABASE IF NOT EXISTS portfolio;
USE portfolio;

CREATE TABLE projets (
  id int(11) NOT NULL AUTO_INCREMENT,
  titre varchar(150) NOT NULL,
  description text NOT NULL,
  technologies varchar(255) NOT NULL,
  image varchar(255) DEFAULT NULL,
  lien varchar(255) DEFAULT NULL,
  date_creation datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE messages_contact (
  id int(11) NOT NULL AUTO_INCREMENT,
  nom varchar(100) NOT NULL,
  email varchar(150) NOT NULL,
  message text NOT NULL,
  lu tinyint(1) NOT NULL DEFAULT 0,
  date_envoi datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE demandes_projet (
  id int(11) NOT NULL AUTO_INCREMENT,
  nom varchar(100) NOT NULL,
  email varchar(150) NOT NULL,
  type_projet varchar(100) NOT NULL,
  description text NOT NULL,
  budget varchar(50) DEFAULT NULL,
  lu tinyint(1) NOT NULL DEFAULT 0,
  date_demande datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE administrateurs (
  id int(11) NOT NULL AUTO_INCREMENT,
  prenom varchar(100) NOT NULL,
  nom varchar(100) NOT NULL,
  email varchar(150) NOT NULL,
  mot_de_passe varchar(255) NOT NULL,
  date_creation datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE visites (
  id int(11) NOT NULL AUTO_INCREMENT,
  adresse_ip varchar(45) NOT NULL,
  page varchar(255) NOT NULL,
  date_visite datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;