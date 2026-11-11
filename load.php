<?php
// Load Test: ocupa a CPU da instância por 60 segundos e recarrega sozinha a cada 5.
// Serve para ver a CPU subir no console (aba Monitoring da EC2 ou CloudWatch).
require __DIR__ . '/comum.php';

$cpu = uso_cpu();
if ($cpu < 50) {
    // Um laço infinito em segundo plano, que o "timeout" encerra depois de 60 s
    exec('timeout 60 sh -c "while :; do :; done" > /dev/null 2>&1 &');
    $mensagem = 'Gerando carga na CPU! (atualiza sozinha em 5 segundos)';
} else {
    $mensagem = 'CPU já está com carga alta! (atualiza sozinha em 5 segundos)';
}

cabecalho('Load Test');
?>
            <meta http-equiv="refresh" content="5;URL=load.php">
            <p><?= h($mensagem) ?></p>
            <p><small>Instância: <?= h(metadado('instance-id')) ?></small></p>
            <hr>
            <p>Carga atual da CPU: <b><?= $cpu ?>%</b></p>
<?php rodape();
