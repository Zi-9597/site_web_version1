document.addEventListener("DOMContentLoaded", () => {

    /* =========================================================
       🔍 RÉFÉRENCES DOM
    ========================================================= */

    const searchInput = document.getElementById("search-nom-event");
    const table       = document.getElementById("table-events");
    const tbody       = table.querySelector("tbody");
    const noResult    = document.getElementById("no-result");

    const viewButtons = document.querySelectorAll(".btn-change");

    /* =========================================================
       🪟 MODAL
    ========================================================= */

    const modal    = document.getElementById("modal-edit-event");
    const modalBox = modal.querySelector(".modal-content");
    const btnClose = modal.querySelector(".modal-btn-cancel");
    const btnSubscribe = modal.querySelector(".modal-btn-save");
    const pikachu_csrf = document.getElementById("pikachu_csrf");

    /* Champs du modal */
    const fNom  = document.getElementById("edit-nom-event");
    const fDate = document.getElementById("edit-date-event");
    const fDesc = document.getElementById("edit-desc-event");


   
    /* =========================================================
       🔎 FILTRAGE TABLEAU
    ========================================================= */

    function refreshTableVisibility() {

        const rows = tbody.querySelectorAll("tr");
        const search = searchInput.value.toLowerCase().trim();
        let visibleCount = 0;

        rows.forEach(row => {
            const nomEvent = row.children[0].textContent.toLowerCase();
            const shouldShow = nomEvent.includes(search);

            row.style.display = shouldShow ? "" : "none";
            if (shouldShow) visibleCount++;
        });

        noResult.classList.toggle("visible", visibleCount === 0);
    }

    searchInput.addEventListener("input", refreshTableVisibility);
    refreshTableVisibility();

    /* =========================================================
       🪟 OUVERTURE / FERMETURE MODAL
    ========================================================= */

    function openModal(modal , modalBox) {
        modal.style.display = "flex";
        modalBox.classList.remove("closing");
        modalBox.classList.add("open");
    }

    function closeModal(modal , modalBox) {
        modalBox.classList.remove("open");
        modalBox.classList.add("closing");

        modalBox.addEventListener("animationend", () => {
            modal.style.display = "none";
            modalBox.classList.remove("closing");
        }, { once: true });
    }

    

    /* =========================================================
       📡 FETCH + AFFICHAGE D’UN ÉVÉNEMENT
    ========================================================= */

    viewButtons.forEach(btn => {

        btn.addEventListener("click", async () => {

            const eventId = btn.dataset.id;
            btnSubscribe.dataset.id = eventId;

            if (!eventId) return;

            try {
                const response = await fetch(`/?dest=get_events&id_event=${eventId}`);
                const result = await response.json();
                if (!result || !result.data) return;

                const ev = result.data;

                fNom.value  = ev.nom_event ?? "";
                fDate.value = ev.date_event ?? "";
                fDesc.value = ev.desc_event ?? "";

                openModal(modal , modalBox);

            } catch {
                // Erreur silencieuse (UX propre)
            }
        });
    });


    btnSubscribe.addEventListener("click" , async ()=>
    {
        try 
        {

            const payload = {
                id_event : btnSubscribe.dataset.id,
                pikachu_csrf : pikachu_csrf.value
            };
            
            const response = await fetch(`/?dest=add_inscris` , 
            {
                method: "POST",
                headers: {"Content-Type" : "application/json"},
                body: JSON.stringify(payload)
            });
            const result = await response.json();
            console.log(result.message);
            closeModal(modal , modalBox);

            if (response.status === 200 && result.success) {
                showCard('card-success', true);   // ✅ recharge après
            }
            else if (response.status === 409) {
                showCard('card-error-repeat');    // ❌ pas de reload
            }
            else {
                showCard('card-error');           // ❌ pas de reload
            }
        } 
        catch {
            // Erreur silencieuse (UX propre)
        }
    });
   
    btnClose.addEventListener("click", ()=>{
        closeModal(modal , modalBox)
    });

    modal.addEventListener("click", e => {
        if (e.target === modal) closeModal(modal , modalBox);
    });

    function showCard(cardId, reload = false) {

        document.querySelectorAll('.notif-card').forEach(card => {
            card.classList.remove('show');
        });

        const card = document.getElementById(cardId);
        if (!card) return;

        card.classList.add('show');

        setTimeout(() => {
            card.classList.remove('show');

            if (reload) {
                window.location.reload();
            }

        }, 3000); // 3 secondes = UX parfaite
    }


  

});
