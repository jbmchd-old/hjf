$(function () {

    $('#admin_fotos_categoria').change(function () {
        if(this.value == '0'){
            $('#admin_fotos_titulo').val('');
            $('#admin_fotos_table_fotos tbody').html('');
        }
        $.ajax({
            url: '/admin/buscar-albuns',
            data: {categoria: $(this).find('option:selected').html()}
        }).done(function (result) {
            $('#admin_fotos_albuns').html('').append('<option value="0">Novo album...</option>');

            for (var i in result) {
                var album = result[i].split('_').join(' ');

                $('#admin_fotos_albuns').append('<option value="' + album + '">' + album + '</option>');
            }
        });
    });

    $('#admin_fotos_albuns').change(function () {
        if(this.value == '0'){
            $('#admin_fotos_titulo').val('');
            $('#admin_fotos_table_fotos tbody').html('');
            return false;
        }
        
        var categoria = $('#admin_fotos_categoria').find('option:selected').html();
        var titulo = $(this).find('option:selected').html();
        $('#admin_fotos_titulo').val(titulo);

        $.ajax({
            url: '/admin/buscar-fotos',
            data: {
                categoria: categoria,
                titulo: titulo
            }
        }).done(function (result) {
            $('#admin_fotos_table_fotos tbody').html('');
            var categoria = result['categoria'];
            var album = result['album'];
            var files = result['files'];
            for (var i in files) {
                var file = files[i];
                $('#admin_fotos_table_fotos tbody').append('<tr data-file="' + file + '"><td>' + file + '</td><td><img style="max-width:150px" src="data\\albuns\\' + categoria + '\\' + album + '\\' + file + '" /></td><td><button type="button" class="btn btn-default"><i class="fa fa-times-circle"></i></button></td></tr>')

            }

        });

    });

    $('#admin_fotos_table_fotos tbody').on('click', 'button', function () {
        var botao = this;
        $.ajax({
            url: '/admin/apagar-foto',
            data: {
                categoria: $('#admin_fotos_categoria').find('option:selected').html(),
                titulo: $('#admin_fotos_albuns').find('option:selected').html(),
                file: $(this).parents('tr').attr('data-file')
            }
        }).done(function (result) {
            $(botao).parents('tr').remove();
        });

    })

    $('#admin_dep_table button').click(function (){
        var id = $(this).parents('tr').attr('data-id');
        var botao = this;
        $.ajax({
            url: '/admin/excluir-depoimento',
            data: {id:id}
        }).done(function (result) {
            console.log(result);
            $(botao).parents('tr').remove();
        });
        
    });
    
    $('#admin_dep_depoimentos').change(function (){
        var id = $(this).val();
        
        if(id=='0'){
            $('#admin_dep_depoimentos').val(0)
            $('#admin_dep_autor,#admin_dep_depoimento,#admin_dep_titulo, #admin_dep_foto').val('');
        } else {
            $.ajax({
                url: '/admin/excluir-depoimento',
                data: {id: id}
            }).done(function (result) {
                result = result[0];
                $('#admin_dep_autor').val(result.autor);
                $('#admin_dep_depoimento').val(result.depoimento);
                $('#admin_dep_titulo').val(result.titulo);
            });
        }
        
    });
   

});