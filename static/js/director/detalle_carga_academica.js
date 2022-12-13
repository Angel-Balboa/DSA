var initd = function () {
    var $id_carga = getUrlParameter("id_carga");

    if ($id_carga) {
        sss("id_carga", $id_carga, false);

        $("#hdnClaveCargaAcademica").val($id_carga);
        limpia_campos_tarjeta_meg();

        llena_tarjeta_datos_carga();
        obten_grupos_carga();

        muestra_fechas_modalAddGrupo();
        submit_agrega_grupo_a_carga();

        modal_agregar_materia_a_grupo();
        submit_agregar_materia_a_grupo();

        modal_cambia_materiaEnMeg();
        submit_cambia_materiaEnMeg();

        modal_cambia_profesorEnMeg();
        submit_cambia_profesorEnMeg();

        finalizar_grupo();
    } else {
        rep("Error", "Se esperaba el Id de la Carga Académica a Editar", 'e');
    }
};

var finalizar_grupo = function () {
  $("#btnFinalizarGrupo").click(function () {
      Notiflix.Confirm.show(
          'Finalizar grupo',
          '¿Realmente deseas finalizar el grupo?',
          'Yes',
          'No',
          () => {
              var dataSubmit = {"id_grupo": $(this).data("idGrupo")};
              var result = post_data("../../api/requests/director/updates/grupo/finalizar_grupo.php", dataSubmit, true);

              if (result.done) {
                  nfy(result.message, 'i');
                  $(this).hide();
                  $("#btnCambiarMateriaEnMeg").hide();
                  $("#btnCambiaProfesorEnMeg").hide();
                  sss("grupo_finalizado", true, false);
              }
          },
          () => {},
          {
              titleColor: "rgba(29,165,245,0.8)",
              okButtonBackground: "rgba(29,165,245,0.8)"
          }
      );
  });
};

var modal_cambia_materiaEnMeg = function () {
    $("#btnCambiarMateriaEnMeg").click(function () {
        llena_lista_materias("#sctListaMateriasEnCambio");
        $("#mdlCambiarMateriaEnMeg").modal("show");
    });
};

var modal_cambia_profesorEnMeg = function () {
    $("#btnCambiaProfesorEnMeg").click(function (){
        llena_lista_profesores("#sctListaProfesoresEnCambio");
        $("#mdlCambiarProfesorMateria").modal("show");
    });
};

var submit_cambia_profesorEnMeg = function () {
    $("#frmCambiarProfesorMateria").submit(function (e) {
       e.preventDefault();
        var dataSubmit = get_object_from_serializedArray($(this).serializeArray());
        //dataSubmit['materia']=$("#materia")
        var result = post_data("../../api/requests/director/updates/materia_en_grupo/actualiza_datos.php",  $(this).serialize(), true);
        //console.log($(this).serialize());
        if (result.done) {
            nfy(result.message, "i");
            $("#mdlCambiarProfesorMateria").modal("hide");
        }
    });
};

var submit_cambia_materiaEnMeg = function () {
    $("#frmCambiarMateriaEnMeg").submit(function (e) {
       e.preventDefault();
        var result = post_data("../../api/requests/director/updates/materia_en_grupo/actualiza_datos.php",  $(this).serialize(), true);
        if (result.done) {
            nfy(result.message, "i");
            $("#mdlCambiarMateriaEnMeg").modal("hide");
        }
       console.log($(this).serialize());
    });
};

