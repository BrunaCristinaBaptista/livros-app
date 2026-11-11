<?php
// Lista de livros com Adicionar / Editar / Remover (equivale ao Address Book do lab).
// Incluída pelo rds.php quando o banco já está configurado ($config vem de lá).

$erro = null;
$livros = [];
$editando = null;
$modo = $_GET['modo'] ?? '';                 // '', 'adicionar' ou 'editar'
$avisos = ['adicionado' => 'Livro adicionado!', 'editado' => 'Livro atualizado!', 'removido' => 'Livro removido!'];

try {
    $db = conectar($config);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $acao   = $_POST['acao'] ?? '';
        $id     = (int)($_POST['id'] ?? 0);
        $titulo = trim($_POST['titulo'] ?? '');
        $autor  = trim($_POST['autor'] ?? '');
        $codigo = trim($_POST['codigo'] ?? '');

        if ($acao === 'remover') {
            $st = $db->prepare('DELETE FROM livros WHERE id = ?');
            $st->bind_param('i', $id);
            $st->execute();
            $feito = 'removido';
        } elseif ($titulo === '' || $autor === '' || $codigo === '') {
            $erro = 'Preencha título, autor e código.';
        } elseif ($acao === 'adicionar') {
            // Prepared statement: o texto digitado nunca vira comando SQL (evita SQL injection)
            $st = $db->prepare('INSERT INTO livros (titulo, autor, codigo) VALUES (?, ?, ?)');
            $st->bind_param('sss', $titulo, $autor, $codigo);
            $st->execute();
            $feito = 'adicionado';
        } elseif ($acao === 'editar') {
            $st = $db->prepare('UPDATE livros SET titulo = ?, autor = ?, codigo = ? WHERE id = ?');
            $st->bind_param('sssi', $titulo, $autor, $codigo, $id);
            $st->execute();
            $feito = 'editado';
        }
        if (!$erro && isset($feito)) {        // volta para a lista (evita reenviar o formulário)
            header('Location: rds.php?ok=' . $feito);
            exit;
        }
        $modo = $acao === 'editar' ? 'editar' : 'adicionar';
        $editando = ['id' => $id, 'titulo' => $titulo, 'autor' => $autor, 'codigo' => $codigo];
    }

    if ($modo === 'editar' && $editando === null) {   // carrega o livro do banco
        $st = $db->prepare('SELECT * FROM livros WHERE id = ?');
        $idEditar = (int)($_GET['id'] ?? 0);
        $st->bind_param('i', $idEditar);
        $st->execute();
        $editando = $st->get_result()->fetch_assoc();
        if (!$editando) {
            $modo = '';
        }
    }

    $livros = $db->query('SELECT * FROM livros ORDER BY titulo')->fetch_all(MYSQLI_ASSOC);
} catch (mysqli_sql_exception $e) {
    $erro = 'Erro ao acessar o banco: ' . $e->getMessage();
}

$f = $editando ?? ['id' => 0, 'titulo' => '', 'autor' => '', 'codigo' => ''];
cabecalho('RDS');
?>
            <h2>Livros recomendados</h2>
            <?php if (isset($avisos[$_GET['ok'] ?? ''])): ?><p class="text-success"><?= $avisos[$_GET['ok']] ?></p><?php endif; ?>
            <?php if ($erro): ?><p class="text-danger"><?= h($erro) ?></p><?php endif; ?>

            <?php if ($modo === 'adicionar' || $modo === 'editar'): ?>
              <h3><?= $modo === 'editar' ? 'Editar livro' : 'Adicionar livro' ?></h3>
              <form action="rds.php" method="post" class="form-horizontal">
                <input type="hidden" name="acao" value="<?= $modo ?>">
                <input type="hidden" name="id" value="<?= (int)$f['id'] ?>">
                <div class="form-group">
                  <label for="titulo" class="col-sm-2 control-label">Título</label>
                  <div class="col-sm-10"><input type="text" class="form-control" id="titulo" name="titulo" maxlength="200" value="<?= h($f['titulo']) ?>" required></div>
                </div>
                <div class="form-group">
                  <label for="autor" class="col-sm-2 control-label">Autor</label>
                  <div class="col-sm-10"><input type="text" class="form-control" id="autor" name="autor" maxlength="150" value="<?= h($f['autor']) ?>" required></div>
                </div>
                <div class="form-group">
                  <label for="codigo" class="col-sm-2 control-label">Código</label>
                  <div class="col-sm-10"><input type="text" class="form-control" id="codigo" name="codigo" maxlength="50" value="<?= h($f['codigo']) ?>" required></div>
                </div>
                <div class="form-group">
                  <div class="col-sm-offset-2 col-sm-10">
                    <input type="submit" value="Salvar" class="btn btn-default">
                    <a href="rds.php">Cancelar</a>
                  </div>
                </div>
              </form>
            <?php endif; ?>

            <table class="table table-bordered">
              <tr>
                <th>Título</th><th>Autor</th><th>Código na biblioteca</th>
                <th colspan="2" class="text-right"><a href="rds.php?modo=adicionar">Adicionar livro</a></th>
              </tr>
              <?php foreach ($livros as $l): ?>
                <tr>
                  <td><?= h($l['titulo']) ?></td>
                  <td><?= h($l['autor']) ?></td>
                  <td><?= h($l['codigo']) ?></td>
                  <td><a href="rds.php?modo=editar&amp;id=<?= (int)$l['id'] ?>">Editar</a></td>
                  <td>
                    <!-- Remover é um botão (POST), não um link: um link pode ser aberto sem querer -->
                    <form action="rds.php" method="post" class="form-inline-remover">
                      <input type="hidden" name="acao" value="remover">
                      <input type="hidden" name="id" value="<?= (int)$l['id'] ?>">
                      <button type="submit" class="btn btn-link btn-xs">Remover</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if (!$livros && !$erro): ?><tr><td colspan="5">Nenhum livro cadastrado.</td></tr><?php endif; ?>
            </table>
<?php rodape();
