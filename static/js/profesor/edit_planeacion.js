var initd = function () {

    var $id_planeacion = getUrlParameter("id_planeacion");
    // var $estado = getUrlParameter("estado");

    if ($id_planeacion) {
        sss("id_planeacion", $id_planeacion, false);
        $(".id-planeacion").val($id_planeacion);
        llena_tarjetas();
        submit_frm_gestion_academica();
        submit_frm_capacitacion();
        submit_frm_vinculacion();
        submit_frm_asesorias();
        submit_frm_promocion();
        submit_frm_investigacion();
        finalizar_planeacion();
    } else {
        rep("Error", "Se esperaba el Id de la Planeación Académica", 'e');
    }

};

var finalizar_planeacion = function () {
  $("#btnFinalizarPlaneacion").click(function() {
      Notiflix.Confirm.show('Finalizar Planeación',
          'Realmente desea finalizar la planeación',
          'Si',
          'No', () => {
          var dataSubmit = {'id_planeacion': ssg("id_planeacion", false)};

          var result = post_data("../../api/requests/profesor/updates/planeacion_academica/finaliza_planeacion_academica.php", dataSubmit, true);

          if (result.done) {
              location = "planeaciones_academicas.php";
          }
          }, );
  });
};

var llena_tarjetas = function () {
    var result = get_data("../../api/requests/common/getters/planeacion_academica/obten_datos_completos_planeacion.php?id_planeacion=" + ssg("id_planeacion", false), true);

    if (result.done) {
        llena_tarjeta_gestion(result.data.gestion);
        llena_tarjeta_capacitacion(result.data.capacitacion);
        llena_tarjeta_vinculacion(result.data.vinculacion);
        llena_tarjeta_promocion(result.data.promocion);
        llena_tarjeta_asesorias(result.data.asesorias);
        llena_tarjeta_investigacion(result.data.investigacion);
    }
}

var llena_tarjeta_investigacion = function (dataInvestigacion) {
    $("#totHorasInvestigacion").text(dataInvestigacion.horas_totales);
    var contentInvestigacion = $("#content-investigacion");
    var numActividades = dataInvestigacion.actividades.length;

    if (numActividades < 1) {
        contentInvestigacion.append(make_template_investigacion(1, false));
    } else {
        for(i=1; i <= numActividades; i++) {
            if (i === 1) {
                contentInvestigacion.append(make_template_investigacion(i, false, false, dataInvestigacion.actividades[i-1]));
            } else {
                contentInvestigacion.append(make_template_investigacion(i, true, false, dataInvestigacion.actividades[i-1]));
            }
        }
    }
    add_field_investigacion();

};

var llena_tarjeta_asesorias = function (dataAsesorias) {
    var $frmPlaneacionAsesorias = $("#frmPlaneacionAsesorias");

    $frmPlaneacionAsesorias.find("#hdnIdPlenacionAsesoria").val(dataAsesorias.id);
    $frmPlaneacionAsesorias.find('#sctInstitucionalEstancia option[value="' + dataAsesorias.institucional_estancia + '"]').attr("selected", true);
    $frmPlaneacionAsesorias.find('#sctInstitucionalEstadia option[value="' + dataAsesorias.institucional_estadia + '"]').attr("selected", true);
    $frmPlaneacionAsesorias.find('#sctEmpresarialEstancia option[value="' + dataAsesorias.empresarial_estancia + '"]').attr("selected", true);
    $frmPlaneacionAsesorias.find('#sctEmpresarialEstadia option[value="' + dataAsesorias.empresarial_estadia + '"]').attr("selected", true);

    $(".select-horas-asesorias").change(function (){
       llena_txtHoras_asesorias();
    });

    llena_txtHoras_asesorias();
};

