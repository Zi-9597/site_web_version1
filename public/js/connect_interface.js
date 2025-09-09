document.addEventListener('DOMContentLoaded', () => {
    /**
     * Récupération des éléments nécessaires du DOM
     * Chaque élément représente un champ ou un composant du formulaire.
     */
    const elements = {
        mailInput: document.getElementById("email-fill"), // Champ pour l'adresse e-mail
        passwordInput: document.getElementById("mdp-fill"), // Champ pour le mot de passe
        submitButton: document.getElementById("button_submit"), // Bouton de soumission du formulaire
    };

    // Désactive le bouton de soumission par défaut pour éviter une soumission prématurée.
    elements.submitButton.disabled = true;

    /**
     * Valide l'adresse e-mail
     */
    function validateMail() {
        if (elements.mailInput.value.length === 0) return false;
        if (elements.mailInput.value.includes("@univ-lille.fr")) return false;
        if (!elements.mailInput.value.includes("@")) return false;
        return true;
    }

    /**
     * Valide l'ensemble du formulaire
     */
    function validateForm() {
        const valid_mail = validateMail();
        const valid_pass = elements.passwordInput.value !== "";
        const isValid = valid_mail && valid_pass;
        elements.submitButton.disabled = !isValid;
    }

    /**
     * Attache les écouteurs d'événements
     */
    function attachEventListeners() {
        elements.mailInput.addEventListener('input', validateForm);
        elements.passwordInput.addEventListener('input', validateForm);
    }

    /**
     * Réinitialise les champs du formulaire
     */
    function resetForm() {
        elements.mailInput.value = "";
        elements.passwordInput.value = "";
        elements.submitButton.disabled = true;
    }

    // Réinitialiser au chargement normal de la page
    resetForm();

    // Réinitialiser aussi quand on revient avec le bouton "Retour" du navigateur
    window.addEventListener("pageshow", (event) => {
        if (event.persisted) {
            resetForm();
        }
    });

    // Attache les écouteurs d'événements après le chargement du DOM
    attachEventListeners();
});
document.addEventListener('DOMContentLoaded', () => {
    /**
     * Récupération des éléments nécessaires du DOM
     * Chaque élément représente un champ ou un composant du formulaire.
     */
    const elements = {
        mailInput: document.getElementById("email-fill"), // Champ pour l'adresse e-mail
        passwordInput: document.getElementById("mdp-fill"), // Champ pour le mot de passe
        submitButton: document.getElementById("button_submit"), // Bouton de soumission du formulaire
    };

    // Désactive le bouton de soumission par défaut pour éviter une soumission prématurée.
    elements.submitButton.disabled = true;

    /**
     * Valide l'adresse e-mail
     */
    function validateMail() {
        if (elements.mailInput.value.length === 0) return false;
        if (elements.mailInput.value.includes("@univ-lille.fr")) return false;
        if (!elements.mailInput.value.includes("@")) return false;
        return true;
    }

    /**
     * Valide l'ensemble du formulaire
     */
    function validateForm() {
        const valid_mail = validateMail();
        const valid_pass = elements.passwordInput.value !== "";
        const isValid = valid_mail && valid_pass;
        elements.submitButton.disabled = !isValid;
    }

    /**
     * Attache les écouteurs d'événements
     */
    function attachEventListeners() {
        elements.mailInput.addEventListener('input', validateForm);
        elements.passwordInput.addEventListener('input', validateForm);
    }

    /**
     * Réinitialise les champs du formulaire
     */
    function resetForm() {
        elements.mailInput.value = "";
        elements.passwordInput.value = "";
        elements.submitButton.disabled = true;
    }

    // Réinitialiser au chargement normal de la page
    resetForm();

    // Réinitialiser aussi quand on revient avec le bouton "Retour" du navigateur
    window.addEventListener("pageshow", (event) => {
        if (event.persisted) {
            resetForm();
        }
    });

    // Attache les écouteurs d'événements après le chargement du DOM
    attachEventListeners();
});
