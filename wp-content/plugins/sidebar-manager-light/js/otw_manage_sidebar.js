var otw_sbm_items = {};

jQuery(document).ready(function() {
	init_manage_page();
});
function init_manage_page(){
	
	var s_labels = jQuery( '.sitems label' ).click(  function( event ){
		event.preventDefault();
		otw_select_sitem( this );
	} );
	
	jQuery( '.sitems .otw_sbm_validfor' ).each( function(){
		
		var valid_handler = jQuery( this );
		var matches = false;
		
		if( matches = valid_handler.attr( 'id' ).match( /^otw_sbm_type_(.*)_validfor$/ ) )
		{
			var values = valid_handler.val().split( ',' );
			
			if( values.length )
			{
				otw_sbm_items[ matches[1] ] = {};
				for( var cV = 0; cV < values.length; cV++ )
				{
					if( values[ cV ].length )
					{
						otw_sbm_items[ matches[1] ][ values[ cV ] ] = true;
					};
				};
				otw_display_selected_items( matches[1] );
			};
		};
	} );
	
	jQuery('#col-right').find('.sitem_toggle, .sitem_header').click(function() {
		jQuery(this).parent().find( '.inside').toggleClass('otw_closed');
	});
	
	var replace_select = jQuery( '#sbm_replace' );
	
	replace_select.change( function(){
	
		if( this.value.length ){
			jQuery( '#sbm_widget_alignment_cnt' ).css( 'display', 'none' );
		}else{
			jQuery( '#sbm_widget_alignment_cnt' ).css( 'display', 'block' );
		}
	} );
	
	if( replace_select.val().length ){
		jQuery( '#sbm_widget_alignment_cnt' ).css( 'display', 'none' );
	}else{
		jQuery( '#sbm_widget_alignment_cnt' ).css( 'display', 'block' );
	};
	
	jQuery('input.otw_sbm_q_filter').focus( function(){
		this.value='';
	});
	jQuery('input.otw_sbm_q_filter').keyup( function( event ){
		var search_box = jQuery( this );
		try{
			clearTimeout( window.otw_q_filter_timeout );
		}catch(e){}
		
		if (event.which == 13) {
			event.stopPropagation();
			event.preventDefault();
		}
		window.otw_q_filter_timeout = setTimeout( function(){otw_sbm_man_filter_wp_items( search_box )}, 300 );
	});
	jQuery('input.otw_sbm_q_filter').keydown( function( event ){
		
		var search_box = jQuery( this );
		try{
			clearTimeout( window.otw_q_filter_timeout );
		}catch(e){}
		if (event.which == 13) {
			event.stopPropagation();
			event.preventDefault();
		}
		window.otw_q_filter_timeout = setTimeout( function(){otw_sbm_man_filter_wp_items( search_box )}, 300 );
		
	});
	jQuery('div.otw_sidebar_filter_order select').change( function( event ){
		
		otw_sbm_man_filter_wp_items( jQuery( this ) );
	});
	jQuery('div.otw_sidebar_filter_show select').change( function( event ){
		
		otw_sbm_man_filter_wp_items( jQuery( this ) );
	});
	jQuery('div.otw_sidebar_filter_clear a').click( function( event ){
		
		var a_matches = false;
		if( a_matches = this.id.match( /^otw_type_(.*)_clear$/ ) )
		{
			jQuery( '#otw_type_' + a_matches[1] + '_search_field' ).val( '' );
			jQuery( '#otw_type_' + a_matches[1] + '_show_field' ).val( 'all' );
			jQuery( '#otw_type_' + a_matches[1] + '_order_field' ).val( 'a_z' );
			jQuery( '#otw_type_' + a_matches[1] + '_page_field' ).val( '0' );
			
			jQuery( '#otw_type_' + a_matches[1] + '_per_page_field' ).attr( 'id', 'otw_type_' + a_matches[1] + '_per_page_field_old' );
			
			otw_sbm_man_filter_wp_items( jQuery( this ) );
		};
	});
	jQuery( 'a.otw_sbm_select_all_items' ).click( function(){
		
		var link = jQuery( this );
		var item_type = link.attr( 'rel' );
		
		if( item_type ){
		
			jQuery( '#otw_sbm_type_' + item_type +' .f_items p' ).addClass( 'sitem_loading' );
			
			var req_url = 'admin-ajax.php?action=otw_sml_items_by_type';
			
			var post_params = { string_filter: '', type: item_type, format: 'ids' };
			
			var settings = {
				url: req_url,
				type: 'post',
				data: post_params,
				success:function( data ){
					
					otw_sbm_items[ item_type ][ 'all' ] = true;
					
					var t_data = data.trim().split( ',' );
					
					for( var tCD = 0; tCD < t_data.length; tCD++ ){
						if( t_data[ tCD ].length )
						{
							otw_sbm_items[ item_type ][ t_data[ tCD ] ] = true;
						};
					};
					
					jQuery( '#otw_sbm_type_' + item_type +' .f_items p' ).removeClass( 'sitem_loading' );
					
					otw_display_selected_items( item_type, true );
				}
			};
			jQuery.ajax( settings );
		};
	});
	jQuery( 'a.otw_sbm_unselect_all_items' ).click( function(){
		
		var link = jQuery( this );
		var item_type = link.attr( 'rel' );
		
		if( item_type ){
			
			otw_sbm_items[ item_type ][ 'all' ] = false;
			otw_display_selected_items( item_type, true );
		};
	});
	
	otw_sbm_init_items();
};
function otw_sbm_man_filter_wp_items( search_box ){

	var bar = search_box.parent().parent().parent();
	
	var matches = false;
	
	if( matches = bar.attr( 'id' ).match( /^otw_sbm_type_(.*)$/ ) ){
		otw_sbm_load_items( matches[1] , bar );
	};
};
function otw_sbm_init_items(){

	var bars = jQuery( '#col-right' ).find( 'div.sitems' );
	
	if( bars.size() ){
		for( var cB = 0; cB < bars.size(); cB++ ){
			
			var matches = false;
			
			if( matches = jQuery( bars[cB] ).attr( 'id' ).match( /^otw_sbm_type_(.*)$/ ) ){
				otw_sbm_load_items( matches[1] , bars[cB] );
			};
		};
	};
};
function otw_sbm_load_items( item_type, item_node ){

	var post_params = { string_filter: '', type: item_type, show: 'all', order: 'a_z', page: 0 };
	
	var sidebar_id = jQuery( '#col-right' ).attr( 'class' );
	
	var matches = false;
	if( matches = sidebar_id.match( /^otw_sbm_([a-z0-9\-]+)$/ ) )
	{
		post_params['sidebar'] = matches[1];
	}
	post_params.string_filter = jQuery( '#otw_type_' + item_type + '_search_field' ).val();
	post_params.show = jQuery( '#otw_type_' + item_type + '_show_field' ).val();
	post_params.order = jQuery( '#otw_type_' + item_type + '_order_field' ).val();
	post_params.page = jQuery( '#otw_type_' + item_type + '_page_field' ).val();
	
	if( jQuery( '#otw_type_' + item_type + '_per_page_field' ).size() ){
		post_params.per_page = jQuery( '#otw_type_' + item_type + '_per_page_field' ).val();
	}
	
	jQuery( '#otw_type_' + item_type + '_filter' ).addClass( 'sitem_loading' );
	
	var req_url = 'admin-ajax.php?action=otw_sml_items_by_type';
	
	var settings = {
		url: req_url,
		type: 'post',
		data: post_params,
		success:function( data ){
			
			var t_data = data.trim();
			jQuery( item_node ).find( 'div.a_item' ).html( t_data );
			jQuery( item_node ).find( 'div.a_item label' ).click(  function( event ){
				event.preventDefault();
				otw_select_sitem( this );
			} );
			otw_init_item_pager( item_type );
			otw_display_selected_items( item_type );
			jQuery( '#otw_type_' + item_type + '_filter' ).removeClass( 'sitem_loading' );
		}
	};
	
	jQuery.ajax( settings );
};
function otw_init_item_pager( item_type ){
	jQuery('#otw_type_' + item_type + '_per_page_field').change( function( event ){
		otw_sbm_load_items( item_type , jQuery( '#otw_sbm_type_' + item_type ) );
	});
	jQuery('#otw_sbm_type_' + item_type + ' .otw_sidebar_pager_links a ').click( function(){
		
		if( jQuery( this ).attr( 'rel' ).match( /^\d+$/ ) ){
			jQuery( '#otw_type_' + item_type + '_page_field' ).val( jQuery( this ).attr( 'rel' ) );
			otw_sbm_load_items( item_type , jQuery( '#otw_sbm_type_' + item_type ) );
		};
		
	} );
}
function otw_select_sitem( param, force, force_value ){

	var label = jQuery( param );
	var block = label.parent();
	var input = block.find( 'input' );
	
	var matches = false;
	
	if( matches = input.attr( 'id' ).match( /^otw_sbi_(.*)_sbi_([a-z0-9\.\-\_]+)$/ ) ){
		
		if( otw_sbm_items[ matches[1] ][ matches[2] ] != undefined && otw_sbm_items[ matches[1] ][ matches[2] ] ){
			otw_sbm_items[ matches[1] ][ matches[2] ] = false;
		}else{
			otw_sbm_items[ matches[1] ][ matches[2] ] = true;
		};
		
		if( matches[2] == 'all' ){
			if( otw_sbm_items[ matches[1] ][ matches[2] ] )
			{
				jQuery( jQuery( '#otw_sbm_type_' + matches[1] ).find( 'p' )[0] ).addClass( 'sitem_loading' );
				
				var req_url = 'admin-ajax.php?action=otw_sml_items_by_type';
				
				var post_params = { string_filter: '', type: matches[1], format: 'ids' };
				
				var settings = {
					url: req_url,
					type: 'post',
					data: post_params,
					success:function( data ){
					
						var t_data = data.trim().split( ',' );
						
						for( var tCD = 0; tCD < t_data.length; tCD++ ){
							if( t_data[ tCD ].length )
							{
								otw_sbm_items[ matches[1] ][ t_data[ tCD ] ] = true;
							};
						};
						
						jQuery( '#otw_sbm_type_' + matches[1] ).find( 'p.all' ).removeClass( 'sitem_loading' );
						
						otw_display_selected_items( matches[1], true );
					}
				};
				jQuery.ajax( settings );
			}else{
				otw_display_selected_items( matches[1], true );
			}
			
		}else{
			otw_sbm_items[ matches[1] ][ 'all' ] = false;
			otw_display_selected_items( matches[1], false );
		};
	};
};
function otw_display_selected_items( type, apply_to_all ){
	
	if( ( type != undefined ) && ( otw_sbm_items[type] != undefined ) && ( otw_sbm_items[ type ]['all'] != undefined ) &&  otw_sbm_items[ type ]['all'] ){
		apply_to_all = true;
	};
	
	//check if all is selected
	if( apply_to_all ){
		var apply_class = 'sitem_notselected';
		var remove_class = 'sitem_selected';
		
		if( otw_sbm_items[ type ]['all'] ){
			apply_class = 'sitem_selected';
			remove_class = 'sitem_notselected';
		}else{
			otw_sbm_items[ type ] = {};
		}
		jQuery( '#otw_sbm_type_' + type ).find( 'p.all' ).removeClass( remove_class );
		jQuery( '#otw_sbm_type_' + type ).find( 'p.all' ).addClass( apply_class );
		
		jQuery( '#otw_sbm_type_' + type ).find( 'div.f_items input[type=checkbox]' ).each( function(){
			jQuery( this ).parent().attr( 'class', apply_class );
		});
	}
	else
	{
		jQuery( '#otw_sbm_type_' + type ).find( 'p.all' ).removeClass( 'sitem_selected' );
		jQuery( '#otw_sbm_type_' + type ).find( 'p.all' ).addClass( 'sitem_notselected' );
		
		jQuery( '#otw_sbm_type_' + type ).find( 'div.f_items input[type=checkbox]' ).each( function(){
			
			if( otw_sbm_items[type][this.value ] ){
				jQuery( this ).parent().attr( 'class', 'sitem_selected' );
			}else{
				jQuery( this ).parent().attr( 'class', 'sitem_notselected' );
			}
		});
	}
	var joined_values = '';
	var total_values  = 0;
	for( item_key in otw_sbm_items[type] ){
	
		if( item_key && otw_sbm_items[type][ item_key ] ){
			if( item_key != 'all' ){
				total_values++;
			}
			joined_values = joined_values + item_key + ',';
		}
	};
	
	if( joined_values.length ){
		joined_values = joined_values.substring( 0, joined_values.length - 1 );
		jQuery( '#otw_sbm_type_' + type + '_validfor' ).val(joined_values );
	}else{
		jQuery( '#otw_sbm_type_' + type + '_validfor' ).val( '' );
	}
	jQuery( '#otw_sbm_type_' + type ).find( 'div.otw_sbm_selected_items span.otw_selected_items_number' ).html( total_values );
	if( total_values == 1 ){
		jQuery( '#otw_sbm_type_' + type ).find( 'div.otw_sbm_selected_items span.otw_seleted_items_plural' ).hide();
		jQuery( '#otw_sbm_type_' + type ).find( 'div.otw_sbm_selected_items span.otw_selected_items_singular' ).show();
	}else{
		jQuery( '#otw_sbm_type_' + type ).find( 'div.otw_sbm_selected_items span.otw_seleted_items_plural' ).show();
		jQuery( '#otw_sbm_type_' + type ).find( 'div.otw_sbm_selected_items span.otw_selected_items_singular' ).hide();
	}
	jQuery( '#otw_sbm_type_' + type ).find( 'div.otw_sbm_selected_items').show();
};