var llena_txtHoras_asesorias = function () {
    var instEstancia = parseInt($("#sctInstitucionalEstancia option:selected").val());
    var instEstadia = parseInt($("#sctInstitucionalEstadia option:selected").val());
    var empreEstancia = parseInt($("#sctEmpresarialEstancia option:selected").val());
    var empreEstadia = parseInt($("#sctEmpresarialEstadia option:selected").val());

    var horas_institucional_estancia = (3 * instEstancia);
    $("#horas_institucional_estancia").text(horas_institucional_estancia);

    var horas_institucional_estadia = (5 * instEstadia);
    $("#horas_institucional_estadia").text(horas_institucional_estadia);

    var horas_empresarial_estancia = (5 * empreEstancia);
    $("#horas_empresarial_estancia").text(horas_empresarial_estancia);

    var horas_empresarial_estadia = (20 * empreEstadia);
    $("#horas_empresarial_estadia").text(horas_empresarial_estadia);

    var horas_totales = horas_institucional_estancia + horas_institucional_estadia + horas_empresarial_estancia + horas_empresarial_estadia;

    $("#totHorasAsesoria").text(horas_totales);
};

var llena_tarjeta_promocion = function(dataPromocion) {
    $("#totHorasPromocion").text(dataPromocion.horas_totales);
    var numActividades = dataPromocion.actividades.length;
    var horas = [0, 5, 10, 15, 20];

    if (numActividades !== 0) {
        $('#sctHorasPromocion option[value="' + dataPromocion.actividades[0].horas + '"]').attr("selected", true);
        $('#hdnIdActividadPromocion').val(dataPromocion.actividades[0].id);
    }
};

var llena_tarjeta_vinculacion = function(dataVinculacion) {
    $("#totHorasVinculacion").text(dataVinculacion.horas_totales);
    var contentVinculacion = $("#content-vinculacion");
    var numActividades = dataVinculacion.actividades.length;

    if (numActividades < 1) {
        contentVinculacion.append(make_template_vinculacion(1, false));
    } else {
        for(i=1; i <= numActividades; i++) {
            if (i === 1) {
                contentVinculacion.append(make_template_vinculacion(i, false, false, dataVinculacion.actividades[i-1]));
            } else {
                contentVinculacion.append(make_template_vinculacion(i, true, false, dataVinculacion.actividades[i-1]));
            }
        }
    }

    add_field_vinculacion();
};

var llena_tarjeta_gestion = function (dataGestion) {

    $("#totHorasGestion").text(dataGestion.horas_totales);
    var contentGestionAcademica = $("#content-gestion-academica");
    var numActividades = dataGestion.actividades.length;

    if (numActividades < 1) {
        contentGestionAcademica.append(make_template_gestion(1, false));
    } else {
        for(i=1; i<=numActividades; i++) {
            if (i === 1) {
                contentGestionAcademica.append(make_template_gestion(i, false, false, dataGestion.actividades[i-1]));
            } else {
                contentGestionAcademica.append(make_template_gestion(i, true, false, dataGestion.actividades[i-1]));
            }
        }
    }

    add_field_gestion();
};

var llena_tarjeta_capacitacion = function (dataCapacitacion) {
    $("#totHorasCapacitacion").text(dataCapacitacion.horas_totales);
    var contentCapacitacion = $("#content-capacitacion");
    var numActividades = dataCapacitacion.actividades.length;

    if (numActividades < 1) {
        contentCapacitacion.append(make_template_capacitacion(1, false));
    } else {
        for (i=1; i <= numActividades; i++) {
            if (i === 1) {
                contentCapacitacion.append(make_template_capacitacion(i, false, false, dataCapacitacion.actividades[i-1]));
            } else {
                contentCapacitacion.append(make_template_capacitacion(i, true, false, dataCapacitacion.actividades[i-1]));
            }
        }
    }
    add_field_capacitacion();
}

var submit_frm_gestion_academica = function () {
    $("#frmGestionAcademica").submit(function (e) {
        e.preventDefault();
        var result = post_data("../../api/requests/profesor/updates/planeacion_academica/actualiza_actividades.php", $(this).serialize(), true);

        if (result.done) {
            location.reload();
        }
    });
};

var submit_frm_capacitacion = function() {
    $("#frmCapacitacionAcademica").submit(function (e) {
        e.preventDefault();
        var result = post_data("../../api/requests/profesor/updates/planeacion_academica/actualiza_actividades.php", $(this).serialize(), true);

        if (result.done) {
            location.reload();
        }
    });
}

