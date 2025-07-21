<?php
// header.php
$userName = $_SESSION['usuario']['DisplayName'] ?? 'Usuário';
?>

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm" id="header">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>?url=home/index">
      <img src="<?= BASE_URL ?>assets/images/logo.png" alt="Logo" height="60" class="me-2" style="object-fit: contain;">
    </a>

    <button
      class="navbar-toggler"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#navbarHeader"
      aria-controls="navbarHeader"
      aria-expanded="false"
      aria-label="Toggle navigation"
    >
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarHeader">
      <ul class="navbar-nav align-items-center">
        <li class="nav-item">
          <span class="text-white">Olá, <strong><?= htmlspecialchars(explode(' ', trim($userName))[0]) ?></strong></span>
        </li>
        <li class="nav-item">
          <a href="<?= BASE_URL ?>?url=login/sair" class="btn btn-danger">
            Sair
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
