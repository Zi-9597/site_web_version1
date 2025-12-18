<?php
// On gère la route par défaut
if ($_SERVER['REQUEST_URI'] !== '/') {
    // Définition des routes
    $routes = [
        "inscription"    => "templates/externe/inscription_v2.php",
        "info_insc"      => "templates/externe/info_inscription.php",
        "goodies"        => "templates/externe/goodies_site.php",
        "actualite"      => "templates/externe/actualite_site.php",
        "connection"     => "templates/externe/connection.php",
        "add_subscriber" => "templates/externe/add_subscriber.php",
        "info_conn"      => "templates/externe/info_conn.php",
        "success"        => "templates/externe/success.php",
        "aides"           => "templates/externe/non_membre/aide_nm.php",
        "acceuil"        => "templates/externe/acceuil_site.php", // default page
        "evenements"     => "templates/externe/non_membre/evenement_nm.php",
        "parametres"     => "templates/externe/commun/parametres.php",
        "dep_evenmt"     => "templates/externe/membre/depot_event.php",
        "add_event"      => "templates/externe/ajouter_evenement.php",
        "rech_event"     => "templates/externe/membre/access_event.php",
        "fetch_data"     => "templates/externe/search_event.php",
        "dep_offre"      => "templates/externe/ancien/depot_offre.php",
        "ajout_emploie"  => "templates/externe/ajouter_job.php",
        "cp_fetch"        => "commun/recherche_departement.php",
        "offre_emploie"  => "templates/externe/commun/offre_emploi.php",
        "reche_emploie"   => "templates/externe/recherche_emploie.php",
        "update_data"    => "templates/externe/update_new_info.php",
        "depot_job"     =>  "templates/externe/etudiant/depot_job.php",
        "fetch_job"  => "templates/externe/etudiant/recherche_job.php",
        "change_membre" => "templates/externe/president/gestion_etudiant.php",
        "update_membre_assoc" => "templates/externe/mise_jour_membre.php",
        "gestion_offres"  => "templates/externe/membre/gestion_offres_eea.php",
        "info_fetch_offre" => "templates/externe/fetch_offre.php",
        "info_update_offre" => "templates/externe/update_new_offre.php",
        "remove_offre" => "templates/externe/remove_job.php",
        "gestion_evenements" => "templates/externe/membre/gestion_event.php",
        "info_cherche_events" => "templates/externe/recherche_gestion_events.php",
        "update_event" => "templates/externe/update_event_new.php",
        "suppression_event" => "templates/externe/suppress_event_new.php",
        "gestion_actualite" => "templates/externe/membre/gestion_actualite.php",
        "add_actualite" => "templates/externe/add_actualite.php",
        "get_actualite" => "templates/externe/gets_actualite.php",
        "update_actualite" => "templates/externe/update_new_actualite.php",
        "remove_actualite" => "templates/externe/remove_actualite.php",
        "get_actualite" => "templates/externe/display_actualite.php",
        "add_aide" => "templates/externe/add_aide_new.php",
        "add_aide_conn" => "templates/externe/etudiant/aides_connectes.php",
        "gestion_aides_etudiants" => "templates/externe/membre/gestion_aides.php",
        "fetch_aide" => "templates/externe/fetch_aides_etud.php",
        "delete_aide" => "templates/externe/delete_aide_etu.php"
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