var submit_frm_vinculacion = function () {
  $("#frmVinculacionAcademica").submit(function (e) {
      e.preventDefault();
      var result = post_data("../../api/requests/profesor/updates/planeacion_academica/actualiza_actividades.php", $(this).serialize(), true);

      if (result.done) {
          location.reload();
      }
  })
};

var submit_frm_asesorias = function () {
    $("#frmPlaneacionAsesorias").submit(function (e) {
        e.preventDefault();

        var result = post_data("../../api/requests/profesor/updates/planeacion_academica/actualiza_planeacion_asesoria.php", $(this).serialize(), true);

        if (result.done) {
            location.reload();
        }
    });
};

var submit_frm_promocion = function() {
    $("#frmPromocionAcademica").submit(function (e) {
        e.preventDefault();

        var result = post_data("../../api/requests/profesor/updates/planeacion_academica/actualiza_actividad_promocion.php", $(this).serialize(), true);

        if (result.done) {
            location.reload();
        }
    });
};

var submit_frm_investigacion = function() {
    $("#frmPlaneacionInvestigacion").submit(function (e) {
        e.preventDefault();

        var result = post_data("../../api/requests/profesor/updates/planeacion_academica/actualiza_actividades_investigacion.php", $(this).serialize(), true);

        if (result.done) {
            location.reload();
        }
    });
};

var add_field_investigacion = function () {
    $("#btnAddRowInvestigacion").click(function () {
        var new_val = parseInt($("#content-investigacion .row:last-child").data("lastValue")) + 1;
        var template = make_template_investigacion(new_val, true, true);
        $("#content-investigacion").append(template);

        $(".remove-field-investigacion").click(function () {
            var id_count = $(this).data("countBtn");
            var divToRemove = $("#content-investigacion").find("[data-last-value=" + id_count + "]");
            divToRemove.remove();
        });
    });
};

var add_field_vinculacion = function () {
    $("#btnAddRowVinculacion").click(function () {
        var new_val = parseInt($("#content-vinculacion .row:last-child").data("lastValue")) + 1;
        var template = make_template_vinculacion(new_val);
        $("#content-vinculacion").append(template);

        $(".remove-field-vinculacion").click(function () {
            var id_count = $(this).data("countBtn");
            var divToRemove = $("#content-vinculacion").find("[data-last-value=" + id_count + "]");
            divToRemove.remove();
        });
    });
};

var add_field_capacitacion = function () {
    $("#btnAddRowCapacitacion").click(function () {
        var new_val = parseInt($("#content-capacitacion .row:last-child").data("lastValue")) + 1;
        var template = make_template_capacitacion(new_val);
        $("#content-capacitacion").append(template);

        $(".remove-field-capacitacion").click(function () {
            var id_count = $(this).data("countBtn");
            var divToRemove = $("#content-capacitacion").find("[data-last-value=" + id_count + "]");
            divToRemove.remove();
        });
    });
};

var add_field_gestion = function () {
    $("#btnAddRowGestion").click(function () {
        var new_val = parseInt($("#content-gestion-academica .row:last-child").data("lastValue")) + 1;
        var template = make_template_gestion(new_val);
        $("#content-gestion-academica").append(template);

        $(".remove-field-gestion").click(function () {
            var id_count = $(this).data("countBtn");
            var divToRemove = $("#content-gestion-academica").find("[data-last-value=" + id_count + "]");
            divToRemove.remove();
        });
    });
};

var select_option = function (value, text, is_selected=false) {
    var txtIsSelected = is_selected ? 'selected="selected"' : "";
    return '<option value="' + value + '" ' + txtIsSelected + '>' + text + '</option>';
}

