$( document ).ready(function() {
    $('#album-family input').click(function (e) {
        var familyId = $(this).val();
        $('#album-cover').slideUp({
            complete: function () {

                $('#cover-preview').slideUp();

                $.get(Routing.generate('pl__RG__album_ajax_family', {id: familyId}), function(data) {
                    console.log(data);
                    $('#promo-image').attr('src', data.response.image);
                    $('#promo-description').html(data.response.description);

                    var coverContainer = $('#cover-container');

                    coverContainer.html('');

                    for(var coverId in data.response.covers) {
                        var cover = data.response.covers[coverId];
                        coverContainer.append('<div class="col-md-2">' +
                        '<a style="width: 66px; height: 66px;" href="#" class="album-cover-item thumbnail"  data-image-big="'+cover.image_big+'">' +
                        '<img data-value="child.vars.value" src="'+cover.image+'" />' +
                        '</a>' +
                        '</div>');
                        console.log(cover);
                    }

                    $('#album-cover').slideDown();
                });
            }
        });
    });

    $('.album-cover-item').click(function(e) {
        var albumCoverItem = $(this);
        e.preventDefault();

        var coverPreview = $('#cover-preview');
        coverPreview.slideUp({
            complete: function() {
                $(this).attr('src', albumCoverItem.data('image-big'));
                $(this).slideDown();
            }
        });
    });
});