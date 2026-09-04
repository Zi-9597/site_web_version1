document.addEventListener("DOMContentLoaded", () => {

    /* ============================================================================
       🔗 RÉFÉRENCES DOM
       ============================================================================
       - Boutons "Lire la suite"
       - Éléments du modal
       - Bouton de fermeture
    ============================================================================ */

    const btnLirePlus = document.querySelectorAll(".btn-lire-plus");

    const modal    = document.getElementById("modal-read-actu");
    const modalBox = modal.querySelector(".modal-content");

    const titleEl     = document.getElementById("modal-actu-title");
    const dateEl      = document.getElementById("modal-actu-date");
    const descEl      = document.getElementById("modal-actu-desc");
    const linkEl      = document.getElementById("modal-actu-link");
    const linkBox     = document.getElementById("modal-link-box");
    const signatureEl = document.getElementById("modal-actu-signature");

    const btnClose = modal.querySelector(".modal-btn-cancel");

    /* ============================================================================
       🪟 OUVERTURE / FERMETURE DU MODAL
       ============================================================================
       Compatible avec ton CSS (display:none + animations)
    ============================================================================ */

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

    modal.addEventListener("click", (e) => {
        if (e.target === modal) closeModal();
    });

    /* ============================================================================
       📖 ACTION "LIRE LA SUITE"
       ============================================================================
       - Récupération de l'ID
       - Fetch de l’actualité
       - Injection dynamique dans le modal
    ============================================================================ */

    btnLirePlus.forEach(btn => {

        btn.addEventListener("click", async () => {

            const actuId = btn.dataset.id;
      
            if (!actuId) return;

            try {
                const response = await fetch(`/?dest=get_actualites&id_actu=${actuId}`);
                const data = await response.json();

                /* ------------------------------------------------
                   📝 REMPLISSAGE DU CONTENU DU MODAL
                -----------------------------------------   ------- */
          
                
                // Titre
                titleEl.textContent = data.titre_actu || "";

                // Date de publication
                dateEl.textContent = "Date de publication : " + formatDate(data.date_depot);
                dateEl.style.fontSize = '15px';

                // Description (retours à la ligne respectés)
                descEl.textContent = data.desc_actu || "";
                descEl.style.whiteSpace = "pre-line";

                // Lien complémentaire (optionnel)
                try {
                    const url = new URL(data.lien_actu);
                    if (url.protocol !== "https:") throw new Error("Unsafe URL");
                    // CHANGE (URL safety): only render validated HTTPS links from content records.
                    linkEl.href = url.href;
                    linkEl.rel = "noopener noreferrer";
                    linkEl.textContent = "Consulter le lien pour plus d’informations";
                    linkBox.style.display = "block";
                } catch (_) {
                    linkBox.style.display = "none";
                }

                const em = document.createElement("em");
                em.textContent = `Publié par le Bureau EEA le ${formatDate(data.date_depot)}`
                // Signature institutionnelle
                signatureEl.innerHTML =  "";
                signatureEl.appendChild(em);
                   

                // Ouverture du modal
                openModal();

            } catch (error) {
            }
        });
    });

    /* ============================================================================
       🗓️ FORMATAGE DE DATE (FR)
    ============================================================================ */

    function formatDate(dateStr) {
        if (!dateStr) return "";
        const d = new Date(dateStr);
        return d.toLocaleDateString("fr-FR", {
            day: "2-digit",
            month: "long",
            year: "numeric"
        });
    }

});