var make_template_vinculacion = function (new_val, is_del=true, is_new=true, dataNotNew=null) {
    var template = null;
    var horas = [0, 1, 5, 10, 15, 20, 25, 30, 40, 50];
    if (is_new) {
        template = '<div class="row" data-last-value="' + new_val + '">\n' +
            '         <input type="hidden" name="hdnIdActividad[]" value="-1">\n' +
            '         <div class="col-lg-3">\n' +
            '           <input type="text" class="form-control" name="descripcionActividad[]">\n' +
            '         </div>\n' +
            '         <div class="col-lg-3">\n' +
            '           <input type="text" class="form-control" name="empresaReceptoraActividad[]">\n' +
            '         </div>\n' +
            '         <div class="col-lg-2">\n' +
            '           <select class="custom-select" name="horasActividad[]">\n';

        horas.forEach(function (hora) {
            template += select_option(hora, hora);
        });

        template += '   </select>\n' +
            '         </div>\n' +
            '         <div class="col-lg-3">\n' +
            '           <input type="text" class="form-control" name="evidenciaActividad[]">\n' +
            '         </div>\n';

        if (is_del) {
            template += '<button type="button" class="btn btn-danger glyphicon glyphicon-remove remove-field-vinculacion" data-count-btn="' + new_val + '">-</button>\n';
        } else {
            template += '<button type="button" class="btn btn-primary glyphicon glyphicon-plus" id="btnAddRowVinculacion">+</button>';
        }
        template += '</div>';
    } else {
        if (dataNotNew !== null) {
            template = '<div class="row" data-last-value="' + new_val + '">\n' +
                '         <input type="hidden" name="hdnIdActividad[]" value="' + dataNotNew.id + '">\n' +
                '         <div class="col-lg-3">\n' +
                '           <input type="text" class="form-control" name="descripcionActividad[]" value="' + dataNotNew.descripcion + '">\n' +
                '         </div>\n' +
                '         <div class="col-lg-3">\n' +
                '           <input type="text" class="form-control" name="empresaReceptoraActividad[]" value="' + dataNotNew.empresa_receptora + '">\n' +
                '         </div>\n' +
                '         <div class="col-lg-2">\n' +
                '           <select class="custom-select" name="horasActividad[]">\n';

            horas.forEach(function (hora) {
                if (hora === dataNotNew.horas) template += select_option(hora, hora, true);
                else template += select_option(hora, hora);
            });

            template += '   </select>\n' +
                '         </div>\n' +
                '         <div class="col-lg-3">\n' +
                '           <input type="text" class="form-control" name="evidenciaActividad[]" value="' + dataNotNew.evidencia + '">\n' +
                '         </div>\n';
            if (is_del) {
                template += '<button type="button" class="btn btn-danger glyphicon glyphicon-remove remove-field-vinculacion" data-count-btn="' + new_val + '">-</button>\n';
            } else {
                template += '<button type="button" class="btn btn-primary glyphicon glyphicon-plus" id="btnAddRowVinculacion">+</button>';
            }
            template += '</div>';
        }
    }
    return template;
};

var make_template_gestion = function (new_val, is_del=true, is_new=true, dataNotNew=null) {
    var template = null;
    var horas = [0, 1, 5, 10, 15, 20, 25];
    if (is_new) {
        template = '<div class="row" data-last-value="' + new_val + '">\n' +
            '         <input type="hidden" name="hdnIdActividad[]" value="-1">\n' +
            '         <div class="col-lg-4">\n' +
            '           <input type="text" class="form-control" name="descripcionActividad[]" required/>\n' +
            '         </div>\n' +
            '         <div class="col-lg-3">\n' +
            '           <select class="custom-select"  name="horasActividad[]" required>\n';

        horas.forEach(function (hora) {
            template += select_option(hora, hora);
        });

        template += '   </select>\n' +
            '         </div>\n' +
            '         <div class="col-lg-4">\n' +
            '           <input type="text" class="form-control" name="evidenciaActividad[]" required />\n' +
            '         </div>\n';

        if (is_del) {
            template += '<button type="button" class="btn btn-danger glyphicon glyphicon-remove remove-field-gestion" data-count-btn="' + new_val + '">-</button>\n';
        } else {
            template += '<button type="button" class="btn btn-primary glyphicon glyphicon-plus add-field-gestion-academica" id="btnAddRowGestion">+</button>\n';
        }
        template += '</div>';
    } else {
        if (dataNotNew !== null) {
            template = '<div class="row" data-last-value="' + new_val + '">\n' +
                '         <input type="hidden" name="hdnIdActividad[]" value="' + dataNotNew.id + '">\n' +
                '         <div class="col-lg-4">\n' +
                '           <input type="text" class="form-control" name="descripcionActividad[]" value="' + dataNotNew.descripcion + '" required>\n' +
                '         </div>\n' +
                '         <div class="col-lg-3">\n' +
                '           <select class="custom-select"  name="horasActividad[]" required>\n';

            horas.forEach(function (hora) {
                if (hora === dataNotNew.horas) template += select_option(hora, hora, true);
                else template += select_option(hora, hora);
            });
            template += '   </select>\n' +
                '         </div>\n' +
                '         <div class="col-lg-4">\n' +
                '           <input type="text" class="form-control" name="evidenciaActividad[]" value="' + dataNotNew.evidencia + '" required />\n' +
                '         </div>\n';
            if (is_del) {
                template += '<button type="button" class="btn btn-danger glyphicon glyphicon-remove remove-field-gestion" data-count-btn="' + new_val + '">-</button>\n';
            } else {
                template += '<button type="button" class="btn btn-primary glyphicon glyphicon-plus add-field-gestion-academica" id="btnAddRowGestion">+</button>\n';
            }

            template += '</div>';
        }
    }

    return template;
};

