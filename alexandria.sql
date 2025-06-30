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

DELIMITER //

CREATE PROCEDURE atualizacao_diaria_biblioteca()
BEGIN
    -- ATUALIZANDO EMPRÉSTIMOS ATRASADOS
    UPDATE EMPRESTIMO 
    SET emp_status = 'Empréstimo Atrasado'
    WHERE emp_status = 'Empréstimo Ativo' AND emp_dataDev < CURDATE();
    
    UPDATE EMPRESTIMO 
    SET emp_status = 'Renovação Atrasada'
    WHERE emp_status = 'Renovação Ativa' AND emp_dataDev < CURDATE();
    
    -- GERANDO MULTAS
    INSERT INTO MULTA (mul_valor, mul_qtdDias, mul_status, fk_mem, fk_emp)
    SELECT 
        DATEDIFF(CURDATE(), e.emp_dataDev) * e.emp_valorMultaDiaria,
        DATEDIFF(CURDATE(), e.emp_dataDev),
        'Aberta',
        e.fk_mem,
        e.pk_emp
    FROM EMPRESTIMO e
    LEFT JOIN MULTA m ON m.fk_emp = e.pk_emp
    WHERE (e.emp_status = 'Empréstimo Atrasado' OR e.emp_status = 'Renovação Atrasada')
    AND m.pk_mul IS NULL -- Garante que não existe multa já criada
    AND e.emp_dataDev < CURDATE();
    
    -- ATUALIZANDO MULTAS EXISTENTES
    UPDATE MULTA m
    JOIN EMPRESTIMO e ON m.fk_emp = e.pk_emp
    SET 
        m.mul_qtdDias = DATEDIFF(CURDATE(), e.emp_dataDev),
        m.mul_valor = DATEDIFF(CURDATE(), e.emp_dataDev) * e.emp_valorMultaDiaria
    WHERE m.mul_status = 'Aberta' AND e.emp_dataDev < CURDATE();
    
    -- ATUALIZANDO RESERVAS ATRASADAS
    UPDATE RESERVA 
    SET res_status = 'Atrasada'
    WHERE res_status = 'Aberta' AND res_dataVencimento < CURDATE();
END//

DELIMITER ;

-- RODA EVENTO DIARIAMENTE
CREATE EVENT evento_atualizacao_diaria
ON SCHEDULE EVERY 1 DAY
STARTS TIMESTAMP(CURRENT_DATE, '00:05:00')
DO
CALL atualizacao_diaria_biblioteca();

SET GLOBAL event_scheduler = ON;

/*INSERT DE DADOS | IA UTILIZADA: DEEPSEEK*/
INSERT INTO CATEGORIA (cat_nome) VALUES 
('Literatura Brasileira'),
('Ficção Científica'),
('Fantasia'),
('Romance'),
('Biografia'),
('História'),
('Autoajuda'),
('Terror'),
('Infantil'),
('Técnico/Científico');

INSERT INTO AUTOR (aut_nome, aut_dataNascimento, fk_cat) VALUES
('Machado de Assis', '1839-06-21', 1),
('Clarice Lispector', '1920-12-10', 1),
('Jorge Amado', '1912-08-10', 1),
('Isaac Asimov', '1920-01-02', 2),
('Philip K. Dick', '1928-12-16', 2),
('J.R.R. Tolkien', '1892-01-03', 3),
('George R.R. Martin', '1948-09-20', 3),
('Jane Austen', '1775-12-16', 4),
('Nicholas Sparks', '1965-12-31', 4),
('Walter Isaacson', '1952-05-20', 5),
('Laurentino Gomes', '1956-02-11', 6),
('Eduardo Bueno', '1958-10-30', 6),
('Augusto Cury', '1958-10-02', 7),
('Stephen King', '1947-09-21', 8),
('H.P. Lovecraft', '1890-08-20', 8),
('Monteiro Lobato', '1882-04-18', 9),
('Ziraldo', '1932-10-24', 9),
('Carl Sagan', '1934-11-09', 10),
('Neil deGrasse Tyson', '1958-10-05', 10),
('Yuval Noah Harari', '1976-02-24', 6);

