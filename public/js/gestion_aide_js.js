document.addEventListener("DOMContentLoaded", () => {

    /* ============================================================================
       🔍 RÉFÉRENCES DOM
    ============================================================================ */

    const searchTitre = document.getElementById("search-name");
    const selectType = document.getElementById("filter-type");


    const table = document.getElementById("table-aides");
    const rows  = document.querySelectorAll("#table-aides tbody tr");
    const noResult = document.getElementById("no-result");

    const changeButtons = document.querySelectorAll(".btn-display");
    const btnRemo =document.querySelectorAll(".btn-delete");


    // Nouveau modal spécifique aux évènements
    const modal = document.getElementById("modal-edit-aide");
    const modalBox = modal.querySelector(".modal-content");
    const btnView = document.querySelectorAll(".btn-display");

     /* ============================================================
   🗑️ MODAL SUPPRESSION
    ============================================================ */
    const modalDelete    = document.getElementById("modal-delete-aides");
    const modalDeleteBox = modalDelete?.querySelector(".modal-content");
    const btnConfirmDelete = document.getElementById("btn-confirm-delete");
    const btnCancelDelete  = document.getElementById("btn-cancel-delete");

    /* ===== Champs du modal ===== */
    const fNomPrenom = document.getElementById("edit-nom-prenom");
    const fType      = document.getElementById("edit-type-aide");
    const fSujet     = document.getElementById("edit-sujet-aide");
    const fMessage   = document.getElementById("edit-message-aide");
    const fEmail     = document.getElementById("edit-email-aide");
    const fTelephone = document.getElementById("edit-telephone-aide");
    const fDate      = document.getElementById("edit-date-aide");

    let aideIdToDelete = null

    /* ===== Récupération CSRF ===== */
    const fPikachu = document.getElementById("pikachu_csrf")

   
    /* ============================================================================
       🔎 FILTRAGE TABLEAU
    ============================================================================ */

    function refreshTableVisibility() {

        table.style.opacity = "0.4";

        setTimeout(() => {

            let visibleCount = 0;

            rows.forEach(row => {

                const titre = row.children[0].textContent.toLowerCase();
                const typeAide  = row.children[2].textContent.toLowerCase();

               
                const search = searchTitre.value.toLowerCase().trim();
                const typeValue = selectType.value.toLowerCase().trim();


                const matchType = typeValue === "" || typeAide.includes(typeValue);
                

                const shouldShow = titre.includes(search) && matchType;

                row.style.display = shouldShow ? "" : "none";



                if (shouldShow) visibleCount++;
            });

            noResult.classList.toggle("visible", visibleCount === 0);

            table.style.opacity = "1";

        }, 120);
    }

    searchTitre.addEventListener("input", refreshTableVisibility);
    selectType.addEventListener("change", refreshTableVisibility);
    refreshTableVisibility();



    /* =========================================================
       🪟 MODAL – CONSULTATION D’UNE DEMANDE D’AIDE
    ========================================================= */
   
    /* =========================================================
       👁️ CONSULTATION D’UNE AIDE
       - Fetch
       - Remplissage
       - Ouverture du modal
    ========================================================= */

    btnView.forEach(btn => {

        btn.addEventListener("click", async () => {

            const aideId = btn.dataset.id;
            if (!aideId) return;

            try {
                const response = await fetch(`/?dest=fetch_aides&aide_id=${aideId}`);
                const data = await response.json();

                const nom = data.nom ?? "";
                const prenom = data.prenom ?? "";
                fNomPrenom.value = `${prenom} ${nom}`.trim();

                fType.value      = data.type_libelle ?? "";
                fSujet.value     = data.sujet ?? "";
                fMessage.value   = data.message ?? "";
                fEmail.value     = data.email ?? "";
                fTelephone.value = data.telephone_num ?? "Non renseigné";

                if (data.date_demande) {
                    fDate.value = data.date_demande.split(" ")[0];
                } else {
                    fDate.value = "";
                }

                openModal(modal , modalBox);

            } catch {
                // Erreur silencieuse (UX non bloquée)
            }
        });
    });
    
    document.getElementById("btn-close-modal").addEventListener("click" ,()=>
    {
        closeModal(modal , modalBox);
    });
    modal.onclick = e => { if (e.target === modal) closeModal(modal , modalBox); };
    /* =========================================================
       🔔 CARTES DE NOTIFICATION
    ========================================================= */

    const cardSuccess = document.getElementById("card-success");
    const cardError   = document.getElementById("card-error");

    /**
     * Affiche une notification (success | error)
     */
    function showNotif(type) {
        const card = type === "success" ? cardSuccess : cardError;

        card.classList.add("show");

        // Disparition automatique après 3 secondes
        setTimeout(() => {
            card.classList.remove("show");
        }, 3000);
    }


    /* =========================================================
       🗑️ SUPPRESSION D’UNE AIDE
    ========================================================= */

    const deleteButtons = document.querySelectorAll(".btn-delete");

    deleteButtons.forEach(btn => 
    {
        btn.addEventListener("click", () => {
            aideIdToDelete = btn.dataset.id;
            openModal(modalDelete, modalDeleteBox);
        });
    });

    /* Annuler suppression */
    btnCancelDelete?.addEventListener("click", () => 
    {
        aideIdToDelete = null;
        closeModal(modalDelete, modalDeleteBox);
    });

    modalDelete?.addEventListener("click", e => {
        if (e.target === modalDelete) closeModal(modalDelete, modalDeleteBox);
    });

    
    btnConfirmDelete.addEventListener("click", async () => 
    {

        const aideId = aideIdToDelete;
        if (!aideId) return;

        try 
        {
            const response = await fetch("/?dest=delete_aides", 
            {
                method: "POST",
                headers: {
                "Content-Type": "application/json"
                },
                body: JSON.stringify({
                aide_id: aideId,
                pikachu_csrf : fPikachu.value.trim()
                })
            });
            closeModal(modalDelete , modalDeleteBox)
            const result = await response.json();

            if (result.success) {
                showNotif("success");
                setTimeout(() => location.reload(), 2000);
            } else {
                showNotif("error");
            }

        }
        catch (e)
        {
            showNotif("error");
        }
    });
   
            


    window.addEventListener("pageshow", () => {

        // Réinitialisation des champs
        searchTitre.value  = "";
        selectType.value = "";

        // Réaffichage de toutes les lignes
        rows.forEach(row => {
            row.style.display = "";
        });

        table.style.opacity = "1";
    });


    


    function openModal(modal, box) {
        modal.style.display = "flex";
        box.classList.remove("closing");
        box.classList.add("open");
    }

    function closeModal(modal, box) {
        box.classList.remove("open");
        box.classList.add("closing");
        box.addEventListener("animationend", () => {
            modal.style.display = "none";
            box.classList.remove("closing");
        }, { once: true });
    }


});
