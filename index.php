<?php
// On gère la route par défaut
if ($_SERVER['REQUEST_URI'] !== '/') {
    // Définition des routes
    $routes = [
        "inscription"    => "templates/externe/inscription_v2.php",
        "info_insc"      => "templates/externe/info_inscription.php",
        "goodies"        => "templates/externe/goodies_site.php",
        "actualite"      => "templates/externe/actualite_site.php",
        "dbb"            => "index_con.php",
        "connection"     => "templates/externe/connection.php",
        "add_subscriber" => "templates/externe/add_subscriber.php",
        "info_conn"      => "templates/externe/info_conn.php",
        "success"        => "templates/externe/success.php",
        "acceuil"        => "templates/externe/acceuil_site.php", // default page
        "parametres"     => "templates/externe/commun/parametres.php",
        "dep_evenmt"     => "templates/externe/membre/depot_event.php",
        "add_event"      => "templates/externe/ajouter_evenement.php",
        "rech_event"     => "templates/externe/membre/access_event.php",
        "fetch_data"     => "templates/externe/search_event.php",
        "dep_offre"      => "templates/externe/ancien/depot_offre.php"
    ];

    // 1. On récupère le paramètre dest (par défaut = acceuil)
    $dest = $_GET['dest'] ?? 'acceuil';

    // 2. On vérifie si la route existe
    $page = $routes[$dest] ?? $routes['acceuil'];

    // 3. On récupère l'id utilisateur si présent
    $id_user = $_GET['id_user'] ?? null;

    // 4. On charge la page correspondante
    require_once $page;
} else {
    require 'templates/externe/acceuil_site.php';
}
