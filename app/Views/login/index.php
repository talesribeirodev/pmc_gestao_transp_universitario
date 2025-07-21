<?php
// app/Views/login/index.php
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <title>Login - Prefeitura de Cajamar</title>
  <link rel="shortcut icon" href="<?= BASE_URL ?>assets/images/favicon.png" type="image/x-icon">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.7/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/styles.css">
</head>

<body class="d-flex align-items-center justify-content-center vh-100" id="login">

  <div class="card shadow-sm" style="width: 100%; max-width: 450px;">
    <div class="card-body px-4">
      <div class="text-center mb-4 pt-4">
        <img src="<?= BASE_URL ?>assets/images/favicon.png" alt="Logo" style="max-width: 150px; height: auto;">
      </div>

      <div class="py-2">
        <h4 class="text-center">Login</h4>
        <p class="text-center lead"><small>Seja bem-vindo(a) à gestão de Transporte Universitário</small></p>
      </div>

      <?php if (isset($erro)): ?>
        <div class="alert alert-danger" role="alert">
          <?= htmlspecialchars($erro) ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="<?= BASE_URL ?>?url=login/autenticar">
        <div class="mb-3">
          <label for="cpf" class="form-label">CPF</label>
          <input
            type="text"
            class="form-control"
            id="cpf"
            name="cpf"
            placeholder="00000000000"
            maxlength="11"
            inputmode="numeric"
            pattern="\d{11}"
            oninput="this.value = this.value.replace(/\D/g, '')"
            required
            autofocus
          >
        </div>

        <div class="mb-3">
          <label for="senha" class="form-label">Senha</label>
          <input
            type="password"
            class="form-control"
            id="senha"
            name="senha"
            placeholder="********"
            minlength=7
            required
          >
        </div>

        <button type="submit" class="btn w-100" id="btn-login">Entrar</button>

        <?php require_once __DIR__ . '/../layouts/footer.php'; ?>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
