create database ALEXANDRIA;
use ALEXANDRIA;

CREATE TABLE TIPO_USUARIO (
    pk_tipo_user INT PRIMARY KEY auto_increment,
    tipo_user_nome VARCHAR(50) not null,
    tipo_user_descricao VARCHAR(300)
);

CREATE TABLE CATEGORIA ( 
 pk_cat INT PRIMARY KEY auto_increment, 
 cat_nome VARCHAR(100) not null
);

CREATE TABLE AUTOR ( 
 pk_aut INT PRIMARY KEY auto_increment,
 aut_nome VARCHAR(100) not null,
 aut_data_nascimento DATE
);

CREATE TABLE FORNECEDOR ( 
 pk_forn INT PRIMARY KEY auto_increment,
 forn_nome VARCHAR(250) not null,
 forn_cnpj VARCHAR(18) unique,
 forn_telefone VARCHAR(16),
 forn_email VARCHAR(100),
 forn_endereco VARCHAR(250)

);

CREATE TABLE LIVRO ( 
 pk_liv INT PRIMARY KEY auto_increment,
 liv_titulo VARCHAR(200) not null,
 liv_isbn VARCHAR(17) not null unique,
 liv_edicao INT,
 liv_anoPublicacao INT,
 liv_sinopse VARCHAR(3000),
 liv_estoque INT,
 liv_dataAlteracaoEstoque DATE,
 liv_idioma VARCHAR(30),
 liv_num_paginas INT,
 liv_capa VARCHAR(255)
 fk_autor INT not null,
 fk_categoria INT not null,
 FOREIGN KEY(fk_autor) references AUTOR(pk_aut) on delete restrict on update cascade
 FOREIGN KEY(fk_categoria) references CATEGORIA(pk_cat) on delete restrict on update cascade
);

CREATE TABLE PLANO ( 
 pk_plan INT PRIMARY KEY auto_increment,
 plan_nome VARCHAR(100) not null,
 plan_valor DECIMAL(10,2),
 plan_duracao VARCHAR(50),
 plan_descricao VARCHAR(3000),
 plan_limite_emp INT default 2
);

CREATE TABLE USUARIO ( 
 pk_user INT PRIMARY KEY auto_increment,
 user_nome VARCHAR(250) not null,
 user_cpf VARCHAR(14) unique not null,
 user_email VARCHAR(250),
 user_telefone VARCHAR(16),
 user_senha VARCHAR(255) not null,
 user_login VARCHAR(20) unique,
 user_dataAdmissao DATE,
 user_dataDemissao DATE,
 user_foto VARCHAR(255),
 user_status ENUM('Ativo', 'Inativo'),
 fk_tipoUser INT not null,
 FOREIGN KEY(fk_tipoUser) references TIPO_USUARIO(pk_tipo_user) on delete restrict on update cascade
);

CREATE TABLE MEMBRO (
 pk_mem INT PRIMARY KEY auto_increment,
 mem_nome VARCHAR(250) not null,
 mem_cpf VARCHAR(14) unique not null,
 mem_senha VARCHAR(250) not null,
 mem_email VARCHAR(250),
 mem_telefone VARCHAR(16),
 mem_dataInscricao DATE,
 mem_status ENUM('Ativo', 'Suspenso'),
 fk_plan INT,
 FOREIGN KEY(fk_plan) references PLANO(pk_plan) on delete restrict on update cascade
);

CREATE TABLE EMPRESTIMO (
 pk_emp INT PRIMARY KEY auto_increment,
 emp_prazo INT,
 emp_dataEmp DATE,
 emp_dataDev DATE,
 emp_dataDevReal DATE,
 emp_valorMultaDiaria DECIMAL(10,2) default 1.50,
 emp_status ENUM('Empréstimo Ativo', 'Empréstimo Atrasado', 'Renovação Ativa', 'Renovação Atrasada', 'Finalizado') not null default 'Empréstimo Ativo',
 fk_mem INT not null,
 fk_user INT not null,
 fk_liv INT not null,
 FOREIGN KEY(fk_mem) references MEMBRO(pk_mem) on delete restrict on update cascade,
 FOREIGN KEY(fk_user) references USUARIO(pk_user) on delete restrict on update cascade,
 FOREIGN KEY(fk_liv) references LIVRO(pk_liv) on delete restrict on update cascade
);


CREATE TABLE RESERVA ( 
 pk_res INT PRIMARY KEY auto_increment,
 res_prazo INT,
 res_dataMarcada DATE,
 res_dataVencimento DATE,
 res_dataFinalizada DATE,
 res_observacoes VARCHAR(1000),
 res_status ENUM('Aberta', 'Cancelada', 'Finalizada', 'Atrasada') not null default 'Aberta',
 fk_mem INT not null,
 fk_liv INT not null,
 fk_user INT not null,
 FOREIGN KEY(fk_mem) references MEMBRO(pk_mem) on delete restrict on update cascade,
 FOREIGN KEY(fk_liv) references LIVRO(pk_liv) on delete restrict on update cascade,
 FOREIGN KEY(fk_user) references USUARIO(pk_user) on delete restrict on update cascade
);

