var TheWidgets;
(function ($) {
    TheWidgets = {
        init: function () {
        	var widgets = $(".widgets-sortables"),
        		avWidgets = $('#available-widget'), 
        		wi, multi;
        		
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
            /* aksi save */
            $("input.widget-control-save").live("click", function () {
                TheWidgets.save($(this).closest("div.portlet"), 0, 0);
                return false;
            });
            /* aksi hapus */
            $("a.widget-control-remove").live("click", function () {
                TheWidgets.save($(this).closest("div.portlet"), 0, 1);
                return false;
            });
            /* aksi close */
            $("a.widget-control-close").live("click", function () {
                TheWidgets.close($(this).closest("div.portlet"));
                return false;
            });
            /* draggable */
            avWidgets.children(".portlet").draggable({
				connectToSortable: 'div.widgets-sortables',
                handle: "> .portlet-header",
		        helper: "clone",
				cursor: "move",
				start: function(event, ui)
				{
					ui.helper.css('height', '25px');
					wi = this.id;
				},
				stop: function(event, ui)
				{
					console.log(ui.helper);
				}
			});
            /* sortable */
            widgets.sortable({
                placeholder: "widget-placeholder",
				items: "> .portlet",
				cursor: "move",
				start: function(event, ui)
				{
		          	// TheWidgets.close( ui.item );
				},
				stop: function(event, ui)
				{
					// drop delete, jika item memiliki class "deleting" aksi hapus widget dgn droppable  
					if (ui.item.hasClass("deleting")) {
						// hapus item
                        TheWidgets.del( ui.item, 0, 1 );
                        return;
                    }
                    
                    // drag add, jika item memiliki class "ui-draggable"
					if (ui.item.hasClass("ui-draggable")) {
						// hapus class "ui-draggable"
		            	ui.item.removeClass("ui-draggable");
		          	}
		          	
		          	// setelah drag, ambil input.add
		          	var add = ui.item.find("input.add").val();
		          	// jika memiliki value, adalah tambah widget
		          	if( add )
		          	{
		          		// hapus deskripsi
		            	ui.item.find('.portlet-description').remove();
		          		// ubah item ID [widget_id]-[position] dengan variabel multi
						ui.item.attr("id", wi+'-'+multi);
						// increase value multi
						multi++;
						// ubah value input.multi
						$('#available-widget #'+wi).find('input.multi').val(multi);
		          		// slideDown hanya untuk draggable (aksi live click)
		          		ui.item.find('a.ui-icon').click();
		          		// hapus input hidden
						ui.item.each(function(){
							$(this).find('input[type=hidden]').remove();
						});
		          		// set value input class ADD kosong, 
		          		// untuk selanjutnya hanya sortable widget,
		          		// bukan tambah widget
                        ui.item.find('input.add').val('');
                        // save
                       	TheWidgets.save(ui.item, 1, 0);
                       	// selesai, tanpa saveOrder
                       	return;
		          	}
		          	// aksi sortorder : save order
		          	TheWidgets.saveOrder(ui.item.parent(), 1);
				},
				receive: function(event, ui)
				{
					// set value multi, digunakan untuk ubah item ID
					multi = ui.helper.find('.multi').val();
				}
			});
            /* droppable */
			avWidgets.droppable({
                tolerance: "pointer",
                accept: function (ui) {
                    return $(ui).parent().attr("id") != "widget-list"
                },
                drop: function (event, ui) {
                    ui.draggable.addClass("deleting");
                    $("#removing-widget").hide().children("span").html("")
                },
                over: function (event, ui) {
                    ui.draggable.addClass("deleting");
                    $("div.widget-placeholder").hide();
                    if (ui.draggable.hasClass("ui-sortable-helper")) {
                        $("#removing-widget").show().children("span").html(ui.draggable.find("div.portlet-header").children("h4").html())
                    }
                },
                out: function (event, ui) {
                    ui.draggable.removeClass("deleting");
                    $("div.widget-placeholder").show();
                    $("#removing-widget").hide().children("span").html("")
                }
            })
        },
        saveOrder: function (item, loader) {
        	console.log(item);
        	
			var widgetId = item.attr('id'), 
				sortorder = '';
        	
        	// item sortorder adalah parent/sibiling widgets-sortables
        	if(loader){
        		// tampil header loader
        		item.parents('.widget-holder-wrap').find(".ajax-loader-widget:first").css("visibility", "visible");
        	}
			
			/* sort order */
			sortorder += item.sortable('toArray').toString();
			
			param = { 
				act: 'sort', 
				widget: widgetId, 
				data: sortorder 
			};
			data = $.param(param);
			
			// debug 
			console.log(data);
			
			$.ajax({
				url: TheWidgets.ajaxurl,
				type: 'POST',
				data: data,
				dataType: 'json',
				success: function( response ){
					$(".ajax-loader-widget").css("visibility", "hidden");
				}
			});
			
		},
	    save: function (item, add, del) {
	     	var wId = item.attr('id'),
	        	data = item.find('form').serialize(),
	        	param;
	                
			item = $(item);
			$(".ajax-loader-widget", item).css("visibility", "visible");
				
			// parameter
			if( del ) /* aksi delete widget */	
			{
				param = { 
					act: 'save',
					act_widget: 'delete-widget',
					id: wId 
				};
				// parameter tanpa data form
				data = $.param(param);
			} 
			else { /* aksi tambah/update widget */
				param = { 
					act: 'save',
					act_widget: (add) ? 'add-widget' : 'update-widget',
					id: wId 
				};
				data += "&" + $.param(param);
			}
				
			// debug
			console.log(data);
			
			$.ajax({
				url: TheWidgets.ajaxurl,
				type: 'POST',
				data: data,
				dataType: 'json',
				success: function( response ){
					if( response.ok )
					{
						if( del ) /* aksi delete widget */
						{
							$(".ajax-loader-widget", item).css("visibility", "hidden");
							// setelah delete widget name, delete item kemudian sortorder
							TheWidgets.del(item, 1, 0);
						} 
						else if( add ) { /* aksi tambah widget */
							// tambah widget dgn sortorder, tanpa header loader 
							TheWidgets.saveOrder(item.parent(), 0);
						} 
						else { /* aksi update widget */
							$(".ajax-loader-widget", item).css("visibility", "hidden");
							
							// update widget title header
							
							/*
							 * ambil title dr parameter
							var d = this.getParam(data),
								t = d.title;
							*/
							// ambil title dr input
							var t = item.find('input:text[name=title]').val(),
								w_title = ( t ) ? ':' + t : '';
							item.find('.widget-name').html(w_title);
							
							// setelah update widget, tampil info success
							item.find( ".portlet-inside" ).append('<div id="response_msg" class="alert alert-success">' + response.message + '</div>');
							// fadeout info success selama 3secs, kemudian hapus info tsb dan tutup item
							$('#response_msg').fadeOut(3000, function(){
								$(this).remove();
								TheWidgets.close(item);
							});
						}
							
					} else {
						$(".ajax-loader-widget", item).css("visibility", "hidden");
						// jika error tampil info error
						item.find( ".portlet-form" ).prepend('<div id="response_msg" class="notification error">' + response.message + '</div>');
					}
				}
			});
		},
		del: function(item, slide, drop) {
	       	// ambil element parent
			parent = item.parent();
			if( slide ){  // (delete) slideUp -> hapus -> order
				TheWidgets.close(item);
				item.remove();
			} 
			else if( drop ){ // (droppable) hapus -> order
				item.remove();
			}
			TheWidgets.saveOrder(parent, 1);
		},
		close: function (item) {
	   		$(item).find('a.ui-icon').toggleClass( "ui-icon-triangle-1-s" ).toggleClass( "ui-icon-triangle-1-n" );
	   		item.find('.portlet-inside').slideUp('fast');
		},
		getParam : function( uri ) {
		  	var obj = {};
			var pairs = uri.split('&');
			for(i in pairs){
			    var split = pairs[i].split('=');
			    obj[decodeURIComponent(split[0])] = unescape(split[1]).replace(/[+]/g," ");
			}

  			return obj;
		}
	};
	
})(jQuery);

