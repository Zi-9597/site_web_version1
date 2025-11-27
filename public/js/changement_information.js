document.addEventListener('DOMContentLoaded', () => {

    /**
     * ----------------------------------------------------------------------
     * RÉCUPÉRATION DES ÉLÉMENTS DU FORMULAIRE
     * ----------------------------------------------------------------------
     * On regroupe ici tous les éléments HTML nécessaires dans un seul objet.
     * Cela rend le code plus propre et évite d'appeler plusieurs fois
     * document.getElementById() dans le code.
     */
    const elements = {
        phoneInput: document.getElementById("phone"),                  // Champ numéro de téléphone
        membreInput: document.getElementById("membre-assoc"),         // Sélection du statut de membre
        sectionInput: document.getElementById("filiere-section"),     // Liste des sections (EEA, Master…)
        inputFiliere: document.getElementById("autre-filiere"),       // Champ filière personnalisée
        mailInput: document.getElementById("mail-input"),             // Champ email
        passwordInput: document.getElementById("mdp-inp"),            // Champ mot de passe
        cityInput: document.getElementById("city-input"),             // Champ ville
        professionInput: document.getElementById("profession-input"), // Champ profession
        motTelephone: document.getElementById("bon_num"),             // Message d’erreur téléphone
        pMail: document.getElementById("p_mail"),                     // Message d’erreur email
    };

    // On désactive le bouton de validation au chargement de la page

    /**
     * ----------------------------------------------------------------------
     * VALIDATION DU NUMÉRO DE TÉLÉPHONE
     * ----------------------------------------------------------------------
     * Le numéro doit respecter le format français : commence par 06 ou 07
     * suivi de 8 chiffres.
     * Si la checkbox "pas de numéro français" est cochée, on ignore ce champ.
     */
    function validatePhone() {
        const regexTelephone = /\b(06[0-9]{8}|07[0-9]{8})\b/;
        const estValide = regexTelephone.test(elements.phoneInput.value);

        // Affiche une erreur uniquement si le numéro est invalide ET non vide
        elements.motTelephone.style.display =
            estValide || elements.phoneInput.value.length === 0
                ? "none"
                : "block";

        // Si "pas de numéro français" est coché → on considère comme valide
        return estValide;
    }


    /**
     * ----------------------------------------------------------------------
     * VALIDATION DE L’E-MAIL
     * ----------------------------------------------------------------------
     * On vérifie :
     * - que le champ n’est pas vide
     * - que l’adresse n’est pas une adresse @univ-lille.fr
     * - qu’elle contient bien un '@'
     */
    function validateMail() {
        const mail = elements.mailInput.value;

        if (mail.length === 0) {
            elements.pMail.innerText = "";
            return false;
        }

        if (mail.includes("@univ-lille.fr")) {
            elements.pMail.innerText = "Ne doit pas être une adresse université de Lille";
            return false;
        }

        if (!mail.includes("@")) {
            elements.pMail.innerText = "Ceci n'est pas une adresse valide";
            return false;
        }

        elements.pMail.innerText = "";
        return true;
    }


    /**
     * ----------------------------------------------------------------------
     * VALIDATION DU MOT DE PASSE
     * ----------------------------------------------------------------------
     * Le mot de passe doit contenir :
     * - 8 caractères minimum
     * - au moins un chiffre
     * - au moins une minuscule ET une majuscule
     * - un caractère spécial (+, -, !, _, @)
     * Chaque règle est associée à un <li> coloré en rouge/vert.
     */
    function validatePassword() {
        const pwd = elements.passwordInput.value;

        // Conditions requises
        const rules = {
            rule_1: pwd.length >= 8,
            rule_2: /\d/.test(pwd),
            rule_3: /(?=.*[a-z])(?=.*[A-Z])/.test(pwd),
            rule_4: /[+\-!_@]/.test(pwd),
        };

        // Mise à jour visuelle des règles
        let isValid = true;
        Object.entries(rules).forEach(([rule, ok]) => {
            const element = document.getElementById(rule);
            element.style.color = ok ? "green" : "red";
            if (!ok) isValid = false;
        });

        return isValid;
    }


    /**
     * ----------------------------------------------------------------------
     * VALIDATION GÉNÉRALE DU FORMULAIRE
     * ----------------------------------------------------------------------
     * Elle combine toutes les validations :
     * - email
     * - mot de passe
     * - téléphone
     * - section EEA
     * - ville & profession
     */
    function validateForm() {
        const valid_mail = validateMail();
        const valid_pass = validatePassword();
        const valid_phone = validatePhone();

        // Vérifie si tous les champs obligatoires sont OK
        const isValid =
            valid_mail &&
            valid_pass &&
            valid_phone &&
            (elements.sectionInput.value !== "Autre" ||
                elements.inputFiliere.value.trim() !== "") &&
            elements.cityInput.value.trim() !== "" &&
            elements.professionInput.value.trim() !== "";

        // Active/désactive le bouton de validation

    }


    /**
     * ----------------------------------------------------------------------
     * GESTION DES ÉVÉNEMENTS
     * ----------------------------------------------------------------------
     * Chaque champ écoute un événement et déclenche validateForm().
     * Cela permet une vérification en direct.
     */
    function attachEventListeners() {

        // Téléphone
        elements.phoneInput.addEventListener('input', validateForm);

      

        // Email
        elements.mailInput.addEventListener('input', validateForm);

        // Mot de passe
        elements.passwordInput.addEventListener('input', validateForm);

        // Section / filière
        elements.sectionInput.addEventListener('change', () => {
            elements.inputFiliere.disabled =
                elements.sectionInput.value !== "Autre";
            validateForm();
        });

        elements.inputFiliere.addEventListener('input', validateForm);

        // Ville & Profession
        elements.cityInput.addEventListener('input', validateForm);
        elements.professionInput.addEventListener('input', validateForm);
    }


    /**
     * ----------------------------------------------------------------------
     * RÉINITIALISATION DU FORMULAIRE AVANT RECHARGEMENT
     * ----------------------------------------------------------------------
     * Si l’utilisateur recharge la page, on remet tout à zéro proprement.
     */
    window.addEventListener('beforeunload', () => {
        Object.keys(elements).forEach((key) => {
            const element = elements[key];

            if (element instanceof HTMLInputElement) element.value = "";
            if (element instanceof HTMLSelectElement) element.selectedIndex = 0;

            if (key === "inputFiliere") element.disabled = true;
        });
    });

    // Activation initiale des écouteurs d’événements
    attachEventListeners();
});
