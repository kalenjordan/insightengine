InsightEngine_Data = typeof(InsightEngine_Data) != 'undefined' ? InsightEngine_Data : {};

InsightEngine_App = {
    baseUrl: 'NOT_INITIALIZED',

    documentReady: function() {
        this.bindToolTips();
        this.bindForms();
        this.bindAnimations();
    },

    domContentLoaded: function() {
        // Nothing yet
    },

    getBaseUrl: function() {
        return InsightEngine_Data.base_url;
    },

    bindToolTips: function() {
        if (typeof(jQuery.fn.tooltipster) == 'function') {
            $('.tooltip').tooltipster({
                maxWidth: 400,
                contentAsHTML: true
            });
        }
    },

    bindForms: function() {
        var self = this;

        $('.button-check-key').click(function() {
            self.validateMandrillKey();
        });

        $('.mandrill-tag-toggle').click(function() {
            self.toggleMandrillTag($(this).attr('id'));
        })
    },

    toggleMandrillTag: function(tagId) {
        console.log(tagId);
        $.ajax({
            url: this.getBaseUrl() + '/manage/toggle-tag/' + tagId,
            method: 'GET',
            success: function(data) {
                if (! data.success) {
                    alert("Uh-oh, there was a problem: " + data.error_message);
                }
            }
        });
    },

    refreshMandrillTag: function(tagId) {
        var self = this;
        var tagElement = $('#tag-' + tagId);
        tagElement.find('.signals .last-sent').hide();
        tagElement.find('.signals .no-data').hide();
        tagElement.find('.signals .loading').show();
        tagElement.find('.signals .not-enough-data').hide();

        $.ajax({
            url: this.getBaseUrl() + '/manage/tag/' + tagId + '/process',
            method: 'GET',
            success: function(data) {
                if (data.success) {
                    tagElement.find('.signals .no-data').hide();
                    tagElement.find('.signals .loading').fadeOut(function() {
                        if (data.is_active) {
                            tagElement.removeClass('tag-inactive');
                            tagElement.find('.mandrill-tag-toggle').attr("checked", "checked");
                        }
                        if (data.subject) {
                            tagElement.find('.tag-subject').text(data.subject);
                        }

                        tagElement.find('.signals .last-sent').addClass(data.last_sent_status);
                        tagElement.find('.signals .last-sent').tooltipster('content', data.summary);
                        tagElement.find('.signals .last-sent-amount').text(data.last_sent_friendly);
                        tagElement.find('.signals .last-sent').fadeIn();
                    });
                } else {
                    alert("Uh-oh, there was a problem: " + data.error_message);
                }
            }
        });
    },

    bindAnimations: function() {
        var self = this;
        $('.button-start').click(function() {
            self.animateStep('.signup-step-1', '.signup-step-2')
        });
    },

    animateStep: function(fromStep, toStep) {
        $(fromStep).animate({
            "margin-right": '+=50',
            opacity: 0
        }, 250, function() {
            $(this).hide();
            $(toStep).css('opacity', 0)
                .show()
                .css('margin-left', '50px')
                .animate({
                    "margin-left": '-=50',
                    opacity: 1
                }, 250);
        });
    },

    validateMandrillKey: function() {
        var self = this;
        $('.status-error').hide();
        $('.status-checking').fadeIn();
        $('.button-check-key').addClass('pure-button-disabled').text("Checking...");

        $.ajax({
            url: this.getBaseUrl() + '/manage/check-mandrill-key',
            method: 'GET',
            data: {
                'mandrill_api_key': $('.mandrill-api-key').val()
            },
            success: function(data) {
                if (data.success) {
                    self.validateMandrillKeySuccess(data);
                } else {
                    self.validateMandrillKeyError(data);
                }
            }
        });
    },

    validateMandrillKeySuccess: function(data)
    {
        var self = this;

        $('.status-checking').fadeOut(function() {
            $('.status-success .mandrill-username').text(data.username);
            $('.status-success').fadeIn();
            setTimeout(function() {
                self.animateStep('.signup-step-2', '.signup-step-3')
                self.fetchTags();
            }, 1000);
        });
    },

    fetchTags: function() {
        var self = this;

        $.ajax({
            url: this.getBaseUrl() + '/manage/fetch-tags',
            method: 'GET',
            success: function(data) {
                if (data.success) {
                    window.location = self.getBaseUrl() + '/manage/';
                } else {
                    alert("Problem loading tags");
                }
            }
        });
    },

    validateMandrillKeyError: function(data)
    {
        var self = this;

        $('.status-checking').fadeOut(function() {
            $('.status-error .error-message').text(data.error_message);
            $('.status-error').fadeIn();
            $('.button-check-key').removeClass('pure-button-disabled').text("Check");
        });
    }
};

$(document).ready(function() {
    InsightEngine_App.documentReady();
});

document.addEventListener('DOMContentLoaded', function() {
    InsightEngine_App.domContentLoaded();
});

/**
 * Menu handling for purecss menu
 */
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
