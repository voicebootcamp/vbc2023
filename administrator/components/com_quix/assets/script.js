(function($) {

    $(document).ready(function() {
        // $('form#adminForm,form#message-form').fadeIn(50);

        /* Toggle toolbar button*/
        const btnSelector = $('#toolbar-publish,#toolbar-checkin,#toolbar-unpublish,#toolbar-trash, #toolbar-remove');
        $('#adminForm input[type=\'checkbox\']').change(function() {

            if (!$('input[type=\'checkbox\']').is(':checked')) {
                btnSelector.addClass('qx-hidden');
            }
            else {
                btnSelector.removeClass('qx-hidden');
            }

            $('input[name="checkall-toggle"]').change(function() {
                if (this.checked) {
                    btnSelector.removeClass('qx-hidden');
                }
                else {
                    btnSelector.addClass('qx-hidden');
                }
            });
        });

        function saveAjaxIntegration(item) {
            $('input[name=task]').val('integrations.update');

            let value = $('#adminForm').serializeArray();
            $.ajax({
                type: 'POST',
                data: value,
                beforeSend: function() {
                    item.parent().parent().parent().addClass('disabled');
                    item.attr('disabled', true);
                },
                success: function(res) {
                    let response = JSON.parse(res);
                    if (!response.success) {
                        console.log(response.data);
                    }

                    item.parent().parent().parent().removeClass('disabled');
                    item.attr('disabled', false);
                    qxUIkit.notification({message: 'Settings saved.', status: 'success', pos: 'top-right'});
                },
            });
        }

        $('.toggleIntegration').change(function() {
            let item = $(this);
            saveAjaxIntegration(item);
        });

        $('#customIntegrationSave').on('click', function(e) {
            e.preventDefault();
            var item = $(this);
            saveAjaxIntegration(item);
        });

        // new template modal
        $('#new-template form').on('submit', function(e) {
            e.preventDefault();
            var Success = false;
            $.ajax({
                url: 'index.php?option=com_quix&view=collections',
                type: 'post',
                dataType: 'json',
                data: $(this).serialize(),
                complete: function() {
                    if(Success){
                        qxUIkit.notification({message: 'Template created. Redirecting...', status: 'success', pos: 'top-right'});
                    }
                },
                success: function(result) {
                    if (result.success) {
                        var data = result.data;
                        window.parent.location = data.url;
                        Success = true;
                    }
                    else {
                        qxUIkit.notification({message: result.message, status: 'danger', pos: 'bottom-right'});
                        Success = false;
                    }
                },
                error: function(result) {
                    // $('#collection-window-modal form .error').fadeIn();
                    qxUIkit.notification({message: 'Something went wrong! Please try again.', status: 'warning', pos: 'top-right'});

                },
            });
        });

        setTimeout(function() {
            // replace trash icon
            $('.icon-trash').replaceWith('<i class="icon-trash"></i>');

            if (window.QuixVersion == 'free') {
                var sideBanner = '<div class="filter-select hidden-phone" style="margin-top: 20px;">' +
                    '<a href="https://www.themexpert.com/quix-pagebuilder?utm_campaign=quix-pro&utm_source=joomla-admin&utm_medium=sidebar-banner" target="_blank">' +
                    '<img src="https://www.themexpert.com/images/quix-banner/banner.png">' +
                    '</a></div>';
                $('#sidebar .sidebar-nav').append(sideBanner);
            }
        }, 5000);

        setTimeout(function() {
            // replace trash icon
            $('.subhead .icon-trash').replaceWith('<i class="icon-trash"></i>');
        }, 100);

        let validation = $('[data-validation-submit]');
        let licenses = $('[data-message]');

        // Change the behavior of form submission
        validation.on('click', function(e) {
            e.preventDefault();
            let f = document.adminForm;
            if (document.formvalidator.isValid(f)) {
                validation.addClass('disabled');
                licenses.html('<p class="qx-alert">Activating your license...</p>');
                licenses.removeClass('hide');

                let username = $('#jform_username').val();
                let key = $('#jform_key').val();

                // let url = "https://www.themexpert.com/index.php?option=com_digicom&task=responses&source=authapi&catid=38&username=" + username + "&key=" + key;
                let url = 'index.php?option=com_quix&task=config.validateLicense&catid=38&username=' + username + '&key=' + key;

                fetch(url).then(function(response) {
                    return response.json();
                }).then(function(myJson) {
                    let jsonData = JSON.stringify(myJson);
                    submitValidationJSON(jsonData);
                });

            }

            return;
        });

        function submitValidationJSON(jsonData) {
            // Validate api key
            $.ajax({
                type: 'POST',
                url: 'index.php?option=com_quix&task=verify',
                // data: {'username': $('#jform_username').val(), 'key': $('#jform_key').val()}
                data: {'data': jsonData},
            }).done(function(result) {
                    // console.log(result);
                    var data = JSON.parse(result);
                    // User is not allowed to install
                    if (!data.success) {
                        // Set the error message
                        licenses.html('<p class="qx-alert qx-alert-danger">' + data.message + '</p>');
                        $('#jform_activated').val(0);
                        Joomla.submitform('config.save');
                    }
                    else {
                        licenses.html('<p class="qx-alert qx-alert-success">' + data.message + '</p><p class="alert alert-info">Updating your configuration. Please wait...</p>');
                        $('#jform_activated').val(1);
                        Joomla.submitform('config.save');

                        setTimeout(function() {
                            Joomla.submitform('config.save');
                            window.top.setTimeout('window.parent.jModalClose();location.reload();', 700);
                        }, 2000);
                    }
                })
                .fail(function(jqXHR, textStatus) {
                    // Set the error message
                    licenses.html('<p class="qx-alert qx-alert-success">Request failed: ' + textStatus + '</p>');
                })
                .always(function() {
                    validation.removeClass('disabled');
                });
        }

        $('[data-clear-cache]').on('click', function(e) {
            $.get('index.php?option=com_quix&task=cache.cleanBuilders&format=json', function(data) {
                let response = JSON.parse(data);
                if (response.success === true) {
                    qxUIkit.notification({message: response.message, status: 'success', pos: 'bottom-right'});
                }
                else {
                    qxUIkit.notification({message: response.message, status: 'danger', pos: 'bottom-right'});
                }
            }).fail(function() {
                qxUIkit.notification({message: 'Something went wrong!', status: 'danger', pos: 'bottom-right'});
            });
        });

        $('[data-clear-legacy]').on('click', function(e) {
            $.get('index.php?option=com_quix&task=clear_cache&step=0', function(data) {
                if (typeof (data) === 'object') {
                    qxUIkit.notification({message: 'Cache cleaned successfully.', status: 'success', pos: 'bottom-right'});
                }
                else {
                    qxUIkit.notification({message: 'Something went wrong! Please reload the page and try again.', status: 'danger', pos: 'bottom-right'});
                }
            });
        });

        $('[data-quix-ajax]').on('click', function(e) {
            e.preventDefault();
            let link = e.target.href;
            $('#admin-spinner').removeClass('qx-hidden');
            qxUIkit.spinner('#admin-spinner span', {ratio: 5});

            setTimeout(() => {
                $.get(link, function(data) {
                    let response = JSON.parse(data);
                    if (response.success === true) {
                        qxUIkit.notification({message: response.message, status: 'success', pos: 'bottom-right'});
                    }
                    else {
                        qxUIkit.notification({message: response.message, status: 'danger', pos: 'bottom-right'});
                    }
                    $('#admin-spinner').addClass('qx-hidden');
                }).fail(function() {
                    qxUIkit.notification({message: 'Something went wrong!', status: 'danger', pos: 'bottom-right'});
                    $('#admin-spinner').addClass('qx-hidden');
                });
            }, 1000);
        });

        $('#js-new-page-prompt').on('click', function(e) {
            e.preventDefault();
            e.target.blur();
            qxUIkit.modal.prompt('Page Name:', '').then(function(title) {
                if (!title.length) {
                    alert('Page name is required!');
                    return;
                }

                let token = Joomla.getOptions('csrf.token');
                let data = {title: title};
                data[token] = 1;

                $.ajax({
                    url: 'index.php?option=com_quix&task=page.pageCreateAjax',
                    type: 'POST',
                    data: data,

                    success: function(res) {
                        let response = JSON.parse(res);
                        if (!response.success) {
                            alert(response.message);
                        }

                        window.open(response.data);
                    },
                });
            });
        });

        $('.qx-alert-close').on('click', e => {
            let notification = e.currentTarget?.dataset?.session;
            if (notification !== undefined) {
                window.setQuixSession({'key': notification, 'value': 'collapse'});
            }
        });
    });

})(jQuery);

