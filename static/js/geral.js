function animaIconeMenu(x) {
    //Animação do icone
    x.classList.toggle("change");

    //Rotina para abrir e fechar o menu lateral se baseando na opacidade da barra do meio
    let barra2 = document.getElementById("barra2");

    if (barra2.style.opacity == 0 || barra2.style.opacity == 1)
    {
        document.getElementById("navegaMenu").style.width = "230px";
        document.getElementById("iconeMenu").style.marginLeft = "150px";
        barra2.style.opacity = 0.0000001;
    }

    else if (barra2.style.opacity != 0)
    {
        document.getElementById("navegaMenu").style.width = "0";
        document.getElementById("iconeMenu").style.marginLeft = "0";
        barra2.style.opacity = 1;
    }
}