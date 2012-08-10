/**
 * AJAX EDITOR
 */
var Editor, SlugEdit, CustomFields, Category, Tags, FeaturedImage, ajaxFileManagerPluginUrl, callback;

(function ($) {
	Editor = {
		baseUrl: '',
		init: function( data ) {
			/* tinyMCE */
			tinyMCE.init({
				mode : "exact",
				elements : "content_description",
				theme : "advanced",
				plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave,ibrowser,advimagecustom",
				// Theme options
				theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,bullist,numlist,blockquote,|,justifyleft,justifycenter,justifyright,justifyfull,|,link,unlink,pagebreak,|,image,ibrowser,|,code,preview",
				theme_advanced_buttons2 : "formatselect,pastetext,pasteword,removeformat,charmap,|,outdent,indent,|,undo,redo,|,forecolor,help",
				theme_advanced_buttons3 : "",
				theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "left",
				theme_advanced_statusbar_location : "bottom",
				theme_advanced_resizing : true,
				theme_advanced_resize_horizontal : true,
				file_browser_callback : "Editor.ajaxfilemanager",
				paste_use_dialog : false,
				apply_source_formatting : true
			});
			// ajaxfilemanager plugin url
			ajaxFileManagerPluginUrl = data.baseUrl + '/js/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php';
			
			$( ".portlet" ).addClass( "ui-widget ui-widget-content ui-helper-clearfix" )
				   .find( ".portlet-header" )
				   .addClass( "ui-widget-header ui-corner-all" )
				   .prepend( "<a class='ui-icon ui-icon-triangle-1-s'></a>")
				   .end()			
				   .find( ".portlet-content" );
			/* aksi show/hide */   
			$( 'a.ui-icon' ).live("click", function () {
				$( this ).toggleClass( "ui-icon-triangle-1-s" ).toggleClass( "ui-icon-triangle-1-n" );
				$( this ).parents( ".portlet:first" ).find( ".portlet-inside:first" ).toggle();
                return false;
			});
			
			// initialize
			Editor.baseUrl = data.baseUrl;
			SlugEdit.init( data.baseUrl + "/../admin/ajax/content" );
			FeaturedImage.init( data.baseUrl + "/../admin/ajax/images" );
			Category.init( data.baseUrl + "/../admin/ajax/content" );
			CustomFields.init( data.baseUrl + "/../admin/ajax/field" );
			Tags.init();
		},
		/* Callback ajaxfilemanager */
		ajaxfilemanager: function( field_name, url, type, win ) {
			var view = 'detail';
			switch (type) {
				case "image":
					view = 'thumbnail';
					break;
				case "media":
					break;
				case "flash":
					break;
				case "file":
					break;
				default:
					return false;
			}
			tinyMCE.activeEditor.windowManager.open({
				url : ajaxFileManagerPluginUrl + "?view=" + view,
				width : 782,
				height : 440,
				inline : "yes",
				close_previous : "no"
			}, {
				window : win,
				input : field_name
			});
		},
		animateToTitle: function(){
			$('html,body').animate({scrollTop:0}, 'slow');
   			$('#content_name').focus(); // title focus
	   		$('#content_name').tooltip('show');
		}
	},
	/* Autosave/Edit Slug */
    SlugEdit = {
        init: function( ajaxurl ) {
				
			SlugEdit.makeSlugeditClickable();
			
			// autosave
			// hanya untuk aksi tambah kontent atau contentId kosong
			$("input#content_name").blur(function(){
				var name = $(this).val(),
					type = $('input[name="content_type"]').val(),
					id, content_id = $('input[name="content_id"]'),
					isSaved = content_id.val();
					
					console.log(isSaved);
					
				if( !isSaved && type && name )
				{
					$.post( ajaxurl, {
						act: 'autosave',
						content_name: name,
						content_type: type
					}, 
					function(data) {
						$('#content_permalink').html(data);
						// set value input hidden content_id
						id = $('a#editPermalink').attr('data-parameter');
						content_id.val(id);
						// set clickable
						SlugEdit.makeSlugeditClickable();
					});
				}
			});
			
			// edit slug
			$('a#editPermalink').live('click', function(){
				var e = $('#editable-content-slug'), 
					revert_e = e.html(), 
					real_slug = $('input[name="content_slug"]'), 
					revert_slug = real_slug.val(), 
					b = $('#edit-slug-buttons'), 
					revert_b = b.html(), 
					content_id = $(this).data('parameter');
				// ubah slug button "Edit" menjadi button "Ok" dan "Cancel"			
				b.html('<a href="#" class="save btn">Ok</a> <a class="cancel" href="#">Cancel</a>');
				// listener button save
				b.children('.save').click(function() {
					var content_slug = e.children('input').val();
					// jika tidak ada perubahan atau nilai input sama dengan nilai slug asli
					// aksi listener button cancel
					if ( content_slug == revert_slug ) {
						$('.cancel', '#edit-slug-buttons').click();
						return false;
					}
					// ajax post
					$.post( ajaxurl, {
						act: 'edit-permalink',
						content_id: content_id,
						content_slug: content_slug,
						content_name: $('#content_name').val(),
					}, function(data) {
						// ubah slug box dengan html baru
						$('#edit-slug-box').html(data);
						// balikkan button edit keadaan awal
						b.html(revert_b);
						// set clickable
						SlugEdit.makeSlugeditClickable();
					});
					return false;
				});
				// listener button cancel
				// balikkan ke kondisi awal
				b.children('.cancel', '#edit-slug-buttons').click(function() {
					e.html(revert_e);
					b.html(revert_b);
					real_slug.val(revert_slug);
					return false;
				});
		
				// ubah content slug menjadi input text
				e.html('<input type="text" id="new-content-slug" class="input-small" value="'+revert_slug+'" />').focus();
					
				return false;
			});
       },
       makeSlugeditClickable : function() {
			$('#editable-content-slug').click(function() {
				$('#edit-slug-buttons').children('#editPermalink').click();
			});
		}
   	},
   	/* Custom Fields */
   	CustomFields = {
   		init: function( ajaxurl ){
   			var field_name = $('#field_name'),
   				field_list = $('#field_list'),
   				field_value = $('#field_value'),
   				custom_fields = $('#custom_fields'),
   				content_id = $('input[name="content_id"]');
   				
   			// add new field
   			$('#field_new').click(function(){
   				var	f = $(this),
   					revert_f = f.html();
   				// toggle element	
   				field_name.toggle();
   				field_list.toggle();
   				// ubah teks link
   				f.html(revert_f == 'Enter new' ? 'Cancel' : 'Enter new');
   				
   				return false;
   			})
   			// add custom field
   			$('#add-custom-field').click(function(){
   				var fl = field_list.val(),
   					fn = field_name.val(),
   					f_name, name, f_null = false,
   					value = field_value.val(),
   					id = content_id.val();
   					
   				if( !id ){
   					Editor.animateToTitle();
   					return false;
   				}
   					
   				// set value field name
   				name = field_name.is(':hidden') ? fl : fn ;
   				if( field_name.is(':hidden') ){
   					if( fl == 0 )
		   				f_null = true;
	   				f_name = field_list;
   				} else {
   					if( !fn )
		   				f_null = true;
	   				f_name = field_name;
   				}
   				
   				// fokus element dan tampil tooltip jika nilai element kosong
   				if ( f_null ){
	   				f_name.focus();
	   				f_name.tooltip('show');
   				} else if ( !value ){
	   				field_value.focus();
	   				field_value.tooltip('show');
   				} else{ 
   					// Callback : AJAX tambah field
   					// append pada tabel custom field dengan animasi
   					callback = function( response )
					{
						custom_fields.each(function(){
   							if( $(this).hasClass('hide'))
   								$(this).removeClass('hide');
   									
   							$(this).find('tbody').append( response );
   							$(this).find('tr:last').animate({ backgroundColor: "#dafda5" }, "fast")
   							.animate({ backgroundColor: "#ffffff" }, "slow")
   						})
   						
   						// add option field list
	   					if( fn )
	   						field_list.append($('<option></option>').val(fn).text(fn));					 	
	   					// kosongkan field
	   					$('#new_fields').find(':input').each(function(){
	   						$(this).val('');
	   					});
					}
					
					var params = { act:'add', id:id, name:name, value:value };
					CustomFields.save( ajaxurl, params );
   				}
   				return false;
   			})
   			//update field
   			$('#update-field').live('click', function(){
   				var tr = $(this).parents('tr'),
   					id = content_id.val(),
   					name = tr.find('input').val(), // input field_name
   					value = tr.find('textarea').val(); // textarea field_value
   					
   				callback = function( response )
				{
					if( response.ok ){
	   					tr.animate({ backgroundColor: "#dafda5" }, "fast")
	   					.animate({ backgroundColor: "#ffffff" }, "slow");
   					}
				}
   				
				var params = { act:'update', id:id, name:name, value:value };
				CustomFields.save( ajaxurl, params );
   				return false;
   			})
   			// delete field
   			$('#delete-field').live('click', function(){
   				var id = content_id.val(),
   					name = $(this).parent().siblings('input').val(), // input field_name
   					tr = $(this).parents('tr'),
   					tb = custom_fields.find('tbody');
   					
   				callback = function( response )
				{
					if( response.ok ){
	   					tr.animate({ backgroundColor: "#fbc7c7" }, "fast")
			   			.animate({ opacity: "hide" }, "slow", function(){
				   			tr.remove();
				   			if( !tb.children().length ){
				   				custom_fields.addClass('hide');
				   			}
			   			});
   					}
				}
   				
				var params = { act:'delete', id:id, name:name };
				CustomFields.save( ajaxurl, params );
   				return false;
   			})
   		},
   		save: function( ajaxurl, params ){
   			$.post( ajaxurl, params, 
   				function( response ){
   					callback( response );	
   				}
   			)
   		}
   	},
   	/* Category */
   	Category = {
   		init: function( ajaxurl ){
   			$('#add_category_submit').live('click', function(){
   				var cat_name = $('#add_category_input'),
   					category = cat_name.val(),
   					cat_parent = $('#add_category_parent'),
   					parent = cat_parent.val(),
   					taxonomy = $('input[name="content_type"]').val();
   				
   				if( !category )	{
   					cat_name.focus();
   				} else {
   					$.post( ajaxurl, 
	   					{ act:'category', category:category, parent:parent, taxonomy:taxonomy }, 
	   					function( data ){
	   						if( data ){
	   							$('#treecheckbox').html(data.cats);
	   							if( parent == 0 )
	   								$('#add_category_parent').append($('<option></option>').val(data.id).text(category));
	   								
	   							cat_name.val('');
	   						}
	   					}, 'json'
   					);
   				}
   				return false;
   			})
   		}
   	},
   	/* Tags */
   	Tags = {
   		init: function(){
   			var content_tags = $('#content_tags'), 
   				tag, terms, value;
   			// tambah/delete tag dari tag cloud
   			// hapus tag, jika tag sudah ada pada tag input
   			$('#tags a').live('click', function(){
   				tag = $(this).html();
   				terms = Tags.split( content_tags.val() );
   				value = Tags.getTerms(terms, tag, 1);
				content_tags.val(value);
   				return false;
   			})
   		},
		/* tag Split */
		split: function( val ){
			return val.split( /,\s*/ );
		},
		/* set last tag */
		setLast: function( term ) {
			return Tags.split( term ).pop();
		},
		getTerms: function( terms, tag, splice ) {
			var found = jQuery.inArray(tag, terms);
			if ( found >= 0 ) {
				if( splice ){
					// tag cloud
			   		terms.splice(found, 1); // hapus tag, jika sudah ada
			   	} else {
			   		// tag autocomplete
			   		terms.pop(); // hapus element akhir
					terms.push( '' ); // tambah element kosong
			   	}
			} else {
			    // tidak ditemukan, tambah tag
			    terms.pop(); // hapus element akhir
				terms.push( tag ); // tambah element tag
				terms.push( '' ); // tambah element kosong
			}
			return terms.join( ',' );
		}
   	}
   	/* Featured Image */
    FeaturedImage = {
        init: function( modalurl ) {
			var modalImage = $('#modalImage'),
				f = $('#featured_image'),
				btn_img = $('#set_featured_image'),
				content_img = $('input[name="content_image"]');
			
        	modalImage.modal({
			    backdrop: true,
			    keyboard: true,
			    show: false
			});
			
			// display modal image
			$('a[rel="popup"]').live('click', function(e) {
				var id = $('input[name="content_id"]').val(),
					url = modalurl + '/id/'+ id;
				
				if( !id ){
					// jika sudah autosave atau nilai input "content_id" kosong
					modalImage.modal('hide'); // modal hide
					Editor.animateToTitle(); // animate to title
				} else {
					// tampil modal image
				    modalImage.find('h3').html($(this).data('title'));
				    modalImage.find('.modal-body').load( url );
				    modalImage.modal('show');
				}
			    
			}); 
			// set featured image	
			$('a[rel="insertimage"]').live('click', function(e) {
				var p = $(this).parent().siblings('.preview'),
					img = p.find('img').attr('src'),
					f_img = f.find('img');
					
				if( img )
				{
					if( f_img.length ) {
						// update image, jika featured image sudah ada
						f_img.attr('src', img);
					} else {
						// set featured image
						f.html('<img src="'+img+'" /><br/><a href="#" class="remove-featured-image">Remove featured image</a>');
					}
					// set nilai input "content_image"
					content_img.val(img);
					// hide link set featured image
					btn_img.hide();
				}
				
				// hide modal
				modalImage.modal('hide');
				return false;
			});
			// remove featured image
			$('a.remove-featured-image').live('click', function(e) {
				f.html('');
				content_img.val('');
				btn_img.show();
				return false;
			});
       }
    }
})(jQuery);
