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


     /* =========================================================
    👥 BOUTON : VOIR PARTICIPANTS
    ========================================================= */

    const table_participants = document.getElementById("participants-table");
    const tbodyParticipants  = document.getElementById("participants-table-body");

    const modalParticipants = document.getElementById("modal-participants-event");
    const modalParticipantsBox = modalParticipants.querySelector(".modal-content");
    const btnCloseParticipants = modalParticipants.querySelector(".modal-btn-cancel");
    const btnParticipateOpen  = document.querySelectorAll(".btn-participate");
    // Message "aucun participant"
    const noResult_paticipants = document.getElementById("participants-empty");
    // Compteur
    const participantsCount = document.getElementById("participants-count");
    //Download CSV
    const csvParticipants = modalParticipants.querySelector(".modal-btn-save");

    let currentParticipants = [];

     /* ============================================================
   🗑️ MODAL SUPPRESSION
    ============================================================ */
    const modalDelete    = document.getElementById("modal-delete-aides");
    const modalDeleteBox = modalDelete?.querySelector(".modal-content");
    const btnConfirmDelete = document.getElementById("btn-confirm-delete");
    const btnCancelDelete  = document.getElementById("btn-cancel-delete");

    let EventIdDelete = null;   


    /* ============================================================================
       🔎 FILTRAGE TABLEAU EVENEMNTS
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
     /* ============================================================================
       🔎 FILTRAGE TABLEAU EVENEMNTS
    ============================================================================ */

    function refreshTableVisibility_participants() 
    {

        // Atténuation pendant la vérification
        table_participants.style.opacity = "0.4";
        

        setTimeout(() => {
            
            // Recalcule les lignes du tableau (important pour l’AJAX)
            const rows_participants = table_participants.querySelectorAll("tbody tr");
            csvParticipants.disabled = rows_participants.length === 0;
            // Affiche le message "aucun participant" s'il n'y a aucune ligne
            noResult_paticipants.classList.toggle(
                "visible",
                rows_participants.length === 0
            );

            // Restaure l'opacité du tableau participants
            table_participants.style.opacity = "1";

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
    💾 RECHERCHE DES PARTICIPANTS   (AJAX)
    ============================================================================ */
    btnParticipateOpen.forEach(btn =>
    {
        btn.addEventListener("click", async () => 
        {

            const eventId = btn.dataset.id;
            csvParticipants.dataset.id = eventId;
            if (!eventId) return;

            // Reset UI
            tbodyParticipants.textContent = "";
            participantsCount.textContent = "👥 Total participants : 0";
            noResult_paticipants.classList.remove("visible");
            table_participants.style.display = "";

            try {
                const response = await fetch(
                    `/?dest=get_participants&id_event=${eventId}`
                );

                const result = await response.json();

                if (!response.ok || !result.success || !Array.isArray(result.data)) {
                    throw new Error("Réponse invalide");
                }

                const participants = result.data;

                // Compteur
                participantsCount.textContent =
                    `👥 Total participants : ${participants.length}`;

                // Remplissage du tableau (sans innerHTML)
                participants.forEach(p => {

                    const tr = document.createElement("tr");

                    const tdNom = document.createElement("td");
                    tdNom.textContent = p.nom ?? "";
                    tr.appendChild(tdNom);

                    const tdPrenom = document.createElement("td");
                    tdPrenom.textContent = p.prenom ?? "";
                    tr.appendChild(tdPrenom);

                    const tdEmail = document.createElement("td");
                    tdEmail.textContent = p.email ?? "";
                    tr.appendChild(tdEmail);

                    const tdTel = document.createElement("td");
                    tdTel.textContent = p.tel_num ?? "-";
                    tr.appendChild(tdTel);

                    const tdDate = document.createElement("td");
                    tdDate.classList.add("td-date-inscription");

                    const { date, heure } = formatDateTime(p.date_inscription);

                    const spanDate = document.createElement("span");
                    spanDate.classList.add("date");
                    spanDate.textContent = date;

                    const spanHeure = document.createElement("span");
                    spanHeure.classList.add("heure");
                    spanHeure.textContent = heure;

                    tdDate.appendChild(spanDate);
                    tdDate.appendChild(spanHeure);

                    tr.appendChild(tdDate);

                    tbodyParticipants.appendChild(tr);
                });
                currentParticipants = participants;
                openParticipantsModal();

            } catch (e) {
                // Erreur → tableau vide
                participantsCount.textContent = "👥 Total participants : 0";
                tbodyParticipants.textContent = "";
                refreshParticipantsVisibility();
                openParticipantsModal();
            }
        });
    })
    btnCloseParticipants.addEventListener("click" , ()=>
    {
        closeParticipantsModal();
    })

    modalParticipants.addEventListener("click", e => {
        if (e.target === modalParticipants) closeParticipantsModal();
    });

    /* ============================================================================
       💾 ENREGISTREMENT EN CSV
    ============================================================================ */
    csvParticipants.addEventListener("click" , ()=>{


        if (!currentParticipants || currentParticipants.length === 0) 
        {
            
            return;
        }

        downlaodCSV(currentParticipants , csvParticipants.dataset.id);
    })
    

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

    function openParticipantsModal() {
        refreshTableVisibility_participants();
        modalParticipants.style.display = "flex";
        modalParticipantsBox.classList.remove("closing");
        modalParticipantsBox.classList.add("open");
    }

    function closeParticipantsModal() {
        modalParticipantsBox.classList.remove("open");
        modalParticipantsBox.classList.add("closing");

        modalParticipantsBox.addEventListener("animationend", () => {
            modalParticipants.style.display = "none";
            modalParticipantsBox.classList.remove("closing");
        }, { once: true });
    }

    function formatDateTime(dateStr) 
    {
        if (!dateStr) return "";

        const d = new Date(dateStr);

        const date = d.toLocaleDateString("fr-FR", {
            day: "2-digit",
            month: "2-digit",
            year: "numeric"
        });

        const heure = d.toLocaleTimeString("fr-FR", {
            hour: "2-digit",
            minute: "2-digit"
        });

        return { date, heure };
    }

    function table2CSV(data)
    {
        // 1️⃣ Définition des en-têtes du fichier CSV (noms des colonnes)
        // L’ordre est important : il doit correspondre aux données ci-dessous
        const headers = ["Nom","Prénom","Email","Téléphone","Date d’inscription"];
        // 2️⃣ Transformation des en-têtes en première ligne du CSV
        // Les colonnes sont séparées par un point-virgule (format Excel FR)
        const header_line = headers.join(";");
        // 3️⃣ Transformation des données (tableau d’objets) en lignes CSV
        const dataLines = data.map(p=>{
            // 3.1 Construction d’une ligne de données (une colonne par valeur)
            // Le ?? "" garantit qu’on n’a jamais null/undefined dans le CSV
            const row = [
                p.nom ?? "",
                p.prenom ?? "",
                p.email ?? "",
                p.tel_num ?? "",
                p.date_inscription ?? ""
            ];
            // 3.2 Sécurisation CSV :
            // - conversion en chaîne de caractères
            // - échappement de tous les guillemets internes ("" au lieu de ")
            // - encapsulation de chaque valeur entre guillemets
            // - séparation des colonnes par ";"
            return row.map(value => `"${String(value).replace(/"/g , '""')}"`).join(";");


        })
        // 4️⃣ Assemblage final du fichier CSV :
        // - première ligne : en-têtes
        // - lignes suivantes : données
        // Chaque ligne est séparée par un retour à la ligne
        return [header_line , ...dataLines].join("\n");
    }

    function downlaodCSV(data , id_event)
    {
        const csv_text = table2CSV(data );

        const blob = new Blob([csv_text] , {type : "text/csv;charset=utf-8"});

        const url = URL.createObjectURL(blob); 

        const a = document.createElement("a");
        a.href = url;
        a.download = `participants_${id_event}.csv`;

        document.body.appendChild(a);
        a.click();

        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }



});
