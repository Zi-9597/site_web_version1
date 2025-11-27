document.addEventListener("DOMContentLoaded", () => {

    // ----------------------------------------------------
    // 1) RÉCUPÉRATION DES INPUTS
    // ----------------------------------------------------
    const fields = {
        email: document.getElementById("mail-input"),
        password: document.getElementById("mdp-inp"),
        membre: document.getElementById("membre-assoc"),
        section: document.getElementById("filiere-section"),
        autreFiliere: document.getElementById("autre-filiere"),
        phone: document.getElementById("phone"),
        city: document.getElementById("city-input"),
        profession: document.getElementById("profession-input")
    };

    // ----------------------------------------------------
    // 2) RÉCUPÉRATION DES BOUTONS
    // ----------------------------------------------------
    const buttons = {
        email: document.querySelector('button[data-target="mail-input"]'),
        password: document.querySelector('button[data-target="mdp-inp"]'),
        membre: document.querySelector('button[data-target="membre-assoc"]'),
        section: document.querySelector('button[data-target="filiere-section"]'),
        phone: document.querySelector('button[data-target="phone"]'),
        city: document.querySelector('button[data-target="city-input"]'),
        profession: document.querySelector('button[data-target="profession-input"]')
    };










    const params_url = new URLSearchParams(window.location.search);
    const id_web = params_url.get("id_user");
    // Tous désactivés au début
    Object.values(buttons).forEach(btn => btn.disabled = true);

    // ----------------------------------------------------
    // 3) VALIDATIONS
    // ----------------------------------------------------

    // Email
    function validateEmail() {
        const mail = fields.email.value.trim();
        const ok = mail.includes("@") && !mail.includes("@univ-lille.fr");
        buttons.email.disabled = !ok;
    }

    // Mot de passe
    function validatePassword() {
        const pwd = fields.password.value;
        const ok =
            pwd.length >= 8 &&
            /\d/.test(pwd) &&
            /[a-z]/.test(pwd) &&
            /[A-Z]/.test(pwd) &&
            /[+\-!_@]/.test(pwd);
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
    fields.membre.addEventListener("change", () => validateNotEmpty(fields.membre, buttons.membre));
    fields.section.addEventListener("change", validateSection);
    fields.autreFiliere.addEventListener("input", validateSection);

    // ----------------------------------------------------
    // 5) ACTION AU CLIC : LOG + SUPPRESSION + DÉSACTIVATION
    // ----------------------------------------------------
    Object.values(buttons).forEach(btn => {
        btn.addEventListener("click", async () => {

            // ID du champ
            const target = btn.dataset.target;

            // Input concerné
            const input = document.getElementById(target);

            // Valeur à envoyer
            const newValue = input.value.trim();

            // ID utilisateur récupéré dans l’URL
            const params_url = new URLSearchParams(window.location.search);
            const id_web = params_url.get("id_user");
            const [id_member] = id_web.split("_");  // vrai ID utilisateur

            // console.log("Changement demandé :", {
            //     id_user: id_member,
            //     field: target,
            //     value: newValue
            // });
             // --- 1) Requête AJAX vers PHP ---
            try {
                const res = await fetch(`/?dest=update_data&id_user=${encodeURIComponent(id_web)}`, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        id_user: id_member,
                        field: target,
                        value: newValue
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
                btn.innerText = "Valider";
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
                case "membre-assoc": validateNotEmpty(fields.membre, buttons.membre); break;
                case "filiere-section": validateSection(); break;
            }
        });
    });
});
