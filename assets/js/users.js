var url = '/webflotas/index.php/';
var tabla;

function init() {
        // limpiarForm();
        // mostrarForm(false);
        listar();
}
init();

function listar() {
    tabla = $('#table_id').DataTable({
        "processing": true,
        "serverSide": false,
        ajax: {
            method: 'post',
            url: url+'users/list',
            dataType: 'json',
            error: function(e) {
                console.log(e.responseText);
            }
        },
        language: {
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },
        "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        "pageLength": 5,
        dom: 'Bfrtip', // Blfrtip
        // buttons: [
        //     'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
        // ],
        buttons: [
            {
                extend: 'copy',    
                exportOptions: {
                    columns: [0, 1, 2, 3, 4] //Es lo mismo que thead th:not(.noExport)
                }
            },
            {
                extend: 'csv',    
                exportOptions: {
                    columns: "thead th:not(.noExport)"
                }
            },
            {
                extend: 'excel',            
                exportOptions: {
                    columns: "thead th:not(.noExport)"
                }
            },
            {
                extend: 'pdf',
                exportOptions: {
                    columns: "thead th:not(.noExport)"
                }
            },
            {
                extend: 'print',
                exportOptions: {
                    columns: "thead th:not(.noExport)"
                }
            },
            {
                extend: 'pageLength',        
            }
        ],
        // "columnDefs": [
        //     {
        //         "targets": [ 0 ],
        //         "visible": false,                
        //     }
        // ]
    });
    
    var div = $('<div class="row">\n\
                    <div id="div" class="col-sm-12 col-md-7">\n\
                    </div>\n\
                    <div id="div2" class="col-sm-12 col-md-5">\n\
                    </div>\n\
                    </div>');
    $('#table_id').before(div);              // Creamos el div antes de id='table_id'
    $('#wea').appendTo('#div');              // Agregamos id='wea' dentro del div
    $('.dt-buttons').appendTo('#div');       // Agregamos class="dt-buttons" dentro del div
    $('#table_id_filter').appendTo('#div2'); // Agregamos id="table_id_filter" dentro del div

    var div2 = $('<div class="row">\n\
                    <div id="div3" class="col-sm-12 col-md-5">\n\
                    </div>\n\
                    <div id="div4" class="col-sm-12 col-md-7">\n\
                    </div>\n\
                    </div>');
    $('#table_id').after(div2);                // Creamos el div2 antes de id='table_id'
    $('#table_id_info').appendTo('#div3');     // Agregamos id="table_id_info" dentro del div2
    $('#table_id_paginate').appendTo('#div4'); // Agregamos id="table_id_paginate" dentro del div2

    $('.dt-buttons button').removeClass('btn-secondary'); // Remueve class="btn-secondary"
    $('.dt-buttons button').addClass('btn-primary');      // Agrega class="btn-primary"
}