INSERT INTO FORNECEDOR (forn_nome, forn_cnpj, forn_telefone, forn_email, forn_endereco) VALUES
('Editora Abril', '12.345.678/0001-01', '(11) 1234-5678', 'contato@abril.com.br', 'Rua das Editoras, 100 - São Paulo/SP'),
('Companhia das Letras', '23.456.789/0001-02', '(11) 2345-6789', 'contato@companhiadasletras.com.br', 'Av. dos Livros, 200 - Rio de Janeiro/RJ'),
('Editora Record', '34.567.890/0001-03', '(21) 3456-7890', 'contato@record.com.br', 'Rua dos Autores, 300 - Rio de Janeiro/RJ'),
('Editora Intrínseca', '45.678.901/0001-04', '(21) 4567-8901', 'contato@intrinseca.com.br', 'Av. Cultural, 400 - Rio de Janeiro/RJ'),
('Editora Rocco', '56.789.012/0001-05', '(21) 5678-9012', 'contato@rocco.com.br', 'Rua Literária, 500 - Rio de Janeiro/RJ'),
('Editora Globo', '67.890.123/0001-06', '(11) 6789-0123', 'contato@globo.com.br', 'Av. das Publicações, 600 - São Paulo/SP'),
('Editora Saraiva', '78.901.234/0001-07', '(11) 7890-1234', 'contato@saraiva.com.br', 'Rua das Livrarias, 700 - São Paulo/SP'),
('Editora Moderna', '89.012.345/0001-08', '(11) 8901-2345', 'contato@moderna.com.br', 'Av. Educacional, 800 - São Paulo/SP'),
('Editora Melhoramentos', '90.123.456/0001-09', '(11) 9012-3456', 'contato@melhoramentos.com.br', 'Rua das Melhorias, 900 - São Paulo/SP'),
('Editora Arqueiro', '01.234.567/0001-10', '(31) 1234-5678', 'contato@arqueiro.com.br', 'Av. dos Best-sellers, 1000 - Belo Horizonte/MG');

