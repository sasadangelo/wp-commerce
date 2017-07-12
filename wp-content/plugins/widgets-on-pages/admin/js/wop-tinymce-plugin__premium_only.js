(function() {

    tinymce.PluginManager.add('wop_tc_button', function( editor, url ) {

			function showDialog() {
				editor.windowManager.open( {
	        title: 'Insert Turbo Sidebar',
	        body: [
		        {
	            type: 'listbox',
	            name: 'sidebar',
	            label: 'Turbo Sidebar',
							'values': editor.settings.cptPostsList
		        },
		        {
		        	type: 'label',
		        	text: 'Number of widget columms per screen size',
		        	tooltip: 'Small < 768px, etc',
		        	style: 'font-weight: bold',
		        },
		        {
	            type: 'listbox',
	            name: 'small',
	            label: 'Small Screen',
	            values: [
	                {text: '1', value: '1'},
	                {text: '2', value: '2'},
	            ]
		        },
		        {
	            type: 'listbox',
	            name: 'medium',
	            label: 'Medium Screen',
	            values: [
	                {text: '1', value: '1'},
	                {text: '2', value: '2'},
	                {text: '3', value: '3'},
	            ]
		        },
		        {
	            type: 'listbox',
	            name: 'large',
	            label: 'Large Screen',
	            values: [
	                {text: '1', value: '1'},
	                {text: '2', value: '2'},
	                {text: '3', value: '3'},
	                {text: '4', value: '4'}
	            ]
		        },
		        {
	            type: 'listbox',
	            name: 'wide',
	            label: 'Wide Screen',
	            values: [
	                {text: '1', value: '1'},
	                {text: '2', value: '2'},
	                {text: '3', value: '3'},
	                {text: '4', value: '4'}
	            ]
		        },
	        ],
	        onsubmit: function( e ) {
	          editor.insertContent( '[widgets_on_pages id="' + e.data.sidebar + '" small="' + e.data.small + '" medium="' + e.data.medium + '" large="' + e.data.large + '" wide="' + e.data.wide + '"]');
	         // editor.insertContent( '[widgets_on_pages id="' + e.data.sidebar + '"]' );
	          //
	        }
	    	});
			} // end showDialog

      editor.addButton( 'wop_tc_button', {
          tooltip: 'Add Turbo Sidebar',
          icon: 'icon dashicons-welcome-widgets-menus',
          onclick: showDialog,
      });
    });
})();