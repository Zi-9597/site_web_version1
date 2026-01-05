document.addEventListener("DOMContentLoaded", () => {

    /* =====================================================
       🔗 RÉFÉRENCES DOM
    ===================================================== */
    const form        = document.querySelector(".aide-form");
    const submitBtn  = form.querySelector("button[type='submit']");

    const nomInput    = document.getElementById("nom");
    const prenomInput = document.getElementById("prenom");
    const emailInput  = document.getElementById("email");
    const telInput    = document.getElementById("telephone");
    const typeInput   = document.getElementById("type_aide");
    const sujetInput  = document.getElementById("sujet");
    const msgInput    = document.getElementById("message");

    const counter   = document.querySelector(".char-counter");
    const emailInfo = document.querySelector(".email-info");

    /* =====================================================
       🔢 COMPTEUR DE CARACTÈRES (MESSAGE)
    ===================================================== */
    msgInput.addEventListener("input", () => {
        counter.textContent = `${msgInput.value.length} / 2500 caractères`;
        validateForm();
    });

    /* =====================================================
       ✉️ VALIDATION EMAIL
       - Refuse @univ-lille.fr
       - Message devient rouge si interdit
    ===================================================== */
    function isEmailValid() {
        const email = emailInput.value.trim().toLowerCase();
        const forbidden = email.endsWith("@univ-lille.fr");

        emailInfo.style.color = forbidden ? "red" : "#444";
        return !forbidden;
    }

    emailInput.addEventListener("input", validateForm);

    /* =====================================================
       ✅ VALIDATION GLOBALE DU FORMULAIRE
       - Active / désactive le bouton
    ===================================================== */
    function validateForm() {
        const isValid =
            nomInput.value.trim() !== "" &&
            prenomInput.value.trim() !== "" &&
            emailInput.value.trim() !== "" &&
            typeInput.value !== "" &&
            sujetInput.value.trim() !== "" &&
            msgInput.value.trim() !== "" &&
            isEmailValid();

        submitBtn.disabled = !isValid;
    }

    form.addEventListener("input", validateForm);

    /* =====================================================
       🔔 NOTIFICATIONS (cartes HTML existantes)
    ===================================================== */
    function showNotif(type) {
        const card = document.getElementById(
            type === "success" ? "notif-success" : "notif-error"
        );

        if (!card) return;

        card.classList.remove("show");
        setTimeout(() => card.classList.add("show"), 10);

        setTimeout(() => {
            card.classList.remove("show");
        }, 3500);
    }

    /* =====================================================
       ♻️ RESET COMPLET DU FORMULAIRE
    ===================================================== */
    function resetFormulaire() {
        form.reset();
        counter.textContent = "0 / 2500 caractères";
        submitBtn.disabled = true;
        emailInfo.style.color = "#444";
    }

    /* =====================================================
       🚀 ENVOI AJAX DU FORMULAIRE
       - Avec ou sans id_user (URL)
    ===================================================== */
    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        if (submitBtn.disabled) return;

        const payload = {
            nom: nomInput.value.trim(),
            prenom: prenomInput.value.trim(),
            email: emailInput.value.trim(),
            telephone: telInput.value.trim(),
            type_aide_id: typeInput.value,
            sujet: sujetInput.value.trim(),
            message: msgInput.value.trim()
        };

        // id_user optionnel
        const params = new URLSearchParams(window.location.search);
        const idUser = params.get("id_user");

        let url = "/?dest=add_aide";
        if (idUser) {
            url += `&id_user=${encodeURIComponent(idUser)}`;
        }

        try {
            const response = await fetch(url, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (result.success) {
                showNotif("success");
                resetFormulaire();
            } else {
                showNotif("error");
            }

        } catch {
            showNotif("error");
        }
    });

    /* =====================================================
       🔄 RESET AUTOMATIQUE AU RECHARGEMENT / RETOUR PAGE
    ===================================================== */
    window.addEventListener("pageshow", resetFormulaire);
});
