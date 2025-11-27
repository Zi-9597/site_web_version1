document.addEventListener("DOMContentLoaded", () => {

    const resultsDiv = document.getElementById("resultats");


    const params = new URLSearchParams(window.location.search);
    
    const user_id = params.get("id_user");

    const url =  `/?dest=reche_emploie&id_user=${encodeURIComponent(user_id)}`;

   
    // (⚠️ adapte si ton routing est différent)

    // ✅ On force la spécialité = 7 (Job Étudiant)
    const filters = {
        specialites: ["7"]
    };

    // ✅ Requête FETCH en JSON
    fetch(url, {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(filters)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Erreur serveur : ${response.status}`);
        }
        return response.json();
    })
    .then(data => {

        resultsDiv.innerHTML = "";

        // ❌ Aucun résultat
        if (!data.status || data.count === 0) {
            resultsDiv.innerHTML = "<p>Aucune offre trouvée.</p>";
            return;
        }

        // ✅ Génération des cartes
        data.jobs.forEach(job => {

            const card = document.createElement("div");
            card.className = "job-card";

            card.innerHTML = `
                <h3>${job.titre_offre}</h3>

                <p><strong>Type de contrat :</strong> ${job.type_contrat}</p>


                <p>
                    <strong>Contact :</strong> 
                    <a href="mailto:${job.email_user}">
                        ${job.email_user}
                    </a>
                </p>

                <p>${job.description || ""}</p>

                ${
                    job.url_linkedin 
                        ? `<p><a href="${job.url_linkedin}" target="_blank">Voir l'offre sur LinkedIn</a></p>` 
                        : ""
                }
            `;

            resultsDiv.appendChild(card);
        });

    })
    .catch(err => {
        resultsDiv.innerHTML = `<p style="color:red;">❌ ${err.message}</p>`;
    });

});