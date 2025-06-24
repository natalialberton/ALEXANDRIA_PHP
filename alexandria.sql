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
 rem_data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
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
-- Inserindo categorias
INSERT INTO CATEGORIA (cat_nome) VALUES 
('Ficção Científica'),
('Fantasia'),
('Romance'),
('Mistério'),
('Terror'),
('Biografia'),
('História'),
('Ciência'),
('Autoajuda'),
('Infantil');

-- Inserindo autores
INSERT INTO AUTOR (aut_nome, aut_dataNascimento, fk_cat) VALUES
('Isaac Asimov', '1920-01-02', 1),
('J.R.R. Tolkien', '1892-01-03', 2),
('Jane Austen', '1775-12-16', 3),
('Agatha Christie', '1890-09-15', 4),
('Stephen King', '1947-09-21', 5),
('Walter Isaacson', '1952-05-20', 6),
('Yuval Noah Harari', '1976-02-24', 7),
('Carl Sagan', '1934-11-09', 8),
('Dale Carnegie', '1888-11-24', 9),
('Monteiro Lobato', '1882-04-18', 10),
('Arthur C. Clarke', '1917-12-16', 1),
('George R.R. Martin', '1948-09-20', 2),
('Machado de Assis', '1839-06-21', 3),
('Arthur Conan Doyle', '1859-05-22', 4),
('Mary Shelley', '1797-08-30', 5),
('Michelle Obama', '1964-01-17', 6),
('Jared Diamond', '1937-09-10', 7),
('Neil deGrasse Tyson', '1958-10-05', 8),
('Napoleon Hill', '1883-10-26', 9),
('Ziraldo', '1932-10-24', 10);

-- Inserindo fornecedores
INSERT INTO FORNECEDOR (forn_nome, forn_cnpj, forn_telefone, forn_email, forn_endereco) VALUES
('Editora Arqueiro', '12.345.678/0001-01', '(11) 1234-5678', 'contato@arqueiro.com.br', 'Rua dos Livros, 123 - São Paulo/SP'),
('Companhia das Letras', '12.345.678/0001-02', '(11) 2345-6789', 'contato@companhiadasletras.com.br', 'Av. Literária, 456 - São Paulo/SP'),
('Editora Rocco', '12.345.678/0001-03', '(21) 3456-7890', 'contato@rocco.com.br', 'Rua das Letras, 789 - Rio de Janeiro/RJ'),
('Editora Intrínseca', '12.345.678/0001-04', '(21) 4567-8901', 'contato@intrinseca.com.br', 'Av. Cultural, 101 - Rio de Janeiro/RJ'),
('Editora Abril', '12.345.678/0001-05', '(11) 5678-9012', 'contato@abril.com.br', 'Rua das Revistas, 202 - São Paulo/SP'),
('Editora Globo', '12.345.678/0001-06', '(11) 6789-0123', 'contato@globo.com.br', 'Av. da Imprensa, 303 - São Paulo/SP'),
('Editora Record', '12.345.678/0001-07', '(21) 7890-1234', 'contato@record.com.br', 'Rua dos Editores, 404 - Rio de Janeiro/RJ'),
('Editora Nova Fronteira', '12.345.678/0001-08', '(21) 8901-2345', 'contato@novafronteira.com.br', 'Av. dos Autores, 505 - Rio de Janeiro/RJ'),
('Editora Melhoramentos', '12.345.678/0001-09', '(11) 9012-3456', 'contato@melhoramentos.com.br', 'Rua das Melhorias, 606 - São Paulo/SP'),
('Editora Saraiva', '12.345.678/0001-10', '(11) 0123-4567', 'contato@saraiva.com.br', 'Av. dos Estudantes, 707 - São Paulo/SP');

