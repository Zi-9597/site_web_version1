document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("formulaire-offre");
    const submitBtn = document.getElementById("button_submit");
    const results = document.getElementById("resultats");
    if (!form || !submitBtn || !results) return;

    function appendText(parent, tagName, text) {
        const element = document.createElement(tagName);
        element.textContent = text;
        parent.appendChild(element);
        return element;
    }

    function appendJob(job) {
        /* CHANGE (XSS): database fields are inserted with textContent, never innerHTML. */
        const card = document.createElement("article");
        card.className = "job-card";
        appendText(card, "h3", job.titre_offre || "Offre sans titre");
        appendText(card, "p", `Type de contrat : ${job.type_contrat || "-"}`);
        appendText(card, "p", `Spécialités : ${job.specialites || "-"}`);

        if (typeof job.email_user === "string" && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(job.email_user)) {
            const contact = appendText(card, "p", "Contact : ");
            const link = document.createElement("a");
            link.href = `mailto:${encodeURIComponent(job.email_user)}`;
            link.textContent = job.email_user;
            contact.appendChild(link);
        }
        appendText(card, "p", job.description || "");

        try {
            const linkUrl = new URL(job.url_linkedin);
            if (linkUrl.protocol === "https:") {
                const link = document.createElement("a");
                link.href = linkUrl.href;
                link.target = "_blank";
                link.rel = "noopener noreferrer";
                link.textContent = "Voir l'offre sur LinkedIn";
                card.appendChild(link);
            }
        } catch (_) {
            // Invalid or absent user URL: do not create a link.
        }
        results.appendChild(card);
    }

    form.addEventListener("submit", async (event) => {
        event.preventDefault();
        const userId = new URLSearchParams(window.location.search).get("id_user");
        const url = `/?dest=reche_emploie&id_user=${encodeURIComponent(userId || "")}`;
        results.replaceChildren();
        submitBtn.disabled = true;

        try {
            const response = await fetch(url, { method: "POST", body: new FormData(form) });
            if (!response.ok) throw new Error("La recherche est indisponible.");
            const data = await response.json();
            if (!data.status || !Array.isArray(data.jobs) || data.jobs.length === 0) {
                appendText(results, "p", "Aucune offre trouvée.");
            } else {
                data.jobs.forEach(appendJob);
            }
        } catch (_) {
            appendText(results, "p", "La recherche a échoué. Veuillez réessayer.");
        } finally {
            submitBtn.disabled = false;
        }
    });
});