INSERT INTO LIVRO (liv_titulo, liv_isbn, liv_edicao, liv_anoPublicacao, liv_sinopse, liv_estoque, liv_idioma, liv_num_paginas, liv_capa, fk_aut, fk_cat) VALUES
('Dom Casmurro', '978-85-7232-227-1', 1, 1899, 'Clássico da literatura brasileira sobre ciúme e amor', 15, 'Português', 256, 'dom_casmurro.jpg', 1, 1),
('A Hora da Estrela', '978-85-325-0264-2', 3, 1977, 'Último romance de Clarice Lispector', 10, 'Português', 96, 'hora_estrela.jpg', 2, 1),
('Capitães da Areia', '978-85-01-05192-3', 5, 1937, 'Romance sobre meninos de rua em Salvador', 12, 'Português', 264, 'capitaes_areia.jpg', 3, 1),
('Fundação', '978-85-359-0917-4', 2, 1951, 'Primeiro livro da trilogia da Fundação', 8, 'Português', 320, 'fundacao.jpg', 4, 2),
('Androides Sonham com Ovelhas Elétricas?', '978-85-359-1300-5', 1, 1968, 'Livro que inspirou Blade Runner', 6, 'Português', 256, 'androides.jpg', 5, 2),
('O Senhor dos Anéis: A Sociedade do Anel', '978-85-359-0645-6', 10, 1954, 'Primeiro volume da trilogia épica', 20, 'Português', 576, 'sociedade_anel.jpg', 6, 3),
('As Crônicas de Gelo e Fogo: A Guerra dos Tronos', '978-85-01-05054-7', 1, 1996, 'Primeiro livro da série Game of Thrones', 18, 'Português', 592, 'guerra_tronos.jpg', 7, 3),
('Orgulho e Preconceito', '978-85-7232-227-8', 5, 1813, 'Clássico romance de Jane Austen', 14, 'Português', 424, 'orgulho_preconceito.jpg', 8, 4),
('Diário de uma Paixão', '978-85-325-2074-9', 3, 1996, 'Romance emocionante sobre amor eterno', 9, 'Português', 224, 'diario_paixao.jpg', 9, 4),
('Steve Jobs', '978-85-8057-156-0', 1, 2011, 'Biografia do cofundador da Apple', 7, 'Português', 656, 'steve_jobs.jpg', 10, 5),
('1808', '978-85-7302-939-1', 10, 2007, 'Sobre a fuga da família real portuguesa para o Brasil', 11, 'Português', 408, '1808.jpg', 11, 6),
('Brasil: Uma História', '978-85-01-05663-2', 2, 2003, 'História do Brasil de forma acessível', 8, 'Português', 464, 'brasil_historia.jpg', 12, 6),
('O Vendedor de Sonhos', '978-85-7542-296-3', 5, 2008, 'Sobre um homem que vende sonhos nas ruas', 10, 'Português', 224, 'vendedor_sonhos.jpg', 13, 7),
('O Iluminado', '978-85-01-05054-1', 8, 1977, 'Clássico do terror sobre um hotel mal-assombrado', 12, 'Português', 464, 'iluminado.jpg', 14, 8),
('O Chamado de Cthulhu', '978-85-66631-41-2', 1, 1928, 'Conto que introduz o mito de Cthulhu', 5, 'Português', 96, 'cthulhu.jpg', 15, 8),
('Reinações de Narizinho', '978-85-7232-227-2', 20, 1931, 'Primeiro livro do Sítio do Picapau Amarelo', 15, 'Português', 192, 'reinações.jpg', 16, 9),
('O Menino Maluquinho', '978-85-7232-227-3', 15, 1980, 'Clássico infantil brasileiro', 20, 'Português', 128, 'menino_maluquinho.jpg', 17, 9),
('Cosmos', '978-85-7542-296-4', 3, 1980, 'Sobre a evolução do universo e da ciência', 8, 'Português', 384, 'cosmos.jpg', 18, 10),
('Astrofísica para Apressados', '978-85-7542-296-5', 2, 2017, 'Introdução à astrofísica em linguagem acessível', 9, 'Português', 224, 'astrofisica.jpg', 19, 10),
('Sapiens: Uma Breve História da Humanidade', '978-85-7542-296-6', 5, 2011, 'Sobre a evolução da espécie humana', 12, 'Português', 464, 'sapiens.jpg', 20, 6),
('Memórias Póstumas de Brás Cubas', '978-85-7232-227-4', 1, 1881, 'Romance inovador de Machado de Assis', 10, 'Português', 288, 'bras_cubas.jpg', 1, 1),
('Quincas Borba', '978-85-7232-227-5', 1, 1891, 'Outro clássico de Machado de Assis', 8, 'Português', 320, 'quincas_borba.jpg', 1, 1),
('A Paixão Segundo G.H.', '978-85-325-0264-3', 2, 1964, 'Obra complexa de Clarice Lispector', 6, 'Português', 192, 'paixao_gh.jpg', 2, 1),
('Gabriela, Cravo e Canela', '978-85-01-05192-4', 5, 1958, 'Romance ambientado no cacau da Bahia', 10, 'Português', 352, 'gabriela.jpg', 3, 1),
('Eu, Robô', '978-85-359-0917-5', 3, 1950, 'Coletânea de contos sobre robôs', 7, 'Português', 320, 'eu_robo.jpg', 4, 2),
('O Homem do Castelo Alto', '978-85-359-1300-6', 1, 1962, 'Ficção sobre história alternativa', 5, 'Português', 272, 'castelo_alto.jpg', 5, 2),
('O Hobbit', '978-85-359-0645-7', 10, 1937, 'Aventura que precede O Senhor dos Anéis', 15, 'Português', 336, 'hobbit.jpg', 6, 3),
('As Crônicas de Gelo e Fogo: A Fúria dos Reis', '978-85-01-05054-8', 1, 1998, 'Segundo livro da série', 12, 'Português', 656, 'furia_reis.jpg', 7, 3),
('Razão e Sensibilidade', '978-85-7232-227-9', 3, 1811, 'Outro clássico de Jane Austen', 8, 'Português', 384, 'razao_sensibilidade.jpg', 8, 4),
('Um Amor para Recordar', '978-85-325-2074-0', 2, 1999, 'Romance emocionante sobre amor e perda', 7, 'Português', 240, 'amor_recordar.jpg', 9, 4),
('Einstein: Sua Vida, Seu Universo', '978-85-8057-156-1', 1, 2007, 'Biografia de Albert Einstein', 6, 'Português', 704, 'einstein.jpg', 10, 5),
('1822', '978-85-7302-939-2', 5, 2010, 'Sobre a independência do Brasil', 9, 'Português', 352, '1822.jpg', 11, 6),
('A Viagem do Descobrimento', '978-85-01-05663-3', 1, 1998, 'Sobre o descobrimento do Brasil', 7, 'Português', 224, 'viagem_descobrimento.jpg', 12, 6),
('Ansiedade: Como Enfrentar o Mal do Século', '978-85-7542-296-7', 3, 2013, 'Sobre controle da ansiedade', 10, 'Português', 192, 'ansiedade.jpg', 13, 7),
('It: A Coisa', '978-85-01-05054-2', 5, 1986, 'Clássico do terror sobre um palhaço maligno', 11, 'Português', 1104, 'it.jpg', 14, 8),
('O Caso de Charles Dexter Ward', '978-85-66631-41-3', 1, 1941, 'Novela de terror lovecraftiana', 4, 'Português', 160, 'charles_ward.jpg', 15, 8),
('O Saci', '978-85-7232-227-6', 15, 1921, 'Clássico infantil sobre o personagem folclórico', 12, 'Português', 96, 'saci.jpg', 16, 9),
('Flicts', '978-85-7232-227-7', 10, 1969, 'Livro infantil sobre cores e diferenças', 10, 'Português', 48, 'flicts.jpg', 17, 9),
('O Mundo Assombrado pelos Demônios', '978-85-7542-296-8', 2, 1995, 'Sobre o pensamento cético e científico', 8, 'Português', 480, 'mundo_assombrado.jpg', 18, 10),
('Origens', '978-85-7542-296-9', 1, 2014, 'Sobre as origens do universo e da vida', 7, 'Português', 352, 'origens.jpg', 19, 10),
('Homo Deus: Uma Breve História do Amanhã', '978-85-7542-296-0', 2, 2015, 'Sobre o futuro da humanidade', 9, 'Português', 448, 'homo_deus.jpg', 20, 6);

