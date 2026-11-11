<?php
// Página RDS: igual ao app do lab.
//   - Banco ainda não configurado → mostra o formulário de conexão
//   - Banco configurado           → mostra a lista de livros
require __DIR__ . '/comum.php';

$config = config_banco();
if ($config === null) {
    cabecalho('RDS');
    include __DIR__ . '/rds-config.php';
    rodape();
} else {
    include __DIR__ . '/rds-livros.php';
}
