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

        // Champ texte : lieu de l'événement
        lieu_event: document.getElementById("lieu-event"), 

        //Champ de texte : description de l'évenement
        desc_event : document.getElementById('desc-event'),

        // Élément compteur de caractères de la description
        char_count: document.getElementById("char-count"), 

        // Liste déroulante : catégorie de l'événement
        categorie: document.getElementById("categorie"), 

        // Bouton de soumission du formulaire
        submitButton: document.getElementById("button_submit"),

        form_event : document.getElementById("formulaire-evenement")
    };

    

    elements.desc_event.max

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
        const isValid = elements.categorie.value !== "" &&
            valid_date &&
            valid_description &&
            elements.nom_event.value.trim() !== "" &&
            elements.lieu_event.value.trim() !== "";
        console.log(isValid)
        elements.submitButton.disabled = !isValid; // Active ou désactive le bouton
    }

    function resetForm() {
        elements.form_event.reset();
        elements.char_count.textContent = "0 / 500 caractères";
        elements.char_count.style.color = 'red';
        elements.date_format.style.color = 'red';
    }


    elements.event_date.addEventListener('input', validateForm);
    elements.desc_event.addEventListener('input' , validateForm);
    elements.nom_event.addEventListener('input', validateForm);
    elements.lieu_event.addEventListener('input', validateForm);
    elements.categorie.addEventListener('change', validateForm);

    /**
     * ⚡ Quand on soumet le formulaire → réinitialisation
     */
    elements.form_event.addEventListener("submit", function () {
        resetForm();
    });

    /**
     * ⚡ Quand on recharge ou revient en arrière → réinitialisation
     */
    window.addEventListener("pageshow", function () {
        resetForm();
    });



   


   
});