INSERT INTO USUARIO (user_nome, user_cpf, user_email, user_telefone, user_senha, user_login, user_dataAdmissao, user_status, user_tipoUser) VALUES
('João Silva', '111.222.333-44', 'joao.silva@biblioteca.com', '(11) 91234-5678', '$2y$10$g/8TNtHHDSIaZwUOqtAdT.s8FaHhlKHe7nUUIPeMT7ngpBUcX0zui', 'joao.silva', '2018-03-15', 'Ativo', 'Administrador'),
('Maria Oliveira', '222.333.444-55', 'maria.oliveira@biblioteca.com', '(11) 92345-6789', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', 'maria.oliveira', '2019-05-20', 'Ativo', 'Secretaria'),
('Carlos Pereira', '333.444.555-66', 'carlos.pereira@biblioteca.com', '(11) 93456-7890', '15e2b0d3c33891ebb0f1ef609ec419420c20e320ce94c65fbc8c3312448eb225', 'carlos.pereira', '2020-01-10', 'Ativo', 'Almoxarife'),
('Ana Santos', '444.555.666-77', 'ana.santos@biblioteca.com', '(11) 94567-8901', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', 'ana.santos', '2017-11-05', 'Ativo', 'Secretaria'),
('Pedro Costa', '555.666.777-88', 'pedro.costa@biblioteca.com', '(11) 95678-9012', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'pedro.costa', '2019-08-25', 'Ativo', 'Almoxarife'),
('Luiza Fernandes', '666.777.888-99', 'luiza.fernandes@biblioteca.com', '(11) 96789-0123', '6b86b273ff34fce19d6b804eff5a3f5747ada4eaa22f1d49c01e52ddb7875b4b', 'luiza.fernandes', '2020-07-30', 'Ativo', 'Secretaria'),
('Ricardo Almeida', '777.888.999-00', 'ricardo.almeida@biblioteca.com', '(11) 97890-1234', 'd4735e3a265e16eee03f59718b9b5d03019c07d8b6c51f90da3a666eec13ab35', 'ricardo.almeida', '2018-09-12', 'Ativo', 'Administrador'),
('Fernanda Lima', '888.999.000-11', 'fernanda.lima@biblioteca.com', '(11) 98901-2345', '4e07408562bedb8b60ce05c1decfe3ad16b72230967de01f640b7e4729b49fce', 'fernanda.lima', '2019-04-18', 'Ativo', 'Secretaria'),
('Marcos Souza', '999.000.111-22', 'marcos.souza@biblioteca.com', '(11) 99012-3456', '4b227777d4dd1fc61c6f884f48641d02b4d121d3fd328cb08b5531fcacdabf8a', 'marcos.souza', '2020-02-22', 'Ativo', 'Almoxarife'),
('Juliana Rocha', '000.111.222-33', 'juliana.rocha@biblioteca.com', '(11) 90123-4567', '4fc82b26aecb47d2868c4efbe3581732a3e7cbcc6c2efb32062c08170a05eeb8', 'juliana.rocha', '2017-12-01', 'Inativo', 'Secretaria');

INSERT INTO MEMBRO (mem_nome, mem_cpf, mem_senha, mem_email, mem_telefone, mem_status) VALUES
('Lucas Martins', '123.456.789-01', '6b86b273ff34fce19d6b804eff5a3f5747ada4eaa22f1d49c01e52ddb7875b4b', 'lucas.martins@email.com', '(11) 91234-5678', 'Ativo'),
('Amanda Silva', '222.222.222-22', '4cc8f4d609b717356701c57a03e737e5ac8fe885da8c7163d3de47e01849c635', 'amanda.silva@email.com', '(11) 92345-6789', 'Ativo'),
('Rafael Oliveira', '345.678.901-23', '5f70bf18a086007016e948b04aed3b82103a36bea41755b6cddfaf10ace3c6ef', 'rafael.oliveira@email.com', '(11) 93456-7890', 'Ativo'),
('Patricia Costa', '456.789.012-34', '6b86b273ff34fce19d6b804eff5a3f5747ada4eaa22f1d49c01e52ddb7875b4b', 'patricia.costa@email.com', '(11) 94567-8901', 'Ativo'),
('Daniel Santos', '567.890.123-45', '5f70bf18a086007016e948b04aed3b82103a36bea41755b6cddfaf10ace3c6ef', 'daniel.santos@email.com', '(11) 95678-9012', 'Ativo'),
('Camila Pereira', '678.901.234-56', '6b86b273ff34fce19d6b804eff5a3f5747ada4eaa22f1d49c01e52ddb7875b4b', 'camila.pereira@email.com', '(11) 96789-0123', 'Ativo'),
('Bruno Almeida', '789.012.345-67', '5f70bf18a086007016e948b04aed3b82103a36bea41755b6cddfaf10ace3c6ef', 'bruno.almeida@email.com', '(11) 97890-1234', 'Ativo'),
('Tatiane Lima', '890.123.456-78', '6b86b273ff34fce19d6b804eff5a3f5747ada4eaa22f1d49c01e52ddb7875b4b', 'tatiane.lima@email.com', '(11) 98901-2345', 'Ativo'),
('Gustavo Fernandes', '901.234.567-89', '5f70bf18a086007016e948b04aed3b82103a36bea41755b6cddfaf10ace3c6ef', 'gustavo.fernandes@email.com', '(11) 99012-3456', 'Ativo'),
('Vanessa Souza', '012.345.678-90', '6b86b273ff34fce19d6b804eff5a3f5747ada4eaa22f1d49c01e52ddb7875b4b', 'vanessa.souza@email.com', '(11) 90123-4567', 'Ativo'),
('Roberto Rocha', '321.654.987-09', '5f70bf18a086007016e948b04aed3b82103a36bea41755b6cddfaf10ace3c6ef', 'roberto.rocha@email.com', '(11) 93216-5498', 'Suspenso'),
('Carla Mendes', '432.765.098-18', '6b86b273ff34fce19d6b804eff5a3f5747ada4eaa22f1d49c01e52ddb7875b4b', 'carla.mendes@email.com', '(11) 94327-6509', 'Suspenso'),
('Felipe Gonçalves', '543.876.109-27', '5f70bf18a086007016e948b04aed3b82103a36bea41755b6cddfaf10ace3c6ef', 'felipe.goncalves@email.com', '(11) 95438-7610', 'Ativo'),
('Isabela Torres', '654.987.210-36', '6b86b273ff34fce19d6b804eff5a3f5747ada4eaa22f1d49c01e52ddb7875b4b', 'isabela.torres@email.com', '(11) 96549-8721', 'Ativo'),
('Marcelo Castro', '765.098.321-45', '5f70bf18a086007016e948b04aed3b82103a36bea41755b6cddfaf10ace3c6ef', 'marcelo.castro@email.com', '(11) 97650-9832', 'Ativo'),
('Larissa Nunes', '876.109.432-54', '6b86b273ff34fce19d6b804eff5a3f5747ada4eaa22f1d49c01e52ddb7875b4b', 'larissa.nunes@email.com', '(11) 98761-0943', 'Ativo'),
('Rodrigo Pires', '987.210.543-63', '5f70bf18a086007016e948b04aed3b82103a36bea41755b6cddfaf10ace3c6ef', 'rodrigo.pires@email.com', '(11) 99872-1054', 'Suspenso'),
('Simone Cardoso', '098.321.654-72', '6b86b273ff34fce19d6b804eff5a3f5747ada4eaa22f1d49c01e52ddb7875b4b', 'simone.cardoso@email.com', '(11) 90983-2165', 'Ativo'),
('Eduardo Ramos', '109.432.765-81', '5f70bf18a086007016e948b04aed3b82103a36bea41755b6cddfaf10ace3c6ef', 'eduardo.ramos@email.com', '(11) 91094-3276', 'Ativo'),
('Mariana Duarte', '210.543.876-90', '6b86b273ff34fce19d6b804eff5a3f5747ada4eaa22f1d49c01e52ddb7875b4b', 'mariana.duarte@email.com', '(11) 92105-4387', 'Ativo');

-- Inserir novos registros
INSERT INTO EMPRESTIMO (emp_prazo, emp_dataEmp, emp_dataDev, emp_dataDevReal, emp_status, fk_mem, fk_user, fk_liv) VALUES
-- 20 empréstimos finalizados
(7, '2025-01-05', '2025-01-12', '2025-01-11', 'Finalizado', 1, 2, 1),
(7, '2025-01-10', '2025-01-17', '2025-01-16', 'Finalizado', 2, 4, 5),
(7, '2025-01-15', '2025-01-22', '2025-01-21', 'Finalizado', 3, 6, 10),
(7, '2025-01-20', '2025-01-27', '2025-01-26', 'Finalizado', 4, 8, 15),
(7, '2025-01-25', '2025-02-01', '2025-01-31', 'Finalizado', 5, 2, 20),
(7, '2025-02-01', '2025-02-08', '2025-02-07', 'Finalizado', 6, 4, 25),
(7, '2025-02-05', '2025-02-12', '2025-02-11', 'Finalizado', 7, 6, 30),
(7, '2025-02-10', '2025-02-17', '2025-02-16', 'Finalizado', 8, 8, 35),
(7, '2025-02-15', '2025-02-22', '2025-02-21', 'Finalizado', 9, 2, 2),
(7, '2025-02-20', '2025-02-27', '2025-02-26', 'Finalizado', 10, 4, 7),
(7, '2025-03-01', '2025-03-08', '2025-03-07', 'Finalizado', 11, 6, 12),
(7, '2025-03-05', '2025-03-12', '2025-03-11', 'Finalizado', 12, 8, 17),
(7, '2025-03-10', '2025-03-17', '2025-03-16', 'Finalizado', 13, 2, 22),
(7, '2025-03-15', '2025-03-22', '2025-03-21', 'Finalizado', 14, 4, 27),
(7, '2025-03-20', '2025-03-27', '2025-03-26', 'Finalizado', 15, 6, 32),
(7, '2025-03-25', '2025-04-01', '2025-03-31', 'Finalizado', 16, 8, 37),
(7, '2025-04-01', '2025-04-08', '2025-04-07', 'Finalizado', 17, 2, 4),
(7, '2025-04-05', '2025-04-12', '2025-04-11', 'Finalizado', 18, 4, 9),
(7, '2025-04-10', '2025-04-17', '2025-04-16', 'Finalizado', 19, 6, 14),
(7, '2025-04-15', '2025-04-22', '2025-04-21', 'Finalizado', 20, 8, 19),

-- 8 empréstimos atrasados (datas de devolução no passado sem data de devolução real)
(7, '2025-05-01', '2025-05-08', NULL, 'Empréstimo Atrasado', 1, 2, 24),
(7, '2025-05-05', '2025-05-12', NULL, 'Empréstimo Atrasado', 2, 4, 29),
(7, '2025-05-10', '2025-05-17', NULL, 'Empréstimo Atrasado', 3, 6, 34),
(7, '2025-05-15', '2025-05-22', NULL, 'Empréstimo Atrasado', 4, 8, 39),
(7, '2025-05-20', '2025-05-27', NULL, 'Empréstimo Atrasado', 5, 2, 6),
(7, '2025-05-25', '2025-06-01', NULL, 'Empréstimo Atrasado', 6, 4, 11),
(7, '2025-06-01', '2025-06-08', NULL, 'Empréstimo Atrasado', 7, 6, 16),
(7, '2025-06-05', '2025-06-12', NULL, 'Empréstimo Atrasado', 8, 8, 21),

-- 20 empréstimos ativos (datas de devolução no futuro)
(7, '2025-06-10', '2025-06-17', NULL, 'Empréstimo Ativo', 9, 2, 26),
(7, '2025-06-15', '2025-06-22', NULL, 'Empréstimo Ativo', 10, 4, 31),
(7, '2025-06-20', '2025-06-27', NULL, 'Empréstimo Ativo', 11, 6, 36),
(7, '2025-06-25', '2025-07-02', NULL, 'Empréstimo Ativo', 12, 8, 3),
(7, '2025-07-01', '2025-07-08', NULL, 'Empréstimo Ativo', 13, 2, 8),
(7, '2025-07-05', '2025-07-12', NULL, 'Empréstimo Ativo', 14, 4, 13),
(7, '2025-07-10', '2025-07-17', NULL, 'Empréstimo Ativo', 15, 6, 18),
(7, '2025-07-15', '2025-07-22', NULL, 'Empréstimo Ativo', 16, 8, 23),
(7, '2025-07-20', '2025-07-27', NULL, 'Empréstimo Ativo', 17, 2, 28),
(7, '2025-07-25', '2025-08-01', NULL, 'Empréstimo Ativo', 18, 4, 33),
(7, '2025-08-01', '2025-08-08', NULL, 'Empréstimo Ativo', 19, 6, 38),
(7, '2025-08-05', '2025-08-12', NULL, 'Empréstimo Ativo', 20, 8, 40),
(7, '2025-08-10', '2025-08-17', NULL, 'Empréstimo Ativo', 1, 2, 1),
(7, '2025-08-15', '2025-08-22', NULL, 'Empréstimo Ativo', 2, 4, 5),
(7, '2025-08-20', '2025-08-27', NULL, 'Empréstimo Ativo', 3, 6, 10),
(7, '2025-08-25', '2025-09-01', NULL, 'Empréstimo Ativo', 4, 8, 15),
(7, '2025-09-01', '2025-09-08', NULL, 'Empréstimo Ativo', 5, 2, 20),
(7, '2025-09-05', '2025-09-12', NULL, 'Empréstimo Ativo', 6, 4, 25),
(7, '2025-09-10', '2025-09-17', NULL, 'Empréstimo Ativo', 7, 6, 30),
(7, '2025-09-15', '2025-09-22', NULL, 'Empréstimo Ativo', 8, 8, 35),

-- 5 renovações ativas (datas de devolução no futuro com status 'Renovação Ativa')
(7, '2025-09-20', '2025-09-27', NULL, 'Renovação Ativa', 9, 2, 2),
(7, '2025-09-25', '2025-10-02', NULL, 'Renovação Ativa', 10, 4, 7),
(7, '2025-10-01', '2025-10-08', NULL, 'Renovação Ativa', 11, 6, 12),
(7, '2025-10-05', '2025-10-12', NULL, 'Renovação Ativa', 12, 8, 17),
(7, '2025-10-10', '2025-10-17', NULL, 'Renovação Ativa', 13, 2, 22),

-- 2 renovações atrasadas (datas de devolução no passado com status 'Renovação Atrasada')
(7, '2025-10-15', '2025-10-22', NULL, 'Renovação Atrasada', 14, 4, 27),
(7, '2025-10-20', '2025-10-27', NULL, 'Renovação Atrasada', 15, 6, 32);

-- Inserir novos registros
INSERT INTO RESERVA (res_prazo, res_dataMarcada, res_dataVencimento, res_dataFinalizada, res_status, fk_mem, fk_liv, fk_user) VALUES
-- 5 reservas canceladas
(3, '2025-01-05', '2025-01-08', '2025-01-06', 'Cancelada', 1, 1, 2),
(3, '2025-01-10', '2025-01-13', '2025-01-11', 'Cancelada', 2, 5, 4),
(3, '2025-01-15', '2025-01-18', '2025-01-16', 'Cancelada', 3, 10, 6),
(3, '2025-01-20', '2025-01-23', '2025-01-21', 'Cancelada', 4, 15, 8),
(3, '2025-01-25', '2025-01-28', '2025-01-26', 'Cancelada', 5, 20, 2),

-- 17 reservas abertas (datas de vencimento no futuro)
(3, '2025-06-01', '2025-06-04', NULL, 'Aberta', 6, 25, 4),
(3, '2025-06-05', '2025-06-08', NULL, 'Aberta', 7, 30, 6),
(3, '2025-06-10', '2025-06-13', NULL, 'Aberta', 8, 35, 8),
(3, '2025-06-15', '2025-06-18', NULL, 'Aberta', 9, 2, 2),
(3, '2025-06-20', '2025-06-23', NULL, 'Aberta', 10, 7, 4),
(3, '2025-06-25', '2025-06-28', NULL, 'Aberta', 11, 12, 6),
(3, '2025-07-01', '2025-07-04', NULL, 'Aberta', 12, 17, 8),
(3, '2025-07-05', '2025-07-08', NULL, 'Aberta', 13, 22, 2),
(3, '2025-07-10', '2025-07-13', NULL, 'Aberta', 14, 27, 4),
(3, '2025-07-15', '2025-07-18', NULL, 'Aberta', 15, 32, 6),
(3, '2025-07-20', '2025-07-23', NULL, 'Aberta', 16, 37, 8),
(3, '2025-07-25', '2025-07-28', NULL, 'Aberta', 17, 4, 2),
(3, '2025-08-01', '2025-08-04', NULL, 'Aberta', 18, 9, 4),
(3, '2025-08-05', '2025-08-08', NULL, 'Aberta', 19, 14, 6),
(3, '2025-08-10', '2025-08-13', NULL, 'Aberta', 20, 19, 8),
(3, '2025-08-15', '2025-08-18', NULL, 'Aberta', 1, 24, 2),
(3, '2025-08-20', '2025-08-23', NULL, 'Aberta', 2, 29, 4),

-- 3 reservas atrasadas (datas de vencimento no passado sem data finalizada)
(3, '2025-05-01', '2025-05-04', NULL, 'Atrasada', 3, 34, 6),
(3, '2025-05-05', '2025-05-08', NULL, 'Atrasada', 4, 39, 8),
(3, '2025-05-10', '2025-05-13', NULL, 'Atrasada', 5, 6, 2),

-- 22 reservas finalizadas
(3, '2025-02-01', '2025-02-04', '2025-02-03', 'Finalizada', 6, 11, 4),
(3, '2025-02-05', '2025-02-08', '2025-02-07', 'Finalizada', 7, 16, 6),
(3, '2025-02-10', '2025-02-13', '2025-02-12', 'Finalizada', 8, 21, 8),
(3, '2025-02-15', '2025-02-18', '2025-02-17', 'Finalizada', 9, 26, 2),
(3, '2025-02-20', '2025-02-23', '2025-02-22', 'Finalizada', 10, 31, 4),
(3, '2025-02-25', '2025-02-28', '2025-02-27', 'Finalizada', 11, 36, 6),
(3, '2025-03-01', '2025-03-04', '2025-03-03', 'Finalizada', 12, 3, 8),
(3, '2025-03-05', '2025-03-08', '2025-03-07', 'Finalizada', 13, 8, 2),
(3, '2025-03-10', '2025-03-13', '2025-03-12', 'Finalizada', 14, 13, 4),
(3, '2025-03-15', '2025-03-18', '2025-03-17', 'Finalizada', 15, 18, 6),
(3, '2025-03-20', '2025-03-23', '2025-03-22', 'Finalizada', 16, 23, 8),
(3, '2025-03-25', '2025-03-28', '2025-03-27', 'Finalizada', 17, 28, 2),
(3, '2025-04-01', '2025-04-04', '2025-04-03', 'Finalizada', 18, 33, 4),
(3, '2025-04-05', '2025-04-08', '2025-04-07', 'Finalizada', 19, 38, 6),
(3, '2025-04-10', '2025-04-13', '2025-04-12', 'Finalizada', 20, 40, 8),
(3, '2025-04-15', '2025-04-18', '2025-04-17', 'Finalizada', 1, 1, 2),
(3, '2025-04-20', '2025-04-23', '2025-04-22', 'Finalizada', 2, 5, 4),
(3, '2025-04-25', '2025-04-28', '2025-04-27', 'Finalizada', 3, 10, 6),
(3, '2025-05-01', '2025-05-04', '2025-05-03', 'Finalizada', 4, 15, 8),
(3, '2025-05-05', '2025-05-08', '2025-05-07', 'Finalizada', 5, 20, 2),
(3, '2025-05-10', '2025-05-13', '2025-05-12', 'Finalizada', 6, 25, 4),
(3, '2025-05-15', '2025-05-18', '2025-05-17', 'Finalizada', 7, 30, 6);

INSERT INTO REMESSA (rem_data, rem_qtd, fk_forn, fk_liv, fk_user) VALUES
('2025-01-10', 5, 1, 1, 3),
('2025-01-15', 3, 2, 2, 5),
('2025-01-20', 4, 3, 3, 9),
('2025-02-05', 6, 4, 4, 3),
('2025-02-10', 2, 5, 5, 5),
('2025-02-15', 3, 6, 6, 9),
('2025-03-01', 5, 7, 7, 3),
('2025-03-05', 4, 8, 8, 5),
('2025-03-10', 3, 9, 9, 9),
('2025-03-15', 6, 10, 10, 3),
('2025-04-02', 2, 1, 11, 5),
('2025-04-07', 4, 2, 12, 9),
('2025-04-12', 3, 3, 13, 3),
('2025-04-17', 5, 4, 14, 5),
('2025-04-22', 2, 5, 15, 9),
('2025-05-03', 4, 6, 16, 3),
('2025-05-08', 3, 7, 17, 5),
('2025-05-13', 6, 8, 18, 9),
('2025-05-18', 2, 9, 19, 3),
('2025-05-23', 5, 10, 20, 5),
('2025-06-02', 3, 1, 21, 9),
('2025-06-07', 4, 2, 22, 3),
('2025-06-12', 2, 3, 23, 5),
('2025-06-17', 6, 4, 24, 9),
('2025-07-01', 3, 5, 25, 3),
('2025-07-06', 5, 6, 26, 5),
('2025-07-11', 2, 7, 27, 9),
('2025-07-16', 4, 8, 28, 3),
('2025-07-21', 3, 9, 29, 5),
('2025-07-26', 6, 10, 30, 9);

-- Inserir novos registros
-- 8 multas em aberto (correspondendo aos 8 empréstimos atrasados)
INSERT INTO MULTA (mul_valor, mul_qtdDias, mul_status, fk_mem, fk_emp) VALUES
-- Calculando dias de atraso até 30/06/2025
(90.00, 60, 'Aberta', 1, 21),   -- Atraso desde 08/05 (53 dias)
(82.50, 55, 'Aberta', 2, 22),   -- Atraso desde 12/05 (49 dias)
(75.00, 50, 'Aberta', 3, 23),   -- Atraso desde 17/05 (44 dias)
(67.50, 45, 'Aberta', 4, 24),   -- Atraso desde 22/05 (39 dias)
(60.00, 40, 'Aberta', 5, 25),   -- Atraso desde 27/05 (34 dias)
(52.50, 35, 'Aberta', 6, 26),   -- Atraso desde 01/06 (29 dias)
(45.00, 30, 'Aberta', 7, 27),   -- Atraso desde 08/06 (22 dias)
(37.50, 25, 'Aberta', 8, 28),   -- Atraso desde 12/06 (18 dias)

-- 4 multas finalizadas (para empréstimos finalizados que estiveram atrasados)
(15.00, 10, 'Finalizada', 9, 5),    -- Empréstimo finalizado após atraso
(12.00, 8, 'Finalizada', 10, 10),   -- Empréstimo finalizado após atraso
(9.00, 6, 'Finalizada', 11, 15),    -- Empréstimo finalizado após atraso
(6.00, 4, 'Finalizada', 12, 20);    -- Empréstimo finalizado após atraso