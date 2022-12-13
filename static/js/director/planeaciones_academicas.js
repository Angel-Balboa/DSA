var initd = function () {
    var $id_carrera = ssg("id_carrera", false);

    oculta_tarjeta();

    if ($id_carrera !== null) {
        $("#aniosPeriodos").load("../components/html/director/card_periodos_planeaciones.php", function () {
            $(".btn-get-planeaciones").click(function(){
                sss("tarjetas_are_shown", 1, true);

                var $periodo = $(this).data("periodo");
                var $anio = $(this).data("anio");

                llena_tarjeta_planeacionesPTCs($periodo, $anio);

                $("#crdPlaneacionesPTC").show();
            });
        });
        solicitar_planeaciones();

    } else {
        rep("Error", "No se ha podido obtener información sobre la carrera", 'e');
    }
};

var aceptar_planeacion = function () {
    $(".btn-aceptar-planeacion").click(function (){
        var dataSubmit = {"id_planeacion": $(this).data("idPlaneacion")};
        var result = post_data("../../api/requests/director/updates/planeacion_academica/aceptar_planeacion.php", dataSubmit, true);

        if (result.done) {
            nfy(result.message, 'i');
            $(this).hide();
        }
    });
};

var solicitar_planeaciones = function () {
  $("#btnSolicitarPlaneaciones").click(function () {
      var dataSubmit = {"periodo": $(this).data("periodo"), "anio": $(this).data("anio")};
      var result = post_data("../../api/requests/director/creates/planeacion_academica/solicita_planeaciones_PTCs.php", dataSubmit, true);

      if (result.done) {
          nfy(result.message, 'i');
          llena_tarjeta_planeacionesPTCs(dataSubmit.periodo, dataSubmit.anio);
      }
  });
};

var llena_tarjeta_planeacionesPTCs = function (periodo, anio) {

    $("#chPeriodo").text(get_txt_periodo(periodo));
    $("#chAnio").text(anio);

    var result = get_data("../../api/requests/director/getters/planeacion_academica/get_all.php?id_carrera=" + ssg("id_carrera", false) + "&periodo=" + periodo + "&anio=" + anio, true);

    if (result.done) {
        if (result.data.length < 1) {
            var $btnSolicitarPlaneaciones = $("#btnSolicitarPlaneaciones");
            $btnSolicitarPlaneaciones.data("periodo", periodo);
            $btnSolicitarPlaneaciones.data("anio", anio);
            $("#cbPlaneacionesPTCs").hide();
            $("#cfSolicitarPlaneaciones").show();
        } else {
            var $tbPlaneacionesPTCs = $("#tbPlaneacionesPTCs");
            var template = '';
            $tbPlaneacionesPTCs.empty();

            result.data.forEach(planeacionPTC => {
                template = '<tr>\n' +
                    '         <td>'+ planeacionPTC.profesor.nivel_adscripcion + ' ' + planeacionPTC.usuario.nombre + ' ' + planeacionPTC.usuario.apellidos +'</td>\n';

                if (planeacionPTC.estado === "finalizada" || planeacionPTC.estado === "aceptada") {
                    template += '<td><span class="material-icons text-success">thumb_up</span></td>';
                } else {
                    template += '<td><span class="material-icons text-danger">thumb_down</span></td>';
                }

                template += '<td><a href="detalle_planeacion_academica.php?id_planeacion='+ planeacionPTC.id +'"><i class="material-icons">visibility</i></a>';

                if (planeacionPTC.estado === "aceptada") {
                    template += '<a href=""><i class="material-icons">print</i></a>';
                }
                template += '</td>';

                if (planeacionPTC.estado === 'finalizada') {
                    template += '<td><button type="button" class="btn btn-link text-success btn-aceptar-planeacion" title="Aceptar Planeacion" data-id-planeacion="'+ planeacionPTC.id +'"><i class="material-icons">done_all</i></button></td>'
                } else {
                    template += '<td></td>';
                }
                template += '</tr>';

                $tbPlaneacionesPTCs.append(template);
            });

            $("#cbPlaneacionesPTCs").show();
            $("#cfSolicitarPlaneaciones").hide();

            aceptar_planeacion();
        }
    }
};

var oculta_tarjeta = function () {
    $("#crdPlaneacionesPTC").hide();
};