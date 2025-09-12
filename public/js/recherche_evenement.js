document.addEventListener("DOMContentLoaded", function () {
    const inputDate = document.getElementById("evenmt_date");
    const btnSubmit = document.getElementById("button_submit");
    const infoText = document.getElementById("forme_id_event");
    const event_form = document.getElementById("formulaire-evenement");
    const resultsDiv = document.getElementById("resultats");


    event_form.addEventListener("submit", function(e) {

            e.preventDefault(); // empêche le rechargement

            const formData = new FormData(event_form);
            const params_url = new URLSearchParams(window.location.search);
            const id_web = params_url.get("id_user");

            const url = `/?dest=fetch_data&id_user=${encodeURIComponent(id_web)}`;
            fetch(url , {
            method: "POST",
            body: formData
            })
            .then(response => {
            if (!response.ok) throw new Error("Erreur serveur " + response.status);
            return response.json(); 
            })
            .then(data => {
            resultsDiv.innerHTML = "";

            if (data.length === 0) {
                resultsDiv.innerHTML = "<p>Aucun événement trouvé.</p>";
                return;
            }

            data.forEach(ev => {
                const card = document.createElement("div");
                card.className = "event-card";
                card.innerHTML = `
                    <h3>${ev.nom_event}</h3>
                    <p><strong>Date :</strong> ${ev.date_event}</p>
                    <p><strong>Lieu :</strong> ${ev.lieu_event}</p>
                    <p><strong>Catégorie :</strong> ${ev.categorie}</p>
                    <p>${ev.desc_event ?? ""}</p>
                    ${ev.url_form ? `<p><a href="${ev.url_form}" target="_blank">🔗 Formulaire</a></p>` : ""}
                `;
                resultsDiv.appendChild(card);
            });
            event_form.reset();
            })
            .catch(err => {
            resultsDiv.innerHTML = `<p style="color:red;">❌ ${err.message}</p>`;
            });
        });
   
    // bouton et message désactivés au départ
    btnSubmit.disabled = false;
    infoText.style.display = "none";

    function validateDate() 
    {
        const value = inputDate.value.trim();
        const regex = /^\d{2}\/\d{2}\/\d{4}$/; // format jj/mm/aaaa

        // Si champ vide → bouton reste actif (pas de blocage)
        if (value.length === 0) {
            infoText.style.display = "none";
            btnSubmit.disabled = false;
            return;
        }

        // Toujours afficher si l'utilisateur tape quelque chose
        infoText.style.display = "block";

        // Vérifie le format
        if (!regex.test(value)) {
            btnSubmit.disabled = true;
            return;
        }

        const [day, month, year] = value.split("/").map(Number);
        const date = new Date(year, month - 1, day);

        const today = new Date();
        today.setHours(0, 0, 0, 0);

        // Vérifie validité + >= aujourd’hui
        if (
            date.getFullYear() === year &&
            date.getMonth() === month - 1 &&
            date.getDate() === day &&
            date >= today
        ) {
            infoText.style.display = "none"; // cache si tout est bon
            btnSubmit.disabled = false;
        } else {
            infoText.style.display = "block"; // montre si mauvais
            btnSubmit.disabled = true;
        }
    }

    // Vérifie à chaque saisie
    inputDate.addEventListener("input", validateDate);


    

     /**
     * ⚡ Quand on recharge ou revient en arrière → réinitialisation
    */
    window.addEventListener("pageshow", function () {
        event_form.reset();
        btnSubmit.disabled = false;
        infoText.style.display = "none";

    });
});


   