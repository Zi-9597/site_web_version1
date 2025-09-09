document.addEventListener('DOMContentLoaded', () => {
    /**
     * Récupération des éléments nécessaires du DOM
     * Chaque élément représente un champ ou un composant du formulaire.
     */
    const elements = {
        civiliteChoice: document.getElementById("civilite-select"), // Liste déroulante pour le choix de la civilité
        phoneInput: document.getElementById("phone"), // Champ pour le numéro de téléphone
        nomInput: document.getElementById("last_name"), // Champ pour le nom de famille
        prenomInput: document.getElementById("name"), // Champ pour le prénom
        anniversaireInput: document.getElementById("birthday"), // Champ pour la date d'anniversaire
        membreInput: document.getElementById("membre-assoc"), // Liste déroulante pour le statut de membre
        sectionInput: document.getElementById("filiere-section"), // Liste déroulante pour la filière
        inputFiliere: document.getElementById("autre-filiere"), // Champ texte pour une filière personnalisée
        telAvailable: document.getElementById("tel-available"), // Case à cocher si le téléphone n'est pas disponible
        mailInput: document.getElementById("mail-input"), // Champ pour l'adresse e-mail
        passwordInput: document.getElementById("mdp-inp"), // Champ pour le mot de passe
        countryInput: document.getElementById("country-born-input"), // Champ pour le pays de naissance
        cityInput: document.getElementById("city-input"), // Champ pour la ville
        professionInput: document.getElementById("profession-input"), // Champ pour la profession
        submitButton: document.getElementById("button_submit"), // Bouton de soumission du formulaire
        motTelephone: document.getElementById("bon_num"), // Message d'erreur pour le téléphone
        pMail: document.getElementById("p_mail"), // Message d'erreur pour l'email
        formElement: document.getElementById("forme_id_anniv"), // Indicateur pour la validation de l'anniversaire
    };

    // Désactive le bouton de soumission par défaut pour éviter une soumission prématurée.
    elements.submitButton.disabled = true;

    /**
     * Valide la date d'anniversaire
     * Vérifie que la date est au format "DD/MM/YYYY" et que l'utilisateur a au moins 18 ans.
     */
    function validateAnniversaire() {
        const anniversaireValue = elements.anniversaireInput.value;
        const regex = /\b(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/\d{4}\b/;

        if (regex.test(anniversaireValue)) {
            const [day, month, year] = anniversaireValue.split('/').map(Number);
            const date = new Date(year, month - 1, day);
            const today = new Date();
            const adultDate = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());

            // Vérifie que la date est bien formatée et qu'elle correspond à un adulte.
            if (date <= adultDate && date.getDate() === day && date.getMonth() === month - 1 && date.getFullYear() === year) {
                elements.formElement.style.color = "green"; // Indique une validation réussie
                return true;
            }
        }

        elements.formElement.style.color = "red"; // Indique une erreur
        return false;
    }

    /**
     * Valide le numéro de téléphone
     * Vérifie que le numéro est au format français valide (commence par 06 ou 07).
     */
    function validatePhone() {
        const regexTelephone = /\b(06[0-9]{8}|07[0-9]{8})\b/;
        const estValide = regexTelephone.test(elements.phoneInput.value);

        // Affiche un message d'erreur si le téléphone est invalide.
        elements.motTelephone.style.display = estValide || elements.phoneInput.value.length === 0 ? "none" : "block";
        return estValide || elements.telAvailable.checked; // Si "téléphone indisponible" est coché, le champ est ignoré.
    }

    /**
     * Valide l'adresse e-mail
     * Vérifie que l'email est bien formaté et qu'il ne provient pas de l'université de Lille.
     */
    function validateMail() {
        if (elements.mailInput.value.length === 0) {
            elements.pMail.innerText = ""; // Pas de message si le champ est vide
            return false;
        }

        if (elements.mailInput.value.includes("@univ-lille.fr")) {
            elements.pMail.innerText = "Ne doit pas être une adresse université de Lille"; // Adresse interdite
            return false;
        }

        if (!elements.mailInput.value.includes("@")) {
            elements.pMail.innerText = "Ceci n'est pas une adresse valide"; // Email invalide
            return false;
        }

        elements.pMail.innerText = ""; // Email valide
        return true;
    }

    /**
     * Valide le mot de passe
     * Vérifie que le mot de passe respecte plusieurs critères :
     * - Au moins 8 caractères
     * - Contient au moins un chiffre
     * - Contient des lettres majuscules et minuscules
     * - Contient au moins un caractère spécial (+, -, !, _, @).
     */
    function validatePassword() {
        const rules = {
            rule_1: elements.passwordInput.value.length >= 8,
            rule_2: /\d/.test(elements.passwordInput.value),
            rule_3: /(?=.*[a-z])(?=.*[A-Z])/.test(elements.passwordInput.value),
            rule_4: /[+\-!_@]/.test(elements.passwordInput.value),
        };

        let isValid = true;
        Object.entries(rules).forEach(([rule, condition]) => {
            const element = document.getElementById(rule);
            element.style.color = condition ? "green" : "red"; // Affiche l'état de chaque règle
            if (!condition) isValid = false;
        });

        return isValid;
    }

    /**
     * Valide l'ensemble du formulaire
     * Active ou désactive le bouton de soumission en fonction des résultats des validations.
     */
    function validateForm() {
        const valid_mail = validateMail();
        const valid_pass = validatePassword();
        const valid_phone = validatePhone();
        const valid_anniv = validateAnniversaire();

        // Vérifie que tous les champs requis sont remplis et valides.
        const isValid = elements.civiliteChoice.value !== "" &&
            valid_mail &&
            valid_pass &&
            valid_phone &&
            valid_anniv &&
            (elements.sectionInput.value !== "Autre" || elements.inputFiliere.value.trim() !== "") &&
            elements.countryInput.value.trim() !== "" &&
            elements.cityInput.value.trim() !== "" &&
            elements.professionInput.value.trim() !== "";

        elements.submitButton.disabled = !isValid; // Active ou désactive le bouton
    }

    /**
     * Attache les écouteurs d'événements aux éléments du formulaire
     * Regroupe tous les événements pour éviter la répétition du code.
     */
    function attachEventListeners() {
        elements.anniversaireInput.addEventListener('input', validateForm);
        elements.phoneInput.addEventListener('input', validateForm);
        elements.telAvailable.addEventListener('change', () => {
            elements.phoneInput.disabled = elements.telAvailable.checked; // Désactive le champ téléphone si la case est cochée
            validateForm();
        });
        elements.mailInput.addEventListener('input', validateForm);
        elements.passwordInput.addEventListener('input', validateForm);
        elements.civiliteChoice.addEventListener('change', validateForm);
        elements.prenomInput.addEventListener('input', validateForm);
        elements.nomInput.addEventListener('input', validateForm);
        elements.sectionInput.addEventListener('change', () => {
            elements.inputFiliere.disabled = elements.sectionInput.value !== "Autre"; // Active ou désactive le champ filière
            validateForm();
        });
        elements.inputFiliere.addEventListener('input', validateForm);
        elements.countryInput.addEventListener('input', validateForm);
        elements.cityInput.addEventListener('input', validateForm);
        elements.professionInput.addEventListener('input', validateForm);
    }

    /**
     * Réinitialise les champs du formulaire lors du rechargement de la page
     */
    window.addEventListener('beforeunload', () => {
        Object.keys(elements).forEach((key) => {
            const element = elements[key];
            if (element instanceof HTMLInputElement) element.value = ""; // Vide les champs texte
            if (element instanceof HTMLSelectElement) element.selectedIndex = 0; // Réinitialise les listes déroulantes
            if (key === "inputFiliere") element.disabled = true; // Désactive le champ filière
            if (key === "telAvailable") element.checked = false; // Décoche la case téléphone indisponible
        });
    });

    // Attache les écouteurs d'événements après le chargement du DOM
    attachEventListeners();
});