var submit_agregar_materia_a_grupo = function () {
    $("#frmAgregaMateriaAGrupo").submit(function (e) {
        e.preventDefault();

        var dataSubmit = get_object_from_serializedArray($(this).serializeArray());
        dataSubmit["id_grupo"] = $("#sctListaGrupos").val();

        if (parseInt(dataSubmit["id_materia"]) < 0) {
            rep("Materia No Seleccionada", "Por favor selecciona una materia", "w");
        } else if (parseInt(dataSubmit["id_profesor"]) < 0) {
            rep ("Profesor No Seleccionado", "Por favor, selecciona a un profesor", "w");
        } else {
            if (parseInt(dataSubmit["id_profesor"]) === 0) {
                delete dataSubmit["id_profesor"];
            }

            var result = post_data("../../api/requests/director/creates/materia_en_grupo/crea_materia_en_grupo.php", dataSubmit, true);

            if (result.done) {
                nfy(result.message, "i");
                $("#tbListaMateriasEnGrupo").append('<tr><td>'+ result.data.id+'</td><td>'+ result.data.materia.nombre +'</td><td><button type="button" class="btn btn-fab btn-fab-mini btn-primary btn-round btn-meg" data-id-meg="'+ result.data.id +'"><i class="material-icons">edit</i> </button><button type="button" class="btn btn-fab btn-fab-mini btn-danger btn-round" data-id-meg="'+ result.data.id +'"><i class="material-icons">close</i> </button> </td></tr>');
                $("#mdlAgregaNuevaMateria").modal("hide");
                obten_detalle_meg();
                del();
            }
        }
    });
};

var modal_agregar_materia_a_grupo = function () {
    $("#btnAgregaMateriaAGrupo").click(function () {

        if (parseInt($("#sctListaGrupos").val()) > 0) {
            llena_lista_materias("#sctMateriaParaAgregar");
            llena_lista_profesores('#sctProfesorParaMateria');
            $("#mdlAgregaNuevaMateria").modal("show");
        } else {
            rep("Grupo No Seleccionado", "Por favor, selecciona un grupo", 'w');
        }
    });
};

var llena_lista_profesores = function (listaTarget) {
    var $sctProfesorParaMateria = $(listaTarget);
    $sctProfesorParaMateria.empty().append('<option value="-1">Selecciona un profesor</option>');
    $sctProfesorParaMateria.append('<option value="0">Profesor Pendiente</option>');

    var result_profsAscritos = get_data("../../api/requests/common/getters/profesor/get_all.php?groupby=tipo_contrato&id_carrera="+ ssg("id_carrera", false), true);

    if (result_profsAscritos.done) {
        var tipoProfesores = Object.keys(result_profsAscritos.data);

        tipoProfesores.forEach(tipo => {
            $sctProfesorParaMateria.append(obten_optGroup_lista_profesor(tipo, result_profsAscritos.data[tipo]));
        });
    }

    var result_profsPrestamo = get_data("../../api/requests/common/getters/profesor/get_profesores_prestamo.php?id_carrera=" + ssg("id_carrera", true), true);

    if (result_profsPrestamo.done) {
        $sctProfesorParaMateria.append(obten_optGroup_lista_profesor("Prestamo", result_profsPrestamo.data));
    }

    $sctProfesorParaMateria.selectpicker("refresh");
};

var llena_lista_materias = function(listaTarget) {
    var $sctMateriaParaAgregar = $(listaTarget);
    $sctMateriaParaAgregar.empty().append('<option value="-1">Selecciona una Materia</option>');
    var result = get_data("../../api/requests/common/getters/materia/get_all.php?group_by=cuatrimestre&id_plan=" + ssg("id_plan", false), true);

    if (result.done) {
        var cuatrimestres = Object.keys(result.data);

        cuatrimestres.forEach(cuatri => {
            $sctMateriaParaAgregar.append(obten_optGroup_lista_materias(cuatri, result.data));
        });
    }

    $sctMateriaParaAgregar.selectpicker("refresh");
};

var obten_optGroup_lista_profesor = function (tipoProf, allProfs) {
    var txtOptGroup = '<optgroup label="'+ tipoProf +'">';

    allProfs.forEach(profesor => {
        txtOptGroup += '<option value="'+ profesor.id +'">'+ profesor.nivel_adscripcion + ' ' + profesor.usuario.nombre + ' ' + profesor.usuario.apellidos +'</option>';
    });

    txtOptGroup += '</optgroup>';
    return txtOptGroup;
};

var obten_optGroup_lista_materias = function(int_cuatri, allData) {
    var txtOptGroup = '<optgroup label="'+ get_txt_cuatrimestre(int_cuatri, true) +' Cuatrimestre">';

    allData[int_cuatri].forEach(materia => {
        txtOptGroup += '<option value="'+ materia.id +'">'+ materia.nombre +'</option>';
    });

    txtOptGroup += '</optgroup>';
    return txtOptGroup;
};

