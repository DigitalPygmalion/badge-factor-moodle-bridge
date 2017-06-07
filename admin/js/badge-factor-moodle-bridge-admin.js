(function( $ ) {
	'use strict';

	/**
	 * Button gets available courses/bagdes to list in modal & insert prepropulated shortcode
	 */
	

	// Modal Dialog
	tinymce.PluginManager.add( 'badgefactor_shortcode', function( editor, url ) {

			var course_id = '';
			var course_title = '';
			var moodle_bridge_options = [];
			var courslist = [];
			
			$.ajax(
				{
					type: "get",
					dataType: "json",
					url: ajaxurl,
					data:  {
								'action': 'get_badge_list_wp2moodle',
							},
					success: function(data){
						for(var k in data) {
							courslist.push( { text: data[k].course_title, value: data[k].course_id}  )
						}		
					}
				})
				
			$.ajax(
				{
					type: "get",
					url: ajaxurl,
					dataType: "json",
					data:  {
								'action': 'get_wp2moodle_options',
							},
					success: function(result){
						moodle_bridge_options.push( { auth: result.badge_factor_moodle_bridge_auth_moodle_text, unauth: result.badge_factor_moodle_bridge_unauth_moodle_text}  )
					}
				})				
							
		// Add button that inserts shortcode into the current position of the editor
		editor.addButton( 'badgefactor_shortcode', {
			title: 'Bouton vers un cours',
			tooltip: 'Wordpress 2 Moodle',
			image : url+'/icon.svg',
			onclick: function() {
				// Open a TinyMCE modal
				editor.windowManager.open({
					title: 'Bouton vers un cours',
					body: [{
						type: 'listbox',
						name: 'cours',
						label: _('Course'),
						values: courslist
					},{
						type: 'textbox',
						name: 'auth_text',
						label: _('Authenticated label'),
						value: moodle_bridge_options[0].auth
					},{
						type: 'textbox',
						name: 'unauth_text',
						label: _('Unauthenticated label'),
						value: moodle_bridge_options[0].unauth
					}],
					onsubmit: function( e ) {
						editor.insertContent( '[wp2moodle class="badgefactor" course="' + e.data.cours + '" cohort="" group="" authtext="' + e.data.auth_text + '" target="_self"]' + e.data.unauth_text + '[/wp2moodle]' );
					}
				});
			}
		});
		
	})

})( jQuery );
