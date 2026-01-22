<?php
/* ============================================================================
📌 INDEX PRINCIPAL — POINT D’ENTRÉE UNIQUE DE L’APPLICATION
-------------------------------------------------------------------------------
- Ce fichier centralise le routage de l’application
- Le routage est basé sur le paramètre GET ?dest=
- Toutes les routes autorisées sont déclarées explicitement
- Toute route inconnue redirige volontairement vers l’accueil
- Aucune logique métier (authentification, rôles, sécurité) n’est gérée ici
============================================================================ */

   /* ============================================================
   🧭 TABLE DE ROUTAGE
   ---------------------------------------------------------------
   - Clé   : valeur attendue dans l’URL (?dest=...)
   - Valeur: fichier PHP correspondant à charger
   - Cette whitelist empêche l’inclusion de fichiers arbitraires
   ============================================================ */
   $routes = [

      /* =========================
         INSCRIPTION
      ========================= */
      "inscription"    => "templates/externe/authentification/inscription.php",
      "add_subscriber" => "templates/externe/data_base_request/add_subscriber.php",
      "success"        => "templates/externe/authentification/success.php",
      "confirmation_inscription" => "templates/externe/authentification/confirmation_inscription.php",
      "confirmed"      => "templates/externe/authentification/succes_token.php",
      "erreur_inscription" => "templates/externe/authentification/echec_inscription.php",

      /* =========================
         CONNEXION
      ========================= */
      "connection"     => "templates/externe/authentification/connection.php",
      "info_conn_v1"   => "templates/externe/data_base_request/fetch_connexion.php",

      /* =========================
         ACCUEIL & ACTUALITÉS
      ========================= */
      "acceuil"        => "templates/externe/features/commun/accueil_interface.php", // page par défaut
      "actualite"      => "templates/externe/features/commun/actualite_interface.php",
      "get_actualites" => "templates/externe/data_base_request/fetch_actualites.php",
      

      /* =========================
         GOODIES
      ========================= */
      "goodies"        => "templates/externe/features/commun/goodies_interface.php",
      "get_goodies"    => "templates/externe/data_base_request/fetch_goodies.php",

      /* =========================
         ÉVÉNEMENTS
      ========================= */
      "rech_event"     => "templates/externe/features/commun/evenements_interface.php",
      "get_events"     => "templates/externe/data_base_request/fetch_events.php",

      /* =========================
         AIDES (ÉTUDIANTS)
      ========================= */
      "aides_etud"     => "templates/externe/features/etudiant/aides_interface.php",
      "add_aide"       => "templates/externe/data_base_request/gestion_aide/add_aide.php",

      /* =========================
         PARAMÈTRES UTILISATEUR
      ========================= */
      "parametres"     => "templates/externe/features/commun/parametres.php",
      "update_data"    => "templates/externe/data_base_request/update_user_info.php",

      /* =========================
         OFFRES D’EMPLOI
      ========================= */
      "offre_emploie"  => "templates/externe/features/commun/recherche_job.php",
      "fetch_emploie"  => "templates/externe/data_base_request/fetch_emploie.php",

      /* =========================
         DÉPÔT D’OFFRES
      ========================= */
      "depot_job"      => "templates/externe/features/etudiant/depot_job_etudiant.php",
      "depot_contrat"  => "templates/externe/features/ancien/depot_contrat.php",
      "ajouter_contrat"=> "templates/externe/data_base_request/gestion_offres/ajout_contrat.php",

      /* =========================
         GESTION DES OFFRES
      ========================= */
      "manage_job"     => "templates/externe/features/commun/gestion_offres.php",
      "update_offre"   => "templates/externe/data_base_request/gestion_offres/update_offre.php",
      "delete_offre"   => "templates/externe/data_base_request/gestion_offres/suppress_offre.php",

      /* =========================
         ÉVÉNEMENTS — BUREAU
      ========================= */
      "depot_event"    => "templates/externe/features/bureau/ajout_event.php",
      "ajouter_event"  => "templates/externe/data_base_request/gestion_event/add_event.php",
      "manage_event"   => "templates/externe/features/bureau/manage_event.php",
      "update_event"   => "templates/externe/data_base_request/gestion_event/update_event.php",
      "delete_event"   => "templates/externe/data_base_request/gestion_event/suppress_event.php",

      /* =========================
         GESTION DES MEMBRES (PRÉSIDENT)
      ========================= */
      "manage_adherent"=> "templates/externe/features/president/gestion_adherent.php",
      "fetch_adherent" => "templates/externe/data_base_request/fetch_membre.php",
      "update_adherent"=> "templates/externe/data_base_request/gestion_etudiant/update_adherent.php",

      /* =========================
         ACTUALITÉS — BUREAU
      ========================= */
      "manage_actualite"=> "templates/externe/features/bureau/gestion_actualite.php",
      "add_actualite"  => "templates/externe/data_base_request/gestion_actualite/add_actualite.php",
      "update_actualite"=> "templates/externe/data_base_request/gestion_actualite/update_actualite.php",
      "delete_actualite"=> "templates/externe/data_base_request/gestion_actualite/delete_actualite.php",

      /* =========================
         GOODIES — BUREAU
      ========================= */
      "manage_goodies" => "templates/externe/features/bureau/gestion_goodies.php",
      "add_goodies"    => "templates/externe/data_base_request/gestion_goodies/add_goodies.php",
      "update_goodies" => "templates/externe/data_base_request/gestion_goodies/update_goodies.php",
      "delete_goodies" => "templates/externe/data_base_request/gestion_goodies/suppress_goodies.php",

      /* =========================
         AIDES — BUREAU
      ========================= */
      "manage_aides"   => "templates/externe/features/bureau/gestion_aides.php",
      "fetch_aides"    => "templates/externe/data_base_request/fetch_aides.php",
      "delete_aides"   => "templates/externe/data_base_request/gestion_aide/suppress_aides.php",

      /* =========================
         INFORMATIONS ASSOCIATION
      ========================= */
      "apropos"        => "commun/propos_nous.php",
      "contact_assoc"  => "commun/contact_eea.php",

      /* =========================
         DÉCONNEXION
      ========================= */
      "logout"         => "templates/externe/authentification/logout.php",

      /* =========================
         CHECKER L'EMAIL LORS DE L'INSCRITPION
      ========================= */
      "check_mail"    => "templates/externe/data_base_request/fetch_same_email.php",



      "mention_legale"   => "commun/mention_legale.php"
   ];

   /* ============================================================
   🎯 RÉCUPÉRATION DE LA ROUTE DEMANDÉE
   ---------------------------------------------------------------
   - Si ?dest= est absent → affichage de l’accueil
   ============================================================ */
   $dest = $_GET['dest'] ?? 'acceuil';

   /* ============================================================
   🛡️ RÉSOLUTION DE LA ROUTE
   ---------------------------------------------------------------
   - Si la route n’existe pas dans la table → accueil (fallback)
   ============================================================ */
   $page = $routes[$dest] ?? $routes['acceuil'];

   /* ============================================================
   🚀 CHARGEMENT DE LA PAGE CORRESPONDANTE
   ============================================================ */
   require_once $page;

?>