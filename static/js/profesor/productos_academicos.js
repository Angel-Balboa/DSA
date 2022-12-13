var initd = function() {
    // trigger del cambio de tipo de producto
    $('#sctTipoProducto').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        carga_y_muestra_formulario_producto(clickedIndex);
    });
    submit_nuevo_producto_academico();
    carga_productos_del_profesor();
};

var carga_productos_del_profesor = function () {
    var result = get_data("../../api/requests/common/getters/producto_cientifico/get_productos_del_profesor.php?id_profesor=" + ssg("id_profesor", false), true);
    console.log(result.data);
    if (result.done){
        var result2 = get_data("../../api/requests/profesor/getters/producto_cientifico/get_total.php?id_profesor=" + ssg("id_profesor", false), true);
        llena_tarjeta_journals(result.data.Article);
        llena_tarjeta('#tbIndexados', result.data.InProceedings);
        llena_tarjeta('#tbIndexados', result.data.InCollection);
        llena_tarjeta('#tbIndexados', result.data.Proceedings);
        llena_tarjeta('#tbCongresos', result.data.Conference);
        llena_tarjeta_libros(result.data.Book);
        llena_tarjeta_otros(result.data.Article);
        llena_tarjeta_otros(result.data.Book);
        llena_tarjeta_otros(result.data.Booklet);
        llena_tarjeta_otros(result.data.Conference);
        llena_tarjeta_otros(result.data.InBook);
        llena_tarjeta_otros(result.data.InCollection);
        llena_tarjeta_otros(result.data.InProceedings);
        llena_tarjeta_otros(result.data.Manual);
        llena_tarjeta_otros(result.data.MasterThesis);
        llena_tarjeta_otros(result.data.PhdThesis);
        llena_tarjeta_otros(result.data.Proceedings);
        llena_tarjeta_otros(result.data.TechReport);
        llena_tarjeta_otros(result.data.Unpublished);

        llena_total(result2.data);

        modal_visualizar_producto();
        modal_actualizar_producto();
        submit_actualizar_producto();
    }
};

var submit_actualizar_producto = function() {
    $("#updateProductForm").submit(function (e) {
        e.preventDefault();
        var result = post_data("../../api/requests/profesor/updates/producto_cientifico/update_producto_cientifico.php", $(this).serialize(),true);

        if (result.done) {
            location.reload();
        }
    });
};

var modal_actualizar_producto = function () {
    $(".btn-update-product").click(function () {
        $("#updateProductForm #frmcont").load("../../v/components/html/profesor/get_form_academic_product.php?id_producto=" + $(this).data("idProducto"));
        var sctUpdTipoProducto = $("#updateProductForm #sctUpdateTipoProducto2");
        sctUpdTipoProducto.val($(this).data("productType"));
        sctUpdTipoProducto.selectpicker("refresh");
        $("#actualizaProducto").modal("show");
    });
};

var modal_visualizar_producto = function() {
  $(".show-product").click(function() {
      var result = get_data("../../api/requests/profesor/getters/producto_cientifico/get_one.php?id_producto=" + $(this).data("idProducto"), true);

      if (result.done) {
          const Cite = require('citation-js');
          var apaCite = new Cite(result.data.entries._original);
          let output = apaCite.format('bibliography', {
              format: 'html',
              template: 'apa',
              lang: 'en-US'
          });

          $("#apaFormat").empty().html(output);
          $("#bibtexFormat").empty().html(result.data.entries._original);
          $("#visualizarProducto").modal("show");
      }
  });
};

var llena_tarjeta_otros = function (lista) {
    lista.forEach(function (producto) {
        var template = '<tr>\n' +
            '             <td>' + producto.id + '</td>\n' +
            '             <td>'+ producto.entries.type +'</td>\n' +
            '             <td>'+ producto.entries.title +'</td>\n' +
            '             <td>' + producto.entries.year + '</td>\n' +
            '             <td class="td-actions text-right">\n' +
            '               <button type="button" role="tooltip" title="Edit Task" class="btn btn-primary btn-link btn-sm btn-update-product" data-product-type="'+ producto.entries.type +'" data-id-producto="'+ producto.id +'" >\n' +
            '                 <i class="material-icons">edit</i>\n' +
            '               </button>\n';
        template += boton_visualizar(producto);
        template +='     </td>\n' +
            '           </tr>';
        $("#tbOthers").append(template);
    });

};

