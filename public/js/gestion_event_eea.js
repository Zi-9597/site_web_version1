document.addEventListener("DOMContentLoaded", () => {

    /* ============================================================================
       🔍 RÉFÉRENCES DOM
    ============================================================================ */

    const searchTitre = document.getElementById("search-titre-offre");

    const table = document.getElementById("table-offres");
    const rows  = document.querySelectorAll("#table-offres tbody tr");
    const noResult = document.getElementById("no-result");

    const changeButtons = document.querySelectorAll(".btn-change");
    const btnRemo =document.querySelectorAll(".btn-delete");

    // Nouveau modal spécifique aux évènements
    const modal = document.getElementById("modal-edit-event");
    const box   = modal.querySelector(".modal-content");

    // Champs du modal
    const fNom          = document.getElementById("edit-nom-event");
    const fDate         = document.getElementById("edit-date-event");
    const fDesc         = document.getElementById("edit-desc-event");
    const fUrl          = document.getElementById("edit-url-form");
    const fDateCreation = document.getElementById("edit-date-creation");
    const btnSave = document.getElementById("btn-save-offre");



    /* ============================================================================
       🔎 FILTRAGE TABLEAU
    ============================================================================ */

    function refreshTableVisibility() {

        table.style.opacity = "0.4";

        setTimeout(() => {

            let visibleCount = 0;

            rows.forEach(row => {

                const titre = row.children[0].textContent.toLowerCase();
                const search = searchTitre.value.toLowerCase().trim();

                const shouldShow = titre.includes(search);

                row.style.display = shouldShow ? "" : "none";

                if (shouldShow) visibleCount++;
            });

            noResult.classList.toggle("visible", visibleCount === 0);

            table.style.opacity = "1";

        }, 120);
    }

    searchTitre.addEventListener("input", refreshTableVisibility);
    refreshTableVisibility();




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
       🟪 OUVERTURE MODAL + REMPLISSAGE AJAX
    ============================================================================ */

    changeButtons.forEach(btn => {
        btn.addEventListener("click", async () => {

            const id = btn.dataset.id;
            btnSave.dataset.id = id;
            console.log(btnSave.dataset.id);
            openModal();
  
            const response = await fetch(`/?dest=info_cherche_events&id_event=${id}`);
            const data = await response.json();

            // Remplissage modal
            fNom.value = data.nom_event;
            fDate.value = data.date_event;
            fDesc.value = data.desc_event;
            fUrl.value  = data.url_form ?? "";
            fDateCreation.value = data.date_creation.split(" ")[0];
        });
    });


    /* ============================================================================
    💾 SAUVEGARDE D’UN ÉVÈNEMENT (AJAX)
    ============================================================================ */

    btnSave.addEventListener("click", async () => {

        const id_event = btnSave.dataset.id;   // ID récupéré lors de l’ouverture du modal

        // Payload envoyé au backend
        const payload = {
            id_event: id_event,
            nom_event: fNom.value.trim(),
            date_event: fDate.value,
            desc_event: fDesc.value.trim(),
            url_form: fUrl.value.trim()
        };

        // Requête serveur
        const response = await fetch(`/?dest=update_event&id_event=${id_event}`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        // Retour visuel
        if (result.success) {
            showCard("success");
            closeModal();
            setTimeout(() => location.reload(), 3000);
        } else {
            showCard("error");
        }
    });



     /* ============================================================================
       💾 SUPRESSION DES ÉVENEMENTS
    ============================================================================ */

    btnRemo.forEach(btn =>{

        btn.addEventListener("click", async () => 
        {

                const id = btn.dataset.id;
                const response = await fetch(`/?dest=suppression_event&id_event=${id}`, {
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


});
