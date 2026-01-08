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
    const EditNum = document.getElementById("edit-desc-counter");
    
    const fPikachu = document.getElementById("pikachu_csrf");

     /* ============================================================
   🗑️ MODAL SUPPRESSION
    ============================================================ */
    const modalDelete    = document.getElementById("modal-delete-aides");
    const modalDeleteBox = modalDelete?.querySelector(".modal-content");
    const btnConfirmDelete = document.getElementById("btn-confirm-delete");
    const btnCancelDelete  = document.getElementById("btn-cancel-delete");

    let EventIdDelete = null;


    

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
            
            openModal(modal , box);
  
            const response = await fetch(`/?dest=get_events&id_event=${id}`);
            const data = await response.json();
            // Remplissage modal
            fNom.value = data.data.nom_event;
            fDate.value = data.data.date_event;
            fDesc.value = data.data.desc_event;
            fUrl.value  = data.data.url_form ?? "";
            
            fDateCreation.value = data.data.date_creation.split(" ")[0];
            EditNum.textContent = `${fDesc.value.trim().length} / 2500 caracters`;
        });
    });

    //Fermeture Modal Ajout
    document.querySelector("#btn-cancel-modal").addEventListener("click" , ()=>{
        closeModal(modal , box);
    })
    modal.onclick = e => { if (e.target === modal) closeModal(modal , box); };
    /* ============================================================================
    💾 SAUVEGARDE D’UN ÉVÈNEMENT (AJAX)
    ============================================================================ */

    fDesc.addEventListener("input" , ()=>{

        fDesc.value = fDesc.value.slice(0,2500);
        EditNum.textContent = `${fDesc.value.trim().length} / 2500 caracters`;
        

    })

    btnSave.addEventListener("click", async () => {

        const id_event = btnSave.dataset.id;   // ID récupéré lors de l’ouverture du modal
        // Payload envoyé au backend
        const payload = {
            id_event: id_event,
            nom_event: fNom.value.trim(),
            date_event: fDate.value,
            desc_event: fDesc.value.trim(),
            url_form: fUrl.value.trim(),
            pikachu_csrf : fPikachu.value.trim()
        };

        // Requête serveur
        const response = await fetch(`/?dest=update_event`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        });

        closeModal(modal , box);
        const result = await response.json();

        // Retour visuel
        if (result.success) {
            showCard("success");
            
            setTimeout(() => location.reload(), 3000);
        } else {
            showCard("error");
        }
    });



     /* ============================================================================
       💾 SUPRESSION DES ÉVENEMENTS
    ============================================================================ */

    btnRemo.forEach(btn =>
    {

        btn.addEventListener("click", async () => 
        {
            
            EventIdDelete = btn.dataset.id; 
            openModal(modalDelete , modalDeleteBox);
        })
    });

    btnCancelDelete?.addEventListener("click" , ()=>
    {
        EventIdDelete = null; 
        closeModal(modalDelete , modalDeleteBox);
    })
    modalDelete?.addEventListener("click", e => {
        if (e.target === modalDelete) closeModal(modalDelete, modalDeleteBox);
    });

    btnConfirmDelete.addEventListener("click", async () => 
    {   
        let id = EventIdDelete;
        const response = await fetch(`/?dest=delete_event`, 
        {   
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                id_event: id,
                pikachu_csrf : fPikachu.value.trim()
            })
        });

        closeModal(modalDelete , modalDeleteBox);
        const result = await response.json();

        if (result.success) {
            showCard("success");
            setTimeout(() => location.reload(), 3000);
        } else {
            showCard("error");
        }
    });
    
   
    /* ============================================================================
       🪟 OUVERTURE / FERMETURE MODAL
    ============================================================================ */

    function openModal(modal , box) {
        modal.style.display = "flex";
        box.classList.add("open");
    }

    function closeModal(modal , box) {
        box.classList.remove("open");
        box.classList.add("closing");

        box.addEventListener("animationend", () => {
            modal.style.display = "none";
            box.classList.remove("closing");
        }, { once: true });
    }


});
