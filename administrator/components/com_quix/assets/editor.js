jQuery(function($) {

    $('#' + window.quixEditorID)
        .parent()
        .before(
            '<p id="quix-switch-mode" style="display:inline-block;"><button id="quix-switch-mode-button" type="button" class="btn btn-primary"><span class="quix-switch-mode-on" style="display:none;">← Back to Joomla! Editor</span><span class="quix-switch-mode-off"><i class="icon-eye" aria-hidden="true"></i>Edit with Quix</span></button></p>');
    $('#' + window.quixEditorID)
        .parent()
        .before(
            '<div id="quix-editor" style="display:none;"><a id="quix-go-to-edit-page-link" href="#"><div id="quix-editor-button" class="btn btn-primary btn-large btn-hero"><i class="icon-eye" aria-hidden="true"></i>Edit with Quix</div></a></div>');

    if (typeof window.builtWithQuixEditor == 'boolean' && window.builtWithQuixEditor == true) {
        $('body').addClass('quix-editor-active');
        $('.quix-switch-mode-on').show();
        $('.quix-switch-mode-off').hide();
        $('#quix-editor').show();
        $('#' + window.quixEditorID).parent().hide();
        $('#jform_attribs_article_layout').val('quix_canvas:quix');
    }

    $('#quix-switch-mode').on('click', function(e) {
        e.preventDefault();
        if ($('body').hasClass('quix-editor-active')) {
            if (confirm('You are switching from Powerful content builder. Are you sure?')) {
                $('body').removeClass('quix-editor-active');
                $('.quix-switch-mode-on').hide();
                $('.quix-switch-mode-off').show();
                $('#quix-editor').hide();
                $('#' + window.quixEditorID).parent().show();
                $('#jform_attribs_article_layout').val('');

                $.ajax({
                    type: 'post',
                    url: 'index.php?option=com_quix&task=get.disableEditor',
                    data: {'quixEditorMapID': window.quixEditorMapID},
                });
            }
        }
        else {
            $('body').addClass('quix-editor-active');
            $('.quix-switch-mode-on').show();
            $('.quix-switch-mode-off').hide();
            $('#quix-editor').show();
            $('#' + window.quixEditorID).parent().hide();
            $('#jform_attribs_article_layout').val('quix_canvas:quix');

            $.ajax({
                type: 'post',
                url: 'index.php?option=com_quix&task=get.enableEditor',
                data: {'quixEditorMapID': window.quixEditorMapID},
            });
        }
    });

    $('#quix-editor-button').on('click', function(e) {
        e.preventDefault();
        if (window.quixEditorItemID == 0) {
            alert('Please save your item first!');
            return;
        }

        window.open(window.quixEditorUrl, '_blank');

    });

    jSelectQuixShortcode = function(id) {
        let tag = `[quix id="${id}"]`;

        /** Use the API, if editor supports it **/
        if (Joomla && Joomla.editors && Joomla.editors.instances && Joomla.editors.instances.hasOwnProperty(window.quixEditorID)) {
            Joomla.editors.instances[window.quixEditorID].replaceSelection(tag);

            jQuery('#' + window.quixEditorID + '_editors-xtd_quix_modal').modal('hide');
        }
        else {
            jInsertEditorText(tag, window.quixEditorID);
        }

        if(typeof SqueezeBox !== 'undefined'){
            SqueezeBox.close();
        }

    };

});
