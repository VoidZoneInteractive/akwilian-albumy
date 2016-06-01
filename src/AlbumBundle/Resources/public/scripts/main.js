$( document ).ready(function() {
    $('#album-family input').click(function (e) {
        var familyId = $(this).val();
        $('#album-cover').slideUp({
            complete: function () {

                $('#cover-preview').slideUp();
                $('#style-preview').slideUp();

                $.get(Routing.generate('pl__RG__album_ajax_family', {id: familyId}), function(data) {
                    console.log(data);
                    $('#promo-image').attr('src', data.response.image);
                    $('#promo-description').html(data.response.description);

                    var coverContainer = $('#cover-container');

                    coverContainer.html('');

                    $('#cover-panel > .panel-heading').html(data.response.label);

                    if (data.response.id == 4 || data.response.cover == 5) {
                        $('#style-panel').show();
                        $('#style-panel input').attr('required', 'required');
                    } else {
                        $('#style-panel').hide();
                        $('#style-panel input').removeAttr('required');
                    }

                    for(var coverId in data.response.covers) {
                        var cover = data.response.covers[coverId];
                        coverContainer.append('<div class="col-md-2">' +
                        '<a data-value="'+cover.id+'" style="width: 66px; height: 66px;" href="#" class="album-cover-item thumbnail"  data-image-big="'+cover.image_big+'">' +
                        '<img src="'+cover.image+'" />' +
                        '</a>' +
                        '</div>');
                        console.log(cover);
                    }

                    $('#album-cover').slideDown();
                });
            }
        });
    });

    $('#cover-container').on('click', '.album-cover-item', function(e) {
        var albumCoverItem = $(this);
        e.preventDefault();

        //console.log(albumCoverItem.data('value'));

        $('#album_cover').val(albumCoverItem.data('value'));

        var coverPreview = $('#cover-preview');
        coverPreview.slideUp({
            complete: function() {
                $(this).attr('src', albumCoverItem.data('image-big'));
                $(this).slideDown();
            }
        });
    });

    $('#style-container').on('click', '.album-style-item', function(e) {
        var albumStyleItem = $(this);
        e.preventDefault();

        //console.log(albumStyleItem.data('value'));

        $('#album_style').val(albumStyleItem.data('value'));

        var coverPreview = $('#style-preview');
        coverPreview.slideUp({
            complete: function() {
                $(this).attr('src', albumStyleItem.data('image-big'));
                $(this).slideDown();
            }
        });
    });

    $('form').submit(function(e) {
        if ($('#album_cover').val() == '' || ($('#album_style').val() == '' && ($('#album_family') == '4' || $('#album_family') == '5'))) {
            alert('Zaznacz wszystkie wymagane pola przed wysłaniem formularza.');
            e.preventDefault();
        }
    });
});