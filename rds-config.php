<!-- Formulário de conexão com o banco (Lab 5, Tarefa 4) -->
<h2>Conectar ao banco de dados</h2>
<p>Preencha com os dados do banco criado no RDS.</p>
<form action="rds-write-config.php" method="post" class="form-horizontal">
  <div class="form-group">
    <label for="endpoint" class="col-sm-2 control-label">Endpoint</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="endpoint" name="endpoint"
             placeholder="livros-db.xxxx.us-east-1.rds.amazonaws.com" required>
    </div>
  </div>
  <div class="form-group">
    <label for="database" class="col-sm-2 control-label">Database</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="database" name="database" value="Livrosdb" required>
    </div>
  </div>
  <div class="form-group">
    <label for="username" class="col-sm-2 control-label">Username</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="username" name="username" value="main" required>
    </div>
  </div>
  <div class="form-group">
    <label for="password" class="col-sm-2 control-label">Password</label>
    <div class="col-sm-10">
      <input type="password" class="form-control" id="password" name="password" required>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <input type="submit" value="Submit" class="btn btn-default">
    </div>
  </div>
</form>
