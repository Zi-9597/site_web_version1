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

    /* Champs du modal */
    const fNom  = document.getElementById("edit-nom-event");
    const fDate = document.getElementById("edit-date-event");
    const fDesc = document.getElementById("edit-desc-event");
    const fUrl  = document.getElementById("edit-url-form");

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

    modal.addEventListener("click", e => {
        if (e.target === modal) closeModal();
    });

    /* =========================================================
       📡 FETCH + AFFICHAGE D’UN ÉVÉNEMENT
    ========================================================= */

    viewButtons.forEach(btn => {

        btn.addEventListener("click", async () => {

            const eventId = btn.dataset.id;
            if (!eventId) return;

            try {
                const response = await fetch(`/?dest=fetch_event&id_event=${eventId}`);
                const result = await response.json();
                console.log(result);
                if (!result || !result.data) return;

                const ev = result.data;

                fNom.value  = ev.nom_event ?? "";
                fDate.value = ev.date_event ?? "";
                fDesc.value = ev.desc_event ?? "";
                fUrl.value  = ev.url_form ?? "";

                openModal();

            } catch {
                // Erreur silencieuse (UX propre)
            }
        });
    });

});
