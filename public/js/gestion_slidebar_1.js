document.addEventListener('DOMContentLoaded' , ()=>
{
    var div_cross = document.getElementById("container_dash_id");
    var nav_rubrique = document.getElementById("nav_2_id");
    var overlay_div = document.getElementById("overlay_id");

    var nav_1_rubrique = document.getElementById("nav_1_id");
    let last_scroll = 0;
    div_cross.addEventListener('click' , ()=>
    {
        nav_rubrique.classList.toggle("nav_2_show");
        overlay_div.classList.toggle("active");
        document.body.classList.toggle("active");
        

    });

    window.addEventListener('scroll' , ()=>
    {
        const scrollTop = window.scrollY || document.documentElement.scrollTop; 

        if(scrollTop > last_scroll)
        {
            nav_1_rubrique.classList.add("nav_1_hidden");
        }
        else
        {
            nav_1_rubrique.classList.remove("nav_1_hidden");
        }
        last_scroll = scrollTop <= 0 ? 0 : scrollTop;
 
        
    });

    // Connection ou Inscription lien de référence

    
    
    



  
});


