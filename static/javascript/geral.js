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

//MÁSCARAS PARA OS FORMULÁRIOS
function mascara(o, f) {
    objeto = o;
    funcao = f;
    setTimeout("executaMascara()", 1);
}

function executaMascara() {
    objeto.value = funcao(objeto.value);
}

//Máscara nome
function nomeMasc(variavel) {
    variavel = variavel.replace(/[^a-zA-Z áÁéÉíÍóÓúÚçÇ]/g,"");
    return variavel;
}

//Máscara CPF
function cpfMasc(variavel) {
    variavel = variavel.replace(/\D/g,"");
    variavel = variavel.replace(/(\d{3})(\d)/,"$1.$2");
    variavel = variavel.replace(/(\d{3})(\d)/,"$1.$2");
    variavel = variavel.replace(/(\d{3})(\d{1,2})$/,"$1-$2");
    return variavel;
}

//Máscara Telefone
function telefoneMasc(variavel) {
    variavel = variavel.replace(/\D/g,"");
    variavel = variavel.replace(/^(\d\d)(\d)/g,"($1) $2");
    variavel = variavel.replace(/(\d{5})(\d)/,"$1-$2");
    return variavel;
}

//Máscara ISBN
function isbnMasc(variavel) {
    variavel = variavel.replace(/\D/g,"");
    variavel = variavel.replace(/^(\d{3})(\d)/, "$1-$2");
    variavel = variavel.replace(/^(\d{3}-\d{2})(\d)/, "$1-$2");
    variavel = variavel.replace(/^(\d{3}-\d{2}-\d{3})(\d)/, "$1-$2");
    variavel = variavel.replace(/^(\d{3}-\d{2}-\d{3}-\d{4})(\d)/, "$1-$2");
    return variavel;
}

function abrePopup(idPopup) {
    let modal = document.getElementById(idPopup);
    modal.showModal();
}

function fechaPopup(idPopup) {
    let modal = document.getElementById(idPopup);
    modal.close();
}

window.addEventListener('DOMContentLoaded', () => {
    if (window.location.hash === '#editarMembro') {
        document.getElementById('popupEdicaoMembro').showModal();
    }
});