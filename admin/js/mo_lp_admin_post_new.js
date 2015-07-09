jQuery(document).ready(function($) {
    /*
     * We would normally recommend that all JavaScript is kept in a seperate .js
     * file, but we have included it in the document head for convenience.
     */

    // INSTANTIATE MIXITUP
    $('#Grid').mixitup();
    var changebutton = jQuery("#mo_template_change");

    jQuery(changebutton).appendTo("#mo_template_change");
    jQuery("#mo_template_change a").removeClass("button-primary").addClass("button");
    jQuery(".mo_template_select").click(function() {
	var mo_lp_template = jQuery(this).attr("id");

    });

    jQuery('.mo_template_select').click(function() {

	var template = jQuery(this).attr('id');
	var selected_template_id = "#" + template;
	var label = jQuery(this).attr('label');
	var template_image = "#" + template + " .mo_template_thumbnail";
	var template_img_obj = jQuery(template_image).attr("src");
	var post_type = jQuery('#post_type').val();
	jQuery("#mo_template_name").text(label);
	jQuery("#mo_current_template").html('<input type="hidden" name="mo_template" value="' + template + '">');
	jQuery("#mo_template_image #c_temp").attr("src", template_img_obj);
	jQuery("#submitdiv .hndle span").text("Create Landing Page");
	if (template != 'mo_sp_blank') {
	    var data = {
		action : 'mo_lp_get_template_content',
		type : post_type,
		template : template
	    };
	    jQuery.post(ajaxurl, data, function(response) {
		if (response.modal_height) {
		    jQuery('input[name="modal_height"]').val(response.modal_height);
		}
		if (response.modal_width) {
		    jQuery('input[name="modal_width"]').val(response.modal_width);
		}
		if (typeof tinymce.get("content") != 'undefined' && tinymce.get("content") != null) {
		    tinymce.get("content").focus();
		    tinymce.activeEditor.setContent(response.content);
		} else {
		    jQuery("#content").val(response.content);
		}

	    });

	} else {
	    if (typeof tinymce.get("content") != 'undefined' && tinymce.get("content") != null) {
		tinymce.get("content").focus();
		tinymce.activeEditor.setContent('');
	    } else {
		jQuery("#content").val("");
	    }
	    jQuery('input[name="modal_height"]').val('');

	    jQuery('input[name="modal_width"]').val('');

	}
	if (template != 'theme') {
	    jQuery('#mo_theme_template').hide();
	} else {
	    jQuery('#mo_theme_template').show();
	}

    });
    jQuery('.mo_template_select').click(function() {
	var template = jQuery(this).attr('id');
	var label = jQuery(this).attr('label');
	jQuery("#mo_template_select_container").fadeOut(500, function() {
	    jQuery(".wrap").fadeIn(500, function() {
	    });
	});

    });
    jQuery('#mo-change-template-button').click(function() {
	jQuery("#dialog-confirm").dialog({
	    dialogClass : 'mo-dialog',
	    resizable : false,
	    height : 220,
	    width : 350,
	    modal : true,
	    buttons : {
		"OK" : function() {
		    jQuery(this).dialog("close");
		    jQuery(".wrap").fadeOut(500, function() {
			jQuery("#mo_template_select_container").fadeIn(500, function() {
			});
		    });
		},
		Cancel : function() {
		    jQuery(this).dialog("close");
		}
	    }
	});

    });
    if (jQuery('input[name="mo_template"]').val() != 'theme') {
	jQuery('#mo_theme_template').hide();
    }
    if (jQuery('#post_type').val() == 'mo_sp') {
	var v_id = jQuery('#mo_sp_open_variation').val();
        var link_ch = (jQuery('#permalink_structure').val()=="")?'&':'?';
	var url = jQuery('#post-preview').attr('href') + link_ch+'mo_sp_variation_id=' + v_id + '&preview=true';
	jQuery('#post-preview').hide();
	jQuery('#preview-action').append('<a class="preview button" href="' + url + '" id="mo-sp-post-preview" target="wp-preview-769" style="display: block;">Save & Preview</a>');
	jQuery('#mo-sp-post-preview').click(function(event) {
	    var $this = $(this);
	    form = jQuery('form#post').clone(true, true);
	    form.attr('id', 'form#save-preview');
	    data = form.serialize();
	    target = $this.attr('target');
	    event.stopPropagation();
	    event.preventDefault();

	    form.unbind('submit').bind('submit', function() {
		request = jQuery.ajax({
		    type : "POST",
		    url : form.attr('action'),
		    data : data,

		}).done(function(data, status, xhr) {
                    width = jQuery('input[name="modal_width"]').val() ? jQuery('input[name="modal_width"]').val() : 250;
		    height = jQuery('input[name="modal_height"]').val() ? jQuery('input[name="modal_height"]').val() : 250;
		    if (!jQuery('#mo_sp_container').length) {
			jQuery('body').append('<div id="mo_sp_container" style="display:none;"><button type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only ui-dialog-titlebar-close" role="button" aria-disabled="false" title="close"><span class="ui-button-icon-primary ui-icon ui-icon-closethick"></span><span class="ui-button-text">close</span></button><iframe id="mo_sp_iframe" src="" style="border:none;height:100%;width:100%;"></iframe></div>');
		    }
		    jQuery("#mo_sp_iframe").prop("src", url);
		    mo_sp = jQuery("#mo_sp_container");
                    
                    mo_sp.dialog({
			modal : true,
			autoOpen : false,
			height : height,
			width : width,
			maxHeight : height,
			maxWidth : width,
			dialogClass : "mo_sp_modal",
			open : function() {
                            jQuery(this).parent().css('position', 'fixed');
                            jQuery(this).parent().removeClass("ui-corner-all");
                            jQuery(this).parent().css('border','0px');
                            jQuery(this).parent().css('top', '25%');
			    jQuery('.ui-widget-overlay').bind('click', function() {
				mo_sp.dialog('close');
			    });
			    jQuery('#mo_sp_container .ui-dialog-titlebar-close').bind('click', function() {
				mo_sp.dialog('close');
			    });
			}
		    });
		    mo_sp.dialog("open");
		});
		return false;
	    });
	    form.submit();
	});
    } else if (jQuery('#post_type').val() == 'mo_landing_page') {
	var v_id = jQuery('#mo_lp_open_variation').val();
        var link_ch = (jQuery('#permalink_structure').val()=="")?'&':'?';
        var url = jQuery('#post-preview').attr('href') +link_ch+'mo_lp_variation_id=' + v_id + '&preview=true';
	jQuery('#post-preview').hide();
	jQuery('#preview-action').append('<a class="preview button" href="' + url + '" id="mo-sp-post-preview" target="wp-preview-769" style="display: block;">Save & Preview</a>');
	jQuery('#mo-sp-post-preview').click(function(event) {
	    var $this = $(this);
	    form = jQuery('form#post').clone(true, true);
	    form.attr('id', 'form#save-preview');
	    data = form.serialize();
	    target = $this.attr('target');
	    event.stopPropagation();
	    event.preventDefault();

	    form.unbind('submit').bind('submit', function() {
		var preview_win = window.open('', 'mo_lp_preview');
		request = jQuery.ajax({
		    type : "POST",
		    url : form.attr('action'),
		    data : data,

		}).done(function(data, status, xhr) {
		    preview_win.location = url;
		});
		return false;
	    });
	    form.submit();
	});

    } else if (jQuery('#post_type').val() == 'page') {
	var v_id = jQuery('#mo_page_open_variation').val();
        var temp_url = jQuery('#post-preview').attr('href');
        var link_ch = (jQuery('#permalink_structure').val()=="")?'&':'?';
        if(jQuery('#permalink_structure').val()=="") {
            if(temp_url.indexOf("page_id=")== -1){
                link_ch = '?';
            }
        }
	var url = jQuery('#post-preview').attr('href') + link_ch+'mo_page_variation_id=' + v_id + '&preview=true';
	jQuery('#post-preview').hide();
	jQuery('#preview-action').append('<a class="preview button" href="' + url + '" id="mo-sp-post-preview" target="wp-preview-769" style="display: block;">Save & Preview</a>');
	jQuery('#mo-sp-post-preview').click(function(event) {
	    var $this = $(this);
	    form = jQuery('form#post').clone(true, true);
	    form.attr('id', 'form#save-preview');
	    data = form.serialize();
	    target = $this.attr('target');
	    event.stopPropagation();
	    event.preventDefault();
            form.unbind('submit').bind('submit', function() {
		var preview_win = window.open('', 'mo_page_preview');
		request = jQuery.ajax({
		    type : "POST",
		    url : form.attr('action'),
		    data : data,

		}).done(function(data, status, xhr) {
		    preview_win.location = url;
		});
		return false;
	    });
	    form.submit();
	});
    }
    jQuery(".custom_trigger").trigger("click");
    jQuery(".wp-editor-wrap").css('z-index',0);
    jQuery(".template_preview").fancybox();
});



    