// Toggle the desktop dropdown when avatar is clicked
$('#avatar').click(function(event) {
    event.stopPropagation(); // Prevent event bubbling
    $('#dropdown').toggleClass('hidden');
});

// Close dropdown when clicking outside of the dropdown or avatar
$(document).click(function(event) {
    if (!$(event.target).closest('#avatar, #dropdown').length) {
        $('#dropdown').addClass('hidden');
    }
});

// Mobile menu toggle
$('#mobile-menu-button').click(function() {
    $('#mobile-menu').slideToggle(200);
});

// Close mobile menu when window resizes above mobile breakpoint
$(window).resize(function() {
    if ($(window).width() >= 768) {
        $('#mobile-menu').hide();
    }
});