var make_template_investigacion = function (new_val, is_del=true, is_new=true, dataNotNew=null) {
    var template = null;
    var tiposActividad = [{"value": "articulo", "text": "Artículo"}, {"value": "tesis_maestria", "text": "Tesis Maestría"}, {"value": "patente", "text": "Patente"}, {"value": "prototipo", "text": "Prototipo"}];
    if (is_new) {
        template = '<div class="row" data-last-value="' + new_val + '">\n' +
            '         <input type="hidden" id="hdnIdActividadInvestigacion" name="id_actividad_investigacion[]" value="-1" />' +
            '         <div class="col-lg-3">\n' +
            '           <input type="text" class="form-control" name="descripcionActividadInvestigacion[]" required>\n' +
            '         </div>\n' +
            '         <div class="col-lg-2">\n' +
            '           <select class="custom-select" name="tipoActividadInvestigacion[]" required>\n';
        tiposActividad.forEach(function (tipo) {
           template += select_option(tipo.value, tipo.text);
        });
            template += '</select>\n' +
            '         </div>\n' +
            '         <div class="col-lg-2">\n' +
            '           <select class="custom-select" name="avanceActualActividadInvestigacion[]" required>\n';
            for (var i=0; i<=100; i=i+10) template += select_option(i, i);

            template += '</select>\n' +
            '         </div>\n' +
            '         <div class="col-lg-2">\n' +
            '           <select class="custom-select" name="avanceEsperadoActividadInvestigacion[]" required>\n';
        for (var i=0; i<=100; i=i+10) template += select_option(i, i);
            template += '</select>\n' +
            '         </div>\n' +
            '         <div class="col-lg-2">\n' +
            '           <input type="date" class="form-control" name="fechaTentativaActividadInvestigacion[]" required>\n' +
            '         </div>\n';
        if (is_del) {
            template += '<button type="button" class="btn btn-danger glyphicon glyphicon-remove remove-field-investigacion" data-count-btn="' + new_val + '">-</button>';
        } else {
            template +='<button type="button" class="btn btn-primary glyphicon glyphicon-plus" id="btnAddRowInvestigacion" data-count-btn="' + new_val + '">+</button>';
        }
        template += '</div>';
    } else {
        if (dataNotNew !== null) {
            template = '<div class="row" data-last-value="' + new_val + '">\n' +
                '         <input type="hidden" id="hdnIdActividadInvestigacion" name="id_actividad_investigacion[]" value="' + dataNotNew.id + '" />' +
                '         <div class="col-lg-3">\n' +
                '           <input type="text" class="form-control" name="descripcionActividadInvestigacion[]" value="' + dataNotNew.actividad +'" required>\n' +
                '         </div>\n' +
                '         <div class="col-lg-2">\n' +
                '           <select class="custom-select" name="tipoActividadInvestigacion[]" required>\n';
            tiposActividad.forEach(function (tipo) {
                if (tipo.value === dataNotNew.tipo) {
                    template += select_option(tipo.value, tipo.text, true);
                } else {
                    template += select_option(tipo.value, tipo.text);
                }

            });

            template += '</select>\n' +
                '         </div>\n' +
                '         <div class="col-lg-2">\n' +
                '           <select class="custom-select" name="avanceActualActividadInvestigacion[]" required>\n';
            for (var i=0; i<=100; i=i+10) {
                if (i === dataNotNew.avance_actual) {
                    template += select_option(i, i, true);
                } else {
                    template += select_option(i, i);
                }
            }
            template += '   </select>\n' +
                '         </div>\n' +
                '         <div class="col-lg-2">\n' +
                '           <select class="custom-select" name="avanceEsperadoActividadInvestigacion[]" required>\n';
            for (var i=0; i<=100; i=i+10) {
                if (i === dataNotNew.avance_esperado) {
                    template += select_option(i, i, true);
                } else {
                    template += select_option(i, i);
                }
            }
            template += '   </select>\n' +
                '         </div>\n' +
                '         <div class="col-lg-2">\n' +
                '           <input type="date" class="form-control" name="fechaTentativaActividadInvestigacion[]" value="' + moment(dataNotNew.fecha_termino).format("YYYY-MM-DD") + '" required>\n' +
                '         </div>\n';
            if (is_del) {
                template += '<button type="button" class="btn btn-danger glyphicon glyphicon-remove remove-field-investigacion" data-count-btn="' + new_val + '">-</button>';
            } else {
                template +='<button type="button" class="btn btn-primary glyphicon glyphicon-plus" id="btnAddRowInvestigacion" data-count-btn="' + new_val + '">+</button>';
            }
            template += '</div>';
        }
    }
    return template;
};

