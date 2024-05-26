jQuery(document).ready(function($) {
    var regions = cdt_data.regions;

    // Update SVG with counts
    $.each(regions, function(region, count) {
        $('#' + region + ' text').text(count);
    });

    // Tooltip handling
    $('.region').hover(function() {
        var regionName = $(this).attr('id');
        $('#map-tooltip').text(regionName).css({
            display: 'block',
            top: event.pageY + 10 + 'px',
            left: event.pageX + 10 + 'px'
        });
    }, function() {
        $('#map-tooltip').css('display', 'none');
    });

    $('.region').mousemove(function(event) {
        $('#map-tooltip').css({
            top: event.pageY + 10 + 'px',
            left: event.pageX + 10 + 'px'
        });
    });
});
