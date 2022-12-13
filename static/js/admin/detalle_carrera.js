var initd = function () {
    var clave = obten_clave_carrera();
    carga_detalles_carrera(clave);
    carga_planes_carrera(clave);
    carga_profesores_adscritos(clave);
    openModalActualizarCarrera();
    actualiza_carrera();
    actualiza_plan_estudios();
    agrega_nuevo_plan();
};

var obten_clave_carrera = function obten_clave_carrera() {
    var $clv_carrera = getUrlParameter("clv_carrera");

    if (!$clv_carrera) {
        Notiflix.Report.failure('Error', 'Se debe proporcionar la clave de la carrera', 'Aceptar');
    }

    return $clv_carrera;
};

var carga_detalles_carrera = function obten_detalles_carrera(clv_carrera) {
    var datos = get_data("../../api/requests/admin/getters/carreras/get_one.php?clv_carrera=" + clv_carrera, true);
    var tblDetallesCarrera = $('#tblDetallesCarrera');

    tblDetallesCarrera.find('#clave_carrera').text(datos.data.clave);
    tblDetallesCarrera.find('#nombre_carrera').text(datos.data.nombre);
    var txtNivelCarrera;
    switch (datos.data.nivel) {
        case 'Lic':
            txtNivelCarrera = 'Licenciatura';
            break;
        case 'M.I.':
            txtNivelCarrera = 'Maestría en Ingeniería';
            break;
        default:
            txtNivelCarrera = "Ingeniería";
            break;

    }
    tblDetallesCarrera.find('#nivel_carrera').text(txtNivelCarrera);
    tblDetallesCarrera.find('#director_carrera').text(datos.data.director.nombre + ' ' + datos.data.director.apellidos);
    tblDetallesCarrera.find('#id_director').text(datos.data.id_director);
};

var carga_planes_carrera = function carga_planes_carrera(clv_carrera) {
  var datos = get_data("../../api/requests/common/getters/plan_de_estudio/get_all.php?clv_carrera=" + clv_carrera, true);
  var tbody_lista_planes = $('#tbody_lista_planes');
  var lista_planes = datos.data;

  lista_planes.forEach(plan => tbody_lista_planes.append('<tr>\n' +
      '                          <td id="clave_plan_estudios">' + plan.clave + '</td>\n' +
      '                          <td id="nivel_plan_estudios">' + plan.nivel + '</td>\n' +
      '                          <td id="nombre_plan_estudios">' + plan.nombre + '</td>\n' +
      '                          <td id="anio_plan_estudios">' + plan.anio + '</td>\n' +
      '                          <td>\n' +
      '                            <button type="button" rel="tooltip" title="Editar" class="btn btn-primary btn-link btn_editar_plan">\n' +
      '                              <i class="material-icons">edit</i>\n' +
      '                            </button>' +
      '                            <a href="detalle_plan_estudio.php?clv_plan=' + plan.clave + '" rel="tooltip" title="Detalle" class="btn btn-primary btn-link btn_detalle_plan">\n' +
      '                              <i class="material-icons">visibility</i>\n' +
      '                            </a>\n' +
      '                          </td>\n' +
      '                        </tr>'));

  $('button.btn_editar_plan').click(function () {
      var item = $(this).closest('tr');
      var frmEditarPlanEstudios = $('#frmEditarPlanEstudios');
      var clavePlanEstudios = item.find('#clave_plan_estudios').text();

      $('#clvPlanInModalTitle').text(clavePlanEstudios);

      frmEditarPlanEstudios.find("#hdnClavePlanEstudios").val(clavePlanEstudios);
      frmEditarPlanEstudios.find('#txtEditarNombrePlan').val(item.find('#nombre_plan_estudios').text());

      $('#sctEditAnioPlan option').each(function() {
          $(this).attr('selected', false);
      });
      frmEditarPlanEstudios.find("#sctEditAnioPlan option[value=" + item.find('#anio_plan_estudios').text() + "]").attr("selected", true);

      $('#sctEditNivelPlan option').each(function() {
          $(this).attr('selected', false);
      });
      frmEditarPlanEstudios.find('#sctEditNivelPlan option[value="' + item.find('#nivel_plan_estudios').text() + '"]').attr("selected", true);

      $('#ActualizarPlanEstudios').modal('show');
  });

};

