(function() {

    tinymce.PluginManager.add('wop_tc_button', function( editor, url ) {

			function showDialog() {
				var lblColsHeader = {
        	type: 'label',
        	text: 'Number of widget columms per screen size',
        	tooltip: 'Small < 768px, etc',
        	style: 'font-weight: bold',
        };
				if ( editor.settings.notPaying ) {
					lblColsHeader = {
						type: 'panel',
	        	layout: 'Stack',
	        	margin: '2 0 2 0',
            style: 'border-left: 5px solid #BD441C; padding: 0.5em 0 0.3em 0.5em',
	        	items: [
	        		{
			        	type: 'label',
			        	text: 'Number of widget columms per screen size',
			        	tooltip: 'Small < 768px, etc',
			        	style: 'font-weight: bold; padding-bottom: 1em;',
			        },
			        {type: 'label',text: 'Column settings are a PRO feature.',
			        	style: 'padding-bottom: 0.5em;',},
			        {type: 'label',text: 'You can upgrade from the Widgets on Pages settings.'},
	        	]
	        };
				}
				editor.windowManager.open( {
	        title: 'Insert Turbo Sidebar',
	        body: [
		        {
	            type: 'listbox',
	            name: 'sidebar',
	            label: 'Turbo Sidebar',
							'values': editor.settings.cptPostsList
		        },
		        lblColsHeader,
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

						if ( editor.settings.notPaying ) {
	          	editor.insertContent( '[widgets_on_pages id="' + e.data.sidebar + '"]');
	         	} else {
	         		editor.insertContent( '[widgets_on_pages id="' + e.data.sidebar + '" small="' + e.data.small + '" medium="' + e.data.medium + '" large="' + e.data.large + '" wide="' + e.data.wide + '"]');
	         	}
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