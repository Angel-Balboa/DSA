var initd = function () {
  if (ssg("id_carrera", false) !== null) {
    llena_tarjetas();
    submit_agrega_plan();
    modal_solicitar_prestamo();
    submit_solicitar_prestamo();
    submit_actualizar_plan();
  }
};

var submit_actualizar_plan = function () {
  $("#frmEditarPlanEstudios").submit(function (e) {
    e.preventDefault();

    var result = post_data("../../api/requests/director/updates/plan_de_estudio/actualiza_datos.php", $(this).serialize(), true);

    if (result.done) {
      llena_tarjeta_planesEstudio();
      $("#mdlActualizarPlanEstudios").modal("hide");
    }
  });
};

var submit_solicitar_prestamo = function () {
  $("#frmSolicitarPrestamo").submit(function(e) {
    e.preventDefault();

    var result = post_data("../../api/requests/director/creates/solicitud_prestamo/solicita_profesor_a_prestamo.php", $(this).serialize(), true);

    if (result.done) {
      nfy(result.message, 's');
    }
  });
};

var modal_solicitar_prestamo = function() {
  $("#btnSolicitarPrestamo").click(function() {
    var $carreras = get_data("../../api/requests/common/getters/carrera/get_all.php", true);

    if ($carreras.done) {
      var $sctCarreraObjetivo = $("#sctCarreraObjetivo");
      var $my_id_carrera = ssg("id_carrera", false);

      $sctCarreraObjetivo.find('option').remove().end().append('<option value="">Selecciona una carrera</option>');
      $carreras.data.forEach(carrera => {
        if (parseInt($my_id_carrera) !== parseInt(carrera.id)) $sctCarreraObjetivo.append('<option value="'+ carrera.clave+'">'+ carrera.nombre +'</option>');
      });
      $sctCarreraObjetivo.selectpicker("refresh");
    }
    $("#mdlSolicitudPrestamo").modal("show");
  });

  $("#sctCarreraObjetivo").change(function() {
    var $sctProfesorObjetivo = $("#sctProfesorObjetivo");
    $sctProfesorObjetivo.find('option').remove().end().append('<option value="" selected="selected">Selecciona un Profesor</option>');
    var $todosProfes = get_data("../../api/requests/common/getters/profesor/get_all.php?clv_carrera=" + $(this).val(), true);
    if ($todosProfes.done) {
      $todosProfes.data.forEach(profesor => {
        if (!es_profesor_en_prestamo(profesor)) {
          $sctProfesorObjetivo.append('<option value="'+ profesor.id +'">'+ profesor.usuario.nombre + ' ' + profesor.usuario.apellidos +'</option>');
        }
      });
      $sctProfesorObjetivo.selectpicker("refresh");
    }
  });

  $("#sctProfesorObjetivo").change(function () {
    var $disponibilidad = get_data("../../api/requests/common/getters/disponibilidad/disponibilidad_profesor.php?id_profesor="+$(this).val(), true);
    if ($disponibilidad.done) {
      var disponibilidad = $disponibilidad.data;

      for(let $dia=0; $dia<5;$dia++) {
        for (let $hora=0; $hora<14; $hora++) {
          if (disponibilidad[$dia][$hora] === 1) {
            $('.qv-disp[data-day="' + $dia + '"][data-hour="' + $hora + '"]').css("background-color", "green");
          } else {
            $('.qv-disp[data-day="' + $dia + '"][data-hour="' + $hora + '"]').css("background-color", "white");
          }
        }
      }
    }
  });
};

var submit_agrega_plan = function () {
  $("#frmAgregaPlan").submit(function (e) {
    e.preventDefault();

    var result = post_data("../../api/requests/director/creates/plan_de_estudio/crea_plan_de_estudio.php", $(this).serialize(), true);
    if (result.done) {
      llena_tarjeta_planesEstudio();
      $("#AgregarNuevoPlan").modal("hide");
    }
  });
};

var llena_tarjetas = function () {
  llena_tarjeta_planesEstudio();
  llena_tarjetas_profesores();
  llena_tarjeta_profesores_prestamo();
};

var llena_tarjeta_profesores_prestamo = function () {
  var result = get_data("../../api/requests/common/getters/profesor/get_profesores_prestamo.php?id_carrera=" + ssg("id_carrera", false), false);
  if (result.done) {
    var profPrestamo = result.data;
    guarda_ids_profesores_prestamo(profPrestamo);
    var $tbPrestamo = $("#tbody_profesores_prestamo");
    $tbPrestamo.empty();
    var $template = '';
    profPrestamo.forEach(profesor => {
      $template = '<tr>' +
          '<td>'+ profesor.usuario.nombre + ' ' + profesor.usuario.apellidos +'</td>'+
          '<td>'+ profesor.usuario.email +'</td>'+
          '<td>'+ profesor.carrera.nombre+'</td>'+
          '<td><a href="detalle_profesor.php?id_profesor='+ profesor.id +'" class="text-primary"><i class="material-icons">visibility</i></a></td>'
      '</tr>';
      $tbPrestamo.append($template);
    });
  }
};

