(function ($){
    var url = PlacesAutocomplete.url + "?action=";
    $( "#places-search-term" ).autocomplete({
        source: url + "places_autocomplete_search",
        delay: 500,
        minLength: 3,
        select: function( event, ui ) {
            
            $( "#place-search-loading" ).show();
            $( "#places-search-id").val( ui.item.link );
            getPlaceDetails( ui.item.link  ); 
        }
    });

    function getPlaceDetails( placeId ) {

        $.getJSON( url + "places_detail", { placeId: placeId } )
            .done(function( json ) {
                showPlace( json );
                console.log( "JSON Data: " + json );
            })
            .fail(function( jqxhr, textStatus, error ) {
                var err = textStatus + ", " + error;
                console.log( "Request Failed: " + err );
            });
    }

    function showPlace( data ) {
        //alert('oleeee');
    }
})(jQuery);
