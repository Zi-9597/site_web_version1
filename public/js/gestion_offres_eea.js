document.addEventListener("DOMContentLoaded", () => {

    /* ============================================================================
       🔍 RÉFÉRENCES DOM
    ============================================================================ */

    const searchTitre = document.getElementById("search-titre-offre");
    const searchType  = document.getElementById("filiere-section");

    const table = document.getElementById("table-offres");
    const rows  = document.querySelectorAll("#table-offres tbody tr");
    const noResult = document.getElementById("no-result");

    const changeButtons = document.querySelectorAll(".btn-change");
    const modal = document.getElementById("modal-edit-offre");
    const box   = modal.querySelector(".modal-content");
    const btnSave = document.getElementById("btn-save-offre");
    const btnRemo =document.querySelectorAll(".btn-delete");

    // Champs du modal
    const fTitre   = document.getElementById("edit-titre-offre");
    const fUrl     = document.getElementById("edit-url");
    const fDesc    = document.getElementById("edit-description");
    const fContrat = document.getElementById("edit-contrat");
    const fSpecs   = document.getElementById("edit-specialites");
    const fDate    = document.getElementById("edit-date");
    const modalFieldSpecialites = document.querySelector(".modal-field-specialites");



    /* ============================================================================
       🔄 CARTES DE NOTIFICATION (SUCCÈS / ERREUR)
    ============================================================================ */

    function showCard(type) {
        const card = document.getElementById(
            type === "success" ? "card-success" : "card-error"
        );

        // Affiche la carte
        card.classList.add("show");

        // Cache après 3 secondes
        setTimeout(() => {
            card.classList.remove("show");
        }, 3000);
    }



    /* ============================================================================
       🔎 FILTRAGE TABLEAU
    ============================================================================ */

    function refreshTableVisibility() {

        table.style.opacity = "0.4";

        setTimeout(() => {

            let visibleCount = 0;

            rows.forEach(row => {

                const shouldShow =
                    row.dataset.matchTitre !== "0" &&
                    row.dataset.matchType  !== "0";

                row.style.display = shouldShow ? "" : "none";

                if (shouldShow) visibleCount++;
            });

            // 🟣 Basculer automatiquement l'état visible/hidden du message
            noResult.classList.toggle("visible", visibleCount === 0);

            table.style.opacity = "1";

        }, 120);
    }

    searchTitre.addEventListener("input", () => {
        const value = searchTitre.value.toLowerCase().trim();
        rows.forEach(row => {
            const titre = row.children[0].textContent.toLowerCase();
            row.dataset.matchTitre = titre.includes(value) ? "1" : "0";
        });
        refreshTableVisibility();
    });

    searchType.addEventListener("change", () => {
        const value = searchType.value.toLowerCase().trim();
        rows.forEach(row => {
            const type = row.children[2].textContent.toLowerCase();
            row.dataset.matchType = value === "" || type.includes(value) ? "1" : "0";
        });
        refreshTableVisibility();
    });



    /* ============================================================================
       🟪 OUVERTURE MODAL + REMPLISSAGE AJAX
    ============================================================================ */

    changeButtons.forEach(btn => {
        btn.addEventListener("click", async () => {

            const id = btn.dataset.id;
            btnSave.dataset.id = id; // assigne l’ID correct
            btnRemo.dataset.id = id;

            openModal();

            const response = await fetch(`/?dest=info_fetch_offre&id_user=${id}`);
            const data = await response.json();

            // Remplir modal
            fTitre.value = data.titre_offre;
            fUrl.value   = data.url_linkedin;
            fDesc.value  = data.description;
            fContrat.value = data.type_contrat;
            fDate.value = data.date_creation.split(" ")[0];

            updateContratAndSpecialites();

            // Remplir spécialités
            const arraySpecs = data.specialites.split(",").map(v => v.trim());
            [...fSpecs.options].forEach(option => {
                option.selected = arraySpecs.includes(option.textContent.trim());
            });
        });
    });



    /* ============================================================================
       🪟 OUVERTURE / FERMETURE MODAL
    ============================================================================ */

    function openModal() {
        modal.style.display = "flex";
        box.classList.add("open");
    }

    function closeModal() {
        box.classList.remove("open");
        box.classList.add("closing");

        box.addEventListener("animationend", () => {
            modal.style.display = "none";
            box.classList.remove("closing");
        }, { once: true });
    }

    document.querySelector(".modal-btn-cancel").onclick = closeModal;
    modal.onclick = e => { if (e.target === modal) closeModal(); };



    /* ============================================================================
       🎛 LOGIQUE SPÉCIALITÉS / CONTRAT
    ============================================================================ */

    function updateContratAndSpecialites() {
        if (fContrat.value === "Job Étudiant") {

            [...fContrat.options].forEach(o => {
                o.style.display = o.value === "Job Étudiant" ? "block" : "none";
            });

            fContrat.disabled = true;
            modalFieldSpecialites.style.display = "none";
        }
        else {
            [...fContrat.options].forEach(o => {
                o.style.display = o.value === "Job Étudiant" ? "none" : "block";
            });

            fContrat.disabled = false;
            modalFieldSpecialites.style.display = "block";
        }
    }



    /* ============================================================================
       💾 SAUVEGARDE OFFRE (AJAX + CARTES + RELOAD)
    ============================================================================ */

    btnSave.addEventListener("click", async () => {

        const id_offre = btnSave.dataset.id;

        const payload = {
            id_offre : id_offre,
            titre_offre : fTitre.value.trim(),
            url_linkedin : fUrl.value.trim(),
            description : fDesc.value.trim(),
            type_contrat : fContrat.value,
            specialites : [...fSpecs.options]
                .filter(o => o.selected)
                .map(o => o.value)
        };

        const response = await fetch(`/?dest=info_update_offre&id_user=${id_offre}`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (result.success) {
            showCard("success");
            closeModal();
            setTimeout(() => location.reload(), 3000);
        } else {
            showCard("error");
        }
    });

    /* ============================================================================
       💾 SUPRESSION DES OFFRES
    ============================================================================ */

    btnRemo.forEach(btn =>{

        btn.addEventListener("click", async () => 
            {

            const id = btn.dataset.id;
            const response = await fetch(`/?dest=remove_offre&id_offre=${id}`, {
            method: "POST"
        });

        const result = await response.json();

        if (result.success) {
            showCard("success");      // Affiche une carte verte
            closeModal();             // Ferme le modal
            setTimeout(() => {
                location.reload();
            }, 3000);
        } else {
            showCard("error");       // Affiche une carte rouge
        }
     });



    });
    refreshTableVisibility() 
    document.querySelector(".modal-btn-cancel").onclick = closeModal;
    modal.onclick = e => { if (e.target === modal) closeModal(); };

    
});
