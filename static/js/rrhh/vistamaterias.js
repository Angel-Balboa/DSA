var initd = function initd() {
    obten_materias();
    llena_materias_carrera();
};

var obten_materias= function (){
    var  pfs = get_data("../../api/requests/common/getters/profesor/get_all.php", true);
    let profesores={};
    var materia,carrera_m={},grupo,totalcarreras=0;
    var mts = get_data("../../api/requests/common/getters/materia_en_grupo/get_all.php", true);
    var materias={},ttlca=0;
    for (let i=0;i<mts.data.length;i++){
        var datosM = get_data("../../api/requests/rrhh/getters/grupo/get_one.php?id_grupo=" + mts.data[i].id_grupo, true);
        var cg= get_data("../../api/requests/director/getters/carga_academica/get_one.php?id_carga="+datosM.data.id_carga_academica,true)
        var nombres= get_data("../../api/requests/director/getters/materia_en_grupo/get_one.php?id_meg="+mts.data[i].id,true)
        var plan= get_data("../../api/requests/common/getters/plan_de_estudio/get_one.php?id_plan="+cg.data.id_plan_estudios,true)
        materias[i]={"nombre":nombres.data.materia.nombre,"carrera":plan.data.carrera.nombre,"id_grupo":datosM.data.id,"clave":datosM.data.clave,"profesor":mts.data[i].id_profesor};
    }
    for (let i=0;i<pfs.data.length;i++){
        var datos = get_data("../../api/requests/rrhh/getters/carreras/get_one.php?clv_carrera=" + pfs.data[i].id_carrera_adscripcion, true);
        for(let v=0;v<mts.data.length;v++){
            if(materias[v].profesor===pfs.data[i].id){
                grupo=materias[v].clave;
                carrera_m[totalcarreras]={"carrera":materias[v].carrera,"profesor":pfs.data[i].id};
                materia=materias[v].nombre;
                totalcarreras+=1;
            }
        }
        profesores[i]={"id":pfs.data[i].id,"nombre":pfs.data[i].usuario.nombre+" "+pfs.data[i].usuario.apellidos,"carrera":datos.data.nombre};
    }
    llena_tabla(profesores,pfs.data,carrera_m,totalcarreras);
}

var llena_tabla=function (profesor,pfs,carrera_ma,total){
    var imagen="";
    for (let i=0;i<pfs.length;i++){
        for(j=0;j<total;j++){
            if(carrera_ma[j].profesor==profesor[i].id){
                switch (carrera_ma[j].carrera){
                    case "Ingeniería en Mecatrónica":
                        if(imagen!=""){
                            imagen=imagen+",settings_suggest";
                        }
                        else{
                            imagen="settings_suggest";
                        }
                        break;
                    case "Ingeniería en Tecnologías de la Información 3":
                        if(imagen!=""){
                            imagen=imagen+",computer";
                        }
                        else{
                            imagen="computer";
                        }
                        break;
                    case undefined:
                        imagen="a";
                        break;
                }
            }
        }
        var template = '<tr>\n' +
            '             <td>' + profesor[i].id + '</td>\n' +
            '             <td> <div class="form-check">\n' +
            '             <label class="form-check-label">\n' +
            '            <input class="form-check-input" type="checkbox" value="">\n' +
            '                                              <span class="form-check-sign">\n' +
            '                                                <span class="check"></span>\n' +
            '                                              </span>\n' +
            '                                            </label>\n' +
            '                                          </div>\n</td>\n' +
            '             <td>'+ profesor[i].nombre+'</td>\n' +
            '             <td>' + profesor[i].carrera + '</td>\n' +
            '             <td> <i class="material-icons text-success">'+imagen+'</i></td>\n' +
            '             <td class="td-actions text-right">\n' +
            '               <button type="button" role="tooltip" title="Edit Task" class="btn btn-primary btn-link btn-sm btn-update-product data-id-profesor="'+ profesor[i].id +'" >\n' +
            '                 <i class="material-icons">edit</i>\n' +
            '               </button>\n';
        template += boton_visualizar(profesor[i]);
        template += '     </td>\n' +
            '           </tr>';
        $("#tlbprof").append(template);
        imagen="";
    }

}

