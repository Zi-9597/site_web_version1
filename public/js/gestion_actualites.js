document.addEventListener("DOMContentLoaded", () => {

    /* ============================================================================
       🔍 FILTRAGE DU TABLEAU DES ACTUALITÉS
    ============================================================================ */

    const searchTitre = document.getElementById("search-titre-actu");
    const table       = document.getElementById("table-offres");
    const rows        = document.querySelectorAll("#table-offres tbody tr");
    const noResult    = document.getElementById("no-result");

    function refreshTableVisibility() {
        table.style.opacity = "0.4";

        setTimeout(() => {
            let visibleCount = 0;
            const search = searchTitre.value.toLowerCase().trim();

            rows.forEach(row => {
                const titre = row.children[0].textContent.toLowerCase();
                const show = titre.includes(search);
                row.style.display = show ? "" : "none";
                if (show) visibleCount++;
            });

            noResult.classList.toggle("visible", visibleCount === 0);
            table.style.opacity = "1";
        }, 120);
    }

    searchTitre?.addEventListener("input", refreshTableVisibility);
    refreshTableVisibility();

    /* ============================================================================
       🔔 NOTIFICATIONS (RÉUTILISATION EXISTANTE)
    ============================================================================ */

    function showCard(type) {
        const card = document.getElementById(
            type === "success" ? "card-success" : "card-error"
        );
        card.classList.add("show");
        setTimeout(() => card.classList.remove("show"), 3000);
    }

    /* ============================================================================
       🟢 MODAL : AJOUTER UNE ACTUALITÉ
    ============================================================================ */

    const btnAddActu    = document.querySelector(".btn-add-actu");
    const modalAdd     = document.getElementById("modal-add-actu");
    const modalAddBox  = modalAdd?.querySelector(".modal-content");
    const btnCancelAdd = modalAdd?.querySelector(".modal-btn-cancel");

    const fAddTitre = document.getElementById("add-titre-actu");
    const fAddLien  = document.getElementById("add-lien-actu");
    const fAddDesc  = document.getElementById("add-desc-actu");


    const deleteButtons = document.querySelectorAll(".btn-delete");



    function openAddModal() {
        modalAdd.style.display = "flex";
        modalAddBox.classList.add("open");
    }

    function closeAddModal() {
        modalAddBox.classList.remove("open");
        modalAddBox.classList.add("closing");
        modalAddBox.addEventListener("animationend", () => {
            modalAdd.style.display = "none";
            modalAddBox.classList.remove("closing");
        }, { once: true });
    }

    btnAddActu?.addEventListener("click", openAddModal);
    btnCancelAdd?.addEventListener("click", closeAddModal);
    modalAdd?.addEventListener("click", e => {
        if (e.target === modalAdd) closeAddModal();
    });

    /* ============================================================================
       ✍️ LIMITATION 2500 CARACTÈRES – AJOUT
    ============================================================================ */

    fAddDesc?.addEventListener("input", () => {
        if (fAddDesc.value.length > 2500) {
            fAddDesc.value = fAddDesc.value.substring(0, 2500);
        }
    });

    /* ============================================================================
       🚀 AJAX – AJOUT ACTUALITÉ
    ============================================================================ */

    const btnSaveAdd = document.getElementById("btn-add-actu");

    btnSaveAdd?.addEventListener("click", async () => {

        const titre = fAddTitre.value.trim();
        const lien  = fAddLien.value.trim();
        const desc  = fAddDesc.value.trim();

        if (!titre || !desc) {
            showCard("error");
            return;
        }

        try {
            const response = await fetch("/?dest=add_actualite", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    titre_actu: titre,
                    lien_actu: lien,
                    desc_actu: desc
                })
            });

            const result = await response.json();

            if (result.success) {
                showCard("success");
                closeAddModal();
                setTimeout(() => location.reload(), 3000);
            } else {
                showCard("error");
            }

        } catch (e) {

            showCard("error");
        }
    });

    /* ============================================================================
       🟣 MODAL : MODIFIER ACTUALITÉ (FRONT + FETCH)
    ============================================================================ */

    const modalEdit     = document.getElementById("modal-edit-event");
    const modalEditBox  = modalEdit?.querySelector(".modal-content");
    const btnCancelEdit = modalEdit?.querySelector(".modal-btn-cancel");

    const fEditTitre = document.getElementById("edit-titre-actu");
    const fEditLien  = document.getElementById("edit-lien-actu");
    const fEditDesc  = document.getElementById("edit-desc-actu");
    const fEditDate  = document.getElementById("edit-date-depot");


    
    const editButtons = document.querySelectorAll(".btn-change");
    const btnSaveEdit = document.getElementById("btn-save-actu");

    function openEditModal() {
        modalEdit.style.display = "flex";
        modalEditBox.classList.add("open");
    }

    function closeEditModal() {
        modalEditBox.classList.remove("open");
        modalEditBox.classList.add("closing");
        modalEditBox.addEventListener("animationend", () => {
            modalEdit.style.display = "none";
            modalEditBox.classList.remove("closing");
        }, { once: true });
    }

    btnCancelEdit?.addEventListener("click", closeEditModal);
    modalEdit?.addEventListener("click", e => {
        if (e.target === modalEdit) closeEditModal();
    });

    /* ============================================================================
       ✍️ LIMITATION 2500 CARACTÈRES – MODIFIER
    ============================================================================ */

    fEditDesc?.addEventListener("input", () => {
        if (fEditDesc.value.length > 2500) {
            fEditDesc.value = fEditDesc.value.substring(0, 2500);
        }
    });

    /* ============================================================================
       📡 FETCH ACTUALITÉ + REMPLISSAGE MODAL
    ============================================================================ */

    editButtons.forEach(btn => {
        btn.addEventListener("click", async () => {

            const id = btn.dataset.id;


            // 🔴 On attache l’ID au bouton Valider
            const btnSave = document.getElementById("btn-save-actu");
            btnSave.dataset.id = id;
            openEditModal();

            try {
                const response = await fetch(`/?dest=get_actualite&id_actu=${id}`);
                const data = await response.json();

                fEditTitre.value = data.titre_actu ?? "";
                fEditLien.value  = data.lien_actu ?? "";
                fEditDesc.value  = data.desc_actu ?? "";
                fEditDate.value  = data.date_depot ?? "";

            } catch (e) {
                showCard("error");
            }
        });
    });


    /* ============================================================================
   💾 MISE À JOUR D’UNE ACTUALITÉ
  ============================================================================ */


    if (btnSaveEdit) {
        btnSaveEdit.addEventListener("click", async () => {

            /* ---------------------------------------------------------------------
            🔎 RÉCUPÉRATION DE L’ID
            ---------------------------------------------------------------------
            L’ID est stocké sur le bouton lors de l’ouverture du modal :
            btnSaveEdit.dataset.id = actu_id
            --------------------------------------------------------------------- */
            const actuId = btnSaveEdit.dataset.id;

            /* ---------------------------------------------------------------------
            ✍️ CONSTRUCTION DU PAYLOAD
            ---------------------------------------------------------------------
            On récupère les valeurs saisies dans le modal "Modifier".
            --------------------------------------------------------------------- */
            const payload = {
                actu_id: actuId,
                titre_actu: fEditTitre.value.trim(),
                lien_actu:  fEditLien.value.trim(),
                desc_actu:  fEditDesc.value.trim()
            };

            /* ---------------------------------------------------------------------
            ✅ VALIDATION MINIMALE CÔTÉ FRONT
            ---------------------------------------------------------------------
            Le titre et la description sont obligatoires.
            --------------------------------------------------------------------- */
            if (!payload.titre_actu || !payload.desc_actu) {
                showCard("error");
                return;
            }

            /* ---------------------------------------------------------------------
            🚀 APPEL AJAX VERS LE CONTROLLER PHP
            ---------------------------------------------------------------------
            Endpoint : /?dest=update_actualite
            Méthode  : POST
            Format   : JSON
            --------------------------------------------------------------------- */
            try {
                const response = await fetch("/?dest=update_actualite", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();

                /* -----------------------------------------------------------------
                🔔 GESTION DU RETOUR SERVEUR
                -----------------------------------------------------------------
                - success = true  → notification verte + reload après 3s
                - success = false → notification rouge
                ----------------------------------------------------------------- */
                if (result.success) {
                    showCard("success");      // Carte verte existante
                    closeEditModal();         // Fermeture du modal

                    // Recharge complète après 3 secondes
                    setTimeout(() => {
                        location.reload();
                    }, 3000);

                } else {
                    showCard("error");        // Carte rouge existante
                }

            } catch (error) {
  
                showCard("error");
            }
        });
    }


    deleteButtons.forEach(btn => {
    btn.addEventListener("click", async () => {

        /* ---------------------------------------------------------------------
           🔎 RÉCUPÉRATION DE L’ID DE L’ACTUALITÉ
           ---------------------------------------------------------------------
           L’ID est stocké dans data-id sur le bouton Supprimer
        --------------------------------------------------------------------- */
        const actuId = btn.dataset.id;
        if (!actuId) return;

        /* ---------------------------------------------------------------------
           🚀 APPEL AJAX – SUPPRESSION DIRECTE
        --------------------------------------------------------------------- */
        try {
            const response = await fetch("/?dest=remove_actualite", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ actu_id: actuId })
            });

            const result = await response.json();

            /* -----------------------------------------------------------------
               🔔 RETOUR VISUEL UTILISATEUR
            ----------------------------------------------------------------- */
            if (result.success) {
                showCard("success");

                // Recharge complète après 3 secondes
                setTimeout(() => {
                    location.reload();
                }, 3000);

            } else {
                showCard("error");
            }

        } catch (error) {
            showCard("error");
        }
    });
});



});
