document.addEventListener("DOMContentLoaded", () => {
     // Tout le code est ici, donc il s’exécute seulement
  // quand la page HTML est complètement chargée.

  /* ==========================================================
   1) RÉFÉRENCES HTML
   - On récupère tous les éléments qu’on va manipuler
    ========================================================== */
    const searchPrenom  = document.getElementById("search-prenom");
    const searchNom     = document.getElementById("search-nom");
    const searchSection = document.getElementById("filiere-section");
    const searchAssoc   = document.getElementById("membre-assoc");
    const searchVille   = document.getElementById("search-ville");
    const bureauSwitch  = document.getElementById("bureau-switch");
    const m2Switch      = document.getElementById("m2-switch");

    const table    = document.getElementById("table-membres");
    const rows     = document.querySelectorAll("#table-membres tbody tr");
    const noResult = document.getElementById("no-result");

    const changeButtons = document.querySelectorAll(".btn-change");

    /* ==========================================================
    2) ÉTAT CENTRAL DES FILTRES
    - Un seul objet = source de vérité
    ========================================================== */
    const filters = {
        prenom: "",
        nom: "",
        section: "",
        assoc: "",
        ville: "",
        bureauOnly: false,
        m2Only: false
    };

    /* ==========================================================
    3) OUTIL : lire une cellule d’une ligne
    ========================================================== */
    function getCell(row, index) {
        return row.children[index].textContent.toLowerCase().trim();
    }

    /* ==========================================================
    4) MOTEUR CENTRAL : applique tous les filtres
    - Si un filtre ne passe pas => on cache la ligne
    ========================================================== */
    function refreshTableVisibility() {

        table.style.opacity = "0.4";

        setTimeout(() => {

            let visibleCount = 0;

            rows.forEach(row => {

            // A) Valeurs de la ligne (une seule lecture)
            const prenom  = getCell(row, 0);
            const nom     = getCell(row, 1);
            const section = getCell(row, 2);
            const assoc   = getCell(row, 3);
            const bureau  = getCell(row, 4);
            const ville   = getCell(row, 7);

            // B) On suppose visible, puis on élimine
            let shouldShow = true;

            // --- filtres texte "contient" ---
            if (filters.prenom && !prenom.includes(filters.prenom)) shouldShow = false;
            if (filters.nom    && !nom.includes(filters.nom))       shouldShow = false;
            if (filters.ville  && !ville.includes(filters.ville))   shouldShow = false;

            // --- filtres "égalité" (select) ---
            if (filters.section && section !== filters.section) shouldShow = false;
            if (filters.assoc   && assoc   !== filters.assoc)   shouldShow = false;

            // --- switch bureau : si ON => on garde uniquement ceux qui ont une valeur ---
            if (filters.bureauOnly && bureau === "") shouldShow = false;

            // --- switch M2 : si ON => uniquement M2 + Étudiant/e ---
            if (filters.m2Only) {
                const isM2 = section.includes("m2") && assoc === "étudiant/e";
                if (!isM2) shouldShow = false;
            }

            // C) Affichage
            row.style.display = shouldShow ? "" : "none";
            if (shouldShow) visibleCount++;

            });

            // Message "aucun résultat"
            noResult.classList.toggle("visible", visibleCount === 0);

            table.style.opacity = "1";

        }, 120);
    }
        /* ==========================================================
    6️⃣ GESTION DES ÉVÉNEMENTS DE FILTRAGE
    ----------------------------------------------------------
    Chaque événement :
    1) lit la valeur de l’input
    2) met à jour l’objet `filters`
    3) relance le moteur de filtrage
    ========================================================== */

    /* 🔎 FILTRE : PRÉNOM (input texte — colonne 0)
    → Recherche partielle (ex: "ali" match "ali", "alima", etc.)
    */
    searchPrenom.addEventListener("input", () => {
        filters.prenom = searchPrenom.value.toLowerCase().trim();
        refreshTableVisibility();
    });

    /* 🔎 FILTRE : NOM (input texte — colonne 1)
    → Recherche partielle (contient)
    */
    searchNom.addEventListener("input", () => {
        filters.nom = searchNom.value.toLowerCase().trim();
        refreshTableVisibility();
    });

    /* 🔎 FILTRE : SECTION (select — colonne 2)
    → Correspondance exacte
    */
    searchSection.addEventListener("change", () => {
        filters.section = searchSection.value.toLowerCase();
        refreshTableVisibility();
    });

    /* 🔎 FILTRE : MEMBRE ASSOCIÉ (select — colonne 3)
    → Correspondance exacte
    */
    searchAssoc.addEventListener("change", () => {
        filters.assoc = searchAssoc.value.toLowerCase();
        refreshTableVisibility();
    });

    /* 🔎 FILTRE : VILLE (input texte — colonne 7)
    → Recherche partielle (contient)
    */
    searchVille.addEventListener("input", () => {
        filters.ville = searchVille.value.toLowerCase().trim();
        refreshTableVisibility();
    });

    /* 🔘 FILTRE : MEMBRE DU BUREAU UNIQUEMENT (switch)
    → Si activé, n’affiche que les lignes
        dont la colonne "bureau" n’est pas vide
    */
    bureauSwitch.addEventListener("change", () => {
        filters.bureauOnly = bureauSwitch.checked;
        refreshTableVisibility();
    });

    /* 🎓 FILTRE : UNIQUEMENT LES ÉTUDIANTS M2 (switch)
    → Combine deux conditions :
        - section contient "m2"
        - statut associé = "étudiant/e"
    */
    m2Switch.addEventListener("change", () => {
        filters.m2Only = m2Switch.checked;
        refreshTableVisibility();
    });

    /* ==========================================================
    5) RESET : remettre tous les filtres à zéro
    ========================================================== */
    function resetAllFilters() {
        searchPrenom.value = "";
        searchNom.value = "";
        searchSection.value = "";
        searchAssoc.value = "";
        searchVille.value = "";
        bureauSwitch.checked = false;
        m2Switch.checked = false;

        filters.prenom = "";
        filters.nom = "";
        filters.section = "";
        filters.assoc = "";
        filters.ville = "";
        filters.bureauOnly = false;
        filters.m2Only = false;

        refreshTableVisibility();
    }

    // Réinitialise tous les filtres à chaque affichage de la page
    // (chargement, rechargement ou retour via le cache navigateur)
    window.addEventListener("pageshow", () => {
        resetAllFilters();
    });


    


    /* ----------------------------------------------------------
   🎯 RÉFÉRENCES DU MODAL
    ---------------------------------------------------------- */
    const modal       = document.getElementById("modal-edit");
    const cancelModal = document.getElementById("modal-btn-cancel");
    const closeBtn    = document.querySelector(".modal-close");

    /* ----------------------------------------------------------
    🎯 RÉFÉRENCES DES CHAMPS DU FORMULAIRE DU MODAL
    ---------------------------------------------------------- */
    const fieldId      = document.getElementById("edit-id");
    const fieldPrenom  = document.getElementById("edit-prenom");
    const fieldNom     = document.getElementById("edit-nom");
    const fieldSection = document.getElementById("edit-section");
    const fieldAssoc   = document.getElementById("edit-assoc");
    const fieldBureau  = document.getElementById("edit-bureau");
    const fieldEmail   = document.getElementById("edit-email");
    const fieldPhone   = document.getElementById("edit-phone");
    const fieldVille   = document.getElementById("edit-ville");
    const fieldMetier  = document.getElementById("edit-metier");


    async function postJSON(url, payload) 
    {
        const res = await fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        });
        return res.json();
    }
    /* ==========================================================
       🟣 MODAL — OUVERTURE + REMPLISSAGE AUTOMATIQUE
    ========================================================== */
    changeButtons.forEach(btn => 
    {
        btn.addEventListener("click", async () => {

            /* ============================================================
            🔎 1) RÉCUPÉRATION DE L’ID MEMBRE
            ============================================================ */
            const id_member = btn.dataset.id;
            saveBtn.dataset.id = id_member;
            
            if (!id_member) return;

            try {

                /* ============================================================
                📡 2) APPEL AJAX – RÉCUPÉRATION DES DONNÉES MEMBRE
                ============================================================ */
                const response = await postJSON("/?dest=fetch_adherent", {
                    id_member: id_member
                });

                if (!response.success) {
                    showUpdateMessage("error");
                    return;
                }

                /* ============================================================
                ✍️ 3) REMPLISSAGE DU MODAL AVEC LES DONNÉES REÇUES
                ============================================================ */
                fillMemberModal(response.data);

                /* ============================================================
                🟣 4) OUVERTURE DU MODAL
                ============================================================ */
                openModal();

            } catch (error) {
                console.error(error);
                showUpdateMessage("error");
            }
        });
    });


    /* ==========================================================
       🟣 OUVERTURE / FERMETURE ANIMÉE DU MODAL
    ========================================================== */

    function openModal() {
        modal.style.display = "flex";
        const box = modal.querySelector(".modal-content");
        box.classList.remove("closing");
        box.classList.add("open");
    }

    function closeModalAnimated() {
        const box = modal.querySelector(".modal-content");
        box.classList.remove("open");
        box.classList.add("closing");

        box.addEventListener("animationend", () => {
            modal.style.display = "none";
        }, { once: true });
    }

    function fillMemberModal(data) {
        /* ============================================================
        📌 CHAMPS CACHÉS / TECHNIQUES
        ============================================================ */
        fieldId.value = data.id_membre ?? "";

        /* ============================================================
        👤 IDENTITÉ
        ============================================================ */
        fieldPrenom.value = data.prenom ?? "";
        fieldNom.value    = data.nom ?? "";

        /* ============================================================
        📚 SECTION (SELECT AUTOMATIQUE)
        ============================================================ */
        const sectionValue = (data.section ?? "").toLowerCase();
        let sectionFound = false;

        for (const option of fieldSection.options) {
            if (option.value.toLowerCase() === sectionValue) {
                fieldSection.value = option.value;
                sectionFound = true;
                break;
            }
        }

        if (!sectionFound) {
            fieldSection.value = "";
        }

        /* ============================================================
        🏷️ STATUT ASSOCIATIF
        ============================================================ */
        fieldAssoc.value  = data.membre_assoc ?? "";
        fieldBureau.value = data.membre_bureau ?? "";

        /* ============================================================
        📞 CONTACT
        ============================================================ */
        fieldEmail.value = data.email ?? "";
        fieldPhone.value = data.phone_number ?? "";

        /* ============================================================
        🌍 AUTRES INFOS
        ============================================================ */
        fieldVille.value  = data.ville ?? "";
        fieldMetier.value = data.metier ?? "";

    }



    cancelModal.addEventListener("click", closeModalAnimated);
    modal.addEventListener("click", e => { if (e.target === modal) closeModalAnimated(); });


    /* ----------------------------------------------------------
    🟣 VALIDATION DU FORMULAIRE (AJAX)
    ---------------------------------------------------------- */
    const saveBtn = document.querySelector(".modal-btn-save");
    const updateMsg = document.getElementById("update-message");

    saveBtn.addEventListener("click", async () => 
    {


        /* ============================================================
        🔎 1) RÉCUPÉRATION DE L’ID MEMBRE (DEPUIS LE BOUTON)
        ============================================================ */
        const id_member = saveBtn.dataset.id;
        if (!id_member) {
            showUpdateMessage("Identifiant membre manquant ❌", true);
            return;
        }

        /* ============================================================
        📥 2) CONSTRUCTION DU PAYLOAD (POST JSON)
        ============================================================ */
        const payload = {
            id_member: id_member,
            prenom:    fieldPrenom.value.trim(),
            nom:       fieldNom.value.trim(),
            section:   fieldSection.value,
            membre_assoc:  fieldAssoc.value,
            membre_bureau: fieldBureau.value.trim(),
            email:     fieldEmail.value.trim(),
            phone:     fieldPhone.value.trim(),
            ville:     fieldVille.value.trim(),
            metier:    fieldMetier.value.trim()
        };

        try {

            /* ============================================================
            📡 3) APPEL AJAX — UPDATE MEMBRE
            ============================================================ */
            const response = await postJSON("/?dest=update_adherent", payload);

            if (!response.success) {
                showUpdateMessage(response.error ?? "Erreur de mise à jour ❌", true);
                return;
            }

            /* ============================================================
            ✅ 4) SUCCÈS — FEEDBACK UTILISATEUR
            ============================================================ */
            showUpdateMessage("Mise à jour réussie ✔️", false);

            /* ============================================================
            🟣 5) FERMETURE DU MODAL + RELOAD
            ============================================================ */
            closeModalAnimated();

            setTimeout(() => {
                window.location.reload();
            }, 2000);

        } catch (error) {
            console.error(error);
            showUpdateMessage("Erreur serveur ❌", true);

            setTimeout(() => {
                window.location.reload();
            }, 2000);
        }
    });


     /* ----------------------------------------------------------
    🟣 CHANGER LES ÉTUDIANTS DIPLÔMÉS EN ANCIENS
    ---------------------------------------------------------- */
    const makeAncienBtn = document.querySelectorAll(".btn-make-ancien");

    makeAncienBtn.forEach(btn =>
    {
        btn.addEventListener("click", async () => {

            const id_member = btn.dataset.id;
            if (!id_member) return;

            try {
                const response = await postJSON("/?dest=update_adherent", {
                    id_member: id_member,
                    action: "make_ancien"
                });

                if (!response.success) {
                    showUpdateMessage(response.error ?? "Erreur ❌", true);
                    return;
                }

                showUpdateMessage("Membre passé en Ancien ✔️", false);

                setTimeout(() => location.reload(), 2000);

            } catch (e) {
                console.error(e);
                showUpdateMessage("Erreur serveur ❌", true);
            }
        });
    });



    /* ----------------------------------------------------------
    🟣 Fonction d'affichage du message
    ---------------------------------------------------------- */
    function showUpdateMessage(text, isError = false) {
        updateMsg.textContent = text;

        if (isError) updateMsg.classList.add("error");
        else updateMsg.classList.remove("error");

        updateMsg.style.display = "block";

        setTimeout(() => {
            updateMsg.style.display = "none";
        }, 5000);
    }


    
});