CREATE TABLE MULTA ( 
 pk_mul INT PRIMARY KEY auto_increment,
 mul_valor DECIMAL(10,2),
 mul_qtdDias INT,
 mul_status ENUM('Aberta', 'Finalizada'),
 fk_mem INT not null,
 fk_emp INT not null,
 FOREIGN KEY(fk_mem) references MEMBRO(pk_mem) on delete restrict on update cascade,
 FOREIGN KEY(fk_emp) references EMPRESTIMO(pk_emp) on delete restrict on update cascade
);

CREATE TABLE PAG_PLANO ( 
 pk_pag_plan INT PRIMARY KEY auto_increment,
 pag_plan_preco DECIMAL(10,2),
 pag_plan_valorPag DECIMAL(10,2),
 pag_plan_dataPag DATE,
 pag_plan_dataVen DATE,
 pag_plan_comprovante VARCHAR(255),
 pag_plan_status ENUM('Em dia', 'Atrasado') not null,
 fk_mem INT not null,
 fk_plan INT not null,
 FOREIGN KEY(fk_mem) references MEMBRO(pk_mem) on delete restrict on update cascade,
 FOREIGN KEY(fk_plan) references PLANO(pk_plan) on delete restrict on update cascade
);

CREATE TABLE REMESSA (
 pk_rem INT PRIMARY KEY auto_increment, 
 rem_data DATE not null,
 rem_qtd INT not null,
 fk_forn INT not null,
 fk_liv INT not null,
 fk_user INT not null,
 FOREIGN KEY(fk_forn) references FORNECEDOR(pk_forn) on delete restrict on update cascade,
 FOREIGN KEY(fk_liv) references LIVRO(pk_liv) on delete restrict on update cascade,
 FOREIGN KEY(fk_user) references USUARIO(pk_user) on delete restrict on update cascade
);

/*--------------------- TABELAS JUNÇÃO ---------------------*/
CREATE TABLE FORN_LIV ( 
 fk_liv INT,
 fk_forn INT,
 PRIMARY KEY(fk_liv, fk_forn),
 FOREIGN KEY(fk_liv) references LIVRO(pk_liv) on delete restrict on update cascade,
 FOREIGN KEY(fk_forn) references FORNECEDOR(pk_forn) on delete restrict on update cascade
);

CREATE TABLE CAT_LIV ( 
 fk_liv INT,
 fk_cat INT,
 PRIMARY KEY(fk_liv, fk_cat),
 FOREIGN KEY(fk_liv) references LIVRO(pk_liv) on delete restrict on update cascade,
 FOREIGN KEY(fk_cat) references CATEGORIA(pk_cat) on delete restrict on update cascade
);

CREATE TABLE AUT_LIV ( 
 fk_liv INT,
 fk_aut INT,
 PRIMARY KEY(fk_liv, fk_aut),
 FOREIGN KEY(fk_liv) references LIVRO(pk_liv) on delete restrict on update cascade,
 FOREIGN KEY(fk_aut) references AUTOR(pk_aut) on delete restrict on update cascade
);

/*INSERT DE DADOS | IA UTILIZADA: DEEPSEEK*/
INSERT INTO TIPO_USUARIO (tipo_user_nome, tipo_user_descricao) VALUES 
('Administrador', 'Acesso total ao sistema, pode gerenciar usuários e configurações'),
('Secretária', 'Atendimento ao público, cadastro de membros e empréstimos'),
('Almoxarife', 'Responsável pelo controle de estoque de livros e recebimento de remessas');

INSERT INTO CATEGORIA (cat_nome) VALUES 
('Ficção Científica'),
('Fantasia'),
('Romance'),
('Suspense'),
('Terror'),
('Biografia'),
('História'),
('Autoajuda'),
('Infantil'),
('Didático');

INSERT INTO AUTOR (aut_nome, aut_sobrenome, aut_data_nascimento) VALUES 
('Jorge', 'Amado', '1912-08-10'),
('Clarice', 'Lispector', '1920-12-10'),
('Machado', 'de Assis', '1839-06-21'),
('Carlos', 'Drummond de Andrade', '1902-10-31'),
('Monteiro', 'Lobato', '1882-04-18'),
('Cecília', 'Meireles', '1901-11-07'),
('Graciliano', 'Ramos', '1892-10-27'),
('Guimarães', 'Rosa', '1908-06-27'),
('Lygia', 'Fagundes Telles', '1923-04-19'),
('Rubem', 'Fonseca', '1925-05-11'),
('Paulo', 'Coelho', '1947-08-24'),
('Augusto', 'Cury', '1958-10-02'),
('J.K.', 'Rowling', '1965-07-31'),
('George R.R.', 'Martin', '1948-09-20'),
('Stephen', 'King', '1947-09-21'),
('Agatha', 'Christie', '1890-09-15'),
('Dan', 'Brown', '1964-06-22'),
('Haruki', 'Murakami', '1949-01-12'),
('Yuval Noah', 'Harari', '1976-02-24'),
('Isabel', 'Allende', '1942-08-02'),
('Gabriel', 'García Márquez', '1927-03-06'),
('Mario', 'Vargas Llosa', '1936-03-28'),
('Julio', 'Cortázar', '1914-08-26'),
('Jorge Luis', 'Borges', '1899-08-24'),
('Pablo', 'Neruda', '1904-07-12'),
('Octavio', 'Paz', '1914-03-31'),
('Fernando', 'Pessoa', '1888-06-13'),
('José', 'Saramago', '1922-11-16'),
('Eça', 'de Queirós', '1845-11-25'),
('Fernando', 'Sabino', '1923-10-12');

