/**
 * Artist Search Autocomplete
 *
 * Provides autocomplete search for associating artists/creators with artworks.
 * Expects artRoutesArtistSearch to be localized with: { nonce, removeText }
 *
 * @package WP Art Routes
 */

jQuery(document).ready(function($) {
    if (typeof artRoutesArtistSearch === 'undefined') {
        return;
    }

    // Autocomplete for artist search
    $('#artist_search').autocomplete({
        source: function(request, response) {
            var postType = $('#post_type_filter').val();

            $.ajax({
                url: ajaxurl,
                dataType: 'json',
                data: {
                    action: 'search_posts_for_artist',
                    term: request.term,
                    post_type: postType,
                    nonce: artRoutesArtistSearch.nonce
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        minLength: 2,
        select: function(event, ui) {
            // Add the selected artist to the list
            addArtistToList(ui.item);

            // Clear the search field
            setTimeout(function() {
                $('#artist_search').val('');
            }, 100);

            return false;
        }
    }).autocomplete('instance')._renderItem = function(ul, item) {
        return $('<li>')
            .append('<div>' + item.label + ' <span class="post-type-label">(' + item.post_type_label + ')</span></div>')
            .appendTo(ul);
    };

    // Function to add artist to the selected list
    function addArtistToList(item) {
        // Check if already added
        if ($('#selected_artists_list li[data-id="' + item.id + '"]').length === 0) {
            var artistItem = $('<li data-id="' + item.id + '"></li>');
            artistItem.append('<span class="artist-title">' + item.label + '</span>');
            artistItem.append(' <span class="post-type-label">(' + item.post_type_label + ')</span>');
            artistItem.append(' <a href="#" class="remove-artist">' + artRoutesArtistSearch.removeText + '</a>');
            artistItem.append('<input type="hidden" name="artwork_artist_ids[]" value="' + item.id + '">');

            $('#selected_artists_list').append(artistItem);
        }
    }

    // Remove artist from the list
    $(document).on('click', '.remove-artist', function(e) {
        e.preventDefault();
        $(this).parent('li').remove();
    });
});
