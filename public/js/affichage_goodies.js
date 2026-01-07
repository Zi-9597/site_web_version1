document.addEventListener("DOMContentLoaded", () => {

    /* =========================================================
       🔍 FILTRAGE DU TABLEAU DES GOODIES
    ========================================================= */

    const searchInput = document.getElementById("search-nom-goodies");
    const table       = document.getElementById("table-goodies");
    const rows        = document.querySelectorAll("#table-goodies tbody tr");
    const noResult    = document.getElementById("no-result");

    function filterTable() {

        
        const search = searchInput.value.toLowerCase().trim();
        let visibleCount = 0;

        table.style.opacity = "0.4";

        setTimeout(() => {
            rows.forEach(row => {
                const nom = row.children[0].textContent.toLowerCase();
                const show = nom.includes(search);

                row.style.display = show ? "" : "none";
                if (show) visibleCount++;
            });

            noResult.classList.toggle("visible", visibleCount === 0);
            table.style.opacity = "1";
        }, 120);
    }

    searchInput?.addEventListener("input", filterTable);
    filterTable();

 



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

    /* =========================================================
       🟣 MODAL : MODIFIER GOODIES
    ========================================================= */

    const modalEdit    = document.getElementById("modal-edit-goodies");
    const modalEditBox = modalEdit?.querySelector(".modal-content");

    const editNom  = document.getElementById("edit-nom-goodies");
    const editPrix = document.getElementById("edit-prix-goodies");
    const editLien = document.getElementById("edit-lien-goodies");
    const editDesc = document.getElementById("edit-desc-goodies");
    const btnSave  = document.getElementById("btn-save-goodies");

    document.querySelectorAll(".btn-change").forEach(btn => {
        btn.addEventListener("click", async () => {

            const id = btn.dataset.id;

            openModal(modalEdit, modalEditBox);

            try {
                const res = await fetch(`/?dest=get_goodies&id_goodies=${id}`);
                const data = await res.json();
                editNom.value  = data.data.nom_goodies ?? "";
                editPrix.value = data.data.prix ?? "";
                editLien.value = data.data.lien ?? "";
                editDesc.value = data.data.description ?? "";


            } catch {
                //
            }
        });
    });

    modalEdit?.querySelector(".modal-btn-cancel")
        ?.addEventListener("click", () =>
            closeModal(modalEdit, modalEditBox)
        );

    modalEdit?.addEventListener("click", e => {
        if (e.target === modalEdit) closeModal(modalEdit, modalEditBox);
    });

    

});
