document.addEventListener('DOMContentLoaded', () => {

    const burger = document.getElementById("container_dash_id");
    const nav2 = document.getElementById("nav_2_id");
    const overlay = document.getElementById("overlay_id");

    // OUVRIR / FERMER via burger
    burger.addEventListener('click', (e) => {
        e.stopPropagation();

        const isOpen = burger.classList.toggle("active");

        nav2.classList.toggle("nav_2_show", isOpen);
        overlay.classList.toggle("active", isOpen);
        document.body.classList.toggle("no-scroll", isOpen);
    });

    // FERMER via écran noir
    overlay.addEventListener('click', () => {
        burger.classList.remove("active");
        nav2.classList.remove("nav_2_show");
        overlay.classList.remove("active");
        document.body.classList.remove("no-scroll");
    });

});
