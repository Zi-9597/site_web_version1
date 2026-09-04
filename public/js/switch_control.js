document.addEventListener("DOMContentLoaded", () => {

    // ----------------------------------------------------
    // 1) RÉCUPÉRATION DES INPUTS
    // ----------------------------------------------------
    const fields = {
        email: document.getElementById("mail-input"),
        password: document.getElementById("mdp-inp"),
        section: document.getElementById("filiere-section"),
        autreFiliere: document.getElementById("autre-filiere"),
        phone: document.getElementById("phone"),
        city: document.getElementById("city-input"),
        profession: document.getElementById("profession-input"),
        pMail: document.getElementById("p_mail"),
        pikachu : document.getElementById("pikachu")
    };

    // ----------------------------------------------------
    // 2) RÉCUPÉRATION DES BOUTONS
    // ----------------------------------------------------
    const buttons = {
        email: document.querySelector('button[data-target="mail-input"]'),
        password: document.querySelector('button[data-target="mdp-inp"]'),
        section: document.querySelector('button[data-target="filiere-section"]'),
        phone: document.querySelector('button[data-target="phone"]'),
        city: document.querySelector('button[data-target="city-input"]'),
        profession: document.querySelector('button[data-target="profession-input"]')
    };

    // Tous désactivés au début
    Object.values(buttons).forEach(btn => btn.disabled = true);

    // ----------------------------------------------------
    // 3) VALIDATIONS
    // ----------------------------------------------------

    // Email
    function validateEmail() {
    const mail = fields.email.value.trim();
    let disable = true;

        // 1️⃣ Champ vide
        if (mail.length === 0) 
        {
            fields.pMail.innerText = "";
            buttons.email.disabled = true;
            return;
        }

        // 2️⃣ Domaine interdit
        const forbiddenDomain = /@univ-lille\.fr$/i;
        if (forbiddenDomain.test(mail)) 
        {
            fields.pMail.innerText = "Adresse Université de Lille interdite";
            buttons.email.disabled = true;
            return;
        }

        // 3️⃣ Format email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
        if (!emailRegex.test(mail)) 
        {
            fields.pMail.innerText = "Adresse e-mail invalide";
            buttons.email.disabled = true;
            return;
        }

        // 4️⃣ Email valide
        fields.pMail.innerText = "";
        buttons.email.disabled = false;
    }



    // CHANGE: a profile password may use any characters; only an empty value is rejected.
    function validatePassword() {
        const pwd = fields.password.value;
        const ok = pwd.length > 0;
        buttons.password.disabled = !ok;
    }

    // Téléphone
    function validatePhone() {
        const phone = fields.phone.value.trim();
        const ok = /^0[67][0-9]{8}$/.test(phone);
        buttons.phone.disabled = !ok;
    }

    // Champ non vide
    function validateNotEmpty(field, button) {
        const ok = field.value.trim().length > 0;
        button.disabled = !ok;
    }

    // Section + autre filière
    function validateSection() {
        if (fields.section.value === "Autre") {
            validateNotEmpty(fields.autreFiliere, buttons.section);
        } else {
            buttons.section.disabled = fields.section.value === "";
        }
    }

    // ----------------------------------------------------
    // 4) ATTACHER LES ÉVÉNEMENTS DE VALIDATION
    // ----------------------------------------------------
    fields.email.addEventListener("input", validateEmail);
    fields.password.addEventListener("input", validatePassword);
    fields.phone.addEventListener("input", validatePhone);
    fields.city.addEventListener("input", () => validateNotEmpty(fields.city, buttons.city));
    fields.profession.addEventListener("input", () => validateNotEmpty(fields.profession, buttons.profession));
    fields.section.addEventListener("change", validateSection);
    fields.autreFiliere.addEventListener("input", validateSection);

    // ----------------------------------------------------
    // 5) ACTION AU CLIC : LOG + SUPPRESSION + DÉSACTIVATION
    // ----------------------------------------------------
    Object.values(buttons).forEach(btn => {
        btn.addEventListener("click", async () => {

            const target = btn.dataset.target;
            const input  = document.getElementById(target);

            let newValue = input.value.trim();
            let pikachu = fields.pikachu.value.trim();

            // ----------------------------------------------------
            // CAS SPÉCIAL : SECTION = "Autre"
            // ➜ On envoie la valeur du champ texte
            // ----------------------------------------------------
            if (target === "filiere-section" && newValue === "Autre") {
                newValue = fields.autreFiliere.value.trim();
            }

            // Sécurité minimale
            if (newValue === "") 
            {
                btn.disabled = true;
                return;
            }
             // --- 1) Requête AJAX vers PHP ---
            try 
            {
                const res = await fetch(`/?dest=update_data`, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        field: target,
                        value: newValue,
                        pikachu_csrf : pikachu
                    })
                });

                const data = await res.json();
                //console.log("Réponse PHP :", data);

                // ❌ --- CAS ERREUR PHP ---
                if (!data.success) {
                    btn.innerText = "Erreur";
                    btn.style.backgroundColor = "#d63031";
                    btn.style.color = "#fff";

                    setTimeout(() => {
                        btn.innerText = "Modifier";
                        btn.style.backgroundColor = "";
                        btn.style.color = "";
                        btn.disabled = false;
                    }, 3000);

                    return;
                }
                 // ✅ --- CAS SUCCÈS (ne s’exécute QUE si data.success === true) ---
                btn.innerText = "Chargement ...";
                btn.style.backgroundColor = "#27ae60"; // vert
                btn.style.color = "#fff";

              

                setTimeout(() => {
                    location.reload(); // 🔁 Reload automatique
                }, 2000);

            } catch (error) {
                // ❌ --- ERREUR RÉSEAU ---
                //console.error("Erreur AJAX :", error);

                btn.innerText = "Erreur réseau ❌";
                btn.style.backgroundColor = "#d63031";
                btn.style.color = "#fff";

                setTimeout(() => {
                    btn.innerText = "Modifier";
                    btn.style.backgroundColor = "";
                    btn.style.color = "";
                    btn.disabled = false;
                }, 3000);

                return;
            }

            // --- 3) Nettoyage du champ + désactivation ---
            input.value = "";
            btn.disabled = true;

            // --- 4) Re-validation automatique ---
            switch (target) {
                case "mail-input": validateEmail(); break;
                case "mdp-inp": validatePassword(); break;
                case "phone": validatePhone(); break;
                case "city-input": validateNotEmpty(fields.city, buttons.city); break;
                case "profession-input": validateNotEmpty(fields.profession, buttons.profession); break;
                case "filiere-section": validateSection(); break;
            }
        });
    });
    /**
     * ----------------------------------------------------------------------
     * RÉINITIALISATION DU FORMULAIRE AVANT RECHARGEMENT
     * ----------------------------------------------------------------------
     * Si l’utilisateur recharge la page, on remet tout à zéro proprement.
     */
    window.addEventListener('pageshow', () => {
        Object.keys(fields).forEach((key) => {

            if (key !== "pikachu")
            {
                 const element = fields[key];

                if (element instanceof HTMLInputElement) element.value = "";
                

                if (key === "inputFiliere") element.disabled = true;
            }
           
        });
    });

});
