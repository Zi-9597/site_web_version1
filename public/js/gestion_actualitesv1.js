document.addEventListener("DOMContentLoaded", () => {

    /* ============================================================
       🔧 UTILITAIRES
    ============================================================ */
    const $ = (sel, ctx = document) => ctx.querySelector(sel);
    const $$ = (sel, ctx = document) => [...ctx.querySelectorAll(sel)];

    function showCard(type) {
        const card = $("#" + (type === "success" ? "card-success" : "card-error"));
        if (!card) return;
        card.classList.add("show");
        setTimeout(() => card.classList.remove("show"), 3000);
    }

    async function postJSON(url, payload) {
        const res = await fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        });
        return res.json();
    }

    /* ============================================================
       📦 RÉFÉRENCES DOM GLOBALES
    ============================================================ */
    const table       = $("#table-offres");
    const rows        = $$("#table-offres tbody tr");
    const noResult    = $("#no-result");
    const searchTitle = $("#search-titre-actu");

    /* ============================================================
       🔎 FILTRAGE TABLEAU
    ============================================================ */
    function refreshTableVisibility() {
        table.style.opacity = "0.4";

        setTimeout(() => {
            let visible = 0;

            rows.forEach(row => {
                const show = row.dataset.matchTitre === "1";
                row.style.display = show ? "" : "none";
                if (show) visible++;
            });

            noResult.classList.toggle("visible", visible === 0);
            table.style.opacity = "1";
        }, 120);
    }

    searchTitle?.addEventListener("input", () => {
        const value = searchTitle.value.toLowerCase().trim();

        rows.forEach(row => {
            const titre = row.children[0].textContent.toLowerCase();
            row.dataset.matchTitre = titre.includes(value) ? "1" : "0";
        });

        refreshTableVisibility();
    });

    /* ============================================================
       🟢 MODAL AJOUT
    ============================================================ */
    const modalAdd    = $("#modal-add-actu");
    const modalAddBox = modalAdd?.querySelector(".modal-content");
    const btnAddActu  = $(".btn-add-actu");
    const btnSaveAdd  = $("#btn-add-actu");

    const fAddTitre = $("#add-titre-actu");
    const fAddLien  = $("#add-lien-actu");
    const fAddDesc  = $("#add-desc-actu");
    const counterAdd = $("#add-desc-counter");

    btnSaveAdd.disabled = true;

    
    function validateAddForm() {
        btnSaveAdd.disabled = !(fAddTitre.value.trim() && fAddDesc.value.trim() && fAddDesc.value.length > 50);
    }

    fAddTitre?.addEventListener("input", validateAddForm);
    fAddDesc?.addEventListener("input", () => {
        fAddDesc.value = fAddDesc.value.slice(0, 2500);
        counterAdd.textContent = `${fAddDesc.value.length} / 2500 caractères`;
        validateAddForm();
    });

    btnAddActu?.addEventListener("click", () =>
    openModal(modalAdd, modalAddBox));

    $(".modal-btn-cancel", modalAddBox)
        ?.addEventListener("click", () =>
        closeModal(modalAdd, modalAddBox));

    modalAdd?.addEventListener("click", e => {
        if (e.target === modalAdd) {
            closeModal(modalAdd, modalAddBox);
        }
    });


    btnSaveAdd?.addEventListener("click", async () => {
        try {
            const result = await postJSON("/?dest=add_actualite", {
                titre_actu: fAddTitre.value.trim(),
                lien_actu:  fAddLien.value.trim(),
                desc_actu:  fAddDesc.value.trim()
            });

            result.success ? showCard("success") : showCard("error");
            if (result.success) setTimeout(() => location.reload(), 2000);

        } catch {
            showCard("error");
        }
    });

    /* ============================================================
       ✏️ MODAL ÉDITION
    ============================================================ */
    const modalEdit   = $("#modal-edit-event");
    const modalEditBox = modalEdit?.querySelector(".modal-content");
    const btnSaveEdit = $("#btn-save-actu");

    const fEditTitre = $("#edit-titre-actu");
    const fEditLien  = $("#edit-lien-actu");
    const fEditDesc  = $("#edit-desc-actu");
    const fEditDate  = $("#edit-date-depot");
    const counterEdit = $("#edit-desc-counter");

    btnSaveEdit.disabled = true;


    function validateEditForm() {
        btnSaveEdit.disabled = !(fEditTitre.value.trim() && fEditDesc.value.trim() && fEditDesc.value.length > 50);
    }

    fEditTitre?.addEventListener("input", validateEditForm);
    fEditDesc?.addEventListener("input", () => {
        fEditDesc.value = fEditDesc.value.slice(0, 2500);
        counterEdit.textContent = `${fEditDesc.value.length} / 2500 caractères`;
        validateEditForm();
    });

    $$(".btn-change").forEach(btn => {
        btn.addEventListener("click", async () => {
            openModal(modalEdit , modalEditBox);
            try {
                const data = await fetch(`/?dest=get_actualites&id_actu=${btn.dataset.id}`).then(r => r.json());
                console.log(data)
            
                btnSaveEdit.dataset.id = btn.dataset.id;
                fEditTitre.value = data.titre_actu ?? "";
                fEditLien.value  = data.lien_actu ?? "";
                fEditDesc.value  = data.desc_actu ?? "";
                fEditDate.value  = data.date_depot ?? "";

                counterEdit.textContent = `${fEditDesc.value.length} / 2500 caractères`;
                validateEditForm();
                

            } catch {
                showCard("error");
            }
        });
    });

    $(".modal-btn-cancel" , modalEditBox)
        ?.addEventListener("click", () =>
            closeModal(modalEdit, modalEditBox)
        );

    modalEdit?.addEventListener("click", e => {
        if (e.target === modalEdit) closeModal(modalEdit, modalEditBox);
    });


    btnSaveEdit?.addEventListener("click", async () => {
        try {
            const result = await postJSON("/?dest=update_actualite", {
                actu_id: btnSaveEdit.dataset.id,
                titre_actu: fEditTitre.value.trim(),
                lien_actu:  fEditLien.value.trim(),
                desc_actu:  fEditDesc.value.trim()
            });

            result.success ? showCard("success") : showCard("error");
            if (result.success) setTimeout(() => location.reload(), 2000);

        } catch {
            showCard("error");
        }
    });

    /* ============================================================
       🗑️ SUPPRESSION
    ============================================================ */
    $$(".btn-delete").forEach(btn => {
        btn.addEventListener("click", async () => {
            try {
                const result = await postJSON("/?dest=delete_actualite", {
                    actu_id: btn.dataset.id
                });

                result.success ? showCard("success") : showCard("error");
                if (result.success) setTimeout(() => location.reload(), 2000);

            } catch {
                showCard("error");
            }
        });
    });

    /* ============================================================
       🔄 INIT
    ============================================================ */
    rows.forEach(row => row.dataset.matchTitre = "1");
    refreshTableVisibility();

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
