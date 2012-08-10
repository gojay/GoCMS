var TheMenus, callback;

(function ($) {
	TheMenus = {
		init: function(){
			
			$( ".portlet" ).addClass( "ui-widget ui-widget-content ui-helper-clearfix" )
						   .find( ".portlet-header" )
						   .addClass( "ui-widget-header ui-corner-all" )
						   .prepend( "<a class='ui-icon ui-icon-triangle-1-s'></a>")
						   .end();
						   
			$('#menus-list ol.menu-sortable').nestedSortable({
		        handle: '.portlet-header',
				forcePlaceholderSize : true,
				helper : 'clone',
				items : 'li',
				cursor: 'move',
				maxLevels : 5,
				opacity : .6,
				placeholder : 'menu-placeholder',
				revert : 250,
				tabSize : 25,
				tolerance : 'pointer'
			});
			
			 /* AKSI HAPUS */
			$("a.menu-control-remove").live("click", function () {
				var a = $(this).parent().find('.menu-control-close'),
					li = $(this).parents('li:first');
				// animate element li
				li.animate({ backgroundColor: "#fbc7c7" }, "fast").animate({ opacity: "hide" }, "fast", function() {
					a.click();
					li.remove();
				});
		    	return false;
		    });
			/* AKSI CLOSE, VIEW MENU, CLOSE MENU */
			$( 'a.ui-icon, a.menu-control-close, a.menu-view, a.menu-close' ).live("click", function () {
				var parent = $( this ).parents( ".portlet:first" );
				$( this ).toggleClass( "ui-icon-triangle-1-s" ).toggleClass( "ui-icon-triangle-1-n" );
				parent.find( ".portlet-inside:first" ).toggle();
				parent.find( ".portlet-header:first .menu-save-top" ).toggle();
		        return false;
			});
			 /* AKSI CHECK ALL */
			$('a.page-check-all').live('click', function(){
				TheMenus.pageCheck('form-page', 1);
				return false;
			});
			 /* AKSI UNCHECK ALL */
			$('a.page-uncheck-all').live('click', function(){
				TheMenus.pageCheck('form-page', 0);
				return false;
			});
			/* SET NAVIGATION */
			$('form.navigation').submit(function(){
				var data = $(this).serialize();
						
				// set parameter
				param = {  act: 'navigation' };
				data += "&" + $.param(param);
				
				callback = function( response ){ 
					// callback kosong
				}
				TheMenus.save( $(this), data, 0 );
				return false;
			});
			
			/* TAMBAH PAGE CUSTOM LINK */
			$('input.add-page-link').live('click', function(){
				TheMenus.page( $(this), 0 );
				return false;
			})
			/* TAMBAH PAGE */
			$('input.add-page').live('click', function(){
				TheMenus.page( $(this), 1 );
				return false;
			})
			
			/* SAVE MENU */
			$('.menu-save-bottom input[type=submit]').live('click', function() {
				TheMenus.menu( $(this), 1, 0, 0 );
				return false;
			})
			/* UPDATE MENU NAME */
			$('.menu-save-top input[type=submit]').live('click', function() {
				TheMenus.menu( $(this), 0, 1, 0 );
				return false;
			})
			/* REMOVE MENU */
			$('.menu-save-top a.menu-remove').live('click', function() {
				TheMenus.menu( $(this), 0, 0, 1 );
				return false;
			})
		},
		pageCheck: function( id, flag )
		{
			var checkbox = $("form#" + id + " INPUT[type='checkbox']");
		    if ( flag ) {
		        checkbox.attr('checked', true);
		    } else {
		        checkbox.attr('checked', false);
		    }
		},
		save: function( parent, param, page ){
			$('.ajax-loader-widget', parent).css("visibility", "visible");
			$.ajax({
				url: TheMenus.ajaxurl,
				type: 'POST',
				data: param,
				dataType: ( page ) ? 'html' : 'json',
				success: function( response ){
					$('.ajax-loader-widget', parent).css("visibility", "hidden");
					callback( response );
				}
			});
		},
		page: function( item, page ){
			var parent= item.parents('.portlet-form'),
				data = parent.find('form').serialize(),
				menu = parent.find('select.select-menu').val(),
				item_menu, item_inside, action;
				
			if( page ) {
				action = 'add-page';
				TheMenus.pageCheck('form-page', 0); // uncheck all
			} else {
				action = 'add-page-link';
				$('input[name="url"]').val('http://');
				$('input[name="label"]').val('');
			}
			
			// set parameter	
			param = { act:action };
			data += "&" + $.param(param);
			
			console.log(menu);
			
			if( menu && data )
			{
				callback = function( item )
				{
					item_menu = $('#'+menu);
					item_inside = $('.portlet-inside:first', item_menu);
						
					if( item_inside.is(":hidden") ){
						$('a.ui-icon:first', item_menu).click();
					}
					$('.info', item_menu).hide();
					$('.menu-sortable', item_menu).prepend( item );
					$('.menu-save-bottom .alignright', item_menu).show();
				}
				TheMenus.save( parent, data, 1 );
			}
		},
		menu: function( item, save, update, del ){
			var parent = item.parents('.portlet:first'),
				menu = parent.attr('id'),
				a = parent.find('a.ui-icon'),
				img = item.parent(),
				menu_name, param, data;
			
			// set parameter dan callback
			if( save ){
				var form = item.closest('form'),
					data = form.serialize(),
					sort = form.find('ol.menu-sortable').nestedSortable('serialize');
				// parameter
				param = { 
					act: 'save',
					menu: menu 
				};
				data += "&" + sort + "&" + $.param(param);
				// callback
				callback = function( response ){ 
					// callback kosong
					console.log(response);
				}
			} else if( update ) {
				menu_name = parent.find('input#menu-name').val();
				// parameter
				param = { 
					act: 'update',
					menu: menu,
					name: menu_name
				};
				data = $.param(param);
				// callback
				callback = function( response )
				{
					if( response.ok )
					{
						// ubah Title
						parent.find('h4:first').html(response.name);
						// ubah select option text
						$("select.select-menu").find("option").filter(function() {
							return $(this).val() == menu;
						}).val(response.value).text(response.name);
					}
				}
			} else if( del ) {
				// parameter
				param = { 
					act: 'remove',
					menu: menu
				};
				data = $.param(param);
				// callback
				callback = function( response )
				{
					if( response )
					{
						// toogle
						a.click();
						// animate remove item
						parent.animate({ backgroundColor: "#fbc7c7" }, "fast").animate({ opacity: "hide" }, "slow");
						// remove select menu
						$("select.select-menu").find("option").filter(function() {
							return $(this).val() == menu;
						}).remove();
					}
				}
			}
			
			console.log(data); 
			
			// save
			TheMenus.save( parent, data, 0 );
		}
	};
	
})(jQuery);
