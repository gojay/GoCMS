/**
 * AJAX QUICK EDIT
 */
var QuickEdit;

(function ($) {
    QuickEdit = {
        init: function ( init ) {
        	/* EDITOR QUICKEDIT */
        	$('a.quickEdit').live('click', function() {
				var parent = $(this).parents('tr:first'), 
					content_id = parent.attr('id').replace('content-', ''),
					content_type = parent.attr('class'),
					content_edit_box = $('#inline-edit-'+content_type).clone(true),
					content_tags = $('textarea[name=content_tags]', content_edit_box),
					content_data = $('#inline_'+content_id),
					content_fields, content_name, content_category, content_parent, content_status, content_order, i;
				
				// content fields
				content_fields = ['content_name', 'content_slug', 'content_category', 'content_status'];
				if ( content_type == 'page' )
					content_fields.push('content_parent','content_order');
				
				$('#content-'+content_id).hide().after(content_edit_box);
				
				// content title value
				content_name = $('.content_name', content_data).text();
				$('h4 .post-edit-title', content_edit_box).html(content_name);
				// input value
				for ( i = 0; i < content_fields.length; i++ ) {
					$(':input[name="' + content_fields[i] + '"]', content_edit_box).val( $('.'+content_fields[i], content_data).text() );
				}
				// tree categories
				content_category = $('.content_category', content_data);
				if( content_category.length ){
					content_category.each(function(){
						var cat_ids = $(this).text();
						if ( cat_ids ) {
							$('ul#content_categories :checkbox').val(cat_ids.split(','));
						}
					});	
				}
				// content tags value
				$('.content_tags', content_data).each(function(){
					var tags = $(this).text();
					if ( tags )
						content_tags.val(tags);
				});
				// content parent value
				content_parent = $('.content_parent', content_data);
				if ( content_parent.length ) {
					$('select[name="content_parent"]', content_edit_box).val( content_parent.text() );
				}
				// content status value
				content_status = $('.content_status', content_data);
				$('select[name="content_status"]', content_edit_box).val( content_status.text() );
		
				// show editor
				$(content_edit_box).attr('id', 'edit-'+content_id).addClass('inline-editor').show();
				// enable autocomplete
				QuickEdit.suggest( content_tags, init.ajaxurl );
				// focus name
				$('input[name=content_name]', content_edit_box).focus();
		
				return false;
			});
			/* CANCEL */
			$('a.cancelEditor', $('.inline-editor')).live('click', function(){
				var id = $(this).parents('tr:first').attr('id');
				
				if ( id ) {
					$('#'+id).remove();
					id = id.substr( id.lastIndexOf('-') + 1 );
					console.log(id);
					$('#content-'+id).show();
				}
				
				return false;
			});
			/* SAVE */
			$('a.saveEditor', $('.inline-editor')).live('click', function(){
				var parent = $(this).parents('tr:first'),
					id = $(this).parents('tr:first').attr('id'),
					content_id = id.substr( id.lastIndexOf('-') + 1 ),
					params, fields;
				
				// parameter
				params = {
					act: 'inline-save',
					content_id: content_id
				};
				fields = $('#edit-'+content_id+' :input').serialize();
				params = $.param(params) + '&' + fields ;
				
				console.log(params);
				$('.ajax-loading', parent).show();
				
				$.post( init.ajaxurl, params, function( response ) {
					if ( response ) {
						$('#content-'+content_id).remove();
						$('#edit-'+content_id).before(response).remove();
					} 
					$('.ajax-loading', parent).hide();
				});
				return false;
			});
        },
		/* autocomplete */
        suggest: function( tagsField, ajaxurl ) {
	       	tagsField.autocomplete({"source":function( request, response ) {
				$.getJSON( ajaxurl, {
					act: 'tag',
					term: QuickEdit.setLast( request.term )
				}, response );
			},
			select: function( event, ui ) {
				var terms = QuickEdit.split( this.value ),
				tag = ui.item.value;
				this.value = QuickEdit.getTerms(terms, tag);
				return false;
			}}); 
		},
		/* tag Split */
		split: function( val ){
			return val.split( /,\s*/ );
		},
		/* set last tag */
		setLast: function( term ) {
			return QuickEdit.split( term ).pop();
		},
		getTerms: function( terms, tag ) {
			var found = jQuery.inArray(tag, terms);
			if ( found >= 0 ) {
				// tag autocomplete
			   	terms.pop(); // hapus element akhir
				terms.push( '' ); // tambah element kosong
			} else {
			    // tidak ditemukan, tambah tag
			    terms.pop(); // hapus element akhir
				terms.push( tag ); // tambah element tag
				terms.push( '' ); // tambah element kosong
			}
			return terms.join( ',' );
		}
    }
})(jQuery);

