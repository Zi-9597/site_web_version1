<?php
if ($id_user !== null) {
    // Example: split user id into two parts
    list($id_member, $id_num) = explode("_", $id_user);
    require_once "templates/externe/commun/actualite_mem.php";
    exit;
} else {

    require_once "templates/externe/non_membre/actualite.php";
    exit;
    
}
?>
