document.addEventListener('DOMContentLoaded' , ()=>{


    function image_slide_reseau(container)
    {
        if(!max_reached)
        {
            index += 1; 
            if(index >= length_reseau -1)
            {
                max_reached = true;
            }
        }
        else
        {
            index -= 1; 
            if(index <= 0)
            {
                max_reached = false;
            }
        }
        var container_img_network = document.querySelector(".img_sub_reseau");
        container_img_network.style.transform = "translateX("+(-index*100)+"%)";
    }
    function image_slide_activite()
    {
     
        if(!max_reached_activite)
        {
            index_activite += 1; 
            if(index_activite >= length_activite -1)
            {
                max_reached_activite = true;
            }
        }
        else
        {
            index_activite -= 1; 
            if(index_activite <= 0)
            {
                max_reached_activite = false;
            }
        }
        var container_img_activite= document.querySelector(".img_sub_activite");
        container_img_activite.style.transform = "translateX("+(-index_activite*100)+"%)";
    }
    let query_event_network = document.querySelectorAll(".img-div-reseau");
    let query_event_activite = document.querySelectorAll(".img-div-activite")
    let length_reseau = query_event_network.length;
    let length_activite = query_event_activite.length;
    var index = 0;
    var index_activite = 0;
    var max_reached = false;
    var max_reached_activite = false;
    setInterval(image_slide_reseau , 4800);
    setInterval(image_slide_activite , 5200);
})