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

//Máscara CNPJ 12.345.678/0001-01
function cnpjMasc(variavel) {
    variavel = variavel.replace(/\D/g,"");
    variavel = variavel.replace(/(\d{2})(\d)/,"$1.$2");
    variavel = variavel.replace(/(\d{3})(\d)/,"$1.$2");
    variavel = variavel.replace(/(\d{3})(\d)/,"$1/$2");
    variavel = variavel.replace(/(\d{4})(\d)/,"$1-$2");
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

//CONFIGURAÇÕES POPUP
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
    } else if (window.location.hash === '#editarFornecedor') {
        document.getElementById('popupEdicaoFornecedor').showModal();
    }
});

// CONFIGURAÇÕES BARRA DE PESQUISA
function pesquisarDadoTabela(tabela) {
    let timeoutPesquisa;
    clearTimeout(timeoutPesquisa);
    
    timeoutPesquisa = setTimeout(() => {
        const termoBusca = document.getElementById('pesquisaInput').value;
        
        // Mostra loading (opcional)
        document.getElementById('container-tabela').innerHTML = "<tr><td colspan='2' class='text-center'><i class='fas fa-search'></i>Carregando...</td></tr>";
        
        // Faz a requisição AJAX
        fetch(`tabelas.php?tabela=${encodeURIComponent(tabela)}&termo=${encodeURIComponent(termoBusca)}`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('container-tabela').innerHTML = html;
            })
            .catch(error => {
                console.error('Erro:', error);
                document.getElementById('container-tabela').innerHTML = "<tr><td colspan='2' class='text-center'><i class='fas fa-search'></i>Erro ao carregar resultados!</td></tr>";
            });
    }, 300); // Atraso de 300ms após a digitação
}

// ALERTAS ESTILIZADOS (SWEET ALERT)
document.addEventListener('DOMContentLoaded', function() {
    const mensagemErro = sessionStorage.getItem('erroAlerta');
    const mensagemSucesso = sessionStorage.getItem('sucessoAlerta');
    const mensagemAviso = sessionStorage.getItem('avisoAlerta');
    console.log(mensagemErro + ' antes do if');

    if(mensagemErro) {
        console.log(mensagemErro);
        mostraAlerta('erro', mensagemErro, '');
        sessionStorage.removeItem('erroAlerta');
    } else if(mensagemSucesso) {
        mostraAlerta('sucesso', mensagemSucesso, '');
        sessionStorage.removeItem('sucessoAlerta');
    } else if(mensagemAviso) {
        mostraAlerta('aviso', mensagemAviso);
        sessionStorage.removeItem('avisoAlerta');
    }
});

function mostraAlerta(tipoAlerta, mensagem) {
    if(typeof Swal === 'undefined') {
        console.error('SweetAlert2 não está carregado!');
        alert(mensagem); // Fallback
        return;
    }

    if(tipoAlerta == 'sucesso') {
        alertaSucesso(mensagem);
    } else if(tipoAlerta == 'erro') {
        alertaErro(mensagem);
    } else if(tipoAlerta == 'aviso') {
        alertaAviso(mensagem);
    }
}

function alertaErro(mensagem) {
    Swal.fire({
        icon: 'error',
        title: 'Erro!',
        text: mensagem,
        confirmButtonColor: '#a69c60',
        showConfirmButton: true,
        confirmButtonText: 'OK'
    });
}

function alertaSucesso(mensagem) {
    Swal.fire({
        icon: 'success',
        title: 'Sucesso!',
        text: mensagem,
        confirmButtonColor: '#a69c60',
        showConfirmButton: true,
        confirmButtonText: 'OK'
    });
}

function alertaAviso(mensagem) {
    Swal.fire({
        icon: 'warning',
        title: 'Atenção!',
        text: mensagem,
        confirmButtonColor: '#a69c60',
        showConfirmButton: true,
        confirmButtonText: 'OK'
    });
}

function confirmarExclusao(arquivo, acao, id, mensagem) {
    Swal.fire({
        icon: 'warning',
        title: 'Confirmar Exclusão',
        text: mensagem,
        showCancelButton: true,
        confirmButtonText: 'Excluir',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33',
    }).then((result) => {
        if (result.isConfirmed) {
            // Cria e submete formulário
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = arquivo;
            
            const inputId = document.createElement('input');
            inputId.type = 'hidden';
            inputId.name = 'id';
            inputId.value = id;
            
            const inputFormId = document.createElement('input');
            inputFormId.type = 'hidden';
            inputFormId.name = 'form-id';
            inputFormId.value = acao;
            
            form.appendChild(inputId);
            form.appendChild(inputFormId);
            document.body.appendChild(form);
            form.submit();
        }
    });
}