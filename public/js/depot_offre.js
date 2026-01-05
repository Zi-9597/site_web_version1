document.addEventListener('DOMContentLoaded', () => {

    /**
     * =======================================================
     * Récupération des éléments nécessaires du DOM
     * - Structure professionnelle avec objets regroupés
     * - Permet d'éviter les variables globales dispersées
     * =======================================================
     */
    const formElements = {
        form: document.getElementById("formulaire-offre"),
        titreOffre: document.getElementById("titre-offre"),
        specialites: document.querySelectorAll("input[name='specialites[]']"),
        types_contrat : document.querySelectorAll("input[name='types']"),
        linkedin: document.getElementById("linkedin"),
        description: document.getElementById("description"),
        charCount: document.getElementById("char-count"),
        submitBtn: document.getElementById("button_submit"),
        box_div: document.getElementById("block_valid"),

        // Champs localisation
        departement: document.getElementById("departement"),
        commune: document.getElementById("commune"),
        departement_nom: document.getElementById("departement_nom"),
        region: document.getElementById("region"),

        // Messages d’erreur associés
        form_code: document.querySelector(".form_code"),
        form_link: document.querySelector(".form_link")
    };


    // ✅ Détection automatique du mode Job Étudiant
    const IS_JOB_ETUDIANT =
    !formElements.specialites.length &&
    !formElements.types_contrat.length &&
    !formElements.linkedin;

    /**
     * =======================================================
     * Variables globales utilisées pour la validation
     * =======================================================
     */
    const specialiteSet = new Set();   // Ensemble stockant les spécialités cochées
    var contrat_valeur = null;         // Stocke le type de contrat choisi
    const MAX_CHAR = 500;              // Limite de caractères pour la description
    

    /**
     * =======================================================
     * Validation du champ description
     * - Met à jour le compteur
     * - Gère la couleur selon l'état
     * - Empêche de dépasser MAX_CHAR
     * =======================================================
     */
    function validateDescription() {
        let length = formElements.description.value.length;

        // Empêche le dépassement de la limite
        // (pratique pro : éviter les erreurs backend inutilement)
        if (length > MAX_CHAR) {
            formElements.description.value = formElements.description.value.substring(0, MAX_CHAR);
            length = MAX_CHAR;
        }

        // Mise à jour de l'affichage du compteur
        formElements.charCount.textContent = `${length} / ${MAX_CHAR} caractères`;

        // Variation dynamique de couleur
        // -> pédagogiquement : aide visuelle immédiate pour l’utilisateur
        formElements.charCount.style.color =
            length === 0 ? "red" :
            length < 400 ? "green" :
            length < MAX_CHAR ? "orange" : "red";

        return length > 0; // Retourne true si non vide
    }

    
    /**
     * =======================================================
     * Validation du champ LinkedIn
     * - Accepte vide
     * - Sinon doit commencer par /jobs/
     * =======================================================
     */
    function validateLinkJob() {
        const { linkedin, form_link } = formElements;
        const regexLinkJob = /^(https?\/\/)?(www.)?[a-zA-Z0-9-]+\.[a-zA-Z]{2,}(\/\S*)?/
        const linkedin_value = linkedin.value.trim();

        // Cas accepté : vide ou valide
        if (linkedin_value === "" || regexLinkJob.test(linkedin_value)) {
            form_link.style.display = "none";
            return true;
        }

        // Cas erreur : format incorrect
        form_link.style.display = "block";
        return false;
    }


    /**
     * =======================================================
     * Validation globale du formulaire
     * - Active / désactive le bouton Submit
     * - Combine toutes les validations
     * =======================================================
     */
    function validateForm() {


        
        const hasTitle = formElements.titreOffre.value.trim().length > 0;
        const hasDescription = validateDescription();

        // ✅ MODE JOB ÉTUDIANT → seulement titre + description
        if (IS_JOB_ETUDIANT) {
            formElements.submitBtn.disabled = !(hasTitle && hasDescription);
            return;
        }
        const hasSpecialite = specialiteSet.size > 0;
        const linkedinOK = validateLinkJob();

        // Le bouton est activé seulement si
        // → titre rempli, description remplie,
        // → au moins une spécialité,
        // → type de contrat choisi,
        // → lien valide
        formElements.submitBtn.disabled = !(hasTitle && hasDescription && hasSpecialite && contrat_valeur && linkedinOK);
    }


    /**
     * =======================================================
     * Gestion des événements input text
     * =======================================================
     */
    formElements.titreOffre.addEventListener("input", validateForm);
    formElements.description.addEventListener("input", validateForm);
    if (formElements.linkedin) {
        formElements.linkedin.addEventListener("input", validateForm);
    }   
    

    /**
     * =======================================================
     * Gestion des cases à cocher — spécialités
     * =======================================================
     */
    if (formElements.specialites.length > 0) 
    {
        formElements.specialites.forEach(input => {
        input.addEventListener("change", () => {

            // Ajout / suppression dans le Set
            // -> Pro : Set garantit l’unicité
            if (input.checked) {
                specialiteSet.add(input.parentNode.textContent.trim());
            } else {
                specialiteSet.delete(input.parentNode.textContent.trim());
            }

            validateForm();
            });
        });


    }
   

    /**
     * =======================================================
     * Gestion exclusive des types de contrat
     * - Une seule case possible à la fois
     * - Les autres sont désactivées tant qu’une est cochée
     * =======================================================
     */
    if (formElements.types_contrat.length > 0)
    {
        formElements.types_contrat.forEach(input => {
        input.addEventListener("change", () => {

            if (input.checked) {
                // L'utilisateur sélectionne un type
                contrat_valeur = input.parentNode.textContent.trim();

                // Désactivation des autres cases
                formElements.types_contrat.forEach(other => {
                    if (other !== input) other.disabled = true;
                });

            } else {
                // L'utilisateur annule son choix
                contrat_valeur = null;

                // Réactivation de toutes les cases
                formElements.types_contrat.forEach(other => {
                    other.disabled = false;
                });
            }

            validateForm();
            });
        });
    }
    else
    {
        contrat_valeur = "Job Étudiant"
    }
    

    /**
     * =======================================================
     * Message d’information après soumission
     * - Affichage dynamique d’un bloc de confirmation
     * - Disparition automatique
     * =======================================================
     */
    function showMessage(type, formData, mail_user = "", date_depot = "" , erreur = "-") 
    {
        formElements.box_div.innerHTML = ""; // Nettoyage du bloc précédent
        const msgBox = document.createElement("div");

        // Styles du message
        msgBox.style.marginTop = "15px";
        msgBox.style.padding = "12px 16px";
        msgBox.style.borderRadius = "8px";
        msgBox.style.backgroundColor = "#fff";
        msgBox.style.fontFamily = "'Nunito', sans-serif";
        msgBox.style.fontSize = "18px";
        msgBox.style.color = "#333";
        msgBox.style.opacity = "0";
        msgBox.style.transition = "opacity 0.5s ease";
        msgBox.style.borderLeft = `6px solid ${type === "success" ? "#28a745" : "#dc3545"}`;
        if(type === "success")
        {
            // Contenu dynamique (pro + clair)
            msgBox.innerHTML = `
            <strong style="color:${type === "success" ? "#28a745" : "#dc3545"}">
                ${type === "success" ? "✅ Offre ajoutée" : "❌ Erreur"}
            </strong><br>
            <span>Nom de l'offre : ${formData.get("titre_offre") || "-"}</span><br>
            <span>Date de dépôt : ${date_depot || "-"}</span><br>
            <span>Mail : ${mail_user || "-"}</span><br>
            ${IS_JOB_ETUDIANT ? "" : `
            <span>Spécialité : ${[...specialiteSet].join(", ")}</span><br>
            `}
            <span>Type de contrat: ${contrat_valeur}</span>
           
        `;
        }
        else
        {
            msgBox.innerHTML = `
            <strong style="color:${type === "success" ? "#28a745" : "#dc3545"}">
                ${type === "success" ? "✅ Offre ajoutée" : "❌ Erreur"}
            </strong><br>
            <span>Erreur : ${erreur}</span><br>
            `;

        }
        

        formElements.form.appendChild(msgBox);

        // Apparition progressive
        setTimeout(() => { msgBox.style.opacity = "1"; }, 50);

        // Disparition après délai
        setTimeout(() => {
            msgBox.style.opacity = "0";
            setTimeout(() => msgBox.remove(), 500);
        }, 7000);
    }


    /**
     * =======================================================
     * Soumission du formulaire via Fetch (POST)
     * =======================================================
     */
    
    formElements.form.addEventListener("submit", async (e) => 
    {
        e.preventDefault(); // Empêche le rechargement de la page

        const dataForm = new FormData(formElements.form);
        const url = "/?dest=ajouter_contrat";

        try {
            const response = await fetch(url, {
                method: "POST",
                body: dataForm
            });

            if (!response.ok) {
                throw new Error("Erreur réseau");
            }

            const result = await response.json();

            if (result.success === true) {
                showMessage(
                    "success",
                    dataForm,
                    result.email ?? null,
                    result.dateDepot ?? null
                );
            } else {
                showMessage(
                    "error",
                    dataForm,
                    null,
                    null,
                    result.message ?? "Une erreur est survenue"
                );
            }

            resetFormulaire();
            formElements.submitBtn.disabled = true;

        } catch (error) {
            showMessage("error", dataForm);
        }
    });

    /**
     * =======================================================
     * Réinitialisation complète du formulaire
     * =======================================================
     */
    function resetFormulaire() {
        formElements.form.reset();
        specialiteSet.clear();
        formElements.charCount.textContent = `0 / ${MAX_CHAR} caractères`;
        formElements.charCount.style.color = "red";
        formElements.submitBtn.disabled = true;
    }

    // Réinitialisation avant quitter la page
    window.addEventListener("beforeunload", resetFormulaire);

    // Initialisation du formulaire au chargement
    resetFormulaire();
});
