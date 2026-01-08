document.addEventListener('DOMContentLoaded', () => {

    const burger  = document.getElementById("container_dash_id");
    const nav2    = document.getElementById("nav_2_id");
    const overlay = document.getElementById("overlay_id");
    const nav = document.querySelector(".nav_1");

    // 🔎 Détecter l'élément qui scrolle réellement
    const scrollElement = document.scrollingElement || document.documentElement;

    function openMenu() {
        burger.classList.add("active");
        nav2.classList.add("nav_2_show");
        overlay.classList.add("active");

        // Bloquer TOUT scroll possible
        scrollElement.style.overflow = "hidden";
        document.body.style.overflow = "hidden";
    }

    function closeMenu() {
        burger.classList.remove("active");
        nav2.classList.remove("nav_2_show");
        overlay.classList.remove("active");

        // Restaurer le scroll
        scrollElement.style.overflow = "";
        document.body.style.overflow = "";
    }

    burger.addEventListener("click", (e) => {
        e.stopPropagation();

        if (burger.classList.contains("active")) {
            closeMenu();
        } else {
            openMenu();
        }
    });

    overlay.addEventListener("click", closeMenu);
    if (!nav) return;

    window.addEventListener("scroll", () => {
        const y = window.scrollY;

        // Réapparition UNIQUEMENT au top
        if (y === 0) {
            nav.classList.remove("nav_1_hidden");
        }

        // Disparition dès qu'on atteint 10px
        if (y >= 10) {
            nav.classList.add("nav_1_hidden");
        }
    });

});
