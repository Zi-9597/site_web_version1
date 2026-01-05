document.addEventListener('DOMContentLoaded', () => {
    /**
     * Récupération des éléments nécessaires du DOM
     * Chaque élément représente un champ ou un composant du formulaire.
     */
    const elements = 
    {
        // Champ texte : nom de l'événement
        nom_event: document.getElementById("nom-event"), 

        // Champ texte : date de l'événement
        event_date: document.getElementById("evenmt_date"), 


        date_format : document.getElementById("forme_id_anniv"),

        //Champ de texte : description de l'évenement
        desc_event : document.getElementById('desc-event'),

        // Élément compteur de caractères de la description
        char_count: document.getElementById("char-count"), 

        // Bouton de soumission du formulaire
        submitButton: document.getElementById("button_submit"),

        form_event : document.getElementById("formulaire-evenement"),

        box_div : document.getElementById("box_present")
    };

    

  

    // Désactive le bouton de soumission par défaut pour éviter une soumission prématurée.
    elements.submitButton.disabled = true;

    function validateDate() 
    {
        // Récupère la valeur saisie dans le champ de date
        const event_date_Value = elements.event_date.value;

        // Expression régulière pour vérifier le format JJ/MM/AAAA
        const regex = /\b(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/\d{4}\b/;
        
        // Vérifie que la valeur respecte le format attendu
        if (regex.test(event_date_Value)) {
            // Découpe la chaîne (ex : "10/09/2025") en [jour, mois, année]
            const [day, month, year] = event_date_Value.split('/').map(Number);

            // Crée un objet Date avec ces valeurs
            const date = new Date(year, month - 1, day);

            // Récupère la date d’aujourd’hui
            const today = new Date();
            today.setHours(0, 0, 0, 0); // Normalisation → on ignore heures/minutes/secondes

            // Vérifie :
            // 1. Que la date n’est pas antérieure à aujourd’hui
            // 2. Que les valeurs correspondent bien à un vrai jour, mois et année
            if (
                date >= today &&
                date.getDate() === day &&
                date.getMonth() === month - 1 &&
                date.getFullYear() === year
            ) {
                // Si tout est correct → on affiche en vert
                elements.date_format.style.color = "green"; 
                return true;
            }
        }

        // Si le format ou la validité échouent → on affiche en rouge
        elements.date_format.style.color = "red"; 
        return false;
    }

    function validateDescription() 
    {
        let length = elements.desc_event.value.length;

        // Bloquer la saisie si on atteint 500 caractères
        // Bloquer l'ajout après 500 caractères mais laisser effacer
        if (length > 500) 
        {
            elements.desc_event.value = elements.desc_event.value.substring(0, 500);
            length = 500; // recalcule la longueur réelle
        }

        // Mettre à jour le compteur
        elements.char_count.textContent = `${length} / 500 caractères`;

        // Gestion des couleurs optimisée
        switch (true) {
            case (length === 0):
                elements.char_count.style.color = "red";
                break;
            case (length < 400):
                elements.char_count.style.color = "green";
                break;
            case (length < 500):
                elements.char_count.style.color = "orange";
                break;
            case (length === 500):
                elements.char_count.style.color = "red";
                break;
        }

        // Retourne true si longueur > 0
        return length > 0;
    }

    

    function validateForm() {
        const valid_date = validateDate();
        const valid_description = validateDescription();

        // Vérifie que tous les champs requis sont remplis et valides.
        const isValid = valid_date &&
            valid_description &&
            elements.nom_event.value.trim() !== "";

            console.log(isValid)
        elements.submitButton.disabled = !isValid; // Active ou désactive le bouton
    }

    // Fonction pour afficher un message sous le bouton
   function showMessage(type, formData) 
   {

        elements.box_div.innerHTML = ""; // Nettoyage

        const msgBox = document.createElement("div");

        // -------------------------------
        // Styles
        // -------------------------------
        msgBox.style.marginTop = "15px";
        msgBox.style.padding = "12px 16px";
        msgBox.style.borderRadius = "8px";
        msgBox.style.backgroundColor = "#fff";
        msgBox.style.fontFamily = "'Nunito', sans-serif";
        msgBox.style.fontSize = "20px";
        msgBox.style.color = "#333";
        msgBox.style.opacity = "0";
        msgBox.style.transition = "opacity 0.5s ease";
        msgBox.style.borderLeft =
            `6px solid ${type === "success" ? "#28a745" : "#dc3545"}`;

        // -------------------------------
        // Données utilisateur (SAFE)
        // -------------------------------
        const nom  = formData.get("nom_event") || "-";
        const date = formData.get("date") || "-";

        // -------------------------------
        // Construction DOM sécurisée
        // -------------------------------
        const title = document.createElement("strong");
        title.textContent =
            type === "success" ? "✅ Événement ajouté" : "❌ Erreur";
        title.style.color =
            type === "success" ? "#28a745" : "#dc3545";

        const spanNom = document.createElement("span");
        spanNom.textContent = `Nom : ${nom}`;

        const spanDate = document.createElement("span");
        spanDate.textContent = `Date : ${date}`;

       

        // -------------------------------
        // Ajout dans le message
        // -------------------------------
        msgBox.appendChild(title);
        msgBox.appendChild(document.createElement("br"));
        msgBox.appendChild(spanNom);
        msgBox.appendChild(document.createElement("br"));
        msgBox.appendChild(spanDate);
        msgBox.appendChild(document.createElement("br"));

        elements.box_div.appendChild(msgBox);

        // -------------------------------
        // Animations
        // -------------------------------
        setTimeout(() => { msgBox.style.opacity = "1"; }, 50);

        setTimeout(() => {
            msgBox.style.opacity = "0";
            setTimeout(() => msgBox.remove(), 500);
        }, 5000);
    }


    
    elements.form_event.addEventListener("submit" , e=>
    {

        e.preventDefault();

       
        const data_form = new FormData(elements.form_event);


        const url = `/?dest=ajouter_event`; 

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

            showMessage("success", data_form);
            resetForm();
            elements.submitButton.disabled = true;
        })
        .catch(() =>
        {
            showMessage("error", data_form);
        })





        
    })

    function resetForm() {
        elements.form_event.reset();
        elements.char_count.textContent = "0 / 500 caractères";
        elements.char_count.style.color = 'red';
        elements.date_format.style.color = 'red';
    }


  

    elements.event_date.addEventListener('input', validateForm);
    elements.desc_event.addEventListener('input' , validateForm);
    elements.nom_event.addEventListener('input', validateForm);


    
    /**
     * ⚡ Quand on recharge ou revient en arrière → réinitialisation
    */
    window.addEventListener("pageshow", function () {
        resetForm();
        elements.submitButton.disabled = true;
    });



   


   
});
