$(document).ready(function () {
  var table = $("#inscricoesTable").DataTable({
    language: {
      url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json",
    },
    order: [[1, "desc"]],
  });

  $("#inscricoesTable tbody").on("click", "td.details-control", function () {
    var tr = $(this).closest("tr");
    var row = table.row(tr);

    if (row.child.isShown()) {
      row.child.hide();
      tr.removeClass("shown");
      $(this)
        .find("i")
        .removeClass("bi-dash-circle-fill")
        .addClass("bi-plus-circle-fill");
    } else {
      var readData = tr.data("read");

      if (!readData) {
        row
          .child(
            '<div style="padding:10px;"><em>Dados antigos não disponíveis.</em></div>'
          )
          .show();
      } else {
        var detalhesHtml = `
                <div style="padding:10px;">
                    <p><strong>Dados Anteriores:</strong></p>
                    <strong>Instituição:</strong> ${
                      readData.instituicao_ensino || "-"
                    }<br>
                    <strong>Embarque:</strong> ${readData.embarque || "-"}<br>
                    <strong>Destino:</strong> ${readData.destino || "-"}<br>
                    <strong>Retorno:</strong> ${readData.retorno || "-"}<br>
                    <strong>Endereço:</strong> ${readData.endereco || "-"}<br>
                    <strong>Bairro:</strong> ${readData.bairro || "-"}<br>
                </div>
            `;

        row.child(detalhesHtml).show();
      }

      tr.addClass("shown");
      $(this)
        .find("i")
        .removeClass("bi-plus-circle-fill")
        .addClass("bi-dash-circle-fill");
    }
  });

  const camposDatas = [
    "inicio_curso",
    "termino_curso",
    "createdAt",
    "updatedAt",
  ];

  $(".visualizar-btn").on("click", function () {
    const inscricao = $(this).data("inscricao");

    $("#dadosComparacao").empty();
    $("#documentos").empty();

    const read = inscricao.read || {};
    const write = inscricao;

    const camposComparacao = [
      { label: "Nome", read: "nome", write: "nome" },
      { label: "CPF", read: "cpf", write: "cpf" },
      {
        label: "Instituição",
        read: "instituicao_ensino",
        write: "instituicao_ensino",
      },
      { label: "Curso", read: "curso", write: "curso" },
      { label: "Embarque", read: "embarque", write: "embarque" },
      { label: "Destino", read: "destino", write: "destino" },
      { label: "Retorno", read: "retorno", write: "retorno" },
      {
        label: "Duração do Curso",
        read: "duracao_curso",
        write: "duracao_curso",
      },
      { label: "Início do Curso", read: "inicio_curso", write: "inicio_curso" },
      {
        label: "Término do Curso",
        read: "termino_curso",
        write: "termino_curso",
      },
      { label: "Endereco", read: "endereco", write: "endereco" },
      { label: "Bairro", read: "bairro", write: "bairro" },
      { label: "E-mail", read: "email", write: "email" },
      { label: "Celular", read: "celular", write: "celular" },
    ];

    camposComparacao.forEach(({ label, read: readKey, write: writeKey }) => {
      let valorRead = read[readKey] ?? "-";
      let valorWrite = write[writeKey] ?? "-";

      if (camposDatas.includes(readKey)) valorRead = formatarData(valorRead);
      if (camposDatas.includes(writeKey)) valorWrite = formatarData(valorWrite);

      const diferentes =
        valorRead !== valorWrite && valorRead !== "-" && valorWrite !== "-";
      const iconeAtencao = diferentes
        ? ' <i class="fa-solid fa-triangle-exclamation text-warning ms-1" title="Dado alterado"></i>'
        : "";

      $("#dadosComparacao").append(`
        <tr${diferentes ? ' class="table-warning"' : ""}>
          <th>${label}${iconeAtencao}</th>
          <td>${valorRead}</td>
          <td>${valorWrite}</td>
        </tr>
      `);
    });

    if (inscricao.caminhos_imagens && inscricao.caminhos_imagens.length) {
      inscricao.caminhos_imagens.forEach(function (caminho) {
        const nomeArquivo = caminho.split("/").pop();

        let textoBotao = "";
        if (nomeArquivo.includes("residencia")) {
          textoBotao = "Comprovante de Residência";
        } else if (nomeArquivo.includes("matricula")) {
          textoBotao = "Comprovante de Matrícula";
        } else if (nomeArquivo.includes("eleitor")) {
          textoBotao = "Título de Eleitor";
        } else if (nomeArquivo.includes("foto")) {
          textoBotao = "Foto do Estudante";
        } else {
          textoBotao = "Documento";
        }

        $("#documentos").append(`
          <button 
            class="btn btn-outline-primary m-1 abrir-documento-btn"
            data-caminho="${caminho}"
            data-titulo="${textoBotao}"
            data-inscricao='${JSON.stringify(inscricao)}'
          > 
            ${textoBotao}
          </button>
        `);
      });
    } else {
      $("#documentos").append("<p><em>Sem documentos anexados.</em></p>");
    }

    const modal = new bootstrap.Modal(
      document.getElementById("modalVisualizar")
    );
    modal.show();
  });

  function formatarData(dataStr) {
    if (!dataStr) return "-";
    const dt = new Date(dataStr);
    if (isNaN(dt)) return dataStr;
    return dt.toLocaleDateString("pt-BR");
  }

  $("#modalVisualizar").on("hidden.bs.modal", function () {
    $(".modal-backdrop").remove();
    $("body").removeClass("modal-open");
  });

  $(".aprovar-btn").on("click", function () {
    const tr = $(this).closest("tr");
    const inscricaoId =
      tr.data("id") ||
      tr.find(".aprovar-btn").data("id") ||
      tr.data("inscricao-id");

    Swal.fire({
      title: "Confirmar aprovação",
      text: "Você deseja aprovar esta inscrição?",
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Sim, aprovar",
      cancelButtonText: "Cancelar",
      customClass: {
        confirmButton: "btn btn-success me-2",
        cancelButton: "btn btn-secondary",
      },
      buttonsStyling: false,
    }).then((result) => {
      if (result.isConfirmed) {
        $.post("?url=submit/aprovar", { id: inscricaoId })
          .done(function () {
            Swal.fire(
              "Aprovado!",
              "A inscrição foi aprovada com sucesso.",
              "success"
            ).then(() => location.reload());
          })
          .fail(function () {
            Swal.fire("Erro", "Não foi possível aprovar a inscrição.", "error");
          });
      }
    });
  });

  $(".cancelar-btn").on("click", function () {
    const tr = $(this).closest("tr");
    const inscricaoId = tr.data("id");

    Swal.fire({
      title: "Rejeitar inscrição",
      icon: "warning",
      input: "textarea",
      inputLabel: "Descreva o motivo do rejeição da inscrição",
      inputPlaceholder: "Digite a justificativa para a rejeição...",
      inputAttributes: {
        "aria-label": "Justificativa para rejeição",
      },
      showCancelButton: true,
      confirmButtonText: "Confirmar Rejeição",
      cancelButtonText: "Cancelar",
      inputValidator: (value) => {
        if (!value) {
          return "Você precisa informar uma justificativa!";
        }
        if (value.trim().length < 10) {
          return "A justificativa deve ter pelo menos 10 caracteres.";
        }
      },
      customClass: {
        confirmButton: "btn btn-danger me-2",
        cancelButton: "btn btn-secondary",
      },
      buttonsStyling: false,
    }).then((result) => {
      if (result.isConfirmed) {
        const justificativa = result.value;

        $.post("?url=submit/cancelar", {
          id: inscricaoId,
          justificativa: justificativa,
        })
          .done(function () {
            Swal.fire(
              "Concluído!",
              "A inscrição foi rejeitada com sucesso.",
              "success"
            ).then(() => location.reload());
          })
          .fail(function () {
            Swal.fire(
              "Erro",
              "Não foi possível rejeitar a inscrição.",
              "error"
            );
          });
      }
    });
  });

  $(document).on("click", ".abrir-documento-btn", function () {
    const caminho = $(this).data("caminho");
    const titulo = $(this).data("titulo");
    const inscricao = $(this).data("inscricao") || {};
    const extensao = caminho.split(".").pop().toLowerCase();

    $("#modalDocumentoLabel").text(titulo);

    let camposExibir = [];

    if (titulo === "Comprovante de Residência") {
      camposExibir = ["endereco", "bairro"];
    } else if (titulo === "Comprovante de Matrícula") {
      camposExibir = [
        "instituicao_ensino",
        "curso",
        "inicio_curso",
        "termino_curso",
      ];
    } else if (titulo === "Título de Eleitor") {
      camposExibir = ["nome", "cpf"];
    } else if (titulo === "Foto do Estudante") {
      camposExibir = [];
    } else {
      camposExibir = [];
    }

    const labelsCampos = {
      endereco: "Endereço",
      bairro: "Bairro",
      instituicao_ensino: "Instituição",
      curso: "Curso",
      inicio_curso: "Início do Curso",
      termino_curso: "Término do Curso",
      nome: "Nome",
      cpf: "CPF",
    };

    let camposHtml = "";
    camposExibir.forEach((campo) => {
      let valor = inscricao[campo] || "-";
      if (["inicio_curso", "termino_curso"].includes(campo) && valor !== "-") {
        const dt = new Date(valor);
        if (!isNaN(dt)) valor = dt.toLocaleDateString("pt-BR");
      }
      const label = labelsCampos[campo] || campo;
      camposHtml += `<p><strong>${label}:</strong> ${valor}</p>`;
    });

    let conteudo = "";

    if (["jpg", "jpeg", "png", "gif", "bmp", "webp"].includes(extensao)) {
      conteudo = `
      <div class="border rounded p-3 mb-3 bg-light">
        <h6><i class="fa fa-user"></i> Dados do Usuário</h6>
        ${camposHtml}
      </div>
      <img src="${caminho}" alt="${titulo}" class="img-fluid rounded shadow zoomable">
    `;
    } else if (extensao === "pdf") {
      conteudo = `
      <div class="border rounded p-3 mb-3 bg-light">
        <h6><i class="fa fa-user"></i> Dados do Usuário</h6>
        ${camposHtml}
      </div>
      <iframe src="${caminho}" style="width:100%; height:80vh; border:none;" frameborder="0"></iframe>
    `;
    } else {
      conteudo = `
      <div class="border rounded p-3 mb-3 bg-light">
        <h6><i class="fa fa-user"></i> Dados do Usuário</h6>
        ${camposHtml}
      </div>
      <p>Não é possível visualizar este tipo de arquivo diretamente.</p>
      <a href="${caminho}" target="_blank" rel="noopener noreferrer" class="btn btn-primary">Baixar Documento</a>
    `;
    }

    $("#conteudoDocumento").html(conteudo);

    mediumZoom(".zoomable", {
      margin: 24,
      scrollOffset: 40,
    });

    const backdropFilho = $(
      '<div class="modal-backdrop fade modal-backdrop-child show"></div>'
    );
    $("body").append(backdropFilho);

    const modalElem = document.getElementById("modalDocumento");
    const modal = new bootstrap.Modal(modalElem, {
      backdrop: false,
    });
    modal.show();

    $(document).on("click", ".modal-backdrop-child", function () {
      const modalInstance = bootstrap.Modal.getInstance(modalElem);
      if (modalInstance) modalInstance.hide();
    });

    $(modalElem).on("hidden.bs.modal", function () {
      $(".modal-backdrop-child").remove();
      $("#conteudoDocumento").empty();
    });
  });
});