INSERT INTO FORNECEDOR (forn_nome, forn_cnpj, forn_telefone, forn_email, forn_endereco) VALUES 
('Editora Abril', '12.345.678/0001-00', '(11) 1234-5678', 'contato@abril.com.br', 'Av. das Nações Unidas, 7221 - São Paulo/SP'),
('Companhia das Letras', '23.456.789/0001-11', '(11) 2345-6789', 'contato@companhiadasletras.com.br', 'Rua Bandeira Paulista, 702 - São Paulo/SP'),
('Editora Record', '34.567.890/0001-22', '(21) 3456-7890', 'contato@record.com.br', 'Rua Argentina, 171 - Rio de Janeiro/RJ'),
('Editora Globo', '45.678.901/0001-33', '(11) 4567-8901', 'contato@editoraglobo.com.br', 'Av. Jaguaré, 1485 - São Paulo/SP'),
('Editora Saraiva', '56.789.012/0001-44', '(11) 5678-9012', 'contato@saraiva.com.br', 'Rua Henrique Schaumann, 270 - São Paulo/SP'),
('Editora Moderna', '67.890.123/0001-55', '(11) 6789-0123', 'contato@moderna.com.br', 'Av. Princesa Isabel, 577 - São Paulo/SP'),
('Editora Ática', '78.901.234/0001-66', '(11) 7890-1234', 'contato@atica.com.br', 'Rua Padre Adelino, 758 - São Paulo/SP'),
('Editora Melhoramentos', '89.012.345/0001-77', '(11) 8901-2345', 'contato@melhoramentos.com.br', 'Av. Brigadeiro Faria Lima, 1665 - São Paulo/SP'),
('Editora Nova Fronteira', '90.123.456/0001-88', '(21) 9012-3456', 'contato@novafronteira.com.br', 'Rua Voluntários da Pátria, 45 - Rio de Janeiro/RJ'),
('Editora Rocco', '01.234.567/0001-99', '(21) 1234-5678', 'contato@rocco.com.br', 'Rua do Ouvidor, 37 - Rio de Janeiro/RJ');

