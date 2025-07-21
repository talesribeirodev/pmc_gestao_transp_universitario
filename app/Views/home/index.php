<?php
function getStatusBadge($statusId, $statusNome) {
    switch ($statusId) {
        case 1:
            $classe = 'primary';
            break;
        case 2:
            $classe = 'success';
            break;
        case 3:
            $classe = 'danger';
            break;
        default:
            $classe = 'secondary';
    }
    return "<span class='badge bg-$classe'>$statusNome</span>";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Inscrições - Listagem</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    rel="stylesheet"
    />
    <link rel="shortcut icon" href="<?= BASE_URL ?>assets/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/styles.css">
</head>
<body>
<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<main class="container mt-4">
    <h1 style="margin-bottom: 1rem;">Inscrições - Transporte Universitário</h1>

    <table id="inscricoesTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th></th>
                <th>CPF</th>
                <th>Nome</th>
                <th>Instituição</th>
                <th>Embarque</th>
                <th>Destino</th>
                <th>Retorno</th>
                <th>Status</th>
                <th>Data de Registro</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dados as $inscricao): ?>
                <tr data-id="<?= htmlspecialchars($inscricao['id']) ?>" data-cpf="<?= htmlspecialchars($inscricao['cpf']) ?>" 
                    data-read='<?= htmlspecialchars(json_encode($inscricao['read'] ?? null, JSON_HEX_APOS | JSON_HEX_QUOT)) ?>'>
                    <td class="details-control text-center" style="cursor: pointer;">
                        <i class="bi bi-plus-circle-fill"></i>
                    </td>
                    <td><?= htmlspecialchars($inscricao['cpf']) ?></td>
                    <td><?= htmlspecialchars($inscricao['nome']) ?></td>
                    <td><?= htmlspecialchars($inscricao['instituicao_ensino']) ?></td>
                    <td><?= htmlspecialchars($inscricao['embarque']) ?></td>
                    <td><?= htmlspecialchars($inscricao['destino']) ?></td>
                    <td><?= htmlspecialchars($inscricao['retorno']) ?></td>
                    <td><?= getStatusBadge($inscricao['id_status'], htmlspecialchars($inscricao['nome_status'])) ?></td>
                    <td>
                        <?php
                        $dt = new DateTime($inscricao['createdAt']);
                        echo htmlspecialchars($dt->format('d/m/Y H:i'));
                        ?>
                    </td>
                    <td class="d-flex gap-1">
                        <button 
                            class="btn btn-primary btn-sm visualizar-btn" 
                            data-bs-toggle="modal" 
                            data-bs-target="#modalVisualizar" 
                            data-inscricao='<?= json_encode($inscricao, JSON_HEX_APOS | JSON_HEX_QUOT) ?>'
                            title="Visualizar"
                        >
                            <i class="fa fa-eye"></i>
                        </button>

                        <?php if ($inscricao['id_status'] == 1): ?>
                            <button class="btn btn-success btn-sm aprovar-btn" title="Aprovar">
                                <i class="fa fa-check"></i>
                            </button>
                            <button class="btn btn-danger btn-sm cancelar-btn" title="Rejeitar">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="modal fade" id="modalVisualizar" tabindex="-1" aria-labelledby="modalVisualizarLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalVisualizarLabel">Visualizar Inscrição</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Campo</th>
                                <th>Dados Anteriores</th>
                                <th>Dados Atuais</th>
                            </tr>
                        </thead>
                        <tbody id="dadosComparacao"></tbody>
                    </table>

                    <p><i class="fa-solid fa-triangle-exclamation text-warning ms-1" title="Dado alterado"></i> <em>Campos divergentes</em></p>

                    <hr>

                    <h6>Documentos</h6>
                    <div id="documentos" class="d-flex flex-wrap gap-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal detalhes -->
<div class="modal fade" id="modalDocumento" tabindex="-1" aria-labelledby="modalDocumentoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalDocumentoLabel">Visualizar Documento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body" id="conteudoDocumento" style="min-height: 500px; text-align: center;">
      </div>
    </div>
  </div>
</div>


<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/medium-zoom@1.0.6/dist/medium-zoom.min.js"></script>
<script src="<?= BASE_URL ?>assets/js/main.js"></script>
</body>
</html>
