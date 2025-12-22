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
    const btnRemo =document.querySelectorAll(".btn-delete");

    // Champs du modal
    const fTitre   = document.getElementById("edit-titre-offre");
    const fUrl     = document.getElementById("edit-url");
    const fDesc    = document.getElementById("edit-description");
    const fContrat = document.getElementById("edit-contrat");
    const fSpecs   = document.getElementById("edit-specialites");
    const fDate    = document.getElementById("edit-date");
    const modalFieldSpecialites = document.querySelector(".modal-field-specialites");
    const specialiteCheckboxes = document.querySelectorAll(".filter-specialite");


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

    function refreshTableVisibility() 
    {

        table.style.opacity = "0.4";

        setTimeout(() => {

            const searchValue = searchTitre.value.toLowerCase().trim();
            const typeValue   = searchType.value.toLowerCase().trim();

            const selectedSpecs = Array.from(specialiteCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value.toLowerCase());

            let visibleCount = 0;

            rows.forEach(row => {

                const titre = row.children[0].textContent.toLowerCase();
                const type  = row.children[2].textContent.toLowerCase();
                const specs = row.children[1]?.textContent.toLowerCase() ?? "";

                /* ---- CONDITIONS ---- */
                const matchTitre = titre.includes(searchValue);
                const matchType  = typeValue === "" || type.includes(typeValue);

                let matchSpec = true;
                if (selectedSpecs.length > 0) {
                    matchSpec = selectedSpecs.some(spec =>
                        specs.includes(spec)
                    );
                }

                const shouldShow = matchTitre && matchType && matchSpec;

                row.style.display = shouldShow ? "" : "none";
                if (shouldShow) visibleCount++;
            });

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

    specialiteCheckboxes.forEach(cb => {
        cb.addEventListener("change", () => {

            // Récupération des spécialités cochées (en minuscule)
            const selectedValues = Array.from(specialiteCheckboxes)
                .filter(c => c.checked)
                .map(c => c.value.toLowerCase().trim());

            
            console.log(selectedValues);

            rows.forEach(row => {

                const specialites = row.children[1].textContent.toLowerCase();
                console.log(selectedValues);
                console.log(specialites);

                // 👉 EXACTEMENT le même pattern que searchType
                row.dataset.matchSpec =
                    selectedValues.length === 0 ||
                    selectedValues.some(val => specialites.includes(val))
                        ? "1"
                        : "0";
            });

            refreshTableVisibility();
        });
    });




    /* ============================================================================
       🟪 OUVERTURE MODAL + REMPLISSAGE AJAX
    ============================================================================ */

    changeButtons.forEach(btn => {
        btn.addEventListener("click", async () => {

            const id = btn.dataset.id;
            
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


    function resetFilters() {

        // 🔤 Champ recherche titre
        searchTitre.value = "";

        // 🏷️ Select type
        searchType.value = "";

        // ✅ Checkbox spécialités
        document.querySelectorAll(".filter-specialite").forEach(cb => {
            cb.checked = false;
        });

        // 🔁 Réinitialisation des datasets
        rows.forEach(row => {
            row.dataset.matchTitre = "1";
            row.dataset.matchType  = "1";
            row.dataset.matchSpec  = "1";
            row.style.display = "";
        });

        // ❌ Cache le message "aucun résultat"
        noResult.classList.remove("visible");

        table.style.opacity = "1";
    }

    window.addEventListener("pageshow", () => 
    {
        resetFilters();
    });



    
});