var submit_agrega_grupo_a_carga = function () {
    $("#frmAgregaGrupo").submit(function (e) {
        e.preventDefault();

        var result = post_data("../../api/requests/director/creates/grupo/crea_grupo_en_carga.php", $(this).serialize(), true);

        if (result.done) {
            nfy(result.message, 'i');
            obten_grupos_carga();
        }
    });
};

var muestra_fechas_modalAddGrupo = function () {
    $("#chkInicioDefault").change(function () {
        if (this.checked) {
            $("#dvFechaNoDefaultInicio").hide();
            $("#dteFechaNoDefaultInicio").attr("required", false);
        } else {
            $("#dvFechaNoDefaultInicio").show();
            $("#dteFechaNoDefaultInicio").attr("required", true);
        }
    });

    $("#chkCierreDefault").change(function () {
        if (this.checked) {
            $("#dvFechaNoDefaultCierre").hide();
            $("#dteFechaNoDefaultCierre").attr("required", false);
        } else {
            $("#dvFechaNoDefaultCierre").show();
            $("#dteFechaNoDefaultCierre").attr("required", true);
        }
    });
};

var del= function (){
    console.log("aaaaaaaa");
    $(".btn-del").click(function (){
        Notiflix.Confirm.show(
            'Eliminar MEG',
            '¿Realmente deseas eliminar la materia del grupo?',
            'Yes',
            'No',
            () => {
                var dataSubmit = {"id_meg": $(this).data("id-meg")};
                var result = post_data("../../api/requests/director/updates/materia_en_grupo/delete_Meg.php", dataSubmit, true);
                if (result.done) {
                    nfy(result.message, 'i');
                    sss("Materia Eliminada", true, false);
                }
            },
            () => {},
            {
                titleColor: "rgba(29,165,245,0.8)",
                okButtonBackground: "rgba(29,165,245,0.8)"
            }
        );
    });
}

var obten_detalle_meg = function () {
    $(".btn-meg").click(function () {
        var id_meg = $(this).data("idMeg");
        var result = get_data("../../api/requests/director/getters/materia_en_grupo/get_one.php?id_meg=" + id_meg, true);

        if (result.done) {
            $("#nombreMeg").text(result.data.materia.nombre);
            $("#alumnosEstimadosMeg").text(result.data.alumnos_estimados);
            $("#profesorAsignadoMeg").text(result.data.profesor.nivel_adscripcion + " " + result.data.profesor.nombre + " " + result.data.profesor.apellidos);

            var esEquivalente = Boolean(result.data.es_equivalente);

            if (esEquivalente) {
                $("#esCompartidaMeg").text("Si");
                $("#dvVerMateriaOriginal").show();
            } else {
                $("#esCompartidaMeg").text("No");
                $("#dvVerMateriaOriginal").hide();
            }

            var horasXSemana = (result.data.materia.horas_totales / parseInt($("#semanas_grupo").text())) + result.data.modificador_horas;

            $("#horasXSemanaMeg").text(horasXSemana);

            $(".id_meg").val(id_meg);
            $(".materia").val(result.data.materia.id);
            $(".grupo").val(result.data.id_grupo);
            $(".modificador_horas").val(result.data.modificador_horas);
            $(".alumnos_estimados").val(result.data.alumnos_estimados);
            $(".id_profesor").val(result.data.profesor.id);
            var grupoFinalizado = ssg("grupo_finalizado", false) === "true";
            if (!grupoFinalizado) {
                console.log(grupoFinalizado);
                $("#btnCambiarMateriaEnMeg").show();
                $("#btnCambiaProfesorEnMeg").show();
            }
        }

    });
}

