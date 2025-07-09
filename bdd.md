CREATE DATABASE IF NOT EXISTS chat_web;
USE chat_web;

-- Table Rôle
CREATE TABLE Role (
    pkR INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(50) NOT NULL
);

-- Table Utilisateur
CREATE TABLE Utilisateur (
    pkU INT AUTO_INCREMENT PRIMARY KEY,
    fkRole INT,
    pseudo VARCHAR(50) NOT NULL,
    login VARCHAR(100) NOT NULL UNIQUE,
    mdp VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    FOREIGN KEY (fkRole) REFERENCES Role(pkR)
);

-- Table Salon
CREATE TABLE Salon (
    pkS INT AUTO_INCREMENT PRIMARY KEY,
    fkU_proprio INT,
    nom VARCHAR(100) NOT NULL,
    visibilite BOOLEAN DEFAULT TRUE,  -- TRUE = public
    prive BOOLEAN DEFAULT FALSE,
    topic VARCHAR(255),
    FOREIGN KEY (fkU_proprio) REFERENCES Utilisateur(pkU)
);

-- Table Propriétaire (optionnelle car déjà dans Salon.fkU_proprio)
-- Tu peux ne pas la créer si tu utilises juste fkU_proprio dans Salon

-- Table Modérer (relation utilisateur <-> salon)
CREATE TABLE Moderer (
    fkU INT,
    fkS INT,
    PRIMARY KEY (fkU, fkS),
    FOREIGN KEY (fkU) REFERENCES Utilisateur(pkU),
    FOREIGN KEY (fkS) REFERENCES Salon(pkS)
);

-- Table Membre (relation utilisateur <-> salon)
CREATE TABLE Membre (
    fkU INT,
    fkS INT,
    PRIMARY KEY (fkU, fkS),
    FOREIGN KEY (fkU) REFERENCES Utilisateur(pkU),
    FOREIGN KEY (fkS) REFERENCES Salon(pkS)
);

-- Table Message (écriture dans un salon par un utilisateur)
CREATE TABLE Message (
    pkMsg INT AUTO_INCREMENT PRIMARY KEY,
    fkU INT,
    fkS INT,
    message TEXT NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (fkU) REFERENCES Utilisateur(pkU),
    FOREIGN KEY (fkS) REFERENCES Salon(pkS)
);