/* quix welcome blocks dependency */
window.setQuixSession = function(data) {
    jQuery.ajax({
        url: 'index.php?option=com_quix&task=setSession&format=json&' + Joomla.getOptions('csrf.token') + '=1',
        type: 'POST',
        data: data,

        success: function(response) {
            if (!response.success) {
                console.warn('Something went wrong.', response);
            }
        },
    });
};
/* quix welcome blocks dependency */
window.setComponentParams = function(data) {
    jQuery.ajax({
        url: 'index.php?option=com_quix&task=setComponentParams&format=json&' + Joomla.getOptions('csrf.token') + '=1',
        type: 'POST',
        data: data,

        success: function(response) {
            if (!response.success) {
                console.warn('Something went wrong.', response);
            }
        },
    });
};

window.toggleWelcome = function(collapse = false) {
    if (collapse === false) {
        jQuery('#qx-welcome-v3').removeClass('qx-padding-small');
        jQuery('#welcome-collapse').toggle();
        jQuery('#welcome-content').fadeToggle();
        window.setQuixSession({'key': 'welcome-toolbar', 'value': 'open'});
    }
    else {
        jQuery('#qx-welcome-v3').addClass('qx-padding-small');
        jQuery('#welcome-content').toggle();
        jQuery('#welcome-collapse').fadeToggle();
        window.setQuixSession({'key': 'welcome-toolbar', 'value': 'collapse'});
    }
};

window.toggleWelcome = function(collapse = false) {
    if (collapse === false) {
        jQuery('#qx-welcome-v3').removeClass('qx-padding-small');
        jQuery('#welcome-collapse').toggle();
        jQuery('#welcome-content').fadeToggle();
        window.setQuixSession({'key': 'welcome-toolbar', 'value': 'open'});
    }
    else {
        jQuery('#qx-welcome-v3').addClass('qx-padding-small');
        jQuery('#welcome-content').toggle();
        jQuery('#welcome-collapse').fadeToggle();
        window.setQuixSession({'key': 'welcome-toolbar', 'value': 'collapse'});
    }
};