function select_sitem( param, force, force_value ){
	
	var label = jQuery( param );
	var block = label.parent();
	var input = block.find( 'input' );
	
	if( force ){
		if( force_value ){
			input.attr( 'checked', true );
			block.removeClass( 'sitem_notselected' );
			block.addClass( 'sitem_selected' );
		}else{
			input.attr( 'checked', false );
			block.removeClass( 'sitem_selected' );
			block.addClass( 'sitem_notselected' );
		}
	}else{
		if( !input.attr( 'checked' ) ){
			input.attr( 'checked', true );
			block.removeClass( 'sitem_notselected' );
			block.addClass( 'sitem_selected' );
		}else{
			input.attr( 'checked', false );
			block.removeClass( 'sitem_selected' );
			block.addClass( 'sitem_notselected' );
		}
		
		
		if( input.attr( 'id' ).match( /^otw_sbi_(.*)_sbi_all$/ ) ){
			input.parent().parent().find( 'label' ).each( function(){
				if( !jQuery( this ).attr( 'for' ).match( /^otw_sbi_(.*)_sbi_all$/ ) ){
					select_sitem( this, true, input.attr( 'checked' ) );
				}
			} );
		}else{
			input.parent().parent().find( 'label' ).each( function(){
				if( jQuery( this ).attr( 'for' ).match( /^otw_sbi_(.*)_sbi_all$/ ) ){
					select_sitem( this, true, false );
					return;
				}
			} );
		}
	}
}