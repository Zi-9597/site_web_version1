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

    let lastScrollY = window.scrollY;
    const delta = 5; // seuil pour éviter les micro-scrolls

    window.addEventListener("scroll", () => {
        const currentScrollY = window.scrollY;

        // Scroll vers le bas → cacher
        if (currentScrollY > lastScrollY + delta && currentScrollY > 20) {
            nav.classList.add("nav_1_hidden");
        }
        // Scroll vers le haut → afficher
        else if (currentScrollY < lastScrollY - delta) {
            nav.classList.remove("nav_1_hidden");
        }

        lastScrollY = currentScrollY;
    });

});
