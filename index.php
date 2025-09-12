<?php if ($_SERVER['REQUEST_URI'] !== '/') : ?>
    <?php
        // Define your routes in a single array
        $routes = [
            "inscription"    => "templates/externe/inscription_v2.php",
            "info_insc"      => "templates/externe/info_inscription.php",
            "goodies"        => "templates/externe/goodies_site.php",
            "actualite"      => "templates/externe/actualite_site.php",
            "dbb"            => "connect_1.php",
            "connection"     => "templates/externe/connection.php",
            "add_subscriber" => "templates/externe/add_subscriber.php",
            "info_conn"      => "templates/externe/info_conn.php",
            "success"        => "templates/externe/success.php",
            "acceuil"        => "templates/externe/acceuil_site.php", // default page
            "parametres"     => "templates/externe/commun/parametres.php",
            "dep_evenmt"     => "templates/externe/membre/depot_event.php",
            "add_event"      =>  "templates/externe/ajouter_evenement.php",
            "rech_event"    =>  "templates/externe/membre/access_event.php",
            "fetch_data"     => "templates/externe/search_event.php"
        ];

        // 1. Get the dest param (fallback = acceuil)

    
        $dest = $_GET['dest'] ?? 'acceuil';
        
        // 2. Check if the route exists, otherwise fallback
        $page = $routes[$dest] ?? $routes['acceuil'];
        $id_user = $_GET['id_user'] ?? null;
        // 3. Load the resolved page
        require_once $page;
    ?>

<?php else :  ?>
    <?php require 'templates/externe/acceuil_site.php' ?>;
<?php endif; ?>


