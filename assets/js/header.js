$('#avatar').click(function(event) {
    event.stopPropagation(); // Prevent event bubbling
    $('#dropdown').toggleClass('hidden');
});

$(document).click(function(event) {
    if (!$(event.target).closest('#avatar, #dropdown').length) {
        $('#dropdown').addClass('hidden');
    }
});
