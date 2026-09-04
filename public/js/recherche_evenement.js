document.addEventListener("DOMContentLoaded", () => {
    const inputDate = document.getElementById("evenmt_date");
    const submitButton = document.getElementById("button_submit");
    const infoText = document.getElementById("forme_id_event");
    const form = document.getElementById("formulaire-evenement");
    const results = document.getElementById("resultats");
    if (!inputDate || !submitButton || !infoText || !form || !results) return;

    const addText = (parent, tag, text) => {
        const element = document.createElement(tag);
        element.textContent = text;
        parent.appendChild(element);
        return element;
    };

    form.addEventListener("submit", async event => {
        event.preventDefault();
        const userId = new URLSearchParams(window.location.search).get("id_user");
        results.replaceChildren();
        try {
            const response = await fetch(`/?dest=fetch_data&id_user=${encodeURIComponent(userId || "")}`, {
                method: "POST",
                body: new FormData(form)
            });
            if (!response.ok) throw new Error();
            const events = await response.json();
            if (!Array.isArray(events) || events.length === 0) {
                addText(results, "p", "Aucun événement trouvé.");
                return;
            }
            events.forEach(item => {
                /* CHANGE (XSS): render event data as text, not HTML. */
                const card = document.createElement("article");
                card.className = "event-card";
                addText(card, "h3", item.nom_event || "Événement sans nom");
                addText(card, "p", `Date : ${item.date_event || "-"}`);
                addText(card, "p", item.desc_event || "");
                results.appendChild(card);
            });
        } catch (_) {
            addText(results, "p", "La recherche a échoué. Veuillez réessayer.");
        }
    });

    function validateDate() {
        const value = inputDate.value.trim();
        const match = /^(\d{2})\/(\d{2})\/(\d{4})$/.exec(value);
        if (!value) {
            infoText.style.display = "none";
            submitButton.disabled = false;
            return;
        }
        if (!match) {
            infoText.style.display = "block";
            submitButton.disabled = true;
            return;
        }
        const date = new Date(Number(match[3]), Number(match[2]) - 1, Number(match[1]));
        const valid = date.getFullYear() === Number(match[3]) && date.getMonth() === Number(match[2]) - 1 && date.getDate() === Number(match[1]);
        infoText.style.display = valid ? "none" : "block";
        submitButton.disabled = !valid;
    }

    inputDate.addEventListener("input", validateDate);
});
