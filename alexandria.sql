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
 liv_capa VARCHAR(255),
 fk_aut INT not null,
 fk_cat INT not null,
 FOREIGN KEY(fk_aut) references AUTOR(pk_aut),
 FOREIGN KEY(fk_cat) references CATEGORIA(pk_cat)
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
-- Insert data into TIPO_USUARIO
INSERT INTO TIPO_USUARIO (tipo_user_nome, tipo_user_descricao) VALUES
('Administrador', 'Acesso total ao sistema'),
('Bibliotecário', 'Pode gerenciar empréstimos, reservas e livros'),
('Assistente', 'Acesso limitado para auxiliar nas operações');

-- Insert data into CATEGORIA (cat_nome) VALUES
INSERT INTO CATEGORIA (cat_nome) VALUES
('Ficção Científica'),
('Fantasia'),
('Romance'),
('Terror'),
('Mistério'),
('Biografia'),
('História'),
('Ciência'),
('Tecnologia'),
('Autoajuda'),
('Negócios'),
('Arte'),
('Poesia'),
('Infantil'),
('Juvenil');

-- Insert data into AUTOR
INSERT INTO AUTOR (aut_nome, aut_data_nascimento) VALUES
('J.K. Rowling', '1965-07-31'),
('George R.R. Martin', '1948-09-20'),
('Stephen King', '1947-09-21'),
('Agatha Christie', '1890-09-15'),
('J.R.R. Tolkien', '1892-01-03'),
('Isaac Asimov', '1920-01-02'),
('Machado de Assis', '1839-06-21'),
('Clarice Lispector', '1920-12-10'),
('Neil Gaiman', '1960-11-10'),
('Yuval Noah Harari', '1976-02-24'),
('Dan Brown', '1964-06-22'),
('Paulo Coelho', '1947-08-24'),
('Jane Austen', '1775-12-16'),
('Fiódor Dostoiévski', '1821-11-11'),
('Gabriel García Márquez', '1927-03-06'),
('Umberto Eco', '1932-01-05'),  -- Este será pk_aut = 16
('George Orwell', '1903-06-25'),  -- Este será pk_aut = 17
('Antoine de Saint-Exupéry', '1900-06-29');

-- Insert data into FORNECEDOR
INSERT INTO FORNECEDOR (forn_nome, forn_cnpj, forn_telefone, forn_email, forn_endereco) VALUES
('Editora Arqueiro', '12.345.678/0001-01', '(11) 1234-5678', 'contato@arqueiro.com.br', 'Rua dos Livros, 100 - São Paulo, SP'),
('Editora Rocco', '23.456.789/0001-02', '(21) 2345-6789', 'vendas@rocco.com.br', 'Av. Literária, 200 - Rio de Janeiro, RJ'),
('Companhia das Letras', '34.567.890/0001-03', '(31) 3456-7890', 'atendimento@companhiadasletras.com.br', 'Praça das Letras, 300 - Belo Horizonte, MG'),
('Editora Intrínseca', '45.678.901/0001-04', '(41) 4567-8901', 'sac@intrinseca.com.br', 'Alameda dos Autores, 400 - Curitiba, PR'),
('Editora Sextante', '56.789.012/0001-05', '(51) 5678-9012', 'contato@sextante.com.br', 'Travessa dos Best-sellers, 500 - Porto Alegre, RS');

-- Insert data into PLANO
INSERT INTO PLANO (plan_nome, plan_valor, plan_duracao, plan_descricao, plan_limite_emp) VALUES
('Básico', 19.90, 'Mensal', 'Acesso a todos os livros com limite de 2 empréstimos simultâneos', 2),
('Intermediário', 49.90, 'Trimestral', 'Acesso a todos os livros com limite de 3 empréstimos simultâneos', 3),
('Avançado', 89.90, 'Semestral', 'Acesso a todos os livros com limite de 5 empréstimos simultâneos', 5),
('Premium', 159.90, 'Anual', 'Acesso a todos os livros com limite de 7 empréstimos simultâneos e prioridade em reservas', 7),
('Estudante', 9.90, 'Mensal', 'Plano especial para estudantes com limite de 2 empréstimos simultâneos', 2);

