var initd = function () {
    if (ssg("id_carrera", false)) {
        obten_planes_estudio();
        modal_agregar_carga();
        submit_update_create_carga();
    } else {
        rep("Error", "No se ha podido obtener los datos de la carrera");
    }
};

var obten_planes_estudio = function () {
    var $sctPlanesEstudio = $("#sctPlanesEstudio");
    $sctPlanesEstudio.append('<option value="-1">Selecciona un P.E.</option>');

    var result = get_data("../../api/requests/common/getters/plan_de_estudio/get_all.php?id_carrera=" + ssg("id_carrera", false), true);
    if (result.done) {
        result.data.forEach(plan => {
            $sctPlanesEstudio.append('<option value="'+ plan.id +'">'+ plan.clave + " " + plan.nombre +'</option>');
        });
    }
    $sctPlanesEstudio.selectpicker("refresh");

    $sctPlanesEstudio.change(function () {

        obten_cargas($(this).val());
    });
};

var obten_cargas = function (idPlan) {
    var result = get_data("../../api/requests/director/getters/carga_academica/get_all.php?id_plan=" + idPlan, true);

    if (result.done) {
        crea_accordionsCargas(result.data);
    }
};

var crea_accordionsCargas = function (lista_cargas) {
    var $dvAccordionsCargas = $("#dvAccordionsCargas");
    var names = Object.keys(lista_cargas);

    $dvAccordionsCargas.empty();

    names.forEach(anio => {
            var template = '<div id="accordion'+ anio +'" role="tablist">\n' +
                '              <div class="card">\n' +
                '                <div class="card-header" role="tab">\n' +
                '                  <h6 class="mb-0">\n' +
                '                    <a data-toggle="collapse" href="#collapse'+ anio +'" aria-expanded="true" aria-controls="collapse'+ anio +'">\n' +
                '                      <span>'+ anio +'</span>\n' +
                '                    </a>\n' +
                '                  </h6>\n' +
                '                </div><!-- end card-header -->\n' +
                '              <a id="collapse'+ anio +'" class="collapse hide" role="tabpanel" aria-labelledby="year'+ anio +'" data-parent="#accordion'+ anio +'">\n' +
                '                <div class="card-body" id="year'+ anio +'">\n' +
                '                  <div class="row table-responsive">\n' +
                '                    <table class="table table-hover">\n' +
                '                      <thead class="text-primary">\n' +
                '                        <tr>\n' +
                '                          <th>Id</th>\n' +
                '                          <th>Periodo</th>\n' +
                '                          <th>Inicio</th>\n' +
                '                          <th>Término</th>\n' +
                '                          <th class="text-center">Acciones</th>\n' +
                '                        </tr>\n' +
                '                      </thead>\n' +
                '                      <tbody>\n';

            lista_cargas[anio].forEach(carga =>{
                var innerTemplate = '        <tr>\n' +
                    '                          <td>'+ carga.id +'</td>\n' +
                    '                          <td>'+ get_txt_periodo(carga.periodo) +'</td>\n' +
                    '                          <td>'+ carga.fecha_inicio +'</td>\n' +
                    '                          <td>'+ carga.fecha_final +'</td>\n' +
                    '                          <td class="text-center">\n' +
                    '                            <button type="button" class="btn btn-link btn-primary btn-actualizar-carga" title="Actualizar datos de Carga" data-id-carga="'+ carga.id +'">\n' +
                    '                              <i class="material-icons">\n' +
                    '                                edit\n' +
                    '                              </i>\n' +
                    '                            </button>\n' +
                    '                            <a href="#" title="Vista Resumen">\n' +
                    '                              <i class="material-icons">\n' +
                    '                                view_quilt\n' +
                    '                              </i>\n' +
                    '                            </a>\n' +
                    '                            <a href="detalle_carga_academica.php?id_carga='+ carga.id +'" title="Visitar">\n' +
                    '                              <i class="material-icons">\n' +
                    '                                launch\n' +
                    '                              </i>\n' +
                    '                            </a>\n' +
                    '                          </td>\n' +
                    '                        </tr>\n';

                template += innerTemplate;
            });

                template += '          </tbody>\n' +
                '                    </table>\n' +
                '                  </div>\n' +
                '                </div>\n' +
                '              </a>\n' +
                '            </div><!-- end card -->\n' +
                '          </div><!-- end accordion -->';
            $dvAccordionsCargas.append(template);
    });

    modal_actualizar_carga();
};

var modal_actualizar_carga = function (){
    $(".btn-actualizar-carga").click(function () {

        var idCarga = $(this).data("idCarga");
        $("#hdnIdCarga").val(idCarga);
        $("#hdnTipoEnvio").val('update');
        $("#modal_title").text("Actualizar datos de Carga Académica");

        var result = get_data("../../api/requests/director/getters/carga_academica/get_one.php?id_carga=" + idCarga, true);

        if (result.done) {

            $("#sctPeriodoNuevaCarga").val(result.data.periodo).selectpicker("refresh");
            $("#dteFechaInicio").val(result.data.fecha_inicio);
            $("#dteFechaFinal").val(result.data.fecha_final);
            $("#sctAnioCargaAcademia").val(result.data.anio).selectpicker("refresh");

            $("#mdlAgregarActualizarCargaAcademica").modal("show");
        }
    });
};

var modal_agregar_carga = function () {
    $("#btnAgregarCarga").click(function () {
        var $sctPlanesEstudio_val = parseInt($("#sctPlanesEstudio").val());
        if ($sctPlanesEstudio_val < 1) {
            rep("Alerta", "Se necesita seleccionar un plan de estudios", 'w');
        } else {
            $("#hdnIdPlan").val($sctPlanesEstudio_val);
            $("#hdnTipoEnvio").val('create');
            $("#modal_title").text("Agregar Nueva Carga Académica");
            $("#mdlAgregarActualizarCargaAcademica").modal("show");
        }
    });
};

var submit_update_create_carga = function () {
    $("#frmAgregaPlan").submit(function (e){
        e.preventDefault();
        var $frmAgregarPlan = $(this);
        var tipoEnvio = $frmAgregarPlan.find("#hdnTipoEnvio").val();
        var dataSubmit = get_object_from_serializedArray($frmAgregarPlan.serializeArray());
        if (tipoEnvio === "create") {
            delete dataSubmit["id_carga"];

            var result = post_data("../../api/requests/director/creates/carga_academica/crea_nueva_carga_academica.php", dataSubmit, true);
        } else {
            delete dataSubmit["id_plan"];

            var result = post_data("../../api/requests/director/updates/carga_academica/actualiza_datos.php", dataSubmit, true);
        }

        if (result.done) {
            nfy(result.message, 'i');
            obten_cargas($("#sctPlanesEstudio").val());
            $("#mdlAgregarActualizarCargaAcademica").modal("hide");
        }
    });
}