-- Inserindo usuários (funcionários) com hashes pré-gerados
INSERT INTO USUARIO (user_nome, user_cpf, user_email, user_telefone, user_senha, user_login, user_tipoUser, user_status) VALUES
('João Silva', '111.222.333-44', 'joao.silva@biblioteca.com', '(11) 91234-5678', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', 'joaosilva', 'Administrador', 'Ativo'),
('Maria Santos', '222.333.444-55', 'maria.santos@biblioteca.com', '(11) 92345-6789', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', 'mariasantos', 'Administrador', 'Ativo'),
('Carlos Oliveira', '333.444.555-66', 'carlos.oliveira@biblioteca.com', '(11) 93456-7890', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', 'carlosoli', 'Secretaria', 'Ativo'),
('Ana Pereira', '444.555.666-77', 'ana.pereira@biblioteca.com', '(11) 94567-8901', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', 'anapereira', 'Secretaria', 'Ativo'),
('Pedro Costa', '555.666.777-88', 'pedro.costa@biblioteca.com', '(11) 95678-9012', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', 'pedrocosta', 'Almoxarife', 'Ativo'),
('Lucia Fernandes', '666.777.888-99', 'lucia.fernandes@biblioteca.com', '(11) 96789-0123', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', 'luciafer', 'Almoxarife', 'Ativo'),
('Marcos Souza', '777.888.999-00', 'marcos.souza@biblioteca.com', '(11) 97890-1234', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', 'marcossouza', 'Secretaria', 'Ativo'),
('Juliana Lima', '888.999.000-11', 'juliana.lima@biblioteca.com', '(11) 98901-2345', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', 'julianalima', 'Almoxarife', 'Ativo'),
('Roberto Alves', '999.000.111-22', 'roberto.alves@biblioteca.com', '(11) 99012-3456', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', 'robertoalves', 'Secretaria', 'Ativo'),
('Fernanda Rocha', '000.111.222-33', 'fernanda.rocha@biblioteca.com', '(11) 90123-4567', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', 'fernandarocha', 'Administrador', 'Ativo');

-- Inserindo membros com hashes pré-gerados
INSERT INTO MEMBRO (mem_nome, mem_cpf, mem_senha, mem_email, mem_telefone, mem_status) VALUES
('Lucas Mendes', '123.456.789-01', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', 'lucas.mendes@email.com', '(11) 91234-5678', 'Ativo'),
('Amanda Costa', '234.567.890-12', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', 'amanda.costa@email.com', '(11) 92345-6789', 'Ativo'),
('Rafael Santos', '345.678.901-23', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', 'rafael.santos@email.com', '(11) 93456-7890', 'Ativo'),
('Patricia Oliveira', '456.789.012-34', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', 'patricia.oliveira@email.com', '(11) 94567-8901', 'Ativo'),
('Bruno Pereira', '567.890.123-45', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', 'bruno.pereira@email.com', '(11) 95678-9012', 'Ativo'),
('Camila Souza', '678.901.234-56', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', 'camila.souza@email.com', '(11) 96789-0123', 'Ativo'),
('Diego Fernandes', '789.012.345-67', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', 'diego.fernandes@email.com', '(11) 97890-1234', 'Ativo'),
('Tatiana Lima', '890.123.456-78', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', 'tatiana.lima@email.com', '(11) 98901-2345', 'Ativo'),
('Gustavo Alves', '901.234.567-89', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', 'gustavo.alves@email.com', '(11) 99012-3456', 'Ativo'),
('Vanessa Rocha', '012.345.678-90', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', 'vanessa.rocha@email.com', '(11) 90123-4567', 'Ativo'),
('Rodrigo Silva', '123.456.789-10', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', 'rodrigo.silva@email.com', '(11) 91234-5679', 'Ativo'),
('Cristina Mendonça', '234.567.890-21', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', 'cristina.mendonca@email.com', '(11) 92345-6790', 'Ativo'),
('Marcelo Costa', '345.678.901-32', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', 'marcelo.costa@email.com', '(11) 93456-7901', 'Ativo'),
('Isabela Santos', '456.789.012-43', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', 'isabela.santos@email.com', '(11) 94567-9012', 'Ativo'),
('Leonardo Oliveira', '567.890.123-54', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', 'leonardo.oliveira@email.com', '(11) 95678-0123', 'Ativo'),
('Mariana Pereira', '678.901.234-65', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', 'mariana.pereira@email.com', '(11) 96789-1234', 'Suspenso'),
('Felipe Souza', '789.012.345-76', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', 'felipe.souza@email.com', '(11) 97890-2345', 'Ativo'),
('Aline Fernandes', '890.123.456-87', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', 'aline.fernandes@email.com', '(11) 98901-3456', 'Ativo'),
('Ricardo Lima', '901.234.567-98', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', 'ricardo.lima@email.com', '(11) 99012-4567', 'Ativo'),
('Daniela Alves', '012.345.678-09', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', 'daniela.alves@email.com', '(11) 90123-5678', 'Ativo');
-- Inserindo livros
INSERT INTO LIVRO (liv_titulo, liv_isbn, liv_edicao, liv_anoPublicacao, liv_sinopse, liv_estoque, liv_idioma, liv_num_paginas, liv_capa, fk_aut, fk_cat) VALUES
('Fundação', '978-85-359-0277-1', 1, 1951, 'A história da Fundação, um grupo de cientistas que trabalha para preservar o conhecimento humano contra o colapso da galáxia.', 5, 'Português', 256, 'capa_fundacao.jpg', 1, 1),
('O Senhor dos Anéis: A Sociedade do Anel', '978-85-359-0802-5', 3, 1954, 'A jornada de Frodo para destruir o Um Anel e salvar a Terra-média.', 7, 'Português', 576, 'capa_sociedade_anel.jpg', 2, 2),
('Orgulho e Preconceito', '978-85-7232-314-1', 2, 1813, 'A história de Elizabeth Bennet e Mr. Darcy em uma crítica à sociedade inglesa do século XIX.', 4, 'Português', 424, 'capa_orgulho_preconceito.jpg', 3, 3),
('Assassinato no Expresso do Oriente', '978-85-325-2345-6', 1, 1934, 'Hercule Poirot investiga um assassinato ocorrido no famoso trem.', 6, 'Português', 256, 'capa_expresso_oriente.jpg', 4, 4),
('O Iluminado', '978-85-325-3012-6', 1, 1977, 'A história de uma família que se muda para um hotel isolado onde eventos sobrenaturais ocorrem.', 3, 'Português', 464, 'capa_iluminado.jpg', 5, 5),
('Steve Jobs', '978-85-8057-134-6', 1, 2011, 'A biografia autorizada do cofundador da Apple.', 2, 'Português', 656, 'capa_steve_jobs.jpg', 6, 6),
('Sapiens: Uma Breve História da Humanidade', '978-85-254-3275-0', 1, 2011, 'Uma visão abrangente da história da humanidade.', 8, 'Português', 464, 'capa_sapiens.jpg', 7, 7),
('Cosmos', '978-85-273-0116-7', 1, 1980, 'Uma jornada através do universo e da ciência.', 5, 'Português', 384, 'capa_cosmos.jpg', 8, 8),
('Como Fazer Amigos e Influenciar Pessoas', '978-85-7542-213-6', 1, 1936, 'Um guia clássico para melhorar habilidades sociais.', 9, 'Português', 256, 'capa_amigos.jpg', 9, 9),
('Reinações de Narizinho', '978-85-7232-427-8', 1, 1931, 'As aventuras de Narizinho no Sítio do Picapau Amarelo.', 6, 'Português', 192, 'capa_narizinho.jpg', 10, 10),
('2001: Uma Odisseia no Espaço', '978-85-359-0278-8', 1, 1968, 'A jornada da humanidade desde os primórdios até o encontro com um misterioso monolito no espaço.', 4, 'Português', 336, 'capa_2001.jpg', 11, 1),
('A Guerra dos Tronos', '978-85-7542-463-5', 1, 1996, 'O primeiro livro da série As Crônicas de Gelo e Fogo.', 5, 'Português', 592, 'capa_guerra_tronos.jpg', 12, 2),
('Dom Casmurro', '978-85-7232-293-9', 1, 1899, 'A história de Bentinho e Capitu e a dúvida sobre a traição.', 7, 'Português', 256, 'capa_dom_casmurro.jpg', 13, 3),
('O Cão dos Baskervilles', '978-85-7232-618-0', 1, 1902, 'Sherlock Holmes investiga uma maldição familiar.', 3, 'Português', 192, 'capa_cao_baskervilles.jpg', 14, 4),
('Frankenstein', '978-85-7232-744-6', 1, 1818, 'A história do cientista Victor Frankenstein e sua criatura.', 4, 'Português', 280, 'capa_frankenstein.jpg', 15, 5),
('Minha História', '978-85-510-0274-8', 1, 2018, 'As memórias de Michelle Obama.', 5, 'Português', 464, 'capa_minha_historia.jpg', 16, 6),
('Armas, Germes e Aço', '978-85-359-1304-3', 1, 1997, 'Os destinos das sociedades humanas.', 3, 'Português', 480, 'capa_armas_germes_aco.jpg', 17, 7),
('Astrofísica para Apressados', '978-85-510-0275-5', 1, 2017, 'Os grandes mistérios do universo explicados de forma simples.', 6, 'Português', 208, 'capa_astrofisica.jpg', 18, 8),
('Mais Esperto que o Diabo', '978-85-7542-387-4', 1, 1938, 'Um diálogo revelador sobre superação de medos e limitações.', 4, 'Português', 224, 'capa_mais_esperto.jpg', 19, 9),
('O Menino Maluquinho', '978-85-7232-744-7', 1, 1980, 'As travessuras de um menino alegre e sapeca.', 8, 'Português', 128, 'capa_menino_maluquinho.jpg', 20, 10),
('Eu, Robô', '978-85-359-0279-5', 1, 1950, 'Contos sobre robôs e as Três Leis da Robótica.', 5, 'Português', 320, 'capa_eu_robo.jpg', 1, 1),
('O Hobbit', '978-85-359-0803-2', 2, 1937, 'A aventura de Bilbo Bolseiro para recuperar o tesouro dos dragões.', 6, 'Português', 336, 'capa_hobbit.jpg', 2, 2),
('Razão e Sensibilidade', '978-85-7232-315-8', 1, 1811, 'A história das irmãs Dashwood e seus amores.', 4, 'Português', 352, 'capa_razao_sensibilidade.jpg', 3, 3),
('Morte no Nilo', '978-85-325-2346-3', 1, 1937, 'Hercule Poirot investiga um assassinato durante um cruzeiro pelo Nilo.', 3, 'Português', 288, 'capa_morte_nilo.jpg', 4, 4),
('It: A Coisa', '978-85-325-3013-3', 1, 1986, 'Um grupo de amigos enfrenta um ser maligno que assume várias formas.', 2, 'Português', 1104, 'capa_it.jpg', 5, 5),
('Einstein: Sua Vida, Seu Universo', '978-85-8057-135-3', 1, 2007, 'A biografia de Albert Einstein.', 3, 'Português', 704, 'capa_einstein.jpg', 6, 6),
('Homo Deus: Uma Breve História do Amanhã', '978-85-254-3276-7', 1, 2015, 'Uma visão sobre o futuro da humanidade.', 5, 'Português', 448, 'capa_homo_deus.jpg', 7, 7),
('O Mundo Assombrado pelos Demônios', '978-85-273-0117-4', 1, 1995, 'A ciência como uma vela no escuro.', 4, 'Português', 480, 'capa_mundo_assombrado.jpg', 8, 8),
('Como Evitar Preocupações e Começar a Viver', '978-85-7542-214-3', 1, 1948, 'Conselhos práticos para uma vida mais tranquila.', 6, 'Português', 272, 'capa_evitar_preocupacoes.jpg', 9, 9),
('O Saci', '978-85-7232-428-5', 1, 1921, 'As aventuras no Sítio do Picapau Amarelo envolvendo o Saci.', 5, 'Português', 144, 'capa_saci.jpg', 10, 10),
('O Fim da Eternidade', '978-85-359-0280-1', 1, 1955, 'Uma sociedade que controla o tempo e as consequências de suas ações.', 4, 'Português', 256, 'capa_fim_eternidade.jpg', 1, 1),
('A Fúria dos Reis', '978-85-7542-464-2', 1, 1998, 'O segundo livro da série As Crônicas de Gelo e Fogo.', 3, 'Português', 656, 'capa_furia_reis.jpg', 12, 2),
('Memórias Póstumas de Brás Cubas', '978-85-7232-294-6', 1, 1881, 'A autobiografia de um defunto autor.', 5, 'Português', 224, 'capa_memorias_bras_cubas.jpg', 13, 3),
('Um Estudo em Vermelho', '978-85-7232-619-7', 1, 1887, 'A primeira aparição de Sherlock Holmes e Dr. Watson.', 4, 'Português', 160, 'capa_estudo_vermelho.jpg', 14, 4),
('O Médico e o Monstro', '978-85-7232-745-3', 1, 1886, 'A dualidade da natureza humana.', 3, 'Português', 144, 'capa_medico_monstro.jpg', 15, 5),
('Tornar-se', '978-85-510-0276-2', 1, 2018, 'A autobiografia de Michelle Obama.', 6, 'Português', 448, 'capa_tornar_se.jpg', 16, 6),
('Colapso', '978-85-359-1305-0', 1, 2005, 'Como as sociedades escolhem o fracasso ou o sucesso.', 4, 'Português', 592, 'capa_colapso.jpg', 17, 7),
('Origens', '978-85-510-0277-9', 1, 2019, 'Quatorze bilhões de anos de evolução cósmica.', 5, 'Português', 336, 'capa_origens.jpg', 18, 8),
('A Lei do Triunfo', '978-85-7542-388-1', 1, 1928, 'Os princípios para alcançar o sucesso.', 3, 'Português', 432, 'capa_lei_triunfo.jpg', 19, 9),
('Flicts', '978-85-7232-745-4', 1, 1969, 'A história de uma cor diferente.', 7, 'Português', 48, 'capa_flicts.jpg', 20, 10);

-- Inserindo remessas
INSERT INTO REMESSA (rem_data, rem_qtd, fk_forn, fk_liv, fk_user) VALUES
('2023-01-15', 50, 1, 1, 5),
('2023-02-20', 30, 2, 2, 5),
('2023-03-10', 40, 3, 3, 6),
('2023-04-05', 25, 4, 4, 6),
('2023-05-12', 35, 5, 5, 8);

-- Inserindo empréstimos
INSERT INTO EMPRESTIMO (emp_prazo, emp_dataEmp, emp_dataDev, emp_dataDevReal, emp_status, fk_mem, fk_user, fk_liv) VALUES
(14, '2023-01-10', '2023-01-24', '2023-01-24', 'Finalizado', 1, 3, 1),
(14, '2023-01-15', '2023-01-29', '2023-01-30', 'Finalizado', 2, 4, 2),
(14, '2023-02-05', '2023-02-19', '2023-02-18', 'Finalizado', 3, 7, 3),
(14, '2023-02-20', '2023-03-06', NULL, 'Empréstimo Ativo', 4, 9, 4),
(14, '2023-03-01', '2023-03-15', NULL, 'Empréstimo Atrasado', 5, 3, 5);

-- Inserindo reservas
INSERT INTO RESERVA (res_prazo, res_dataMarcada, res_dataVencimento, res_status, fk_mem, fk_liv, fk_user) VALUES
(7, '2023-01-05', '2023-01-12', 'Finalizada', 6, 6, 4),
(7, '2023-01-12', '2023-01-19', 'Finalizada', 7, 7, 7),
(7, '2023-02-01', '2023-02-08', 'Cancelada', 8, 8, 9),
(7, '2023-02-15', '2023-02-22', 'Aberta', 9, 9, 3),
(7, '2023-03-10', '2023-03-17', 'Atrasada', 10, 10, 4);

-- Inserindo multas
INSERT INTO MULTA (mul_valor, mul_qtdDias, mul_status, fk_mem, fk_emp) VALUES
(1.50, 1, 'Finalizada', 2, 2),
(3.00, 2, 'Finalizada', 5, 5),
(4.50, 3, 'Aberta', 10, 5),
(6.00, 4, 'Aberta', 5, 5),
(7.50, 5, 'Finalizada', 2, 2);