INSERT INTO LIVRO (liv_titulo, liv_isbn, liv_edicao, liv_anoPublicacao, liv_sinopse, liv_estoque, liv_dataAlteracaoEstoque, liv_idioma, liv_num_paginas, liv_capa) VALUES 
('Dom Casmurro', '9788535902775', 1, 1899, 'Romance que explora ciúme e ambiguidade através da narrativa de Bentinho sobre seu casamento com Capitu.', 5, '2023-01-15', 'Português', 256, 'dom_casmurro.jpg'),
('Memórias Póstumas de Brás Cubas', '9788535910664', 3, 1881, 'Narrado por um defunto autor, o livro satiriza a sociedade brasileira do século XIX.', 4, '2023-02-20', 'Português', 240, 'bras_cubas.jpg'),
('O Alienista', '9788572326972', 2, 1882, 'Conta a história do Dr. Simão Bacamarte e sua teoria sobre a loucura humana.', 3, '2023-03-10', 'Português', 96, 'alienista.jpg'),
('Quincas Borba', '9788535907220', 1, 1891, 'Segunda obra da trilogia realista de Machado de Assis, abordando a filosofia do Humanitismo.', 4, '2023-01-25', 'Português', 288, 'quincas_borba.jpg'),
('A Hora da Estrela', '9788520923251', 5, 1977, 'Último romance de Clarice Lispector, narra a vida simples de Macabéa, uma datilógrafa alagoana.', 6, '2023-04-05', 'Português', 88, 'hora_estrela.jpg'),
('O Cortiço', '9788572326973', 4, 1890, 'Romance naturalista que retrata a vida nos cortiços do Rio de Janeiro no século XIX.', 5, '2023-02-15', 'Português', 304, 'cortico.jpg'),
('Grande Sertão: Veredas', '9788520923252', 3, 1956, 'Narrativa épica sobre Riobaldo e seu pacto com o diabo no sertão brasileiro.', 4, '2023-03-20', 'Português', 624, 'grande_sertao.jpg'),
('Vidas Secas', '9788520923253', 2, 1938, 'Retrato da vida difícil de uma família de retirantes no sertão nordestino.', 7, '2023-01-10', 'Português', 176, 'vidas_secas.jpg'),
('O Alquimista', '9788575421263', 15, 1988, 'A jornada de Santiago, um pastor andaluz, em busca de um tesouro no Egito.', 8, '2023-04-15', 'Português', 208, 'alquimista.jpg'),
('Harry Potter e a Pedra Filosofal', '9788532511010', 1, 1997, 'Primeiro livro da série sobre o jovem bruxo Harry Potter e seu ingresso em Hogwarts.', 10, '2023-05-01', 'Português', 264, 'harry_potter.jpg'),
('1984', '9788535902776', 2, 1949, 'Clássico distópico sobre um regime totalitário que controla todos os aspectos da vida.', 6, '2023-03-15', 'Português', 416, '1984.jpg'),
('O Senhor dos Anéis: A Sociedade do Anel', '9788533603149', 3, 1954, 'Primeiro volume da trilogia sobre a jornada para destruir o Um Anel na Montanha da Perdição.', 7, '2023-04-20', 'Português', 576, 'sociedade_anel.jpg'),
('Cem Anos de Solidão', '9788535910665', 4, 1967, 'Epopeia da família Buendía na mítica cidade de Macondo.', 5, '2023-02-10', 'Português', 448, 'cem_anos.jpg'),
('O Pequeno Príncipe', '9788572326974', 10, 1943, 'Clássico sobre um principezinho que viaja por planetas e ensina sobre amizade e amor.', 12, '2023-05-10', 'Português', 96, 'pequeno_principe.jpg'),
('A Revolução dos Bichos', '9788520923254', 5, 1945, 'Fábula satírica sobre animais que tomam uma fazenda e estabelecem seu próprio governo.', 8, '2023-03-25', 'Português', 152, 'revolucao_bichos.jpg'),
('O Nome da Rosa', '9788535910666', 2, 1980, 'Mistério medieval sobre uma série de assassinatos em um mosteiro italiano.', 4, '2023-01-20', 'Português', 592, 'nome_rosa.jpg'),
('A Menina que Roubava Livros', '9788575421264', 1, 2005, 'História de Liesel Meminger, uma garota que encontra refúgio nos livros durante a Segunda Guerra.', 6, '2023-04-25', 'Português', 480, 'menina_livros.jpg'),
('O Caçador de Pipas', '9788533603150', 3, 2003, 'Drama sobre amizade e redenção entre dois amigos no Afeganistão.', 5, '2023-02-28', 'Português', 372, 'cacador_pipas.jpg'),
('O Hobbit', '9788535902777', 4, 1937, 'Aventura de Bilbo Bolseiro em sua jornada para recuperar o tesouro guardado pelo dragão Smaug.', 7, '2023-05-05', 'Português', 336, 'hobbit.jpg'),
('Crime e Castigo', '9788520923255', 2, 1866, 'Drama psicológico sobre um estudante que comete um assassinato e lida com a culpa.', 4, '2023-03-05', 'Português', 608, 'crime_castigo.jpg'),
('Orgulho e Preconceito', '9788535910667', 5, 1813, 'Romance sobre Elizabeth Bennet e Mr. Darcy e seus preconceitos na Inglaterra rural.', 6, '2023-04-10', 'Português', 424, 'orgulho_preconceito.jpg'),
('O Retrato de Dorian Gray', '9788572326975', 1, 1890, 'História de um homem cujo retrato envelhece enquanto ele permanece jovem.', 5, '2023-01-30', 'Português', 254, 'dorian_gray.jpg'),
('A Metamorfose', '9788533603151', 3, 1915, 'Gregor Samsa acorda transformado em um inseto monstruoso e reflete sobre sua condição.', 8, '2023-05-15', 'Português', 96, 'metamorfose.jpg'),
('O Sol é para Todos', '9788535902778', 2, 1960, 'Clássico sobre racismo e injustiça no sul dos EUA, narrado pela perspectiva de uma criança.', 7, '2023-02-05', 'Português', 364, 'sol_para_todos.jpg'),
('Fahrenheit 451', '9788520923256', 4, 1953, 'Distopia onde livros são proibidos e bombeiros queimam qualquer obra encontrada.', 5, '2023-03-30', 'Português', 216, 'fahrenheit.jpg'),
('A Culpa é das Estrelas', '9788535910668', 1, 2012, 'História de amor entre Hazel e Augustus, dois adolescentes com câncer.', 9, '2023-05-20', 'Português', 288, 'culpa_estrelas.jpg'),
('O Poder do Hábito', '9788575421265', 2, 2012, 'Análise científica sobre como os hábitos funcionam e como podem ser transformados.', 6, '2023-04-30', 'Português', 408, 'poder_habito.jpg'),
('Sapiens: Uma Breve História da Humanidade', '9788533603152', 5, 2011, 'Panorama da evolução humana desde os primórdios até os dias atuais.', 7, '2023-03-15', 'Português', 464, 'sapiens.jpg'),
('Mais Esperto que o Diabo', '9788535902779', 3, 2011, 'Diálogo imaginário entre Napoleon Hill e o Diabo sobre superação de medos e limitações.', 5, '2023-02-10', 'Português', 240, 'mais_esperto.jpg'),
('Mindset: A Nova Psicologia do Sucesso', '9788520923257', 2, 2006, 'Sobre como nossa mentalidade pode influenciar nosso sucesso e desenvolvimento.', 6, '2023-05-25', 'Português', 336, 'mindset.jpg'),
('O Milagre da Manhã', '9788535910669', 1, 2012, 'Método para transformar sua vida começando o dia com atividades que impulsionam o sucesso.', 8, '2023-04-05', 'Português', 192, 'milagre_manha.jpg'),
('O Segredo', '9788572326976', 4, 2006, 'Sobre a Lei da Atração e como nossos pensamentos influenciam nossa realidade.', 7, '2023-01-15', 'Português', 216, 'segredo.jpg'),
('O Homem Mais Rico da Babilônia', '9788533603153', 6, 1926, 'Parábolas sobre como alcançar sucesso financeiro e prosperidade.', 9, '2023-05-30', 'Português', 160, 'homem_rico.jpg'),
('Pai Rico, Pai Pobre', '9788535902780', 3, 1997, 'Sobre educação financeira e a diferença entre ativos e passivos.', 8, '2023-03-20', 'Português', 336, 'pai_rico.jpg'),
('Os 7 Hábitos das Pessoas Altamente Eficazes', '9788520923258', 5, 1989, 'Princípios para desenvolvimento pessoal e profissional.', 7, '2023-02-15', 'Português', 432, '7_habitos.jpg'),
('A Arte da Guerra', '9788535910670', 10, 500, 'Tratado militar chinês com estratégias aplicáveis a diversos aspectos da vida.', 10, '2023-04-20', 'Português', 96, 'arte_guerra.jpg'),
('O Monge e o Executivo', '9788575421266', 2, 1998, 'História sobre liderança servidora e princípios para uma vida mais significativa.', 6, '2023-05-10', 'Português', 144, 'monge_executivo.jpg'),
('A Cabana', '9788533603154', 1, 2007, 'História de um homem que encontra respostas sobre fé e perdão em um encontro com Deus.', 8, '2023-03-25', 'Português', 240, 'cabana.jpg'),
('O Poder do Agora', '9788535902781', 3, 1997, 'Guia espiritual sobre a importância de viver no momento presente.', 5, '2023-01-30', 'Português', 192, 'poder_agora.jpg'),
('O Pequeno Livro do Investimento', '9788520923259', 1, 2012, 'Princípios básicos para investir no mercado de ações com sabedoria.', 7, '2023-05-15', 'Português', 224, 'pequeno_investimento.jpg'),
('A Sutil Arte de Ligar o F*da-se', '9788535910671', 2, 2016, 'Abordagem irreverente sobre como lidar com as adversidades da vida.', 9, '2023-04-25', 'Português', 224, 'sutil_arte.jpg'),
('O Código da Vinci', '9788572326977', 4, 2003, 'Thriller sobre símbolos ocultos em obras de arte e um segredo milenar.', 6, '2023-02-20', 'Português', 592, 'codigo_da_vinci.jpg'),
('Inferno', '9788533603155', 1, 2013, 'Aventura de Robert Langdon em Florença para decifrar um mistério relacionado a Dante.', 5, '2023-05-20', 'Português', 512, 'inferno.jpg'),
('Anjos e Demônios', '9788535902782', 3, 2000, 'Primeira aventura de Langdon envolvendo a antiga sociedade dos Illuminati.', 7, '2023-03-10', 'Português', 608, 'anjos_demonios.jpg'),
('O Símbolo Perdido', '9788520923260', 2, 2009, 'Mistério envolvendo maçonaria e símbolos ocultos em Washington.', 6, '2023-04-15', 'Português', 528, 'simbolo_perdido.jpg'),
('Origem', '9788535910672', 1, 2017, 'Langdon investiga uma descoberta científica que pode mudar a visão da humanidade.', 8, '2023-01-25', 'Português', 480, 'origem.jpg'),
('O Silmarillion', '9788575421267', 2, 1977, 'História da criação da Terra-média e das primeiras eras do mundo de Tolkien.', 4, '2023-05-25', 'Português', 480, 'silmarillion.jpg'),
('O Guia do Mochileiro das Galáxias', '9788533603156', 5, 1979, 'Comédia de ficção científica sobre Arthur Dent e sua viagem pelo universo.', 9, '2023-02-28', 'Português', 208, 'guia_mochileiro.jpg'),
('Fundação', '9788535902783', 3, 1951, 'Clássico da ficção científica sobre o psicohistoriador Hari Seldon e seu plano para salvar a civilização.', 5, '2023-04-30', 'Português', 320, 'fundacao.jpg'),
('Neuromancer', '9788520923261', 1, 1984, 'Obra seminal do cyberpunk sobre um hacker e uma inteligência artificial.', 6, '2023-03-05', 'Português', 320, 'neuromancer.jpg'),
('Duna', '9788535910673', 2, 1965, 'Épico de ficção científica sobre o planeta Arrakis e a substância melange.', 7, '2023-05-10', 'Português', 680, 'duna.jpg'),
('O Fim da Eternidade', '9788572326978', 1, 1955, 'Sobre uma organização que controla o tempo e altera a história para o bem da humanidade.', 5, '2023-02-15', 'Português', 256, 'fim_eternidade.jpg'),
('Eu, Robô', '9788533603157', 4, 1950, 'Coletânea de contos que estabelecem as Três Leis da Robótica.', 8, '2023-04-20', 'Português', 320, 'eu_robo.jpg'),
('O Problema dos Três Corpos', '9788535902784', 1, 2008, 'Primeiro livro da trilogia sobre um jogo de realidade virtual e uma invasão alienígena.', 6, '2023-01-20', 'Português', 400, 'tres_corpos.jpg'),
('A Mão Esquerda da Escuridão', '9788520923262', 2, 1969, 'Sobre um enviado humano a um planeta onde os habitantes não têm gênero fixo.', 4, '2023-05-15', 'Português', 352, 'mao_esquerda.jpg'),
('O Conto da Aia', '9788535910674', 3, 1985, 'Distopia sobre uma sociedade teocrática onde as mulheres são controladas pelo estado.', 7, '2023-03-25', 'Português', 368, 'conto_aia.jpg'),
('Admirável Mundo Novo', '9788575421268', 5, 1932, 'Clássico distópico sobre uma sociedade futurista altamente controlada.', 6, '2023-02-10', 'Português', 288, 'admirável_mundo.jpg'),
('Laranja Mecânica', '9788533603158', 2, 1962, 'Sobre a violência juvenil e tentativas de controle comportamental em uma sociedade futurista.', 5, '2023-05-20', 'Português', 192, 'laranja_mecanica.jpg'),
('Flores para Algernon', '9788535902785', 1, 1966, 'Sobre um homem com deficiência intelectual que passa por um experimento para aumentar sua inteligência.', 8, '2023-04-05', 'Português', 288, 'flores_algernon.jpg'),
('O Jogo do Exterminador', '9788520923263', 2, 1985, 'Sobre crianças superdotadas treinadas em uma escola militar para uma guerra futura.', 6, '2023-01-15', 'Português', 384, 'jogo_exterminador.jpg'),
('O Homem do Castelo Alto', '9788535910675', 1, 1962, 'História alternativa onde os países do Eixo venceram a Segunda Guerra Mundial.', 5, '2023-05-25', 'Português', 272, 'homem_castelo.jpg'),
('Kindred: Laços de Sangue', '9788572326979', 1, 1979, 'Sobre uma mulher negra que viaja no tempo para uma plantação escravagista.', 7, '2023-03-10', 'Português', 304, 'kindred.jpg'),
('O Círculo', '9788533603159', 2, 2013, 'Sobre uma empresa de tecnologia que busca transparência total e seus perigos.', 6, '2023-02-20', 'Português', 496, 'circulo.jpg'),
('Ready Player One', '9788535902786', 1, 2011, 'Aventura em um mundo virtual onde o herói busca um easter egg que dá controle sobre o OASIS.', 8, '2023-05-30', 'Português', 384, 'ready_player.jpg'),
('Snow Crash', '9788520923264', 2, 1992, 'Cyberpunk sobre um entregador de pizza e hacker que descobre uma ameaça virtual.', 5, '2023-04-15', 'Português', 440, 'snow_crash.jpg');