var boton_visualizar = function (p) {
    return '<button type="button" role="tooltip" title="Visualizar"  class="btn btn-primary btn-link btn-modal" data-id-profesor="' + p.id + '">\n' +
        '     <i class="material-icons">visibility</i>\n' +
        '   </button>\n';
};

var llena_materias_carrera = function() {
    $(".btn-modal").click(function() {
        $("#tlbmaterias").empty();
        var  profesor = get_data("../../api/requests/common/getters/profesor/get_one_profesor.php?id_profesor=" + $(this).data("idProfesor"), true);
        var matrizProfesor={},gruposmaterias="",totalm=0,icono,totalC=0,totalG=0,grupoclave="",grupoac="",matrizmateria={},matrizgrupo={},matrizcarrera={};
        var materiaprofe = get_data("../../api/requests/rrhh/getters/materia_en_grupo/get_all_by_profesor.php?id_profesor="+ $(this).data("idProfesor"), true);
        var carrera = get_data("../../api/requests/rrhh/getters/carreras/get_one.php?clv_carrera=" + profesor.data.id_carrera_adscripcion, true);
        $("#profesorname").text(profesor.data.usuario.nombre+" "+profesor.data.usuario.apellidos);
        $("#carreraAds").text(carrera.data.nombre);
        for (let i=0;i<materiaprofe.data.length;i++){
            var grupos = get_data("../../api/requests/rrhh/getters/grupo/get_one.php?id_grupo=" + materiaprofe.data[i].id_grupo, true);
            var nombresM = get_data("../../api/requests/common/getters/materia/get_one.php?id_materia=" + materiaprofe.data[i].id_materia, true);
            matrizmateria[totalm]={"mod":materiaprofe.data[i].modificador_horas,"horas":nombresM.data.horas_totales,"nombre":nombresM.data.nombre,"grupo":materiaprofe.data[i].id_grupo};
            totalm+=1;
            if(grupoclave!=grupos.data.clave){
                matrizgrupo[i]={"semanas":grupos.data.semanas,"id":grupos.data.id,"clave":grupos.data.clave,"carrera":grupos.data.carrera,"inicio":grupos.data.fecha_inicio,"fin":grupos.data.fecha_final,"Materias":matrizmateria};
                totalG=totalG+1;
            }
            if(grupoac!=grupos.data.carrera){
                if(grupos.data.finalizado===false){
                    icono="thumb_down";
                }
                else {
                    icono="thumb_up";
                }
                matrizcarrera[totalC]={"carrera":grupos.data.carrera,"estado":icono,"Grupos":matrizgrupo};
                totalC=totalC+1;
            }
            grupoac=grupos.data.carrera;
            grupoclave=grupos.data.clave;
        }
        for(var i=0;i<totalC;i++){
            var template1 = '<tr>\n' +
                '<td><i class="material-icons">'+matrizcarrera[i].estado +'</i></td>\n'+
                ' <td id="'+matrizcarrera[i].carrera+'" colspan="4">'+matrizcarrera[i].carrera+'</td>\n' +
                '           </tr>\n'+
                '<tr>';
            var totalCarreraModal=1;
            $("#tlbmaterias").append(template1);
            for(var g=0;g<totalG;g++){
                if(matrizgrupo[g].carrera==matrizcarrera[i].carrera){
                    for(var m=0;m<totalm;m++){
                        if(matrizmateria[m].grupo==matrizgrupo[g].id){
                            var horasXSemana = (matrizmateria[m].horas / parseInt(matrizgrupo[g].semanas) + matrizmateria[m].mod);

                            gruposmaterias= '<tr><td></td>\n'+
                                '<td>'+matrizgrupo[g].clave+'</td>\n'+
                                '<td>'+matrizmateria[m].nombre+'</td>'+
                                '<td>'+horasXSemana+' horas</td>'+
                                '<td>del '+matrizgrupo[g].inicio+' al '+matrizgrupo[g].fin+'</td>'+
                                '</tr>'+
                            '';
                            if(totalCarreraModal==1){
                                $("#tlbmaterias").append(gruposmaterias);
                            }
                        }
                    }
                }
            }
        }
        matrizProfesor={"nombre":profesor.data.usuario.nombre,"nivel":profesor.data.categoria,"carreraAds":carrera.data.nombre,"TipoC":profesor.data.tipo_contrato,"Carreras":matrizcarrera};
        console.log(matrizProfesor);
        $("#visualizarContrato").modal("show");
    });
}