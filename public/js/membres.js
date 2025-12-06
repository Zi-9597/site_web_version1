document.addEventListener("DOMContentLoaded", () => {
    /* ----------------------------------------------------------
       🎯 RÉFÉRENCES DES ÉLÉMENTS DU FILTRE (HTML)
    ---------------------------------------------------------- */
    const searchPrenom  = document.getElementById("search-prenom");
    const searchNom     = document.getElementById("search-nom");
    const searchSection = document.getElementById("filiere-section");
    const searchAssoc   = document.getElementById("membre-assoc");
    const searchVille   = document.getElementById("search-ville");
    const bureauSwitch  = document.getElementById("bureau-switch");

    const table = document.getElementById("table-membres");
    const rows  = document.querySelectorAll("#table-membres tbody tr");
    const noResult = document.getElementById("no-result");


    const changeButtons = document.querySelectorAll(".btn-change")
    /* ==========================================================
       🟣 FONCTION CENTRALE : APPLIQUER TOUS LES FILTRES
    ========================================================== */
    function refreshTableVisibility() {

        table.style.opacity = "0.4";

        setTimeout(() => {

            let visibleCount = 0;

            rows.forEach(row => {

                const shouldShow =
                    row.dataset.matchPrenom  !== "0" &&
                    row.dataset.matchNom     !== "0" &&
                    row.dataset.matchSection !== "0" &&
                    row.dataset.matchAssoc   !== "0" &&
                    row.dataset.matchVille   !== "0" &&
                    row.dataset.matchBureau  !== "0";

                row.style.display = shouldShow ? "" : "none";

                if (shouldShow) visibleCount++;
            });

            noResult.classList.toggle("visible", visibleCount === 0);

            table.style.opacity = "1";

        }, 120);
    }



    /* ==========================================================
       🔎 FILTRE : PRÉNOM (colonne 0)
    ========================================================== */
    searchPrenom.addEventListener("input", () => {
        const value = searchPrenom.value.toLowerCase();

        rows.forEach(row => {
            const prenom = row.children[0].textContent.toLowerCase();
            row.dataset.matchPrenom = prenom.includes(value) ? "1" : "0";
        });

        refreshTableVisibility();
    });



    /* ==========================================================
       🔎 FILTRE : NOM (colonne 1)
    ========================================================== */
    searchNom.addEventListener("input", () => {
        const value = searchNom.value.toLowerCase();

        rows.forEach(row => {
            const nom = row.children[1].textContent.toLowerCase();
            row.dataset.matchNom = nom.includes(value) ? "1" : "0";
        });

        refreshTableVisibility();
    });



    /* ==========================================================
       🔎 FILTRE : SECTION (colonne 2)
    ========================================================== */
    searchSection.addEventListener("change", () => {
        const value = searchSection.value.toLowerCase();

        rows.forEach(row => {
            const section = row.children[2].textContent.toLowerCase();
            row.dataset.matchSection =
                value === "" || section === value ? "1" : "0";
        });

        refreshTableVisibility();
    });



    /* ==========================================================
       🔎 FILTRE : MEMBRE ASSOCIÉ (colonne 3)
    ========================================================== */
    searchAssoc.addEventListener("change", () => {
        const value = searchAssoc.value.toLowerCase();

        rows.forEach(row => {
            const assoc = row.children[3].textContent.toLowerCase();
            row.dataset.matchAssoc =
                value === "" || assoc === value ? "1" : "0";
        });

        refreshTableVisibility();
    });



    /* ==========================================================
       🔎 FILTRE : VILLE (colonne 7)
    ========================================================== */
    searchVille.addEventListener("input", () => {
        const value = searchVille.value.toLowerCase();
        console.log(value)
        rows.forEach(row => {
            const ville = row.children[7].textContent.toLowerCase();
            row.dataset.matchVille = ville.includes(value) ? "1" : "0";
        });

        refreshTableVisibility();
    });



    /* ==========================================================
       🔎 FILTRE : MEMBRE DU BUREAU (colonne 4)
    ========================================================== */
    bureauSwitch.addEventListener("change", () => {
        const isOn = bureauSwitch.checked;

        rows.forEach(row => {
            const bureau = row.children[4].textContent.trim();
            row.dataset.matchBureau =
                (!isOn || bureau !== "") ? "1" : "0";
        });

        refreshTableVisibility();
    });



    /* ==========================================================
       🔄 RESET des filtres au chargement
    ========================================================== */
    function resetAllFilters() {

        searchPrenom.value = "";
        searchNom.value = "";
        searchSection.value = "";
        searchAssoc.value = "";
        searchVille.value = "";
        bureauSwitch.checked = false;

        rows.forEach(row => {
            row.dataset.matchPrenom  = "1";
            row.dataset.matchNom     = "1";
            row.dataset.matchSection = "1";
            row.dataset.matchAssoc   = "1";
            row.dataset.matchVille   = "1";
            row.dataset.matchBureau  = "1";

            row.style.display = "";
        });

        noResult.classList.remove("visible");
        table.style.opacity = "1";
    }

    resetAllFilters();


    /* ----------------------------------------------------------
   🎯 RÉFÉRENCES DU MODAL
    ---------------------------------------------------------- */
    const modal       = document.getElementById("modal-edit");
    const cancelModal = document.getElementById("modal-btn-cancel");
    const closeBtn    = document.querySelector(".modal-close");

    /* ----------------------------------------------------------
    🎯 RÉFÉRENCES DES CHAMPS DU FORMULAIRE DU MODAL
    ---------------------------------------------------------- */
    const fieldId      = document.getElementById("edit-id");
    const fieldPrenom  = document.getElementById("edit-prenom");
    const fieldNom     = document.getElementById("edit-nom");
    const fieldSection = document.getElementById("edit-section");
    const fieldAssoc   = document.getElementById("edit-assoc");
    const fieldBureau  = document.getElementById("edit-bureau");
    const fieldEmail   = document.getElementById("edit-email");
    const fieldPhone   = document.getElementById("edit-phone");
    const fieldVille   = document.getElementById("edit-ville");
    const fieldMetier  = document.getElementById("edit-metier");



    /* ==========================================================
       🟣 MODAL — OUVERTURE + REMPLISSAGE AUTOMATIQUE
    ========================================================== */
    changeButtons.forEach(btn => {
        btn.addEventListener("click", () => {

            const row = btn.closest("tr");

            // Remplissage des champs
            fieldId.value      = btn.dataset.id;
            fieldPrenom.value  = row.children[0].textContent;
            fieldNom.value     = row.children[1].textContent;

            /* ---------- SECTION (select automatique) ---------- */
            const rowSection = row.children[2].textContent.trim().toLowerCase();
            let found = false;

            for (let option of fieldSection.options) {
                if (option.value.toLowerCase() === rowSection) {
                    fieldSection.value = option.value;
                    found = true;
                    break;
                }
            }
            if (!found) fieldSection.value = "";

            /* ----------- AUTRES CHAMPS ----------------- */
            fieldAssoc.value  = row.children[3].textContent;
            fieldBureau.value = row.children[4].textContent;
            fieldEmail.value  = row.children[5].textContent;
            fieldPhone.value  = row.children[6].textContent;
            fieldVille.value  = row.children[7].textContent;
            fieldMetier.value = row.children[8].textContent;

            openModal();
        });
    });


    /* ==========================================================
       🟣 OUVERTURE / FERMETURE ANIMÉE DU MODAL
    ========================================================== */

    function openModal() {
        modal.style.display = "flex";
        const box = modal.querySelector(".modal-content");
        box.classList.remove("closing");
        box.classList.add("open");
    }

    function closeModalAnimated() {
        const box = modal.querySelector(".modal-content");
        box.classList.remove("open");
        box.classList.add("closing");

        box.addEventListener("animationend", () => {
            modal.style.display = "none";
        }, { once: true });
    }



    cancelModal.addEventListener("click", closeModalAnimated);
    modal.addEventListener("click", e => { if (e.target === modal) closeModalAnimated(); });


    /* ----------------------------------------------------------
    🟣 VALIDATION DU FORMULAIRE (AJAX)
    ---------------------------------------------------------- */
    const saveBtn = document.querySelector(".modal-btn-save");
    const updateMsg = document.getElementById("update-message");

    saveBtn.addEventListener("click", () => {

        const formData = {
            id:      fieldId.value,
            prenom:  fieldPrenom.value.trim(),
            nom:     fieldNom.value.trim(),
            section: fieldSection.value,
            assoc:   fieldAssoc.value,
            bureau:  fieldBureau.value.trim(),
            email:   fieldEmail.value.trim(),
            phone:   fieldPhone.value.trim(),
            ville:   fieldVille.value.trim(),
            metier:  fieldMetier.value.trim()
        };
        const params = new URLSearchParams(window.location.search);
        const user_id = params.get("id_user");
        const url = `/?dest=update_membre_assoc&id_user=${encodeURIComponent(user_id)}`;
        fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(formData)
        })
        .then(res => res.json())
        .then(data => {

            if (data.success) {
                showUpdateMessage("Mise à jour réussie ✔️", false);
            } else {
                showUpdateMessage("Erreur : " + data.message, true);
            }

            // On ferme le modal immédiatement
            closeModalAnimated();

            // Recharge la page après 5 secondes
            setTimeout(() => {
                window.location.reload();
            }, 5000);
        })
        .catch(error => {
            showUpdateMessage("Erreur AJAX ❌", true);

            setTimeout(() => {
                window.location.reload();
            }, 5000);
        });
    });


    /* ----------------------------------------------------------
    🟣 Fonction d'affichage du message
    ---------------------------------------------------------- */
    function showUpdateMessage(text, isError = false) {
        updateMsg.textContent = text;

        if (isError) updateMsg.classList.add("error");
        else updateMsg.classList.remove("error");

        updateMsg.style.display = "block";

        setTimeout(() => {
            updateMsg.style.display = "none";
        }, 5000);
    }



    /* ==========================================================
       🔄 RÉINITIALISATION DES FILTRES AU CHARGEMENT
    ========================================================== */
    function resetAllFilters() {

        searchPrenom.value  = "";
        searchNom.value     = "";
        searchSection.value = "";
        searchAssoc.value   = "";
        bureauSwitch.checked = false;

        rows.forEach(row => {
            row.dataset.matchPrenom  = "1";
            row.dataset.matchNom     = "1";
            row.dataset.matchSection = "1";
            row.dataset.matchAssoc   = "1";
            row.dataset.matchBureau  = "1";
            row.style.display        = "";
        });

        noResult.classList.remove("visible");
        table.style.opacity = "1";
    }

    resetAllFilters();
});
