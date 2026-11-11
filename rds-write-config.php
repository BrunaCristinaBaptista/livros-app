<?php
// Recebe o formulário RDS: testa a conexão, cria a tabela de livros e grava a configuração.
require __DIR__ . '/comum.php';

$passos = [];     // mensagens mostradas na tela, como no app do lab
$deu_certo = false;

if (config_banco() !== null) {
    // Já configurado: não deixa trocar o banco por aqui (senão qualquer um poderia)
    $passos[] = 'O banco já está configurado. Para trocar, recrie o servidor.';
} elseif ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $passos[] = 'Preencha o formulário na página RDS.';
} else {
    $novo = [
        'host'    => str_replace(':3306', '', trim($_POST['endpoint'] ?? '')),
        'banco'   => trim($_POST['database'] ?? ''),
        'usuario' => trim($_POST['username'] ?? ''),
        'senha'   => $_POST['password'] ?? '',
    ];
    try {
        $db = conectar($novo);
        $passos[] = 'Conectado ao banco ' . $novo['banco'] . ' em ' . $novo['host'] . '.';

        // Executa o sql/livros.sql (cria a tabela e coloca os livros de exemplo)
        $db->multi_query(file_get_contents(__DIR__ . '/sql/livros.sql'));
        do {
            if ($resultado = $db->store_result()) {
                $resultado->free();
            }
        } while ($db->more_results() && $db->next_result());
        $passos[] = 'Tabela de livros criada (sql/livros.sql).';

        // Grava a configuração fora da pasta pública
        file_put_contents(CONFIG_BANCO, "<?php\nreturn " . var_export($novo, true) . ";\n", LOCK_EX);
        $passos[] = 'Configuração gravada.';
        $deu_certo = true;
    } catch (mysqli_sql_exception $e) {
        $passos[] = 'Não foi possível conectar: ' . $e->getMessage();
    } catch (Throwable $e) {
        $passos[] = 'Erro ao gravar a configuração: ' . $e->getMessage();
    }
}

cabecalho('RDS');
?>
            <?php if ($deu_certo): ?><meta http-equiv="refresh" content="5;URL=rds.php"><?php endif; ?>
            <?php foreach ($passos as $p): ?>
              <p><?= h($p) ?></p>
            <?php endforeach; ?>
            <p><i>
              <?= $deu_certo ? 'Abrindo a lista de livros em 5 segundos (ou clique' : 'Voltar para a' ?>
              <a href="rds.php"><?= $deu_certo ? 'aqui' : 'página RDS' ?></a><?= $deu_certo ? ').' : '.' ?>
            </i></p>
<?php rodape();
