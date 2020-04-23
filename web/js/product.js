var sort = function (event, params) {

    var data = {};
    params.stack.forEach(function(item, i, arr){
        data['stack'+i] = item.key
    });

    $.ajax({
        url: '/admin/catalog/sort',
        data: data,
        type: 'POST'
    })
}

$('#getLink').on('click', function(e){
    e.preventDefault();
    var string = $('#products-name').val();
    var id = $('#products-id').val();
    if(string != ''){
        $.ajax({
            url: '/admin/catalog/slug?string=' + string + '&id=' + id,
            success: function(data){
                $('#products-slug').val(data);
            }
        });
    }
});
