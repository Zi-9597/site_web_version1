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
    const btnClose = modal.querySelector(".modal-btn-cancel");


    const btnView = document.querySelectorAll(".btn-display");

    /* ===== Champs du modal ===== */
    const fNomPrenom = document.getElementById("edit-nom-prenom");
    const fType      = document.getElementById("edit-type-aide");
    const fSujet     = document.getElementById("edit-sujet-aide");
    const fMessage   = document.getElementById("edit-message-aide");
    const fEmail     = document.getElementById("edit-email-aide");
    const fTelephone = document.getElementById("edit-telephone-aide");
    const fDate      = document.getElementById("edit-date-aide");

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
       🪟 OUVERTURE / FERMETURE MODAL
    ========================================================= */

    function openModal() {
        modal.style.display = "flex";
        modalBox.classList.remove("closing");
        modalBox.classList.add("open");
    }

    function closeModal() {
        modalBox.classList.remove("open");
        modalBox.classList.add("closing");

        modalBox.addEventListener("animationend", () => {
            modal.style.display = "none";
            modalBox.classList.remove("closing");
        }, { once: true });
    }

    btnClose.addEventListener("click", closeModal);

    modal.addEventListener("click", (e) => {
        if (e.target === modal) closeModal();
    });


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

                openModal();

            } catch {
                // Erreur silencieuse (UX non bloquée)
            }
        });
    });

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

    deleteButtons.forEach(btn => {
        btn.addEventListener("click", async () => {

            const aideId = btn.dataset.id;
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

            const result = await response.json();

            if (result.success) {
                showNotif("success");
                setTimeout(() => location.reload(), 2000);
            } else {
                showNotif("error");
            }

            } catch (e) {
                showNotif("error");
            }
        });
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

    document.querySelector(".modal-btn-cancel").onclick = closeModal;
    modal.onclick = e => { if (e.target === modal) closeModal(); };


});
