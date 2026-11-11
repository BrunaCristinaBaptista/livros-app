<?php
// Funções usadas por todas as páginas.

// Onde fica a configuração do banco: FORA da pasta pública (/var/www/html),
// para ninguém conseguir abrir pelo navegador. O formulário RDS grava aqui.
const CONFIG_BANCO = '/var/www/livros-config.php';

// Escapa texto antes de mostrar na página (evita XSS)
function h($texto): string {
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}

// Lê um dado da instância no serviço de metadados da AWS (IMDSv2)
function metadado(string $caminho): string {
    static $token = null;
    if ($token === null) {
        $ctx = stream_context_create(['http' => ['method' => 'PUT', 'timeout' => 2,
            'header' => "X-aws-ec2-metadata-token-ttl-seconds: 60\r\n"]]);
        $token = @file_get_contents('http://169.254.169.254/latest/api/token', false, $ctx) ?: '';
    }
    $ctx = stream_context_create(['http' => ['timeout' => 2,
        'header' => "X-aws-ec2-metadata-token: $token\r\n"]]);
    return @file_get_contents("http://169.254.169.254/latest/meta-data/$caminho", false, $ctx) ?: 'desconhecido';
}

// Uso da CPU em %, medido em 1 segundo (lê o /proc/stat do Linux duas vezes)
function uso_cpu(): int {
    $ler = function () {
        $v = preg_split('/\s+/', trim(explode("\n", file_get_contents('/proc/stat'))[0]));
        $ocioso = $v[4] + $v[5];               // idle + iowait
        return [$ocioso, array_sum(array_slice($v, 1, 8))];
    };
    [$ocioso1, $total1] = $ler();
    sleep(1);
    [$ocioso2, $total2] = $ler();
    $total = $total2 - $total1;
    return $total > 0 ? (int)round(100 * (1 - ($ocioso2 - $ocioso1) / $total)) : 0;
}

// Configuração do banco: ['host', 'banco', 'usuario', 'senha'] ou null se ainda não foi feita
function config_banco(): ?array {
    if (!is_readable(CONFIG_BANCO)) {
        return null;
    }
    $c = include CONFIG_BANCO;
    return is_array($c) && !empty($c['host']) ? $c : null;
}

// Conecta ao banco no RDS
function conectar(array $c): mysqli {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);   // erro vira exceção
    $db = new mysqli($c['host'], $c['usuario'], $c['senha'], $c['banco']);
    $db->set_charset('utf8mb4');                                  // acentos
    return $db;
}

// Topo de todas as páginas (Bootstrap 3, o mesmo do app do lab)
function cabecalho(string $titulo): void {
    echo <<<HTML
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$titulo} · Livros Recomendados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
  </head>
  <body>
    <div class="container">
      <div class="row">
        <div class="col-md-12">
HTML;
    include __DIR__ . '/menu.php';
    echo "\n          <div class=\"jumbotron\">\n";
}

// Fim de todas as páginas, com os créditos do projeto
function rodape(): void {
    echo <<<HTML

          </div>
          <footer class="text-center text-muted creditos">
            Elaborado por Bruna Cristina Kato Baptista e Elizeu Elias Ribeiro
          </footer>
        </div>
      </div>
    </div>
  </body>
</html>

HTML;
}
