document.addEventListener('DOMContentLoaded', () => {
    /**
     * =======================================================
     * Récupération des éléments nécessaires du DOM
     * =======================================================
     */
    const formElements = {
        form: document.getElementById("formulaire-offre"),
        titreOffre: document.getElementById("titre-offre"),
        specialites: document.querySelectorAll("input[name='specialites[]']"),
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
    
    /**
     * =======================================================
     * Variables globales
     * =======================================================
     */
    const specialiteSet = new Set(); // stocke les spécialités cochées
    const MAX_CHAR = 500;            // limite de caractères description

    /**
     * =======================================================
     * Validation description (compteur + couleurs dynamiques)
     * =======================================================
     */
    function validateDescription() {
        let length = formElements.description.value.length;

        // Tronque si dépasse la limite
        if (length > MAX_CHAR) {
            formElements.description.value = formElements.description.value.substring(0, MAX_CHAR);
            length = MAX_CHAR;
        }

        // Mise à jour compteur
        formElements.charCount.textContent = `${length} / ${MAX_CHAR} caractères`;

        // Couleur selon l’état
        formElements.charCount.style.color =
            length === 0 ? "red" :
            length < 400 ? "green" :
            length < MAX_CHAR ? "orange" : "red";

        return length > 0; // retourne vrai si non vide
    }

    /**
     * =======================================================
     * Validation champ "Lieu"
     * Autorise vide, sinon uniquement lettres/espaces/tirets
     * =======================================================
     */
    function validateLieu() {
        const { lieu, form_lieu } = formElements;
        const regexLieu = /^[a-zA-ZÀ-ÿ\s-]+$/;

        if (lieu.value.trim() !== "" && !regexLieu.test(lieu.value.trim())) {
            form_lieu.style.display = "block";
            return false;
        }
        form_lieu.style.display = "none";
        return true;
    }

    /**
     * =======================================================
     * Validation champ "Département"
     * Autorise vide, sinon exactement 5 chiffres
     * =======================================================
     */
    function validateDepartement() 
    {
        const { departement, form_code, commune, departement_nom, region } = formElements;

        const regex_dep = /^[0-9]{5}$/;
        const value_dep = departement.value.trim()


        if(value_dep !== "" && !regex_dep.test(value_dep))
        {
            form_code.style.display = "block"; 
            commune.value = ""; 
            departement_nom.value = ""; 
            region.value = "";
            return false;
        }
        form_code.style.display = "none";
        if(regex_dep.test(value_dep))
        {
            
         
            fetch("/?dest=cp_fetch" , {

                method: "POST",
                headers: { "Content-Type": "application/json" },
                body : JSON.stringify({code_postal : value_dep})
            })
            .then(res => res.json())
            .then(data =>{
                if(data.success)
                {
                    
                    commune.value = data.commune; 
                    departement_nom.value = data.departement;
                    region.value = data.region;
                }
                else
                {
                    commune.value = ""; 
                    departement_nom.value = "";
                    region.value = "";
                }
            })
            .catch(err =>{
                console.log("farakh");
                commune.value = ""; 
                departement_nom.value = "";
                region.value = "";

            })


        }   
      
        
        return true;

    }



    
    /**
     * =======================================================
     * Validation champ "LinkedIn"
     * Autorise vide, sinon doit commencer par /jobs/
     * =======================================================
     */
    function validateLinkedin() {
        const { linkedin, form_link } = formElements;
        const regexLinkedInJob = /^https:\/\/(www\.)?linkedin\.com\/jobs(\/|$)/;

        const linkedin_value = linkedin.value.trim();

        if (linkedin_value === "" || regexLinkedInJob.test(linkedin_value)) {
            form_link.style.display = "none";
            return true;
        }
        form_link.style.display = "block";
        return false;
    }

    /**
     * =======================================================
     * Validation globale du formulaire
     * Active/désactive bouton submit
     * =======================================================
     */
    function validateForm() {
        const hasTitle = formElements.titreOffre.value.trim().length > 0;
        const hasDescription = validateDescription();
        const hasSpecialite = specialiteSet.size > 0;
        const valide_dep = validateDepartement();
        const linkedin_link = validateLinkedin();
        
        formElements.submitBtn.disabled = !(hasTitle && hasDescription && hasSpecialite && valide_dep && valide_dep && linkedin_link);
    }

    /**
     * =======================================================
     * Gestion des événements
     * =======================================================
     */
    // Champs texte
    formElements.titreOffre.addEventListener("input", validateForm);
    formElements.description.addEventListener("input", validateForm);
    
    formElements.departement.addEventListener("input", validateForm);
    formElements.linkedin.addEventListener("input", validateForm);

    // Cases à cocher (spécialités)
    formElements.specialites.forEach(input => {
        input.addEventListener("change", () => {
            input.checked
                ? specialiteSet.add(input.parentNode.textContent.trim())
                : specialiteSet.delete(input.parentNode.textContent.trim());
            validateForm();
        });
    });

    /**
     * =======================================================
     * Message de retour sous le bouton
     * =======================================================
     */
    function showMessage(type, formData, mail_user = "", date_depot = "", dep_nom = "-", lieu = "-", reg_nom = "-") 
    {
        formElements.box_div.innerHTML = ""; // reset avant affichage
        const msgBox = document.createElement("div");

        // Styles message global
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

        // Infos à afficher
        msgBox.innerHTML = `
            <strong style="color:${type === "success" ? "#28a745" : "#dc3545"}">
                ${type === "success" ? "✅ Événement ajouté" : "❌ Erreur"}
            </strong><br>
            <span>Nom de l'offre : ${formData.get("titre_offre") || "-"}</span><br>
            <span>Date de dépôt : ${date_depot || "-"}</span><br>
            <span>Mail : ${mail_user || "-"}</span><br>
            <span>Spécialité : ${[...specialiteSet].join(", ")}</span>
            <div style="margin-top:10px;">
                <span><strong>Code postal :</strong> ${formData.get("departement") || "-"}</span><br>
                <span><strong>Commune :</strong> ${lieu}</span><br>
                <span><strong>Département :</strong> ${dep_nom}</span><br>
                <span><strong>Région :</strong> ${reg_nom}</span>
            </div>
        `;

        formElements.form.appendChild(msgBox);

        // Apparition
        setTimeout(() => { msgBox.style.opacity = "1"; }, 50);

        // Disparition après 5s
        setTimeout(() => {
            msgBox.style.opacity = "0";
            setTimeout(() => msgBox.remove(), 500);
        }, 5000);
    }


    /**
     * =======================================================
     * Soumission formulaire (fetch POST)
     * =======================================================
     */
    formElements.form.addEventListener("submit", e => {
        e.preventDefault();

        const params = new URLSearchParams(window.location.search);
        const data_form = new FormData(formElements.form);
        const user_id = params.get("id_user");

        const url = `/?dest=ajout_emploie&id_user=${encodeURIComponent(user_id)}`;

        fetch(url, {
            method: "POST",
            body: data_form
        })
        .then(response => {
            if (!response.ok) throw new Error(`Server problem: ${response.status}`);
            return response.json();
        })
        .then(result => {
            if (result.status) {
                showMessage("success", data_form, result.mail, result.dateDepot , result.dep_nom , result.lieu , result.reg_nom);
            } else {
                showMessage("error", data_form);
            }
            resetFormulaire();
            formElements.submitBtn.disabled = true;
        })
        .catch(() => showMessage("error", data_form));
    });

    /**
     * =======================================================
     * Réinitialisation formulaire
     * =======================================================
     */
    function resetFormulaire() {
        formElements.form.reset();
        specialiteSet.clear();
        formElements.charCount.textContent = `0 / ${MAX_CHAR} caractères`;
        formElements.charCount.style.color = "red";
        formElements.submitBtn.disabled = true;
    }

    // Reset auto avant de quitter la page
    window.addEventListener("beforeunload", resetFormulaire);

    // État initial
    resetFormulaire();
});
