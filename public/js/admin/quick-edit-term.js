/**
 * @author Dani
 */
var QuickEdit;

(function ($) {
    QuickEdit = {
        init: function ( init ) {
        	var ajaxurl = init.ajaxurl,
        		editor = $('#editor_term'),
        		table = $('#table_term table');
        	
			/* EDITOR QUICKEDIT */
        	$('a.quickEdit').live('click', function() {
				var parent = $(this).parents('tr:first'), 
					term_id = parent.attr('id').replace('term-', ''),
					term_fields, term_title, term_slug, term_parent, term_description,
					term_data =  $('#inline_' + term_id);
				
				// term fields
				term_fields = ['term_id', 'term_name', 'term_slug', 'term_parent', 'term_description'];
								
				// input value
				for ( i = 0; i < term_fields.length; i++ ) {
					$(':input[name="' + term_fields[i] + '"]', editor).val( $('.'+term_fields[i], term_data).text() );
				}
				// term parent value
				term_parent = $('.term_parent', term_data);
				$('select[name="term_parent"]', editor).val( term_parent.text() );
				
				// set focus
				$('input[name=term_name]', editor).focus();
		
				return false;
			});
			/* QUICKDELETE */
			$('a.quickDelete').live('click', function() {
   				var id = $(this).data('parameter'),
   					tr = $(this).parents('tr');
   				
   				$('.ajax-loader', tr).show();
   				
   				$.post( ajaxurl, 
   					{ act:'delete', term_id:id }, 
   					function( response ){
   						if( response.ok )
   						{
	   						tr.animate({ backgroundColor: "#fbc7c7" }, 1000)
			   				.animate({ opacity: "hide" }, "fast", function(){
			   					$(this).remove();
			   				});
   						}
   					}
   				)
   				
   				return false;
			});
			/* SAVE TERM */
			$('#save_term').click(function(){
				var data = $(this).parents('form').serialize(),
					term_name = $('input[name="term_name"]'),
					id = $('#term_id').val(), 
					param;
					
				
				if( !term_name.val() ){
					$('html,body').animate({scrollTop:0}, 'slow');
		   			term_name.focus(); // title focus
			   		term_name.tooltip('show'); // tooltip show
					return false;
				}
   					
				param = ( id ) ? { act:'update' } : { act:'add' };
				data += "&" + $.param(param);
				
				$.post( ajaxurl, data, function( html )
				{
					if( id ){
						$('tr#term-'+id).each(function(){
							$(this).animate({ backgroundColor: "#dafda5" }, "fast", function(){
								$(this).replaceWith(html).animate({ backgroundColor: "#ffffff" }, "slow");
							})
						});
					} else {
						table.each(function(){
	   						$(this).find('tbody').html( html );
	   						$(this).find('tr:last').animate({ backgroundColor: "#dafda5" }, "fast")
							.animate({ backgroundColor: "#ffffff" }, "slow");
	   					})	
					}
					
   					$('#reset').click();			
				});
				return false;
			});
			/* CANCEL */
			$('#reset').click(function(){
				$('#term_id').val('');
			})
		}
	}
})(jQuery);
