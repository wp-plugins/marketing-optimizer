jQuery(document)
		.ready(
				function($) {
					/*
					 * We would normally recommend that all JavaScript is kept
					 * in a seperate .js file, but we have included it in the
					 * document head for convenience.
					 */

					// INSTANTIATE MIXITUP
					$('#Grid').mixitup();

//					var tempdiv = jQuery("<div id='mo_lp_templates' class='postbox'><h3 class='hndle'>Selected Template: <span id='mo_lp_template_name'></span></h3><div id='mo_lp_template_image_container'><span id='mo_lp_template_image'><img height='200' width='200' src='' id='c_temp'></span></div><div id='mo_lp_current_template'></div></div>");
//
//					jQuery(tempdiv).appendTo("#titlewrap");
					var changebutton = jQuery("#mo_lp_template_change");

					jQuery(changebutton).appendTo("#mo_lp_template_change");
					jQuery("#mo_lp_template_change a").removeClass(
							"button-primary").addClass("button");
					jQuery(".mo_lp_template_select").click(function() {
						var mo_lp_template = jQuery(this).attr("id");

					});

					jQuery('.mo_lp_template_select').click(
							function() {
									
								var template = jQuery(this).attr('id');
								var selected_template_id = "#" + template;
								var label = jQuery(this).attr('label');
								var template_image = "#" + template
										+ " .mo_lp_template_thumbnail";
								var template_img_obj = jQuery(template_image)
										.attr("src");

								jQuery("#mo_lp_template_name").text(label);
								jQuery("#mo_lp_current_template").html(
										'<input type="hidden" name="mo_lp_template" value="'
												+ template + '">');
								jQuery("#mo_lp_template_image #c_temp").attr(
										"src", template_img_obj);
								jQuery("#submitdiv .hndle span").text(
										"Create Landing Page");
								
								var data = {action:'mo_lp_get_template_content',template:template};
								jQuery.post(ajaxurl,data,function(response){
									jQuery('#wp-content-editor-container .wp-editor-area').val(response);
								});
								if(template != 'theme'){
									jQuery('#mo_lp_theme_template').hide();
								}else{
									jQuery('#mo_lp_theme_template').show();
								}

							});
					jQuery('.mo_lp_template_select').click(
							function() {
								var template = jQuery(this).attr('id');
								var label = jQuery(this).attr('label');
								jQuery("#mo_lp_template_select_container")
										.fadeOut(
												500,
												function() {
													jQuery(".wrap").fadeIn(500,
															function() {
															});
												});
								
							});
					jQuery('#mo_lp-change-template-button').click(
							function() {
								$( "#dialog-confirm" ).dialog({
									dialogClass   : 'mo-dialog',
								      resizable: false,
								      height:220,
								      width:350,
								      modal: true,
								      buttons: {
								        "OK": function() {
								          $( this ).dialog( "close" );
								          jQuery(".wrap")
											.fadeOut(
													500,
													function() {
														jQuery("#mo_lp_template_select_container").fadeIn(500,
																function() {
														});
													});
								        },
								        Cancel: function() {
								          $( this ).dialog( "close" );
								        }
								      }
								    });	
								
							});
					if(jQuery('input[name="mo_lp_template"]').val() != 'theme'){
						jQuery('#mo_lp_theme_template').hide();
					}

				});