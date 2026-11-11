<?php
// Página inicial: mostra qual instância respondeu e a carga da CPU.
// Com o balanceador, atualizar esta página mostra o Instance ID alternando.
require __DIR__ . '/comum.php';
header('Cache-Control: no-cache, must-revalidate');   // sempre buscar a versão nova

cabecalho('Início');
?>
            <table class="table table-bordered">
              <tr><th>Metadado</th><th>Valor</th></tr>
              <tr><td>Instance ID</td><td><i><?= h(metadado('instance-id')) ?></i></td></tr>
              <tr><td>Availability Zone</td><td><i><?= h(metadado('placement/availability-zone')) ?></i></td></tr>
            </table>
            <hr>
            <p>Carga atual da CPU: <b><?= uso_cpu() ?>%</b></p>
<?php rodape();
