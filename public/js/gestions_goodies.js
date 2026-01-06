document.addEventListener("DOMContentLoaded", () => {

     /* =========================================================
       🔍 CSRF TOKEN PIKACHU
    ========================================================= */

    const pikachu_csrf = document.getElementById("pikachu_csfr");
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

    /* =========================================================
       🔔 NOTIFICATIONS
    ========================================================= */

    function showCard(type) {
        const card = document.getElementById(
            type === "success" ? "card-success" : "card-error"
        );
        if (!card) return;

        card.classList.add("show");
        setTimeout(() => card.classList.remove("show"), 3000);
    }

    function validateAddForm() 
    {
        document.getElementById("btn-add-goodies").disabled = !(addNom.value.trim() && addDesc.value.trim() && addPrix.value.trim()  && addDesc.value.length > 50);
    }

    /* =========================================================
       🟢 MODAL : AJOUT GOODIES
    ========================================================= */

    const btnAddGoodies  = document.querySelector(".btn-add-goodies");
    const modalAdd      = document.getElementById("modal-add-goodies");
    const modalAddBox   = modalAdd?.querySelector(".modal-content");

    const addNom  = document.getElementById("add-nom-goodies");
    const addPrix = document.getElementById("add-prix-goodies");
    const addLien = document.getElementById("add-lien-goodies");
    const addDesc = document.getElementById("add-desc-goodies");


    const counterAdd = document.getElementById("add-desc-counter");
    addDesc.value = "";


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

   

  

    addNom?.addEventListener("input", validateAddForm);
    addPrix?.addEventListener("input", validateAddForm);
    addLien?.addEventListener("input", validateAddForm);
    addDesc?.addEventListener("input", validateAddForm);
    addDesc?.addEventListener("input", () => {
        addDesc.value = addDesc.value.slice(0, 2500);
        counterAdd.textContent = `${addDesc.value.length} / 2500 caractères`;
        validateAddForm();
    });

    btnAddGoodies?.addEventListener("click", () =>
        openModal(modalAdd, modalAddBox)
    );


    modalAdd?.querySelector(".modal-btn-cancel")
        ?.addEventListener("click", () =>
            closeModal(modalAdd, modalAddBox)
        );

    modalAdd?.addEventListener("click", e => {
        if (e.target === modalAdd) closeModal(modalAdd, modalAddBox);
    });

    document.getElementById("btn-add-goodies")?.addEventListener("click", async () => {

        const payload = {
            nom_goodies: addNom.value.trim(),
            prix:        addPrix.value,
            lien:        addLien.value.trim(),
            description: addDesc.value.trim(),
            pikachu_csfr : pikachu_csrf.value.trim()
        };
        validateAddForm();
        if (!payload.nom_goodies || !payload.prix) {
            showCard("error");
            return;
        }
        try {
            const response = await fetch("/?dest=add_goodies", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(payload)
            });

            const result = await response.json();
            closeModal(modalAdd, modalAddBox);
            if (result.success) {
                showCard("success");
                
                setTimeout(() => location.reload(), 3000);
            } else {
                showCard("error");
            }

        } catch {
            showCard("error");
        }
    });

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
    const counterEdit    = document.getElementById("edit-desc-counter");



    document.querySelectorAll(".btn-change").forEach(btn => {
        btn.addEventListener("click", async () => {

            const id = btn.dataset.id;
            
            btnSave.dataset.id = id;

            openModal(modalEdit, modalEditBox);

            try {
                const res = await fetch(`/?dest=get_goodies&id_goodies=${id}`);
                const data = await res.json();
                
                editNom.value  = data.data.nom_goodies ?? "";
                editPrix.value = data.data.prix ?? "";
                editLien.value = data.data.lien ?? "";
                editDesc.value = data.data.description ?? "";
                counterEdit.textContent = `${editDesc.value.length} / 2500 caractères`;

            } catch {
                showCard("error");
            }
        });
    });

    editDesc?.addEventListener("input" , ()=>{
        editDesc.value = editDesc.value.slice(0, 2500);
        
        counterEdit.textContent = `${editDesc.value.length} / 2500 caractères`;

    } )
    modalEdit?.querySelector(".modal-btn-cancel")
        ?.addEventListener("click", () =>
            closeModal(modalEdit, modalEditBox)
        );

    modalEdit?.addEventListener("click", e => {
        if (e.target === modalEdit) closeModal(modalEdit, modalEditBox);
    });

    btnSave?.addEventListener("click", async () => {

        const payload = {
            id_goodies:  btnSave.dataset.id,
            nom_goodies: editNom.value.trim(),
            prix:        editPrix.value,
            lien:        editLien.value.trim(),
            description: editDesc.value.trim(),
            pikachu_csfr : pikachu_csrf.value.trim()
        };

        if (!payload.nom_goodies || !payload.prix) {
            showCard("error");
            return;
        }

        try {
            const res = await fetch(`/?dest=update_goodies`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(payload)
            });

            const result = await res.json();
            closeModal(modalEdit, modalEditBox);
            if (result.success) {
                showCard("success");
                setTimeout(() => location.reload(), 3000);
            } else {
                showCard("error");
      
            }

        } catch {
            showCard("error");
            
        }
    });

    /* =========================================================
       🗑️ SUPPRESSION GOODIES
    ========================================================= */

    document.querySelectorAll(".btn-delete").forEach(btn => {
        btn.addEventListener("click", async () => {

            const id = btn.dataset.id;
            console.log(id);
            if (!id) return;

            try {
                const res = await fetch("/?dest=delete_goodies", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ id_goodies: id  , pikachu_csrf : pikachu_csrf.value.trim()})
                });

                const result = await res.json();
                if (result.success) {
                    showCard("success");
                    setTimeout(() => location.reload(), 3000);
                } else {
                    showCard("error");
                }

            } catch {
                showCard("error");
            }
        });
    });

    

});
