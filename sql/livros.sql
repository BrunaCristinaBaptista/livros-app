-- ================================================================
-- Livros Recomendados
-- Elaborado por Bruna Cristina Kato Baptista e Elizeu Elias Ribeiro
-- Projeto final de Fundamentos de Computação em Nuvem
-- ================================================================

-- Créditos do projeto, guardados no próprio banco
CREATE TABLE IF NOT EXISTS creditos (
    id    INT AUTO_INCREMENT PRIMARY KEY,
    texto VARCHAR(255) NOT NULL
);

INSERT INTO creditos (texto)
SELECT 'Elaborado por Bruna Cristina Kato Baptista e Elizeu Elias Ribeiro' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM creditos);

-- Tabela de livros
CREATE TABLE IF NOT EXISTS livros (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    autor  VARCHAR(150) NOT NULL,
    codigo VARCHAR(50)  NOT NULL
);

-- Livros iniciais, inseridos só se a tabela estiver vazia.
-- ATENÇÃO: os códigos são EXEMPLOS no formato de número de chamada.
-- Troque pelos códigos reais do catálogo da biblioteca.
INSERT INTO livros (titulo, autor, codigo)
SELECT * FROM (
              SELECT 'Dom Casmurro' AS titulo, 'Machado de Assis' AS autor, '869.3 A848d' AS codigo
    UNION ALL SELECT 'Engenharia de Software', 'Ian Sommerville', '005.1 S697e'
    UNION ALL SELECT 'Código Limpo', 'Robert C. Martin', '005.1 M379c'
    UNION ALL SELECT 'O Programador Pragmático', 'David Thomas e Andrew Hunt', '005.1 T454p'
    UNION ALL SELECT 'Sistemas de Banco de Dados', 'Ramez Elmasri e Shamkant B. Navathe', '005.74 E48s'
    UNION ALL SELECT 'Redes de Computadores', 'Andrew S. Tanenbaum', '004.6 T164r'
    UNION ALL SELECT 'Algoritmos: Teoria e Prática', 'Thomas H. Cormen et al.', '005.1 A396'
    UNION ALL SELECT 'Computação em Nuvem: Conceitos, Tecnologia e Arquitetura', 'Thomas Erl', '004.6782 E69c'
    UNION ALL SELECT 'Sapiens: Uma Breve História da Humanidade', 'Yuval Noah Harari', '909 H282s'
) AS exemplos
WHERE NOT EXISTS (SELECT 1 FROM livros);
