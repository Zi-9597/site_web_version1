document.addEventListener('DOMContentLoaded', () => {
    /**
     * Récupération des éléments nécessaires du DOM
     * Chaque élément représente un champ ou un composant du formulaire.
     */

   // ================================
    // Select form elements
    // ================================
    const formElements = {
        form: document.getElementById("formulaire-offre"),
        titreOffre: document.getElementById("titre-offre"),
        specialites: document.querySelectorAll("input[name='specialites[]']"),
        linkedin: document.getElementById("linkedin"),
        description: document.getElementById("description"),
        charCount: document.getElementById("char-count"),
        submitBtn: document.getElementById("button_submit"),
        box_div : document.getElementById("block_valid")
    };

   
    // ================================
    // Global variables
    // ================================
    const specialiteSet = new Set(); 
   
    const MAX_CHAR = 500;            

    // ================================
    // Validate description with counter
    // ================================
    function validateDescription() {
        let length = formElements.description.value.length;

        if (length > MAX_CHAR) {
            formElements.description.value = formElements.description.value.substring(0, MAX_CHAR);
            length = MAX_CHAR;
        }

        formElements.charCount.textContent = `${length} / ${MAX_CHAR} caractères`;

        formElements.charCount.style.color = 
            length === 0 ? "red" : 
            length < 400 ? "green" : 
            length < MAX_CHAR ? "orange" : "red";

        return length > 0;
    }

    // ================================
    // Form validation function
    // ================================
    function validateForm() {
        const hasTitle = formElements.titreOffre.value.trim().length > 0;
        const hasDescription = validateDescription();
        const hasSpecialite = specialiteSet.size > 0;
   
        // Enable/disable submit button
        formElements.submitBtn.disabled = !(hasTitle && hasDescription && hasSpecialite);
    }

    // ================================
    // Event listeners
    // ================================

    // Title field
    formElements.titreOffre.addEventListener("input", validateForm);

    // Description field
    formElements.description.addEventListener("input", validateForm);

    // Checkboxes
    formElements.specialites.forEach(input => {
        input.addEventListener("change", () => {
            input.checked 
                ? specialiteSet.add(input.parentNode.textContent.trim()) 
                : specialiteSet.delete(input.parentNode.textContent.trim());
            validateForm();
        });
    });

    
    // Fonction pour afficher un message sous le bouton
    function showMessage(type, formData , mail_user = "", date_depot = "") {
        formElements.box_div.innerHTML = ""; // vider avant de réafficher

        const msgBox = document.createElement("div");

        // Styles
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

        // Récupérer champs
        const nom_offre  = formData.get("titre_offre");
        const date = date_depot;
        const mail = mail_user;


        msgBox.innerHTML = `
            <strong style="color:${type === "success" ? "#28a745" : "#dc3545"}">
                ${type === "success" ? "✅ Événement ajouté" : "❌ Erreur"}
            </strong><br>
            <span>Nom de l'offre : ${nom_offre || "-"}</span><br>
            <span>Date de dépôt: ${date || "-"}</span><br>
            <span>Mail : ${mail || "-"}</span><br>
            <span>Spécialité : ${[...specialiteSet].join(", ")}</span>
        `;

        formElements.form.appendChild(msgBox);

        // Apparition
        setTimeout(() => { msgBox.style.opacity = "1"; }, 50);

        // Disparition après 5s
        setTimeout(() => {
            msgBox.style.opacity = "0";
            setTimeout(() => {
                msgBox.style.display = "none";
                msgBox.remove();
            }, 500);
        }, 5000);
    }




    formElements.form.addEventListener("submit" , e=>
    {

        e.preventDefault();

        const params =  new URLSearchParams(window.location.search); 
        const data_form = new FormData(formElements.form);

        const user_id = params.get("id_user"); 

        const url = `/?dest=ajout_emploie&id_user=${encodeURIComponent(user_id)}`; 

        fetch(url , 
        {
            method: "POST",
            body : data_form

        })
        .then(response =>
        {
            if(!(response.ok))
            {
                throw new Error(`Server problem ... : ${response.status}`);
            }

            return response.json();

            
        })
        .then(result =>
            {
            
                if (result.status)
                {
                    showMessage("success", data_form , result.mail , result.dateDepot);
                }
                else
                {
                    showMessage("error", data_form );
                }
                resetFormulaire();
                formElements.submitBtn.disabled = true;
            }
        )
        .catch(() =>
        {
         
            showMessage("error", data_form);
        })     
    })
   
    // ================================
    // Reset function
    // ================================
    function resetFormulaire() {
        formElements.form.reset();
        specialiteSet.clear();
        formElements.charCount.textContent = `0 / ${MAX_CHAR} caractères`;
        formElements.charCount.style.color = "red";
        formElements.submitBtn.disabled = true; // disable again
    }

    // ================================
    // Reset in all cases
    // ================================
    window.addEventListener("beforeunload", resetFormulaire);

    
    

    

    // ================================
    // Initialize state on load
    // ================================
    resetFormulaire();


   
});
