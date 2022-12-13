var initd = function initd() {
    obtener_listado_carreras();
    agrega_carrera_modal();
    guarda_nueva_carrera();
    actualiza_carrera();
};

var obtener_listado_carreras = function obtener_listado_carreras() {

    var result = get_data("../../api/requests/common/getters/carrera/get_all.php", true);

    if (result.done) {
        var carreras = result.data;
        var tbody_lstCarreras = $("#lstCarreras tbody");

        carreras.forEach(carrera => tbody_lstCarreras.append(' <tr>\n' +
            '                        <td id="clave_carrera">' + carrera.clave + '</td>\n' +
            '                        <td id="nivel_carrera">' + carrera.nivel + '</td>\n' +
            '                        <td id="nombre_carrera">' + carrera.nombre + '</td>\n' +
            '                        <td id="director_carrera">' + carrera.director.nombre + ' ' + carrera.director.apellidos + '</td>\n' +
            '                        <td><div id="id_director_carrera" hidden>' + carrera.director.id + '</div>\n' +
            '                          <button type="button" rel="tooltip" title="Editar" class="btn btn-primary btn-link editar_carrera">\n' +
            '                            <i class="material-icons">edit</i>\n' +
            '                          </button>\n' +
            '                          <a href="detalle_carrera.php?clv_carrera=' + carrera.clave + '" rel="tooltip" title="Vista Rápida" class="btn btn-primary btn-link">\n' +
            '                           <i class="material-icons">preview</i>\n' +
            '                          </a>\n' +
            '                        </td>\n' +
            '                      </tr>'));
        $('button.editar_carrera').click(function (){
            var item = $(this).closest('tr'); // fila con la información de la carrera
            var nivel = item.find('#nivel_carrera').text();

            $('#sctEditNivelCarrera option').each(function() {
                $(this).attr('selected', false);
            });

            $('#sctEditNivelCarrera option[value="' + nivel + '"]').attr('selected', true);

            // agregamos al director actual como primer elemento.
            $('#sctEditDirectorCarrera').find('option').remove().end().append($('<option>', {
                value: item.find('#id_director_carrera').text(),
                text: item.find('#director_carrera').text()
            }));


            // directores no asignados a carreras
            obten_directores_no_asignados('#sctEditDirectorCarrera');

            $('form#frmEditarCarrera input[id=txtEditNombreCarrera]').val(item.find('#nombre_carrera').text());
            $('form#frmEditarCarrera input[id=hdnEditClaveCarrera]').val(item.find("#clave_carrera").text())

            $('#EditarCarrera').modal('show');

        });

    }
}

var obten_directores_no_asignados = function obten_directores_no_asignados(target_select) {

    var result = get_data("../../api/requests/admin/getters/usuario/get_all_directores_no_asignados.php", true);

    if (result.done) {
        var target = $(target_select);
        var directores = result.data;
        if (directores.length < 1) {
            Notiflix.Notify.failure('No hay directores sin asignar');
        } else {
            for(var i=0; i < directores.length; i++) {
                target.append($('<option>', {
                    value: directores[i].id,
                    text: directores[i].nombre + ' ' + directores[i].apellidos
                }));
            }
        }
    }
}

var agrega_carrera_modal = function agrega_carrera_modal() {
    $('#btnAgregarCarrera').click(function() {
        $("#frmAgregarCarrera").trigger("reset");
        $('#sctAgregarDirectorCarrera').find('option').remove().end();
        obten_directores_no_asignados('#sctAgregarDirectorCarrera');

        $('#AgregarNuevaCarrera').modal('show');
    });
}

var actualiza_carrera = function actualiza_carrera() {
    $('#frmEditarCarrera').submit(function(event) {
        event.preventDefault();

        var result = post_data("../../api/requests/admin/updates/carrera/actualiza_datos.php", $(this).serialize(), true);
        if (result.done) {
            Notiflix.Report.success('Éxito', result.message, 'Aceptar');
            $('#EditarCarrera').modal('hide');
        }
    });
}

var guarda_nueva_carrera = function guarda_nueva_carrera() {
    $('#frmAgregarCarrera').submit(function(event) {
        event.preventDefault();
        var flag = true;
        var txtClave = $("#txtAgregarClaveCarrera").val();

        var result = get_data_raw("../../api/requests/admin/getters/carreras/get_one.php?clv_carrera=" + txtClave)

        if (result.exito) {
            flag = false
            rep("Clave duplicada", 'La clave ' + txtClave + ' ya existe en el sistema. Intenta con otra');
        }

        if (flag) {
            $('#frmAgregarCarrera').validate();
            var post_result = post_data("../../api/requests/admin/creates/carrera/crea_carrera.php", $(this).serialize(), true);

            if (post_result.done) {
                rep('Éxito', post_result.message, 's');
                $('#AgregarNuevaCarrera').modal('hide');
            }
        }
    });
}