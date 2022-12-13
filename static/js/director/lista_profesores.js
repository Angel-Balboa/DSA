var initd = function() {

    if (ssg("id_carrera", false !== null)) {
        llena_tarjetas_profesores();
        llena_tarjeta_profesores_prestamo();
        modal_vista_rapida_profesor();
        modal_solicitar_prestamo();
        submit_solicitar_prestamo();

    } else {
        rep("Error", "No se ha podido obtener el Id de la carrera", 'e');
    }
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

var es_profesor_en_prestamo = function (profesor) {
    var ban = false;
    var $profs_prestamo = ssg("profs_prestamo");

    for (i=0; i < $profs_prestamo.length; i++) {
        if (parseInt(profesor.id) === parseInt($profs_prestamo[i].id)) {
            ban = true;
            break;
        }
    }
    return ban;
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

var modal_vista_rapida_profesor = function (){
    $(".btn-qv-profesor").click(function () {
        var tipo = $(this).data("tipo");
        var id = parseInt($(this).data("idProfesor"));
        var listaProfs = ssg("profs_" + tipo);
        var ban = false;
        var prof = null;

        for (i=0; i < listaProfs.length; i++) {
            if (parseInt(listaProfs[i].id) === id) {
                prof = listaProfs[i];
                ban = true;
                break;
            }
        }

        if (ban) {
            $("#qvNombre").text(prof.usuario.nombre + ' ' + prof.usuario.apellidos);
            $("#qvEmail").text(prof.usuario.email);
            $("#qvTelefono").text(prof.usuario.telefono);
            $("#qvExtension").text(prof.usuario.extension);

            var txtTipoContrato = (prof.tipo_contrato === "P.T.C") ? "Profesor Tiempo Completo" : "Profesor Por Asignatura";
            $("#qvTipoContrato").text(txtTipoContrato);

            $("#qvInicioContrato").text(prof.inicio_contrato);
            $("#qvFinContrato").text(prof.fin_contrato);

            llena_disponibilidad_profesor(prof.id);

            $("#mdlVistaRapidaProfesor").modal("show");
        }

    });
};

var llena_disponibilidad_profesor = function (id_profesor) {
    var $disponibilidad = get_data("../../api/requests/common/getters/disponibilidad/disponibilidad_profesor.php?id_profesor="+id_profesor, true);
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
};

var llena_tarjeta_profesores_prestamo = function () {
  var result = get_data("../../api/requests/common/getters/profesor/get_profesores_prestamo.php?id_carrera=" + ssg("id_carrera", false), true);
  if (result.done) {
      var listaProfesores = result.data;
      var carrerasKComparten = obten_lista_carreras_que_comparten(listaProfesores);
      // agregamos los acordeones por cada carrera.
      for (i=0; i < carrerasKComparten.length; i++) {
          anexar_acordion_carrera(carrerasKComparten[i]);
      }

      for (i=0; i < listaProfesores.length; i++) {
          agrega_profesor_a_acordeon(listaProfesores[i]);
      }
      sss("profs_prestamo", listaProfesores);
  }
};

var agrega_profesor_a_acordeon = function (profesor) {
  var template = '<tr>' +
      '             <td>'+ profesor.id +'</td>' +
      '             <td>'+ profesor.usuario.nombre + '' + profesor.usuario.apellidos +'</td>' +
      '             <td>'+ profesor.tipo_contrato +'</td>' +
      '             <td><button type="button" class="btn btn-primary btn-link btn-qv-profesor" data-tipo="prestamo" data-id-profesor="'+ profesor.id +'"><i class="material-icons">preview</i></button></td>' +
      '           </tr>';
  var targetAcordion = "#tbPrestamosDe" + profesor.carrera.clave;
  $(targetAcordion).append(template);
};

var anexar_acordion_carrera = function (carrera) {
    var template = '<div class="card">\n' +
        '             <div class="card-header" role="tab" id="headingIng'+ carrera.clave+'">\n' +
        '               <h6 class="mb-0">\n' +
        '                 <a data-toggle="collapse" href="#collapseIng'+ carrera.clave +'" aria-expanded="true" aria-controls="collapseIng'+ carrera.clave +'">\n' +
        '                          '+ carrera.nombre +'\n' +
        '                        </a>\n' +
        '                      </h6>\n' +
        '                    </div><!-- end card-header -->\n' +
        '                    <a id="collapseIng'+ carrera.clave +'" class="collapse hide" role="tabpanel" aria-labelledby="headingIng'+ carrera.clave +'" data-parent="#accordion2">\n' +
        '                      <div class="card-body table-responsive">\n' +
        '                        <table class="table table-hover">\n' +
        '                          <thead class="text-primary">\n' +
        '                          <tr>\n' +
        '                            <th>Id</th>\n' +
        '                            <th>Nombre</th>\n' +
        '                            <th>Tipo</th>\n' +
        '                            <th>Acciones</th>\n' +
        '                          </tr>\n' +
        '                          </thead>\n' +
        '                          <tbody id="tbPrestamosDe'+ carrera.clave +'">\n' +
        '                          </tbody>\n' +
        '                        </table>\n' +
        '                      </div> <!-- end card-body -->\n' +
        '                    </a>\n' +
        '                  </div><!--end card -->';
    $("#accordion2").append(template);
};

var obten_lista_carreras_que_comparten = function (listaProfesores) {
    var arrayClvsCarreras = [];
    var objsCarreras = []

    for (i=0; i < listaProfesores.length; i++) {
        if (!arrayClvsCarreras.includes(listaProfesores[i].carrera.clave)) {
            arrayClvsCarreras.push(listaProfesores[i].carrera.clave);
            objsCarreras.push({'clave': listaProfesores[i].carrera.clave, 'nombre': listaProfesores[i].carrera.nombre});
        }
    }

    return objsCarreras;
};

var llena_tarjetas_profesores = function () {
    var result = get_data("../../api/requests/common/getters/profesor/get_all.php?groupby=tipo_contrato&id_carrera=" + ssg("id_carrera", false));

    if (result.done) {

        if (result.data.PTC) {
            llena_tarjeta_profesor(result.data.PTC, "PTC");
            sss("profs_PTC", result.data.PTC);
        }

        if (result.data.PA) {
            llena_tarjeta_profesor(result.data.PA, "PA");
            sss("profs_PA", result.data.PA);
        }
    }
};

var llena_tarjeta_profesor = function (listaProfesores, targetType) {
    var target = "#tbProfesores" + targetType;
    var $target = $(target);
    var template = '';
    $target.empty();
    for(i=0; i < listaProfesores.length; i++) {
        template = '<tr>\n' +
            '         <td>'+ listaProfesores[i].id +'</td>\n' +
            '         <td>'+ listaProfesores[i].usuario.nombre + ' ' + listaProfesores[i].usuario.apellidos +'</td>\n' +
            '         <td>\n' +
            '           <button type="button" rel="tooltip" title="Vista Rápida" class="btn btn-primary btn-link btn-qv-profesor" data-tipo="'+ targetType +'" data-id-profesor="'+ listaProfesores[i].id +'" >\n' +
            '             <i class="material-icons">preview</i>\n' +
            '           </button>\n' +
            '         </td>\n' +
            '       </tr>';
        $target.append(template);
    }
};