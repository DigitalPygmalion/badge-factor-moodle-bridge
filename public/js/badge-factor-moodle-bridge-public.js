(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

/*	var ajaxurl = '/wp/wp-admin/admin-ajax.php';
//	var ajaxurl = admin_url.ajaxurl;
	
	tinymce.PluginManager.requireLangPack('badgefactor_shortcode');
	tinymce.PluginManager.add('badgefactor_shortcode', function( editor, url ) {
		var _ = tinymce.util.I18n.translate,
			plugin_url = url + ('/' === url.slice(-1) ? '' : '/')
		;

		//var ajaxurl = admin_url.ajaxurl;
		var ajaxurl = '/wp/wp-admin/admin-ajax.php';
		
		//set
		editor.settings.badgefactor_shortcode = {}
		editor.settings.badgefactor_shortcode.ajaxurl = ajaxurl;
		//peek
		console.log('editor.settings:' + JSON.stringify(editor.settings,null, 4));
		console.log('editor.settings.badgefactor_shortcode:' + JSON.stringify(editor.settings.badgefactor_shortcode,null, 4));
	
		function insert_or_replace( content ) {
			editor.focus( );
			if ( editor.selection )
				editor.selection.setContent( content );
			else
				editor.insertContent( content );
		};
	
		function buildListItems(inputList, itemCallback, startItems) {
			function appendItems(values, output) {
				output = output || [];
	
				tinymce.each(values, function(item) {
					var menuItem = {text: item.text || item.title, value: ""};
					itemCallback(menuItem, item);
					output.push(menuItem);
				});
	
				return output;
			}
	
			return appendItems(inputList, startItems || []);
		}
	
		function ajax_call( ajaxurl, cb ) {
			return function( ) {
				editor.setProgressState( 1 ); // Show progress
				tinymce.util.XHR.send({
					url: ajaxurl,
					success: function( res ) {
						editor.setProgressState( 0 ); // Hide progress
						cb( !!res ? tinymce.util.JSON.parse( res ) : res );
					}
				});
			};
		};
	
		function popup( data ) {
			// Open editor window
			var listBox, win = editor.windowManager.open({
				title: _('Insert'),
				resizable : true,
				maximizable : true,
				width: 400,
				height: 300,
				body: [
				{
					type: 'listbox',
					name: 'my_control',
					label: _('Insert'),
					tooltip: _('Select item'),
					values: buildListItems(data, function( item, datum ) {
						item.value = datum;
						item.text = datum.title;
					}),
					onPostRender: function( ){
						listBox = this;
					}
				}
				],
				buttons: [
					{ text: _('Insert'), subtype: 'primary', onclick: function( ){
						var selected = listBox.value( );
						if ( !!selected )
							insert_or_replace( '[my-shortcode id="'+selected.id+'" title="'+selected.title+'"]' )
						win.close( );
					}},
					{ text: _('Cancel'), onclick: 'close' }
				]
			});
		};
	
		editor.addButton('my_button', {
			title: _('Sample Button'),
			icon: 'my-icon',
			//onclick: ajax_call( editor.settings.badgefactor_shortcode.ajaxurl, popup ),
		});
	
		editor.addMenuItem('my_menu', {
			icon: 'my-icon',
			text: _('Sample Menu'),
			context: 'insert',
			//onclick: ajax_call(editor.settings.badgefactor_shortcode.ajaxurl, popup)
		});
	});
*/	

	
	// Modal Dialog
	tinymce.PluginManager.add( 'badgefactor_shortcode', function( editor, url ) {

		// Add button that inserts shortcode into the current position of the editor
		editor.addButton( 'badgefactor_shortcode', {
			title: 'Bouton vers un cours',
			tooltip: 'wp2Moodle',
			icon: false,
			onPostRender: function() {
				var ctrl = this;
				
				var course_id = '';
				var course_title = '';

					$.ajax(
					{
						type: "get",
						dataType: "json",
						url: ajaxurl,
						data:  {
									'action': 'get_badge_list_wp2moodle',
								},
						success: function(data){
							
							console.dir('response: ' + data);
							console.log('course_title:' + data[2].course_title + ', course_id:' + data[2].course_id);
						}
					});
			},
			onclick: function() {
				// Open a TinyMCE modal
				editor.windowManager.open({
					title: 'Bouton vers un cours',
					body: [{
						type: 'textbox',
						name: 'label',
						label: 'Label'
					},{
						type: 'textbox',
						name: 'link',
						label: 'Link URL'
					},{
						type: 'listbox',
						name: 'cours',
						label: 'Cours',
						values: [
							{ text: 'Test1', value: 'test1' },
							{ text: 'Test2', value: 'test2' },
							{ text: 'Test3', value: 'test3' }
						],
						value: 'test2' //default
					}],
					onsubmit: function( e ) {
						editor.insertContent( '[wp2moodle link="' + e.data.link + '" label="' + e.data.label + '" course="' + e.data.cours + '"]' );
					}
				});
			}
		});
	})

})( jQuery );
