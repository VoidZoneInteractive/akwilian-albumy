$( document ).ready(function() {
    $('#album-family input').click(function (e) {
        $.get('/album/ajax/family/' + $(this).val(), function(data) {
            console.log(data);
            $('#album-cover').slideDown();
        });
    });
});