// javascript Animate onscroll Start
$(document).ready(function () {
    // AOS animations removed
});
/* ============================================================
   Sidenav
 * ============================================================*/
function openNav() {
    document.getElementById("mySidenav").style.width = "250px";
}

function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
}

$(document).on('click', function (e) {
    if ($(e.target).closest("#mySidenav, .slide-menu").length === 0) {
        document.getElementById("mySidenav").style.width = "0";
    }
});
/* ============================================================
   Onscroll - Enhanced for all devices (Desktop + Mobile)
 * ============================================================*/

// Initialize variables
var navbar = document.getElementById("navbar");
var header = document.querySelector("header");
var stickyOffset = 100; // Fixed offset for better desktop compatibility

// Main scroll function
function handleStickyNav() {
    var scrollTop = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
    
    // Use a fixed offset instead of navbar.offsetTop for better desktop compatibility
    if (scrollTop > stickyOffset) {
        if (navbar) navbar.classList.add("sticky");
        if (header) header.classList.add("sticky");
    } else {
        if (navbar) navbar.classList.remove("sticky");
        if (header) header.classList.remove("sticky");
    }
}

// Primary scroll listener
window.addEventListener('scroll', handleStickyNav);

// jQuery backup for better compatibility
$(document).ready(function() {
    $(window).scroll(function() {
        var scrollTop = $(this).scrollTop();
        
        if (scrollTop > stickyOffset) {
            $("#navbar").addClass("sticky");
            $("header").addClass("sticky");
        } else {
            $("#navbar").removeClass("sticky");
            $("header").removeClass("sticky");
        }
    });
});

// Legacy support
window.onscroll = handleStickyNav;

$(function () {
    $(".scroll").click(function () {
        $("html,body").animate({
            scrollTop: $(".top").offset().top
        }, "1000");
        return false
    })
})
/* ============================================================
   Scroll to Top
 * ============================================================*/
$(window).scroll(function () {
    if ($(this).scrollTop() >= 50) { // If page is scrolled more than 50px
        $('#return-to-top').fadeIn(200); // Fade in the arrow
    } else {
        $('#return-to-top').fadeOut(200); // Else fade out the arrow
    }
});
$('#return-to-top').click(function () {
    // When arrow is clicked

    $('body, html').animate({
        scrollTop: 0 // Scroll to top of body
    }, 500);
});
