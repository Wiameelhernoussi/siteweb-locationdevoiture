<?php
include 'Connection.php';

$conn = new Connection();

$queryClients = "
CREATE TABLE IF NOT EXISTS Clients (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(30) NOT NULL,
    lastname VARCHAR(30) NOT NULL,
    email VARCHAR(50) UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    address VARCHAR(255) NOT NULL
);";
$conn->createTable($queryClients);

$queryCars = "
CREATE TABLE IF NOT EXISTS Cars (
    car_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    marque VARCHAR(50) NOT NULL,
    modele VARCHAR(50) NOT NULL,
    annee YEAR NOT NULL,
    plaque VARCHAR(20) NOT NULL UNIQUE,
    statut ENUM('disponible', 'non disponible') NOT NULL,
    prix_par_jour DECIMAL(10, 2) NOT NULL,  -- Il manquait une virgule ici
    image VARCHAR(255) DEFAULT NULL
);";

$conn->createTable($queryCars);

$queryLocations = "
CREATE TABLE IF NOT EXISTS Locations (
    location_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id INT(6) UNSIGNED NOT NULL,
    car_id INT(6) UNSIGNED NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    prix_total DECIMAL(10, 2) NOT NULL,
    statut ENUM('en cours', 'terminé') NOT NULL,
    FOREIGN KEY (client_id) REFERENCES Clients(id) ON DELETE CASCADE,
    FOREIGN KEY (car_id) REFERENCES Cars(car_id) ON DELETE CASCADE
);";
$conn->createTable($queryLocations);

$queryMaintenance = "
CREATE TABLE IF NOT EXISTS Maintenance (
    maintenance_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    car_id INT(6) UNSIGNED NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    description TEXT NOT NULL,
    FOREIGN KEY (car_id) REFERENCES Cars(car_id) ON DELETE CASCADE
);";
$conn->createTable($queryMaintenance);

$queryPayments = "
CREATE TABLE IF NOT EXISTS Payments (
    payment_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    location_id INT(6) UNSIGNED NOT NULL,
    montant DECIMAL(10, 2) NOT NULL,
    date_paiement DATE NOT NULL,
    FOREIGN KEY (location_id) REFERENCES Locations(location_id) ON DELETE CASCADE
);";
$conn->createTable($queryPayments);

$queryAdmins = "
CREATE TABLE IF NOT EXISTS Admins (
    admin_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(30) NOT NULL,
    lastname VARCHAR(30) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL
);";
$conn->createTable($queryAdmins);

$conn->closeConnection();
?>