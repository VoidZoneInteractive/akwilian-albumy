$( document ).ready(function() {
    $('#album-family input').click(function (e) {
        $.get(Routing.generate('pl__RG__album_ajax_family', {id: $(this).val()}), function(data) {
            console.log(data);
            $('#promo-image').attr('src', data.response.image);
            $('#promo-description').html(data.response.description);
            $('#album-cover').slideDown();
        });
    });
});