var carga_profesores_adscritos = function carga_profesores_adscritos(clv_carrera) {
  var datos = get_data("../../api/requests/admin/getters/carreras/obten_profesores_adscritos.php?clv_carrera=" + clv_carrera, true);

  if (datos.data.length < 1) {
      Notiflix.Notify.warning('La carrera actualmente no tiene profesores adscritos');
  } else {
      var tbody_profesores_adscritos = $("#tbody_profesores_adscritos");

      datos.data.forEach(profesor => tbody_profesores_adscritos.append('<tr><td>' + profesor.nombre + ' ' + profesor.apellidos + '</td><td>' + profesor.tipo_contrato + '</td><td><a href="detalle_usuario.php?id_usuario=' + profesor.id_usuario + '" class="btn btn-primary btn-link"><i class="material-icons">edit</i> </a> </td></tr>'));
  }
};

function obten_directores_no_asignados(target_select) {

    var result = get_data("../../api/requests/admin/getters/usuario/get_all_directores_no_asignados.php");

    if (result.done) {
        var target = $(target_select);
        if (result.data.length < 1) {
            Notiflix.Notify.failure('No hay directores sin asignar');
        } else {
            for (var i=0; i < result.data.length; i++) {
                target.append($('<option>', {
                    value: result.data[i].id,
                    text: result.data[i].nombre + ' ' + result.data[i].apellidos
                }));
            }
        }
    }
}

function openModalActualizarCarrera() {
    $('#btnOpenUpdCarrera').click(function() {

        var frmEditarCarrera = $('#frmEditarCarrera');

        frmEditarCarrera.find('input#hdnEditClaveCarrera').val($('#clave_carrera').text());
        frmEditarCarrera.find('input#txtEditNombreCarrera').val($('#nombre_carrera').text());
        // limpiamos el select del nivel de la carrera
        $('#sctEditNivelCarrera option').each(function() {
            $(this).attr('selected', false);
        });
        // seleccionamos el correpondiente al nivel de la carrera
        $('#sctEditNivelCarrera option').filter(function() {return $(this).html() == $('#nivel_carrera').text()}).attr('selected', true);

        // agregamos al director actual como primer elemento.
        $('#sctEditDirectorCarrera').find('option').remove().end().append($('<option>', {
            value: $('#id_director').text(),
            text: $('#director_carrera').text(),
            selected: true
        }));

        // agregamos a los directores no asignados a carreras
        obten_directores_no_asignados('#sctEditDirectorCarrera');

        $('#EditarCarrera').modal('show');
    });
}

function actualiza_carrera() {
    $('#frmEditarCarrera').submit(function(event) {
        event.preventDefault();

        var result = post_data("../../api/requests/admin/updates/carrera/actualiza_datos.php", $(this).serialize(), true);
        if (result.done) {
            Notiflix.Report.success('Éxito', result.message, 'Aceptar');
            $('#EditarCarrera').modal('hide');
        }
    });
}

function actualiza_plan_estudios() {
    $('#frmEditarPlanEstudios').submit(function (event) {
        event.preventDefault();

        var result = post_data("../../api/requests/admin/updates/plan_de_estudio/actualiza_datos.php", $(this).serialize(), true);

        if (result.done) {
            Notiflix.Report.success("Éxito", result.message, "Aceptar");
            $('#ActualizarPlanEstudios').modal('hide');
        }
    });
}

function agrega_nuevo_plan() {
    $('#frmAgregaPlan').submit(function(event) {
        event.preventDefault();

        $("#hdnClaveCarrera").val($("#clave_carrera").text());
        var clvNuevoPlan = $.trim($("#txtClaveNuevoPlan").val());
        var result = get_data_raw("../../api/requests/common/getters/plan_de_estudio/get_one.php?clv_plan=" + clvNuevoPlan);

        if (result.exito) {
            Notiflix.Notify.failure('Ya existe un plan de estudios con la clave: ' + clvNuevoPlan);
        } else if (parseInt(result.respuesta_error.codigo_error) != 4003) {
            Notiflix.Report.failure('Error', result.respuesta_error.mensaje_error, 'Aceptar');
        } else {
            result = post_data("../../api/requests/admin/creates/plan_de_estudio/crea_plan_de_estudio.php", $(this).serialize(), true);

            if (result.done) {
                Notiflix.Report.success('Éxito', result.message, 'Aceptar');
                $('#AgregarNuevoPlan').modal('hide');
            }
        }
    });
}