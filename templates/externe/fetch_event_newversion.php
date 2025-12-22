<?php

require_once "require_db.php";

header("Content-Type: application/json");

try {

    /* =========================================
       🔎 PARAMÈTRES GET
    ========================================= */

    $id_event  = null;
    $id_membre = null;

    // ID événement (prioritaire)
    if (!empty($_GET['id_event'])) {
        $id_event = (int) $_GET['id_event'];
    }

    // ID membre optionnel
    if (!empty($_GET['id_user'])) {
        $id_comb = $_GET['id_user'];
        [$id_membre] = explode("_", $id_comb);
    }

    /* =========================================
       📡 APPEL BASE DE DONNÉES
    ========================================= */

    $events = EEA_Database::fetch_events(
        $id_membre,
        $id_event
    );

    /* =========================================
       ❌ AUCUN RÉSULTAT
    ========================================= */

    if (empty($events)) {
        echo json_encode([
            "success" => false,
            "error"   => "Aucun événement trouvé"
        ]);
        exit;
    }

    /* =========================================
       ✅ RETOUR JSON (1 événement ou liste)
    ========================================= */

    echo json_encode([
        "success" => true,
        "data"    => $id_event ? $events[0] : $events
    ]);

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "error"   => $e->getMessage()
    ]);
}

exit;