-- Insert data into USUARIO
INSERT INTO USUARIO (user_nome, user_cpf, user_email, user_telefone, user_senha, user_login, user_dataAdmissao, user_status, fk_tipoUser) VALUES
('Maria Silva', '111.222.333-44', 'maria.silva@alexandria.com', '(11) 91234-5678', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'msilva', '2020-01-15', 'Ativo', 1),
('João Santos', '222.333.444-55', 'joao.santos@alexandria.com', '(21) 92345-6789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'jsantos', '2020-03-20', 'Ativo', 2),
('Ana Oliveira', '333.444.555-66', 'ana.oliveira@alexandria.com', '(31) 93456-7890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'aoliveira', '2021-05-10', 'Ativo', 2),
('Carlos Pereira', '444.555.666-77', 'carlos.pereira@alexandria.com', '(41) 94567-8901', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cpereira', '2021-07-25', 'Ativo', 3),
('Juliana Costa', '555.666.777-88', 'juliana.costa@alexandria.com', '(51) 95678-9012', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'jcosta', '2022-02-18', 'Ativo', 3);

-- Insert data into MEMBRO
INSERT INTO MEMBRO (mem_nome, mem_cpf, mem_senha, mem_email, mem_telefone, mem_dataInscricao, mem_status, fk_plan) VALUES
('Pedro Alves', '666.777.888-99', 'membro123', 'pedro.alves@gmail.com', '(11) 96666-6666', '2023-01-10', 'Ativo', 1),
('Mariana Rocha', '777.888.999-00', 'membro456', 'mariana.rocha@hotmail.com', '(21) 97777-7777', '2023-02-15', 'Ativo', 2),
('Lucas Mendes', '888.999.000-11', 'membro789', 'lucas.mendes@yahoo.com', '(31) 98888-8888', '2023-03-20', 'Ativo', 3),
('Fernanda Lima', '999.000.111-22', 'membro012', 'fernanda.lima@gmail.com', '(41) 99999-9999', '2023-04-25', 'Ativo', 4),
('Ricardo Sousa', '000.111.222-33', 'membro345', 'ricardo.sousa@hotmail.com', '(51) 90000-0000', '2023-05-30', 'Ativo', 5),
('Camila Castro', '111.222.333-44', 'membro678', 'camila.castro@gmail.com', '(11) 91111-1111', '2023-06-05', 'Ativo', 1),
('Gustavo Nunes', '222.333.444-55', 'membro901', 'gustavo.nunes@yahoo.com', '(21) 92222-2222', '2023-07-10', 'Ativo', 2),
('Patrícia Freitas', '333.444.555-66', 'membro234', 'patricia.freitas@gmail.com', '(31) 93333-3333', '2023-08-15', 'Ativo', 3),
('Roberto Andrade', '444.555.666-77', 'membro567', 'roberto.andrade@hotmail.com', '(41) 94444-4444', '2023-09-20', 'Ativo', 4),
('Tatiane Moraes', '555.666.777-88', 'membro890', 'tatiane.moraes@yahoo.com', '(51) 95555-5555', '2023-10-25', 'Ativo', 5);

-- Insert data into LIVRO (20+ books)
INSERT INTO LIVRO (liv_titulo, liv_isbn, liv_edicao, liv_anoPublicacao, liv_sinopse, liv_estoque, liv_dataAlteracaoEstoque, liv_idioma, liv_num_paginas, liv_capa, fk_aut, fk_cat) VALUES
('Harry Potter e a Pedra Filosofal', '978-85-325-1108-0', 1, 2000, 'O primeiro livro da série Harry Potter', 10, '2023-01-05', 'Português', 264, 'harry_potter_1.jpg', 1, 2),
('Harry Potter e a Câmara Secreta', '978-85-325-1109-7', 1, 2000, 'O segundo livro da série Harry Potter', 8, '2023-01-10', 'Português', 288, 'harry_potter_2.jpg', 1, 2),
('A Guerra dos Tronos', '978-85-209-2324-1', 1, 2011, 'O primeiro livro da série As Crônicas de Gelo e Fogo', 6, '2023-02-15', 'Português', 592, 'guerra_tronos.jpg', 2, 2),
('O Iluminado', '978-85-01-05057-1', 1, 2012, 'Um clássico do terror sobre um hotel mal-assombrado', 5, '2023-03-20', 'Português', 464, 'iluminado.jpg', 3, 4),
('Assassinato no Expresso do Oriente', '978-85-325-3010-4', 1, 2016, 'Um dos mais famosos casos de Hercule Poirot', 7, '2023-04-25', 'Português', 256, 'expresso_oriente.jpg', 4, 5),
('O Senhor dos Anéis: A Sociedade do Anel', '978-85-359-1547-7', 1, 2019, 'O primeiro volume da trilogia O Senhor dos Anéis', 9, '2023-05-30', 'Português', 576, 'sociedade_anel.jpg', 5, 2),
('Fundação', '978-85-359-2175-1', 1, 2009, 'O início da saga da Fundação de Isaac Asimov', 4, '2023-06-05', 'Português', 320, 'fundacao.jpg', 6, 1),
('Dom Casmurro', '978-85-06-07090-4', 1, 2016, 'Um clássico da literatura brasileira', 12, '2023-07-10', 'Português', 256, 'dom_casmurro.jpg', 7, 3),
('A Hora da Estrela', '978-85-325-3012-8', 1, 2015, 'A última obra de Clarice Lispector', 6, '2023-08-15', 'Português', 96, 'hora_estrela.jpg', 8, 3),
('Deuses Americanos', '978-85-325-2409-7', 1, 2017, 'Uma batalha entre deuses antigos e novos', 5, '2023-09-20', 'Português', 592, 'deuses_americanos.jpg', 9, 2),
('Sapiens: Uma Breve História da Humanidade', '978-85-254-3273-0', 1, 2015, 'Uma visão abrangente da história humana', 8, '2023-10-25', 'Português', 464, 'sapiens.jpg', 10, 7),
('Origem', '978-85-8041-566-9', 1, 2017, 'Um novo thriller de Robert Langdon', 7, '2023-11-30', 'Português', 480, 'origem.jpg', 11, 5),
('O Alquimista', '978-85-254-1716-4', 1, 2013, 'A famosa fábula sobre seguir seus sonhos', 15, '2023-12-05', 'Português', 208, 'alquimista.jpg', 12, 10),
('Orgulho e Preconceito', '978-85-7232-227-8', 1, 2008, 'Um clássico romance de Jane Austen', 10, '2024-01-10', 'Português', 424, 'orgulho_preconceito.jpg', 13, 3),
('Crime e Castigo', '978-85-7232-744-0', 1, 2015, 'Uma obra-prima da literatura russa', 6, '2024-02-15', 'Português', 608, 'crime_castigo.jpg', 14, 3),
('Cem Anos de Solidão', '978-85-010-7019-9', 1, 2014, 'O grande romance de Gabriel García Márquez', 8, '2024-03-20', 'Português', 448, 'cem_anos.jpg', 15, 3),
('O Hobbit', '978-85-359-1546-0', 1, 2019, 'A aventura que precede O Senhor dos Anéis', 7, '2024-04-25', 'Português', 336, 'hobbit.jpg', 5, 2),
('It: A Coisa', '978-85-01-05056-4', 1, 2014, 'Um dos mais assustadores romances de Stephen King', 5, '2024-05-30', 'Português', 1104, 'it.jpg', 3, 4),
('O Nome da Rosa', '978-85-325-3011-1', 1, 2010, 'Um mistério medieval em um mosteiro', 6, '2024-06-05', 'Português', 496, 'nome_rosa.jpg', 16, 5),
('1984', '978-85-221-0616-9', 1, 2009, 'Um clássico distópico de George Orwell', 9, '2024-07-10', 'Português', 416, '1984.jpg', 17, 1),
('A Revolução dos Bichos', '978-85-221-0617-6', 1, 2007, 'Uma fábula satírica sobre poder', 11, '2024-08-15', 'Português', 152, 'revolucao_bichos.jpg', 17, 1),
('O Pequeno Príncipe', '978-85-7232-344-2', 1, 2009, 'Um clássico da literatura mundial', 20, '2024-09-20', 'Português', 96, 'pequeno_principe.jpg', 18, 14);

-- Insert data into EMPRESTIMO (20+ loans)
INSERT INTO EMPRESTIMO (emp_prazo, emp_dataEmp, emp_dataDev, emp_dataDevReal, emp_status, fk_mem, fk_user, fk_liv) VALUES
(14, '2023-01-15', '2023-01-29', '2023-01-28', 'Finalizado', 1, 2, 1),
(14, '2023-01-20', '2023-02-03', '2023-02-03', 'Finalizado', 2, 2, 3),
(14, '2023-02-10', '2023-02-24', '2023-02-25', 'Finalizado', 3, 3, 5),
(14, '2023-02-15', '2023-03-01', '2023-03-01', 'Finalizado', 4, 3, 7),
(14, '2023-03-05', '2023-03-19', '2023-03-18', 'Finalizado', 5, 2, 9),
(14, '2023-03-10', '2023-03-24', '2023-03-24', 'Finalizado', 6, 4, 11),
(14, '2023-04-01', '2023-04-15', '2023-04-16', 'Finalizado', 7, 4, 13),
(14, '2023-04-05', '2023-04-19', '2023-04-19', 'Finalizado', 8, 3, 15),
(14, '2023-05-01', '2023-05-15', '2023-05-14', 'Finalizado', 9, 2, 17),
(14, '2023-05-10', '2023-05-24', '2023-05-25', 'Finalizado', 10, 5, 19),
(14, '2023-06-01', '2023-06-15', '2023-06-15', 'Finalizado', 1, 5, 2),
(14, '2023-06-05', '2023-06-19', '2023-06-20', 'Finalizado', 2, 4, 4),
(14, '2023-07-01', '2023-07-15', '2023-07-15', 'Finalizado', 3, 3, 6),
(14, '2023-07-10', '2023-07-24', '2023-07-23', 'Finalizado', 4, 2, 8),
(14, '2023-08-01', '2023-08-15', '2023-08-16', 'Finalizado', 5, 5, 10),
(14, '2023-08-05', '2023-08-19', '2023-08-19', 'Finalizado', 6, 4, 12),
(14, '2023-09-01', '2023-09-15', '2023-09-14', 'Finalizado', 7, 3, 14),
(14, '2023-09-10', '2023-09-24', '2023-09-25', 'Finalizado', 8, 2, 16),
(14, '2023-10-01', '2023-10-15', '2023-10-15', 'Finalizado', 9, 5, 18),
(14, '2023-10-05', '2023-10-19', '2023-10-18', 'Finalizado', 10, 4, 20),
(14, '2023-11-01', '2023-11-15', NULL, 'Empréstimo Atrasado', 1, 3, 1),
(14, '2023-11-05', '2023-11-19', NULL, 'Empréstimo Ativo', 2, 2, 3),
(14, '2023-12-01', '2023-12-15', NULL, 'Empréstimo Ativo', 3, 5, 5),
(14, '2023-12-10', '2023-12-24', NULL, 'Empréstimo Ativo', 4, 4, 7);

-- Insert data into RESERVA
INSERT INTO RESERVA (res_prazo, res_dataMarcada, res_dataVencimento, res_dataFinalizada, res_status, fk_mem, fk_liv, fk_user) VALUES
(7, '2023-01-05', '2023-01-12', '2023-01-10', 'Finalizada', 1, 1, 2),
(7, '2023-02-10', '2023-02-17', '2023-02-15', 'Finalizada', 2, 3, 2),
(7, '2023-03-15', '2023-03-22', '2023-03-20', 'Finalizada', 3, 5, 3),
(7, '2023-04-20', '2023-04-27', '2023-04-25', 'Finalizada', 4, 7, 3),
(7, '2023-05-25', '2023-06-01', '2023-05-30', 'Finalizada', 5, 9, 2),
(7, '2023-06-30', '2023-07-07', NULL, 'Aberta', 6, 11, 4),
(7, '2023-08-05', '2023-08-12', NULL, 'Aberta', 7, 13, 4),
(7, '2023-09-10', '2023-09-17', '2023-09-15', 'Finalizada', 8, 15, 3),
(7, '2023-10-15', '2023-10-22', '2023-10-20', 'Finalizada', 9, 17, 2),
(7, '2023-11-20', '2023-11-27', NULL, 'Aberta', 10, 19, 5);

-- Insert data into MULTA
INSERT INTO MULTA (mul_valor, mul_qtdDias, mul_status, fk_mem, fk_emp) VALUES
(1.50, 1, 'Finalizada', 3, 3),
(3.00, 2, 'Finalizada', 7, 7),
(4.50, 3, 'Finalizada', 6, 2),
(1.50, 1, 'Finalizada', 8, 5),
(3.00, 2, 'Finalizada', 2, 2),
(22.50, 15, 'Aberta', 5, 6);

-- Insert data into PAG_PLANO
INSERT INTO PAG_PLANO (pag_plan_preco, pag_plan_valorPag, pag_plan_dataPag, pag_plan_dataVen, pag_plan_status, fk_mem, fk_plan) VALUES
(19.90, 19.90, '2023-01-05', '2023-02-05', 'Em dia', 1, 1),
(49.90, 49.90, '2023-01-10', '2023-04-10', 'Em dia', 2, 2),
(89.90, 89.90, '2023-01-15', '2023-07-15', 'Em dia', 3, 3),
(159.90, 159.90, '2023-01-20', '2024-01-20', 'Em dia', 4, 4),
(9.90, 9.90, '2023-01-25', '2023-02-25', 'Em dia', 5, 5),
(19.90, 19.90, '2023-02-05', '2023-03-05', 'Em dia', 6, 1),
(49.90, 49.90, '2023-02-10', '2023-05-10', 'Em dia', 7, 2),
(89.90, 89.90, '2023-02-15', '2023-08-15', 'Em dia', 8, 3),
(159.90, 159.90, '2023-02-20', '2024-02-20', 'Em dia', 9, 4),
(9.90, 9.90, '2023-02-25', '2023-03-25', 'Em dia', 10, 5);

-- Insert data into REMESSA
INSERT INTO REMESSA (rem_data, rem_qtd, fk_forn, fk_liv, fk_user) VALUES
('2023-01-05', 10, 1, 1, 1),
('2023-01-10', 8, 1, 2, 1),
('2023-02-15', 6, 2, 3, 2),
('2023-03-20', 5, 3, 4, 3),
('2023-04-25', 7, 4, 5, 4),
('2023-05-30', 9, 5, 6, 5),
('2023-06-05', 4, 1, 7, 1),
('2023-07-10', 12, 2, 8, 2),
('2023-08-15', 6, 3, 9, 3),
('2023-09-20', 5, 4, 10, 4);

-- Insert data into junction tables
-- FORN_LIV
INSERT INTO FORN_LIV (fk_liv, fk_forn) VALUES
(1, 1), (2, 1), (3, 2), (4, 3), (5, 4),
(6, 5), (7, 1), (8, 2), (9, 3), (10, 4),
(11, 5), (12, 1), (13, 2), (14, 3), (15, 4),
(16, 5), (17, 1), (18, 2), (19, 3), (20, 4);

-- CAT_LIV
INSERT INTO CAT_LIV (fk_liv, fk_cat) VALUES
(1, 2), (2, 2), (3, 2), (4, 4), (5, 5),
(6, 2), (7, 1), (8, 3), (9, 3), (10, 2),
(11, 7), (12, 5), (13, 10), (14, 3), (15, 3),
(16, 3), (17, 2), (18, 4), (19, 5), (20, 1);

-- AUT_LIV
INSERT INTO AUT_LIV (fk_liv, fk_aut) VALUES
(1, 1), (2, 1), (3, 2), (4, 3), (5, 4),
(6, 5), (7, 6), (8, 7), (9, 8), (10, 9),
(11, 10), (12, 11), (13, 12), (14, 13), (15, 14),
(16, 15), (17, 5), (18, 3), (19, 16), (20, 17);

