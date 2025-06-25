create database ALEXANDRIA;
use ALEXANDRIA;

CREATE TABLE CATEGORIA ( 
 pk_cat INT PRIMARY KEY auto_increment, 
 cat_nome VARCHAR(100) not null,
 cat_dataCriacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE AUTOR ( 
 pk_aut INT PRIMARY KEY auto_increment,
 aut_nome VARCHAR(100) not null,
 aut_dataNascimento DATE,
 aut_dataCriacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 fk_cat INT,
 FOREIGN KEY (fk_cat) REFERENCES categoria(pk_cat) on update cascade
);

CREATE TABLE FORNECEDOR ( 
 pk_forn INT PRIMARY KEY auto_increment,
 forn_nome VARCHAR(250) not null,
 forn_cnpj VARCHAR(18) unique,
 forn_telefone VARCHAR(16),
 forn_email VARCHAR(100),
 forn_endereco VARCHAR(250),
 forn_dataCriacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE LIVRO ( 
 pk_liv INT PRIMARY KEY auto_increment,
 liv_titulo VARCHAR(200) not null,
 liv_isbn VARCHAR(17) not null unique,
 liv_edicao INT,
 liv_anoPublicacao INT,
 liv_sinopse VARCHAR(3000),
 liv_estoque INT,
 liv_dataAlteracaoEstoque DATETIME,
 liv_dataCriacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 liv_idioma VARCHAR(30),
 liv_num_paginas INT,
 liv_capa VARCHAR(255),
 fk_aut INT not null,
 fk_cat INT not null,
 FOREIGN KEY(fk_aut) references AUTOR(pk_aut),
 FOREIGN KEY(fk_cat) references CATEGORIA(pk_cat)
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
 user_dataCriacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 user_foto VARCHAR(255),
 user_status ENUM('Ativo', 'Inativo'),
 user_tipoUser ENUM('Administrador', 'Secretaria', 'Almoxarife')
);

CREATE TABLE RECUPERA_SENHA (
pk_rs INT AUTO_INCREMENT PRIMARY KEY,
 rs_token VARCHAR(64) NOT NULL,
 rs_expiracao DATETIME NOT NULL,
 rs_usado TINYINT DEFAULT 0,
 rs_dataCriacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 fk_user INT NOT NULL,
 FOREIGN KEY (fk_user) REFERENCES usuario(pk_user)
);

CREATE TABLE MEMBRO (
 pk_mem INT PRIMARY KEY auto_increment,
 mem_nome VARCHAR(250) not null,
 mem_cpf VARCHAR(14) unique not null,
 mem_senha VARCHAR(250) not null,
 mem_email VARCHAR(250),
 mem_telefone VARCHAR(16),
 mem_dataCriacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 mem_status ENUM('Ativo', 'Suspenso')
 );

CREATE TABLE EMPRESTIMO (
 pk_emp INT PRIMARY KEY auto_increment,
 emp_prazo INT,
 emp_dataEmp DATE,
 emp_dataDev DATE,
 emp_dataDevReal DATE,
 emp_valorMultaDiaria DECIMAL(10,2) DEFAULT 1.50,
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

CREATE TABLE REMESSA (
 pk_rem INT PRIMARY KEY auto_increment, 
 rem_data DATE,
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
INSERT INTO CATEGORIA (cat_nome) VALUES 
('Ficção Científica'),
('Fantasia'),
('Romance'),
('Terror'),
('Biografia'),
('História'),
('Ciência'),
('Tecnologia'),
('Autoajuda'),
('Infantil');

INSERT INTO AUTOR (aut_nome, aut_dataNascimento, fk_cat) VALUES 
('Isaac Asimov', '1920-01-02', 1),
('J.R.R. Tolkien', '1892-01-03', 2),
('Jane Austen', '1775-12-16', 3),
('Stephen King', '1947-09-21', 4),
('Walter Isaacson', '1952-05-20', 5),
('Yuval Noah Harari', '1976-02-24', 6),
('Carl Sagan', '1934-11-09', 7),
('Bill Gates', '1955-10-28', 8),
('Dale Carnegie', '1888-11-24', 9),
('Monteiro Lobato', '1882-04-18', 10),
('Arthur C. Clarke', '1917-12-16', 1),
('George R.R. Martin', '1948-09-20', 2),
('Nicholas Sparks', '1965-12-31', 3),
('H.P. Lovecraft', '1890-08-20', 4),
('Michelle Obama', '1964-01-17', 5),
('Jared Diamond', '1937-09-10', 6),
('Neil deGrasse Tyson', '1958-10-05', 7),
('Elon Musk', '1971-06-28', 8),
('Napoleon Hill', '1883-10-26', 9),
('Ziraldo', '1932-10-24', 10);

INSERT INTO FORNECEDOR (forn_nome, forn_cnpj, forn_telefone, forn_email, forn_endereco) VALUES 
('Livraria Cultura', '12.345.678/0001-00', '(11) 1234-5678', 'contato@culturacultura.com.br', 'Av. Paulista, 123 - São Paulo/SP'),
('Saraiva', '23.456.789/0001-11', '(21) 2345-6789', 'contato@saraiva.com.br', 'Rua do Ouvidor, 98 - Rio de Janeiro/RJ'),
('Amazon Brasil', '34.567.890/0001-22', '(31) 3456-7890', 'contato@amazon.com.br', 'Av. Amazonas, 1000 - Belo Horizonte/MG'),
('Editora Abril', '45.678.901/0001-33', '(41) 4567-8901', 'contato@abril.com.br', 'Rua da Paz, 45 - Curitiba/PR'),
('Editora Globo', '56.789.012/0001-44', '(51) 5678-9012', 'contato@globo.com.br', 'Av. Ipiranga, 2000 - Porto Alegre/RS'),
('Livraria Leitura', '67.890.123/0001-55', '(61) 6789-0123', 'contato@leitura.com.br', 'SCS Quadra 8 - Brasília/DF'),
('Editora Record', '78.901.234/0001-66', '(71) 7890-1234', 'contato@record.com.br', 'Av. Sete de Setembro, 100 - Salvador/BA'),
('Companhia das Letras', '89.012.345/0001-77', '(81) 8901-2345', 'contato@companhiadasletras.com.br', 'Rua do Sol, 300 - Recife/PE'),
('Editora Intrínseca', '90.123.456/0001-88', '(85) 9012-3456', 'contato@intrinseca.com.br', 'Av. Santos Dumont, 1500 - Fortaleza/CE'),
('Editora Arqueiro', '01.234.567/0001-99', '(91) 0123-4567', 'contato@arqueiro.com.br', 'Travessa Frutuoso Guimarães, 65 - Belém/PA');

INSERT INTO LIVRO (liv_titulo, liv_isbn, liv_edicao, liv_anoPublicacao, liv_sinopse, liv_estoque, liv_idioma, liv_num_paginas, liv_capa, fk_aut, fk_cat) VALUES 
('Fundação', '978-85-359-0277-5', 1, 1951, 'Clássico da ficção científica sobre o declínio de um império galáctico', 10, 'Português', 320, 'capa_fundacao.jpg', 1, 1),
('O Senhor dos Anéis: A Sociedade do Anel', '978-85-359-0802-9', 3, 1954, 'Primeiro volume da trilogia épica sobre a Terra Média', 15, 'Português', 576, 'capa_sociedade_anel.jpg', 2, 2),
('Orgulho e Preconceito', '978-85-823-8001-2', 5, 1813, 'Romance clássico sobre Elizabeth Bennet e Mr. Darcy', 8, 'Português', 424, 'capa_orgulho.jpg', 3, 3),
('It: A Coisa', '978-85-01-10175-7', 1, 1986, 'História de terror sobre um grupo de amigos e um palhaço maligno', 12, 'Português', 1104, 'capa_it.jpg', 4, 4),
('Steve Jobs', '978-85-8057-134-3', 1, 2011, 'Biografia autorizada do cofundador da Apple', 7, 'Português', 656, 'capa_jobs.jpg', 5, 5),
('Sapiens: Uma Breve História da Humanidade', '978-85-254-3228-5', 10, 2011, 'Visão geral da história da humanidade', 20, 'Português', 464, 'capa_sapiens.jpg', 6, 6),
('Cosmos', '978-85-273-0085-5', 2, 1980, 'Exploração do universo e nosso lugar nele', 9, 'Português', 384, 'capa_cosmos.jpg', 7, 7),
('Como Evitar um Desastre Climático', '978-85-510-0274-8', 1, 2021, 'Soluções para a crise climática', 11, 'Português', 320, 'capa_clima.jpg', 8, 8),
('Como Fazer Amigos e Influenciar Pessoas', '978-85-7542-429-0', 50, 1936, 'Clássico sobre habilidades sociais', 25, 'Português', 256, 'capa_amigos.jpg', 9, 9),
('Reinações de Narizinho', '978-85-7232-427-3', 20, 1931, 'Aventuras no Sítio do Picapau Amarelo', 18, 'Português', 192, 'capa_narizinho.jpg', 10, 10),
('2001: Uma Odisseia no Espaço', '978-85-325-1498-3', 3, 1968, 'Jornada da humanidade ao encontro de um misterioso monolito', 14, 'Português', 336, 'capa_2001.jpg', 11, 1),
('A Guerra dos Tronos', '978-85-7542-643-0', 5, 1996, 'Primeiro livro da série de fantasia medieval', 16, 'Português', 592, 'capa_tronos.jpg', 12, 2),
('Diário de Uma Paixão', '978-85-7542-273-9', 10, 1996, 'História de amor entre Noah e Allie', 13, 'Português', 224, 'capa_paixao.jpg', 13, 3),
('O Chamado de Cthulhu', '978-85-747-0987-5', 2, 1928, 'Conto de terror cósmico', 10, 'Português', 160, 'capa_cthulhu.jpg', 14, 4),
('Minha História', '978-85-510-0291-5', 1, 2018, 'Memórias de Michelle Obama', 12, 'Português', 464, 'capa_michelle.jpg', 15, 5),
('Armas, Germes e Aço', '978-85-713-9984-6', 3, 1997, 'História das sociedades humanas', 9, 'Português', 496, 'capa_armas.jpg', 16, 6),
('Astrofísica para Apressados', '978-85-510-0205-2', 2, 2017, 'Introdução à astrofísica', 11, 'Português', 224, 'capa_astrofisica.jpg', 17, 7),
('O Homem que Venceu Hitler', '978-85-7542-987-5', 1, 2020, 'Biografia de Elon Musk', 8, 'Português', 384, 'capa_musk.jpg', 18, 5),
('Quem Pensa Enriquece', '978-85-7542-429-1', 30, 1937, 'Clássico sobre desenvolvimento pessoal', 22, 'Português', 256, 'capa_quem_pensa.jpg', 19, 9),
('O Menino Maluquinho', '978-85-7232-152-4', 15, 1980, 'História de um menino travesso', 20, 'Português', 128, 'capa_menino.jpg', 20, 10),
('Eu, Robô', '978-85-359-0278-2', 2, 1950, 'Contos sobre robôs e as três leis da robótica', 10, 'Português', 320, 'capa_robo.jpg', 1, 1),
('O Hobbit', '978-85-359-0801-2', 5, 1937, 'Aventura de Bilbo Bolseiro', 15, 'Português', 336, 'capa_hobbit.jpg', 2, 2),
('Razão e Sensibilidade', '978-85-823-8002-9', 4, 1811, 'História das irmãs Dashwood', 8, 'Português', 384, 'capa_razao.jpg', 3, 3),
('O Iluminado', '978-85-01-10176-4', 3, 1977, 'Terror psicológico em um hotel isolado', 12, 'Português', 464, 'capa_iluminado.jpg', 4, 4),
('Leonardo da Vinci', '978-85-8057-135-0', 1, 2017, 'Biografia do gênio renascentista', 7, 'Português', 624, 'capa_davinci.jpg', 5, 5),
('Homo Deus', '978-85-254-3229-2', 5, 2015, 'Futuro da humanidade', 20, 'Português', 448, 'capa_homo_deus.jpg', 6, 6),
('O Mundo Assombrado pelos Demônios', '978-85-273-0086-2', 2, 1995, 'Ciência como uma vela no escuro', 9, 'Português', 480, 'capa_demonios.jpg', 7, 7),
('A Estrada do Futuro', '978-85-510-0275-5', 1, 1995, 'Visão de Bill Gates sobre tecnologia', 11, 'Português', 352, 'capa_estrada.jpg', 8, 8),
('Como Desfrutar a Vida e o Trabalho', '978-85-7542-430-7', 20, 1936, 'Conselhos para uma vida melhor', 25, 'Português', 224, 'capa_desfrutar.jpg', 9, 9),
('O Saci', '978-85-7232-428-0', 15, 1921, 'Aventuras com o personagem folclórico', 18, 'Português', 144, 'capa_saci.jpg', 10, 10),
('2010: Odisseia Dois', '978-85-325-1499-0', 2, 1982, 'Continuação de 2001', 14, 'Português', 320, 'capa_2010.jpg', 11, 1),
('A Fúria dos Reis', '978-85-7542-644-7', 5, 1998, 'Segundo livro da série de fantasia', 16, 'Português', 656, 'capa_furia.jpg', 12, 2),
('Um Amor para Recordar', '978-85-7542-274-6', 8, 1999, 'História de amor entre Landon e Jamie', 13, 'Português', 240, 'capa_amor.jpg', 13, 3),
('Nas Montanhas da Loucura', '978-85-747-0988-2', 2, 1936, 'Expedição à Antártida revela horrores', 10, 'Português', 192, 'capa_montanhas.jpg', 14, 4),
('Tornando-se', '978-85-510-0292-2', 1, 2018, 'Memórias de Michelle Obama (edição juvenil)', 12, 'Português', 320, 'capa_tornando.jpg', 15, 5),
('Colapso', '978-85-713-9985-3', 2, 2005, 'Como as sociedades escolhem fracassar ou ter sucesso', 9, 'Português', 592, 'capa_colapso.jpg', 16, 6),
('Origens', '978-85-510-0206-9', 1, 2019, 'Quatorze bilhões de anos de evolução cósmica', 11, 'Português', 352, 'capa_origens.jpg', 17, 7),
('Tesla: Inventor do Século', '978-85-7542-988-2', 1, 2021, 'Biografia de Nikola Tesla', 8, 'Português', 320, 'capa_tesla.jpg', 18, 5),
('A Lei do Triunfo', '978-85-7542-430-8', 15, 1928, 'Princípios para o sucesso', 22, 'Português', 448, 'capa_triunfo.jpg', 19, 9),
('Flicts', '978-85-7232-153-1', 10, 1969, 'História de uma cor diferente', 20, 'Português', 48, 'capa_flicts.jpg', 20, 10);

INSERT INTO USUARIO (user_nome, user_cpf, user_email, user_telefone, user_senha, user_login, user_dataAdmissao, user_status, user_tipoUser) VALUES 
('Ana Silva', '111.222.333-44', 'ana.silva@alexandria.com', '(11) 91111-1111', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ana.silva', '2015-03-15', 'Ativo', 'Administrador'),
('Carlos Oliveira', '222.333.444-55', 'carlos.oliveira@alexandria.com', '(11) 92222-2222', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'carlos.oliveira', '2016-05-20', 'Ativo', 'Secretaria'),
('Mariana Santos', '333.444.555-66', 'mariana.santos@alexandria.com', '(11) 93333-3333', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mariana.santos', '2017-07-10', 'Ativo', 'Almoxarife'),
('Pedro Almeida', '444.555.666-77', 'pedro.almeida@alexandria.com', '(11) 94444-4444', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pedro.almeida', '2018-01-25', 'Ativo', 'Secretaria'),
('Juliana Costa', '555.666.777-88', 'juliana.costa@alexandria.com', '(11) 95555-5555', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'juliana.costa', '2019-09-05', 'Ativo', 'Almoxarife'),
('Ricardo Pereira', '666.777.888-99', 'ricardo.pereira@alexandria.com', '(11) 96666-6666', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ricardo.pereira', '2020-02-18', 'Ativo', 'Secretaria'),
('Fernanda Lima', '777.888.999-00', 'fernanda.lima@alexandria.com', '(11) 97777-7777', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'fernanda.lima', '2014-11-30', 'Inativo', 'Secretaria'),
('Lucas Martins', '888.999.000-11', 'lucas.martins@alexandria.com', '(11) 98888-8888', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'lucas.martins', '2016-08-22', 'Ativo', 'Almoxarife'),
('Patricia Rocha', '999.000.111-22', 'patricia.rocha@alexandria.com', '(11) 99999-9999', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patricia.rocha', '2017-04-14', 'Ativo', 'Secretaria'),
('Roberto Nunes', '000.111.222-33', 'roberto.nunes@alexandria.com', '(11) 90000-0000', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'roberto.nunes', '2018-12-03', 'Ativo', 'Administrador');

INSERT INTO MEMBRO (mem_nome, mem_cpf, mem_senha, mem_email, mem_telefone, mem_status) VALUES 
('João Souza', '123.456.789-01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'joao.souza@email.com', '(11) 91234-5678', 'Ativo'),
('Maria Oliveira', '234.567.890-12', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'maria.oliveira@email.com', '(11) 92345-6789', 'Ativo'),
('Carlos Pereira', '345.678.901-23', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'carlos.pereira@email.com', '(11) 93456-7890', 'Ativo'),
('Ana Costa', '456.789.012-34', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ana.costa@email.com', '(11) 94567-8901', 'Ativo'),
('Paulo Santos', '567.890.123-45', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'paulo.santos@email.com', '(11) 95678-9012', 'Ativo'),
('Juliana Lima', '678.901.234-56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'juliana.lima@email.com', '(11) 96789-0123', 'Ativo'),
('Marcos Almeida', '789.012.345-67', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'marcos.almeida@email.com', '(11) 97890-1234', 'Suspenso'),
('Fernanda Rocha', '890.123.456-78', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'fernanda.rocha@email.com', '(11) 98901-2345', 'Ativo'),
('Ricardo Silva', '901.234.567-89', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ricardo.silva@email.com', '(11) 99012-3456', 'Ativo'),
('Patricia Nunes', '012.345.678-90', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patricia.nunes@email.com', '(11) 90123-4567', 'Ativo'),
('Lucas Martins', '111.222.333-44', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'lucas.martins@email.com', '(11) 91111-1111', 'Ativo'),
('Camila Oliveira', '222.333.444-55', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'camila.oliveira@email.com', '(11) 92222-2222', 'Ativo'),
('Gustavo Pereira', '333.444.555-66', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'gustavo.pereira@email.com', '(11) 93333-3333', 'Suspenso'),
('Amanda Costa', '444.555.666-77', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'amanda.costa@email.com', '(11) 94444-4444', 'Ativo'),
('Rodrigo Santos', '555.666.777-88', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'rodrigo.santos@email.com', '(11) 95555-5555', 'Ativo'),
('Tatiana Lima', '666.777.888-99', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tatiana.lima@email.com', '(11) 96666-6666', 'Ativo'),
('Bruno Almeida', '777.888.999-00', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'bruno.almeida@email.com', '(11) 97777-7777', 'Ativo'),
('Vanessa Rocha', '888.999.000-11', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'vanessa.rocha@email.com', '(11) 98888-8888', 'Ativo'),
('Felipe Silva', '999.000.111-22', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'felipe.silva@email.com', '(11) 99999-9999', 'Ativo'),
('Daniela Nunes', '000.111.222-33', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'daniela.nunes@email.com', '(11) 90000-0000', 'Ativo');

INSERT INTO RESERVA (res_prazo, res_dataMarcada, res_dataVencimento, res_status, fk_mem, fk_liv, fk_user) VALUES 
(7, '2023-01-05', '2023-01-12', 'Finalizada', 1, 1, 2),
(7, '2023-01-10', '2023-01-17', 'Finalizada', 2, 2, 2),
(7, '2023-01-15', '2023-01-22', 'Finalizada', 3, 3, 4),
(7, '2023-01-20', '2023-01-27', 'Finalizada', 4, 4, 4),
(7, '2023-01-25', '2023-02-01', 'Finalizada', 5, 5, 6),
(7, '2023-02-01', '2023-02-08', 'Finalizada', 6, 6, 6),
(7, '2023-02-05', '2023-02-12', 'Finalizada', 7, 7, 9),
(7, '2023-02-10', '2023-02-17', 'Finalizada', 8, 8, 9),
(7, '2023-02-15', '2023-02-22', 'Finalizada', 9, 9, 2),
(7, '2023-02-20', '2023-02-27', 'Finalizada', 10, 10, 4),
(7, '2023-03-01', '2023-03-08', 'Finalizada', 11, 11, 6),
(7, '2023-03-05', '2023-03-12', 'Finalizada', 12, 12, 6),
(7, '2023-03-10', '2023-03-17', 'Finalizada', 13, 13, 9),
(7, '2023-03-15', '2023-03-22', 'Finalizada', 14, 14, 9),
(7, '2023-03-20', '2023-03-27', 'Finalizada', 15, 15, 2),
(7, '2023-04-01', '2023-04-08', 'Finalizada', 16, 16, 4),
(7, '2023-04-05', '2023-04-12', 'Finalizada', 17, 17, 6),
(7, '2023-04-10', '2023-04-17', 'Finalizada', 18, 18, 6),
(7, '2023-04-15', '2023-04-22', 'Finalizada', 19, 19, 9),
(7, '2023-04-20', '2023-04-27', 'Finalizada', 20, 20, 9),
(7, '2023-05-01', '2023-05-08', 'Aberta', 1, 21, 2),
(7, '2023-05-05', '2023-05-12', 'Aberta', 2, 22, 4),
(7, '2023-05-10', '2023-05-17', 'Aberta', 3, 23, 6),
(7, '2023-05-15', '2023-05-22', 'Aberta', 4, 24, 6),
(7, '2023-05-20', '2023-05-27', 'Aberta', 5, 25, 9),
(7, '2023-06-01', '2023-06-08', 'Aberta', 6, 26, 2),
(7, '2023-06-05', '2023-06-12', 'Aberta', 7, 27, 4),
(7, '2023-06-10', '2023-06-17', 'Aberta', 8, 28, 6),
(7, '2023-06-15', '2023-06-22', 'Aberta', 9, 29, 6),
(7, '2023-06-20', '2023-06-27', 'Aberta', 10, 30, 9),
(7, '2023-07-01', '2023-07-08', 'Aberta', 11, 31, 2),
(7, '2023-07-05', '2023-07-12', 'Aberta', 12, 32, 4),
(7, '2023-07-10', '2023-07-17', 'Aberta', 13, 33, 6),
(7, '2023-07-15', '2023-07-22', 'Aberta', 14, 34, 6),
(7, '2023-07-20', '2023-07-27', 'Aberta', 15, 35, 9),
(7, '2023-08-01', '2023-08-08', 'Aberta', 16, 36, 2),
(7, '2023-08-05', '2023-08-12', 'Aberta', 17, 37, 4),
(7, '2023-08-10', '2023-08-17', 'Aberta', 18, 38, 6),
(7, '2023-08-15', '2023-08-22', 'Aberta', 19, 39, 6),
(7, '2023-08-20', '2023-08-27', 'Aberta', 20, 40, 9);