var make_template_capacitacion = function (new_val, is_del=true, is_new=true, dataNotNew=null) {
    var template = null;
    var horas = [0, 1, 5, 10, 15, 20, 25];
    if (is_new) {
        template = '<div class="row" data-last-value="' + new_val + '">\n' +
            '         <input type="hidden" name="hdnIdActividad[]" value="-1">\n' +
            '         <div class="col-lg-4">\n' +
            '           <input type="text" class="form-control" name="descripcionActividad[]">\n' +
            '         </div>\n' +
            '         <div class="col-lg-3">\n' +
            '           <select class="custom-select" name="horasActividad[]">\n';

        horas.forEach(function (hora) {
            template += select_option(hora, hora);
        });

        template += '   </select>\n' +
            '         </div>\n' +
            '         <div class="col-lg-4">\n' +
            '           <input type="text" class="form-control" name="evidenciaActividad[]">\n' +
            '         </div>\n';

        if (is_del) {
            template += '<button type="button" class="btn btn-danger glyphicon glyphicon-remove remove-field-capacitacion" data-count-btn="' + new_val + '">-</button>\n';
        } else {
            template += '<button type="button" class="btn btn-primary glyphicon glyphicon-plus" id="btnAddRowCapacitacion" data-count-btn="' + new_val + '">+</button>';
        }
        template += '</div>';
    } else {
        if (dataNotNew !== null) {
            template = '<div class="row" data-last-value="' + new_val + '">\n' +
                '         <input type="hidden" name="hdnIdActividad[]" value="' + dataNotNew.id + '">\n' +
                '         <div class="col-lg-4">\n' +
                '           <input type="text" class="form-control" name="descripcionActividad[]" value="' + dataNotNew.descripcion + '">\n' +
                '         </div>\n' +
                '         <div class="col-lg-3">\n' +
                '           <select class="custom-select" name="horasActividad[]">\n';

            horas.forEach(function (hora) {
                if (hora === dataNotNew.horas) template += select_option(hora, hora, true);
                else template += select_option(hora, hora);
            });

            template += '   </select>\n' +
                '         </div>\n' +
                '         <div class="col-lg-4">\n' +
                '           <input type="text" class="form-control" name="evidenciaActividad[]" value="' + dataNotNew.evidencia + '">\n' +
                '         </div>\n';

            if (is_del) {
                template += '<button type="button" class="btn btn-danger glyphicon glyphicon-remove remove-field-capacitacion" data-count-btn="' + new_val + '">-</button>\n';
            } else {
                template += '<button type="button" class="btn btn-primary glyphicon glyphicon-plus" id="btnAddRowCapacitacion" data-count-btn="' + new_val + '">+</button>';
            }
            template += '</div>';
        }
    }

    return template;
};