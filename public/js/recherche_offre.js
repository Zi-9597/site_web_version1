document.addEventListener("DOMContentLoaded", () => {
    const formElements = {
        form: document.getElementById("formulaire-offre"),
        submitBtn: document.getElementById("button_submit"),

        // Champs localisation
        departement: document.getElementById("departement"),
        commune: document.getElementById("commune"),
        departement_nom: document.getElementById("departement_nom"),
        region: document.getElementById("region"),

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
    function validateDepartement() {
        const { departement, form_code, commune, departement_nom, region } = formElements;
        const regex_dep = /^[0-9]{5}$/;
        const value_dep = departement.value.trim();

        if (value_dep !== "" && !regex_dep.test(value_dep)) {
            form_code.style.display = "block";
            commune.value = "";
            departement_nom.value = "";
            region.value = "";
            return false;
        }

        form_code.style.display = "none";

        if (regex_dep.test(value_dep)) {
            fetch("/?dest=cp_fetch", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ code_postal: value_dep })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        commune.value = data.commune;
                        departement_nom.value = data.departement;
                        region.value = data.region;
                    } else {
                        commune.value = "";
                        departement_nom.value = "";
                        region.value = "";
                    }
                })
                .catch(() => {
                    commune.value = "";
                    departement_nom.value = "";
                    region.value = "";
                });
        }

        return true;
    }

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
                        <p><strong>Date :</strong> ${job.date_creation}</p>
                        <p><strong>Localisation :</strong> ${job.nom_commune || "-"}, ${job.nom_departement || "-"}, ${job.nom_region || "-"}</p>
                        <p><strong>Code postal :</strong> ${job.departement || "-"}</p>
                        <p><strong>Spécialités :</strong> ${job.specialites || "-"}</p>
                        <p><strong>Contact :</strong> <a href="mailto:${job.email_user}">${job.email_user}</a></p>
                        <p>${job.description || ""}</p>
                        ${job.url_linkedin ? `<p><a href="${job.url_linkedin}" target="_blank">Voir l'offre sur LinkedIn</a></p>` : ""}
                    `;

                    formElements.resultsDiv.appendChild(card);
                });
            })
            .catch(err => {
                console.log("farakh");
                formElements.resultsDiv.innerHTML = `<p style="color:red;">❌ ${err.message}</p>`;
            });
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

    // Validation champ code postal
    formElements.departement.addEventListener("input", validateDepartement);

    // État initial
    resetFormulaire();
});