var llena_total = function (total) {
        var template = total[0].NumberofRows;
        $("#txttotal").append(template);

};

var llena_tarjeta_libros = function (listaLibros) {
    listaLibros.forEach(function (libro) {
        var template = '<tr>\n' +
            '             <td>' + libro.id + '</td>\n' +
            '             <td>'+ libro.entries.title +'</td>\n' +
            '             <td>'+ libro.entries.publisher +'</td>\n' +
            '             <td>' + libro.entries.year + '</td>\n' +
            '             <td class="td-actions text-right">\n' +
            '               <button type="button" role="tooltip" title="Edit Task" class="btn btn-primary btn-link btn-sm btn-update-product" data-product-type="'+ libro.entries.type +'" data-id-producto="'+ libro.id +'" >\n' +
            '                 <i class="material-icons">edit</i>\n' +
            '               </button>\n';
        template += boton_visualizar(libro);
        template += '     </td>\n' +
            '           </tr>';
        $("#tbLibros").append(template);
    });
}


var llena_tarjeta = function (tbody, listaIndexados) {
    listaIndexados.forEach(function (indexado) {
        var template = '<tr>\n' +
            '             <td>' + indexado.id + '</td>\n' +
            '             <td>'+ indexado.entries.title +'</td>\n' +
            '             <td>'+ indexado.entries.booktitle +'</td>\n' +
            '             <td>' + indexado.entries.year + '</td>\n' +
            '             <td class="td-actions text-right">\n' +
            '               <button type="button" role="tooltip" title="Edit Task" class="btn btn-primary btn-link btn-sm btn-update-product" data-product-type="'+ indexado.entries.type +'" data-id-producto="'+ indexado.id +'" >\n' +
            '                 <i class="material-icons">edit</i>\n' +
            '               </button>\n';
        template += boton_visualizar(indexado);
        template += '             </td>\n' +
            '           </tr>';
        $(tbody).append(template);
    });
};

var llena_tarjeta_journals = function (listaJournals) {
    listaJournals.forEach(function (journal) {
        var template = '<tr>\n' +
            '             <td>' + journal.id + '</td>\n' +
            '             <td>'+ journal.entries.title +'</td>\n' +
            '             <td>'+ journal.entries.journal +'</td>\n' +
            '             <td>' + journal.entries.year + '</td>\n' +
            '             <td class="td-actions text-right">\n' +
            '               <button type="button" role="tooltip" title="Edit Task" class="btn btn-primary btn-link btn-sm btn-update-product" data-product-type="'+ journal.entries.type +'" data-id-producto="'+ journal.id +'" >\n' +
            '                 <i class="material-icons">edit</i>\n' +
            '               </button>\n';
        template += boton_visualizar(journal);
        template += '             </td>\n' +
            '           </tr>';
        $("#tbJournals").append(template);
    });
};

var boton_visualizar = function (p) {
    return '<button type="button" role="tooltip" title="Visualizar" class="btn btn-primary btn-link btn-sm show-product" data-id-producto="' + p.id + '">\n' +
        '     <i class="material-icons">visibility</i>\n' +
        '   </button>\n';
};

var carga_y_muestra_formulario_producto = function (clickedIndex) {
    var form_content = $("#form_content1");
    var realIndex = parseInt(clickedIndex) - 1;
    form_content.empty();
    if (realIndex >= 0) {
        $("#btnGuardarNuevoProducto").removeClass("invisible");
        form_content.load("../components/html/profesor/get_form_academic_product.php?index=" + realIndex);
    } else {
        $("#btnGuardarNuevoProducto").addClass("invisible");
    }
};

var submit_nuevo_producto_academico = function (){

  $("#productForm").submit(function(event) {
      event.preventDefault();
      console.log( $(this).serialize());
      var result = post_data("../../api/requests/profesor/creates/producto_cientifico/crea_producto_cientifico.php", $(this).serialize(), true);
      if (result.done) {
          nfy("Se ha agregado con éxito el producto científico", 'i');
          carga_productos_del_profesor();
          location.reload();
      }
      else{
          nfy("Se petateo", 'i');
      }
  });
};