INSERT INTO PLANO (plan_nome, plan_valor, plan_duracao, plan_descricao, plan_limite_emp) VALUES 
('Básico', 29.90, 'Mensal', 'Acesso a 2 livros por vez, prazo de 15 dias para empréstimos', 2),
('Padrão', 49.90, 'Mensal', 'Acesso a 4 livros por vez, prazo de 21 dias para empréstimos', 4),
('Premium', 79.90, 'Mensal', 'Acesso a 6 livros por vez, prazo de 30 dias para empréstimos, reservas prioritárias', 6),
('Família', 119.90, 'Mensal', 'Para até 4 membros da mesma família, 8 livros no total, prazo de 30 dias', 8),
('Anual Básico', 299.00, 'Anual', 'Plano básico com desconto para pagamento anual', 2);

INSERT INTO USUARIO (user_nome, user_cpf, user_email, user_telefone, user_senha, user_login, user_dataAdmissao, user_status, fk_tipoUser) VALUES 
('João Silva', '123.456.789-00', 'joao.silva@biblioteca.com', '(11) 98765-4321', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'joao.silva', '2020-05-15', 'Ativo', 1),
('Maria Santos', '234.567.890-11', 'maria.santos@biblioteca.com', '(11) 98765-4322', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'maria.santos', '2021-02-10', 'Ativo', 2),
('Carlos Oliveira', '345.678.901-22', 'carlos.oliveira@biblioteca.com', '(11) 98765-4323', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'carlos.oliveira', '2022-01-20', 'Ativo', 3),
('Ana Pereira', '456.789.012-33', 'ana.pereira@biblioteca.com', '(11) 98765-4324', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ana.pereira', '2021-07-05', 'Ativo', 2),
('Pedro Costa', '567.890.123-44', 'pedro.costa@biblioteca.com', '(11) 98765-4325', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pedro.costa', '2022-03-18', 'Ativo', 3);

INSERT INTO MEMBRO (mem_nome, mem_cpf, mem_senha, mem_email, mem_telefone, mem_dataInscricao, mem_status, fk_plan) VALUES 
('Marcos Andrade', '678.901.234-55', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'marcos.andrade@email.com', '(11) 98765-4326', '2023-01-10', 'Ativo', 1),
('Juliana Ferreira', '789.012.345-66', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'juliana.ferreira@email.com', '(11) 98765-4327', '2023-02-15', 'Ativo', 2),
('Ricardo Gomes', '890.123.456-77', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ricardo.gomes@email.com', '(11) 98765-4328', '2023-01-25', 'Ativo', 3),
('Fernanda Lima', '901.234.567-88', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'fernanda.lima@email.com', '(11) 98765-4329', '2022-12-05', 'Ativo', 4),
('Lucas Barbosa', '012.345.678-99', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'lucas.barbosa@email.com', '(11) 98765-4330', '2023-03-01', 'Ativo', 1),
('Patrícia Souza', '123.456.789-01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patricia.souza@email.com', '(11) 98765-4331', '2023-02-20', 'Ativo', 2),
('Gustavo Martins', '234.567.890-12', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'gustavo.martins@email.com', '(11) 98765-4332', '2022-11-15', 'Ativo', 3),
('Amanda Costa', '345.678.901-23', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'amanda.costa@email.com', '(11) 98765-4333', '2023-01-05', 'Ativo', 4),
('Rodrigo Alves', '456.789.012-34', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'rodrigo.alves@email.com', '(11) 98765-4334', '2022-10-10', 'Ativo', 1),
('Tatiane Ribeiro', '567.890.123-45', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tatiane.ribeiro@email.com', '(11) 98765-4335', '2023-03-15', 'Ativo', 2),
('Diego Oliveira', '678.901.234-56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'diego.oliveira@email.com', '(11) 98765-4336', '2022-09-20', 'Ativo', 3),
('Camila Santos', '789.012.345-67', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'camila.santos@email.com', '(11) 98765-4337', '2023-02-01', 'Ativo', 4),
('Bruno Pereira', '890.123.456-78', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'bruno.pereira@email.com', '(11) 98765-4338', '2022-08-15', 'Ativo', 1),
('Vanessa Silva', '901.234.567-89', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'vanessa.silva@email.com', '(11) 98765-4339', '2023-01-15', 'Ativo', 2),
('Felipe Cardoso', '012.345.678-90', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'felipe.cardoso@email.com', '(11) 98765-4340', '2022-07-10', 'Ativo', 3),
('Laura Mendes', '123.456.789-02', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'laura.mendes@email.com', '(11) 98765-4341', '2023-03-10', 'Ativo', 4),
('Roberto Nunes', '234.567.890-13', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'roberto.nunes@email.com', '(11) 98765-4342', '2022-06-05', 'Ativo', 1),
('Cristina Almeida', '345.678.901-24', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cristina.almeida@email.com', '(11) 98765-4343', '2023-02-05', 'Ativo', 2),
('Daniel Rocha', '456.789.012-35', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'daniel.rocha@email.com', '(11) 98765-4344', '2022-05-01', 'Ativo', 3),
('Sandra Vieira', '567.890.123-46', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'sandra.vieira@email.com', '(11) 98765-4345', '2023-01-20', 'Ativo', 4),
('Eduardo Lopes', '678.901.234-57', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'eduardo.lopes@email.com', '(11) 98765-4346', '2022-04-15', 'Suspenso', 1),
('Márcia Fernandes', '789.012.345-68', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'marcia.fernandes@email.com', '(11) 98765-4347', '2023-03-05', 'Ativo', 2),
('Alexandre Cunha', '890.123.456-79', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'alexandre.cunha@email.com', '(11) 98765-4348', '2022-03-10', 'Ativo', 3),
('Beatriz Dias', '901.234.567-80', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'beatriz.dias@email.com', '(11) 98765-4349', '2023-02-10', 'Ativo', 4),
('Rafael Teixeira', '012.345.678-91', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'rafael.teixeira@email.com', '(11) 98765-4350', '2022-02-05', 'Ativo', 1);

INSERT INTO EMPRESTIMO (emp_prazo, emp_dataEmp, emp_dataDev, emp_dataDevReal, emp_status, fk_mem, fk_user, fk_liv) VALUES 
(15, '2023-05-01', '2023-05-16', '2023-05-15', 'Finalizado', 1, 2, 5),
(21, '2023-05-05', '2023-05-26', NULL, 'Empréstimo Ativo', 2, 2, 29),
(15, '2023-04-20', '2023-05-05', '2023-05-10', 'Finalizado', 3, 4, 22),
(30, '2023-05-10', '2023-06-09', NULL, 'Empréstimo Ativo', 4, 4, 34),
(21, '2023-04-15', '2023-05-06', '2023-05-20', 'Empréstimo Atrasado', 5, 2, 12),
(15, '2023-05-15', '2023-05-30', NULL, 'Empréstimo Ativo', 6, 4, 7),
(30, '2023-04-01', '2023-05-01', '2023-04-28', 'Finalizado', 7, 2, 44),
(21, '2023-05-12', '2023-06-02', NULL, 'Empréstimo Ativo', 8, 4, 3),
(15, '2023-04-25', '2023-05-10', '2023-05-05', 'Finalizado', 9, 2, 43),
(30, '2023-05-08', '2023-06-07', NULL, 'Empréstimo Ativo', 10, 4, 17);

INSERT INTO RESERVA (res_prazo, res_dataMarcada, res_dataVencimento, res_status, fk_mem, fk_liv, fk_user) VALUES 
(7, '2023-05-01', '2023-05-08', 'Finalizada', 1, 5, 2),
(7, '2023-05-10', '2023-05-17', 'Aberta', 2, 10, 4),
(7, '2023-04-25', '2023-05-02', 'Cancelada', 3, 15, 2),
(7, '2023-05-15', '2023-05-22', 'Aberta', 4, 20, 4),
(7, '2023-05-05', '2023-05-12', 'Finalizada', 5, 25, 2),
(7, '2023-05-12', '2023-05-19', 'Aberta', 6, 30, 4),
(7, '2023-04-20', '2023-04-27', 'Finalizada', 7, 35, 2),
(7, '2023-05-18', '2023-05-25', 'Aberta', 8, 40, 4),
(7, '2023-05-03', '2023-05-10', 'Finalizada', 9, 45, 2),
(7, '2023-05-20', '2023-05-27', 'Aberta', 10, 50, 4);

INSERT INTO MULTA (mul_valor, mul_qtdDias, mul_status, fk_mem, fk_emp) VALUES 
(7.50, 5, 'Finalizada', 5, 5),
(15.00, 10, 'Aberta', 7, 7),
(3.00, 2, 'Finalizada', 9, 9),
(12.00, 8, 'Aberta', 11, 4),
(6.00, 4, 'Finalizada', 13, 2);

INSERT INTO PAG_PLANO (pag_plan_preco, pag_plan_valorPag, pag_plan_dataPag, pag_plan_dataVen, pag_plan_status, fk_mem, fk_plan) VALUES 
(29.90, 29.90, '2023-05-01', '2023-06-01', 'Em dia', 1, 1),
(49.90, 49.90, '2023-05-05', '2023-06-05', 'Em dia', 2, 2),
(79.90, 79.90, '2023-05-10', '2023-06-10', 'Em dia', 3, 3),
(119.90, 119.90, '2023-05-15', '2023-06-15', 'Em dia', 4, 4),
(29.90, 29.90, '2023-04-01', '2023-05-01', 'Atrasado', 5, 1),
(49.90, 49.90, '2023-05-12', '2023-06-12', 'Em dia', 6, 2),
(79.90, 79.90, '2023-05-18', '2023-06-18', 'Em dia', 7, 3),
(119.90, 119.90, '2023-05-20', '2023-06-20', 'Em dia', 8, 4),
(29.90, 29.90, '2023-05-03', '2023-06-03', 'Em dia', 9, 1),
(49.90, 49.90, '2023-05-08', '2023-06-08', 'Em dia', 10, 2);

INSERT INTO REMESSA (rem_data, rem_qtd, fk_forn, fk_liv, fk_user) VALUES 
('2023-01-15', 10, 1, 1, 3),
('2023-02-20', 5, 2, 5, 3),
('2023-03-10', 8, 3, 10, 5),
('2023-04-05', 12, 4, 15, 5),
('2023-05-01', 6, 5, 20, 3),
('2023-01-25', 7, 6, 25, 3),
('2023-02-15', 9, 7, 30, 5),
('2023-03-20', 4, 8, 35, 5),
('2023-04-15', 11, 9, 40, 3),
('2023-05-10', 8, 10, 45, 3);

INSERT INTO FORN_LIV (fk_liv, fk_forn) VALUES 
(1, 1), (2, 1), (3, 2), (4, 2), (5, 3),
(6, 3), (7, 4), (8, 4), (9, 5), (10, 5),
(11, 6), (12, 6), (13, 7), (14, 7), (15, 8),
(16, 8), (17, 9), (18, 9), (19, 10), (20, 10),
(21, 1), (22, 2), (23, 3), (24, 4), (25, 5),
(26, 6), (27, 7), (28, 8), (29, 9), (30, 10);

INSERT INTO CAT_LIV (fk_liv, fk_cat) VALUES 
(1, 3), (2, 3), (3, 4), (4, 3), (5, 3),
(6, 3), (7, 3), (8, 3), (9, 8), (10, 2),
(11, 1), (12, 2), (13, 3), (14, 9), (15, 4),
(16, 1), (17, 3), (18, 6), (19, 8), (20, 1),
(21, 8), (22, 7), (23, 1), (24, 2), (25, 5),
(26, 1), (27, 1), (28, 1), (29, 1), (30, 2),
(31, 3), (32, 3), (33, 1), (34, 1), (35, 1),
(36, 1), (37, 1), (38, 1), (39, 1), (40, 1),
(41, 1), (42, 1), (43, 1), (44, 1), (45, 1),
(46, 1), (47, 1), (48, 1), (49, 1), (50, 1),
(51, 1), (52, 1), (53, 1), (54, 1), (55, 1),
(56, 1), (57, 1), (58, 1), (59, 1), (60, 1);

INSERT INTO AUT_LIV (fk_liv, fk_aut) VALUES 
(1, 3), (2, 3), (3, 3), (4, 3), (5, 2),
(6, 7), (7, 8), (8, 7), (9, 11), (10, 13),
(11, 15), (12, 14), (13, 21), (14, 22), (15, 16),
(16, 17), (17, 11), (18, 12), (19, 23), (20, 24),
(21, 25), (22, 26), (23, 27), (24, 28), (25, 15),
(26, 29), (27, 30), (28, 11), (29, 12), (30, 13),
(31, 14), (32, 15), (33, 16), (34, 17), (35, 18),
(36, 19), (37, 20), (38, 21), (39, 22), (40, 23),
(41, 24), (42, 25), (43, 26), (44, 27), (45, 28),
(46, 29), (47, 30), (48, 1), (49, 2), (50, 3),
(51, 4), (52, 5), (53, 6), (54, 7), (55, 8),
(56, 9), (57, 10), (58, 11), (59, 12), (60, 13);