var guarda_ids_profesores_prestamo = function (listaProfesores) {
  var $ids_profs_prestamo = [];

  for(var i=0; i < listaProfesores.length; i++) {
    $ids_profs_prestamo.push(listaProfesores[i].id);
  }

  sss("profs_prestamo", $ids_profs_prestamo);
};

var es_profesor_en_prestamo = function (profesor) {
  var ban = false;
  $ids_profs_prestamo = ssg("profs_prestamo");

  for (i=0; i < $ids_profs_prestamo.length; i++) {
    if (profesor.id === $ids_profs_prestamo[i]) {
      ban = true;
      break;
    }
  }
  return ban;
};

var llena_tarjetas_profesores = function () {
  var result = get_data("../../api/requests/common/getters/profesor/get_all.php?groupby=tipo_contrato&id_carrera=" + ssg("id_carrera", false));

  if (result.done) {
    llena_tarjeta_profesor(result.data.PTC, "PTC");
    llena_tarjeta_profesor(result.data.PA, "PA");
    // llena_tarjeta_pa();
  }
};

var llena_tarjeta_profesor = function (listaProfesores, tipo="PTC") {
  var $ttype = (tipo === "PTC") ? "#tbody_profesores_ptc" : "#tbody_profesores_pa";
  var $tbody_profesores = $($ttype);

  $tbody_profesores.empty();
  var $template = "";
  listaProfesores.forEach(profesor => {
    $template = '<tr>' +
        '<td>' + profesor.usuario.nombre + ' ' + profesor.usuario.apellidos + '</td>' +
        '<td><a href="detalle_profesor.php?id_profesor='+ profesor.id +'" rel="tooltip" title="Detalle del Profesor" class="text-primary"><i class="material-icons">visibility</i> </a> </td>' +
        '</tr>';
    $tbody_profesores.append($template);
  });
};

var llena_tarjeta_planesEstudio = function() {
  var $tbListaPlanes = $("#tbody_lista_planes");
  $tbListaPlanes.empty();
  var result = get_data("../../api/requests/common/getters/plan_de_estudio/get_all.php?id_carrera=" + ssg("id_carrera", false), true);

  if (result.done) {
    var lista_planes = result.data;
    lista_planes.forEach(plan => $tbListaPlanes.append('<tr>\n' +
        '                          <td id="clave_plan_estudios">' + plan.clave + '</td>\n' +
        '                          <td id="nombre_plan_estudios">' + plan.nombre + '</td>\n' +
        '                          <td>\n' +
        '                            <button type="button" rel="tooltip" title="Editar" class="btn btn-primary btn-link btn_editar_plan" data-clave-plan="'+ plan.clave +'">\n' +
        '                              <i class="material-icons">edit</i>\n' +
        '                            </button>' +
        '                            <a href="detalle_plan_estudio.php?clv_plan=' + plan.clave + '" rel="tooltip" title="Detalle" class="btn btn-primary btn-link btn_detalle_plan">\n' +
        '                              <i class="material-icons">visibility</i>\n' +
        '                            </a>\n' +
        '                          </td>\n' +
        '                        </tr>'));

    $(".btn_editar_plan").click(function() {
      var clavePlan = $(this).data("clavePlan");
      var result = get_data("../../api/requests/common/getters/plan_de_estudio/get_one.php?clv_plan=" + clavePlan, true);

      if (result.done) {
        var $frmEditarPlanEstudios = $("#frmEditarPlanEstudios");
        $frmEditarPlanEstudios.find("#hdnClavePlanEstudios").val(clavePlan);
        $frmEditarPlanEstudios.find('#txtEditarNombrePlan').val(result.data.nombre);
        $frmEditarPlanEstudios.find('#sctEditAnioPlan option[value="'+ result.data.anio +'"]').attr("selected", true);
        $frmEditarPlanEstudios.find('#sctEditNivelPlan option:selected').attr('selected', false);
        $frmEditarPlanEstudios.find('#sctEditNivelPlan option[value="'+ result.data.nivel +'"]').attr("selected", true);
        $("#mdlActualizarPlanEstudios").modal("show");
      }
    });
  }

}