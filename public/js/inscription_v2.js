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
        mailInput: document.getElementById("mail-input"), // Champ pour l'adresse e-mail
        passwordInput: document.getElementById("mdp-inp"), // Champ pour le mot de passe
        countryInput: document.getElementById("country-born-input"), // Champ pour le pays de naissance
        cityInput: document.getElementById("city-input"), // Champ pour la ville
        professionInput: document.getElementById("profession-input"), // Champ pour la profession
        submitButton: document.getElementById("button_submit"), // Bouton de soumission du formulaire
        motTelephone: document.getElementById("bon_num"), // Message d'erreur pour le téléphone
        pMail: document.getElementById("p_mail"), // Message d'erreur pour l'email
        formElement: document.getElementById("forme_id_anniv"), // Indicateur pour la validation de l'anniversaire,
        form : document.getElementById("loginForm"),
        phoneHidden : document.getElementById("phone_e164")
    };

    // 📞 Initialisation du téléphone international
    const iti = window.intlTelInput(elements.phoneInput, {
        initialCountry: "fr",
        separateDialCode: true,
        preferredCountries: ["fr", "ma", "es", "be"],
        utilsScript:
        "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js"
    });

    // Désactive le bouton de soumission par défaut pour éviter une soumission prématurée.
    elements.submitButton.disabled = true;
    /**
     * 🎂 Validation anniversaire
     * - Format attendu : DD/MM/YYYY
     * - L'utilisateur doit avoir au moins 18 ans
     */
    function validateAnniversaire() 
    {

        // 1) On récupère la valeur (et on enlève les espaces)
        const value = elements.anniversaireInput.value.trim();

        // 2) Regex : jour 01-31 / mois 01-12 / année 4 chiffres
        const regex = /^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/\d{4}$/;

        // 3) Si le format n'est pas bon → rouge + false
        if (!regex.test(value)) {
            elements.formElement.style.color = "red";
            return false;
        }

        // 4) On découpe la date "DD/MM/YYYY"
        const [day, month, year] = value.split("/").map(Number);

        // 5) On construit la date (mois JS = 0-11)
        const birthDate = new Date(year, month - 1, day);

        // 6) On vérifie que la date existe vraiment (évite 31/02)
        const isRealDate =
            birthDate.getDate() === day &&
            birthDate.getMonth() === month - 1 &&
            birthDate.getFullYear() === year;

        if (!isRealDate) {
            elements.formElement.style.color = "red";
            return false;
        }

        // 7) Limite adulte : aujourd'hui - 18 ans
        const today = new Date();
        const adultLimit = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());

        // 8) OK si la date de naissance est <= limite adulte
        const isValid = birthDate <= adultLimit;

        // 9) Feedback visuel (ternaire simple)
        elements.formElement.style.color = isValid ? "green" : "red";

        return isValid;
    }

    /**
     * 📞 Validation du numéro de téléphone (international)
     * - Validation réelle via intl-tel-input
     * - Compatible tous pays
     */
    function validatePhone() {

        const value = elements.phoneInput.value.trim();
        // Champ vide → invalide (ou adapte si facultatif)
        

        // Validation via la lib
        const isValid = iti.isValidNumber();
        // Message d’erreur
        if (isValid) {
            elements.motTelephone.style.display = "none";
        }
        else
        {
            //Deux cas possible que le numéro est faux soit l'input est vide soit le numéro n'est pas respecté
            //On veut afficher ssi le numéro n'est pas respecté
            
            elements.motTelephone.style.display = value.length === 0 ? "none" : "block";
            
        }
        // Feedback Bootstrap (optionnel mais propre)
        elements.phoneInput.classList.toggle("is-invalid", !isValid);

        return isValid;
    }


   /**
 * 📧 Validation de l'adresse e-mail
 * - Format e-mail valide
 * - Interdit : domaine @univ-lille.fr
 */
    async function validateMail() 
    {

        // 1) Récupération et nettoyage de la valeur
        const email = elements.mailInput.value.trim();

        // 2) Champ vide → pas de message, invalide
        if (email.length === 0) {
            elements.pMail.innerText = "";
            return false;
        }

        // 3) Regex e-mail standard
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        // 4) Test du format
        if (!emailRegex.test(email)) {
            elements.pMail.innerText = "Adresse e-mail invalide";
            return false;
        }

        // 5) Domaine interdit (Université de Lille)
        const forbiddenDomain = /@univ-lille\.fr$/i;

        if (forbiddenDomain.test(email)) {
            elements.pMail.innerText = "Adresse Université de Lille interdite";
            return false;
        }
         // 5) Vérification en base de données (AJAX)
        try 
        {
            const response = await fetch("/?dest=check_mail", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ email: email })
            });

            const data = await response.json();
            console.log(data.exists)
            if (data.exists) {
                elements.pMail.innerText = "Ce compte existe déjà !";
                return false;
            }

        } 
        catch (error) {
            console.error("Erreur AJAX :", error);
            elements.pMail.innerText = "Erreur de vérification du mail";
            return false;
        }

        // 6) Email valide
        elements.pMail.innerText = "";
        return true;
    }


    /**
     * 🎓 Gestion du type de membre (Étudiant / Alumni)
     * - Étudiant/e  → profession auto = "Étudiant/e" + champ désactivé
     * - Alumni/e    → profession vidée + champ réactivé
     */
    function handleMembreChange() {

        const isEtudiant = elements.membreInput.value === "Étudiant/e";

        // Si Étudiant → valeur forcée + champ bloqué
        if (isEtudiant) 
        {
            elements.professionInput.value = "Étudiant/e";
            elements.professionInput.disabled = true;
        } 
        // Sinon (Alumni) → champ libre
        else 
        {
            elements.professionInput.value = "";
            elements.professionInput.disabled = false;
        }
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
    async function validateForm() {
        const valid_mail = await validateMail();
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
        elements.mailInput.addEventListener('blur', validateForm);
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
        elements.membreInput.addEventListener("change", () => {
            handleMembreChange();
            validateForm(); // on revalide le formulaire
        });
    }

    elements.form.addEventListener("submit", (e) => 
    {
        // si téléphone invalide → on bloque
        if (!validatePhone()) {
            e.preventDefault();
            return;
        }
        // on prépare la donnée AVANT l'envoi POST

        //Variable interlTelInputUtils deouis la bilbiothèque js <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js"></script> l.36 du fihcier inscrription.php
        elements.phoneHidden.value = iti.getNumber(
            intlTelInputUtils.numberFormat.E164
        );
    });
    /**
     * Réinitialise les champs du formulaire lors du rechargement de la page
     */
    window.addEventListener('pageshow', () => {
        Object.keys(elements).forEach((key) => {
            const element = elements[key];
            if (element instanceof HTMLInputElement) element.value = ""; // Vide les champs texte
            if (element instanceof HTMLSelectElement) element.selectedIndex = 0; // Réinitialise les listes déroulantes
            if (key === "inputFiliere") element.disabled = true; // Désactive le champ filière
        });
    });

    // Attache les écouteurs d'événements après le chargement du DOM
    attachEventListeners();
});
