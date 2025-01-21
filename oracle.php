<?php
// Connexion à Oracle avec OCI8
$conn = oci_connect('ROOT', 'root', 'localhost');

if (!$conn) {
    $e = oci_error();
    die("Erreur de connexion Oracle : " . $e['message']);
} else {
    echo "Connexion réussie à Oracle!";
}

// Fermer la connexion
oci_close($conn);
?>
