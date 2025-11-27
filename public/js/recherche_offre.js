document.addEventListener("DOMContentLoaded", () => {
    const formElements = {
        form: document.getElementById("formulaire-offre"),
        submitBtn: document.getElementById("button_submit"),

        // Messages
        form_code: document.querySelector(".form_code"),

        // Résultats
        resultsDiv: document.getElementById("resultats")
    };

    /**
     * =======================================================
     * Validation champ "Département" et récupération localisation
     * =======================================================
     */
    /**
     * =======================================================
     * Gestion de la soumission du formulaire (recherche offres)
     * =======================================================
     */
    formElements.form.addEventListener("submit", e => {
        e.preventDefault();

        const params = new URLSearchParams(window.location.search);
        const data_form = new FormData(formElements.form);
        const user_id = params.get("id_user");

        const url = `/?dest=reche_emploie&id_user=${encodeURIComponent(user_id)}`;

        fetch(url, {
            method: "POST",
            body: data_form
        })
            .then(response => {
                if (!response.ok) throw new Error(`Erreur serveur : ${response.status}`);
                return response.json();
            })
            .then(data => {
                formElements.resultsDiv.innerHTML = "";

                if (!data.status || data.count === 0) {
                    formElements.resultsDiv.innerHTML = "<p>Aucune offre trouvée.</p>";
                    return;
                }

                data.jobs.forEach(job => {
                    const card = document.createElement("div");
                    card.className = "job-card";

                    card.innerHTML = `
                        <h3>${job.titre_offre}</h3>
                        <p><strong>Type de contrat :</strong> ${job.type_contrat}</p>
                        <p><strong>Spécialités :</strong> ${job.specialites || "-"}</p>
                        <p><strong>Contact :</strong> <a href="mailto:${job.email_user}">${job.email_user}</a></p>
                        <p>${job.description || ""}</p>
                        ${job.url_linkedin ? `<p><a href="${job.url_linkedin}" target="_blank">Voir l'offre sur LinkedIn</a></p>` : ""}
                    `;

                    formElements.resultsDiv.appendChild(card);
                });
            })
            .catch(err => {
                formElements.resultsDiv.innerHTML = `<p style="color:red;">❌ ${err.message}</p>`;
            });

            resetFormulaire();
    });

    /**
     * =======================================================
     * Réinitialisation formulaire
     * =======================================================
     */
    function resetFormulaire() {
        formElements.form.reset();
        formElements.resultsDiv.innerHTML = "";
        formElements.submitBtn.disabled = false;
    }

    // Reset auto avant de quitter la page
    window.addEventListener("beforeunload", resetFormulaire);


    // État initial
    resetFormulaire();
});
