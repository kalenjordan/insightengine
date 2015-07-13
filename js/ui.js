(function (window, document) {

    var layout   = document.getElementById('layout'),
        menu     = document.getElementById('menu'),
        menuLink = document.getElementById('menuLink');

    function toggleClass(element, className) {
        var classes = element.className.split(/\s+/),
            length = classes.length,
            i = 0;

        for(; i < length; i++) {
          if (classes[i] === className) {
            classes.splice(i, 1);
            break;
          }
        }
        // The className is not found
        if (length === classes.length) {
            classes.push(className);
        }

        element.className = classes.join(' ');
    }

    menuLink.onclick = function (e) {
        var active = 'active';

        e.preventDefault();
        toggleClass(layout, active);
        toggleClass(menu, active);
        toggleClass(menuLink, active);
    };

}(this, this.document));

$(document).ready(function() {
    $('.button-start').click(function() {
        $('.signup-step-1').animate({
            "margin-right": '+=50',
            opacity: 0
        }, 250, function() {
            $(this).hide();
            $('.signup-step-2').css('opacity', 0)
                .show()
                .css('margin-left', '50px')
                .animate({
                    "margin-left": '-=50',
                    opacity: 1
                }, 250);
        });
    });

    $('.button-next').click(function() {
        $('.signup-step-2').animate({
            "margin-right": '+=50',
            opacity: 0
        }, 250, function() {
            $(this).hide();
            $('.signup-step-3').css('opacity', 0)
                .show()
                .css('margin-left', '50px')
                .animate({
                    "margin-left": '-=50',
                    opacity: 1
                }, 250);
            setTimeout(function() {
                window.location = '/manage/';
            }, 2000);

        });
    });


    $('.mandrill-api-key').keypress(function() {
        $('.mandrill-validation-status').fadeIn();
        setTimeout(function() {
            $('.mandrill-validation-status .checking').fadeOut(function() {
                $('.mandrill-validation-status .success').fadeIn();
            });
        }, 1000);
    }).bind('paste', function() {
        $('.mandrill-validation-status').fadeIn();
    });
});
