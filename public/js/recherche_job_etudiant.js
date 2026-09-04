document.addEventListener("DOMContentLoaded", async () => {
    const results = document.getElementById("resultats");
    if (!results) return;

    function addText(parent, tag, text) {
        const element = document.createElement(tag);
        element.textContent = text;
        parent.appendChild(element);
        return element;
    }

    try {
        const userId = new URLSearchParams(window.location.search).get("id_user");
        const response = await fetch(`/?dest=reche_emploie&id_user=${encodeURIComponent(userId || "")}`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ specialites: ["7"] })
        });
        if (!response.ok) throw new Error();
        const data = await response.json();
        results.replaceChildren();
        if (!data.status || !Array.isArray(data.jobs) || data.jobs.length === 0) {
            addText(results, "p", "Aucune offre trouvée.");
            return;
        }

        data.jobs.forEach(job => {
            /* CHANGE (XSS): textContent prevents stored offer content becoming HTML. */
            const card = document.createElement("article");
            card.className = "job-card";
            addText(card, "h3", job.titre_offre || "Offre sans titre");
            addText(card, "p", `Type de contrat : ${job.type_contrat || "-"}`);
            addText(card, "p", job.description || "");
            results.appendChild(card);
        });
    } catch (_) {
        results.replaceChildren();
        addText(results, "p", "La recherche a échoué. Veuillez réessayer.");
    }
});