var obten_grupos_carga = function () {
    var $sctListaGrupos = $("#sctListaGrupos");

    $sctListaGrupos.empty();
    $sctListaGrupos.append('<option value="-1">Seleccione un grupo</option>');

    var result = get_data("../../api/requests/director/getters/grupo/get_claves_grupos_de_carga.php?id_carga="+ssg("id_carga", false), true);

    if (result.done) {
        result.data.forEach(grupo => {
            $sctListaGrupos.append('<option value="'+ grupo.id +'">'+ grupo.clave +'</option>');
        });
    }

    $sctListaGrupos.selectpicker("refresh");

    $sctListaGrupos.change(function () {
        var $idGrupo = $(this).val();
        var $tbListaMateriasEnGrupo = $("#tbListaMateriasEnGrupo");

        limpia_campos_tarjeta_meg();
        if ($idGrupo < 1) {
            $("span#clave_grupo").text("");
            $("span#cuatrimestre_grupo").text("");
            $("span#turno_grupo").text("");
            $("span#inicio_grupo").text("");
            $("span#finaliza_grupo").text("");
            $("span#semanas_grupo").text("");
            $("span#semanas_grupo").text("");
            $tbListaMateriasEnGrupo.empty();
        } else {
            var result = get_data("../../api/requests/director/getters/grupo/get_detalles_del_grupo.php?id_grupo=" + $idGrupo, true);

            if (result.done) {
                $("span#clave_grupo").text(result.data.clave);
                $("span#cuatrimestre_grupo").text(result.data.cuatrimestre);
                $("span#turno_grupo").text(result.data.turno);

                if (result.data.fecha_inicio === null) {
                    $("span#inicio_grupo").text($("#fechaInicio").text());
                } else {
                    $("span#inicio_grupo").text(result.data.fecha_inicio);
                }

                if (result.data.fecha_final === null) {
                    $("span#finaliza_grupo").text($("#fechaFin").text());
                } else {
                    $("span#finaliza_grupo").text(result.data.fecha_final);
                }
                $("span#semanas_grupo").text(result.data.semanas);
                $tbListaMateriasEnGrupo.empty();
                result.data.materias.forEach(materia => {
                    $tbListaMateriasEnGrupo.append('<tr><td>'+ materia.id+'</td><td>'+ materia.materia.nombre +'</td><td><button type="button" class="btn btn-fab btn-fab-mini btn-primary btn-round btn-meg" data-id-meg="'+ materia.id +'"><i class="material-icons">edit</i> </button><button type="button" class="btn btn-fab btn-fab-mini btn-danger btn-round btn-del" data-id-meg="'+ materia.id +'"><i class="material-icons">close</i> </button> </td></tr>');
                });

                if (!result.data.finalizado) {
                    sss("grupo_finalizado", false, false);
                    var $btnFinalizarGrupo = $("#btnFinalizarGrupo");
                    $btnFinalizarGrupo.data("id-grupo", $idGrupo);
                    $btnFinalizarGrupo.show();
                    $btnFinalizarGrupo.show();
                } else {
                    sss("grupo_finalizado", true, false);
                }
            }
        }
        del();
        obten_detalle_meg();
    });
};

var llena_tarjeta_datos_carga = function () {
    var id_carga = ssg("id_carga", false);

    var result = get_data('../../api/requests/director/getters/carga_academica/get_one.php?id_carga='+ id_carga, true);

    if (result.done) {
        $("#clave_plan").text(result.data.plan.clave);
        $("#periodo_anio_carga").text(get_txt_periodo(result.data.periodo) + " " + result.data.anio);
        $("#fechaInicio").text(result.data.fecha_inicio);
        $("#fechaFin").text(result.data.fecha_final);

        sss("id_plan", result.data.plan.id, false);
    }
}

var limpia_campos_tarjeta_meg = function () {
    $("#nombreMeg").text("");
    $("#btnCambiarMateriaEnMeg").hide();
    $("#btnCambiaProfesorEnMeg").hide();
    $("#btnVerMateriaOriginal").hide();
    $("#btnFinalizarGrupo").hide();
    $("#horasXSemanaMeg").text("");
    $("#alumnosEstimadosMeg").text("");
    $("#profesorAsignadoMeg").text("");
    $("#esCompartidaMeg").text("");
};