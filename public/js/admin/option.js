$(function()
{
	var isValid = {};
	isValid['option_general'] = true;
	isValid['option_contact'] = true;
	isValid['option_social'] = true;
	isValid['option_profile'] = true;
	
	// validasi saat input
	$("input").blur(function()
	{
		var formElementId = $(this).attr('id');
		var url = $(this).closest('form').attr('ajax-validate');	
		var type = $(this).closest('form').attr('id');		
		// proses validasi
		//doValidation( formElementId, url, type );
		
	});
	
	// aksi submit
	// option_general
	// option_social
	// option_profile
	// option_analytics
	$("form#option_general, form#option_social, form#option_profile, form#option_analytics").submit(function()
	{
		var data = $( this ).serialize();
		var action = $( this ).attr( 'action' );
		var method = $( this ).attr( 'method' );
		var type = $( this ).attr( 'id' );
		
		// debug
		//alert( 'action: ' + action + '\n method : ' + method + '\n type : ' + type + '\n data : ' + data);
		
		if( isValid[type] )
		{
				$.ajax( {  
			        type : method,  
			        url : action + '/type/' + type,  
			        data : data,  
			        success : function( response ) { 
			        	// debug
			        	console.log( response ); 
			        	// show message
						showMessage( response.status, response.message ); 
			        }
		    	});
		    	
		} else {
			showMessage( 'attention', 'Form invalid !! Please check form below' );
		}
		
		return false;
	});
	
	/**
	 * ================ OFFICES ================
	 */
	
	
	/**
	 * add multiple address office
	 * ajax request url : /admin/option/office
	 * 		module : admin
	 * 		controller : option
	 * 		action office
	 */
	$('a#addElement').click(function() {
		var action = $(this).closest('form').attr('action'),
			office_id = $('#office_id').val(); 
		$.ajax({
			type : 'POST',
			url : action,
			dataType: 'html',
			data : { id:office_id, act:'office' },
			success : function( officeElement ) {
				// tampilkan sebelum tombol add
				$('#addElement').after(officeElement);
				// looping id
				$('#office_id').val(++office_id);
			}
		});

		return false;
	});
	
	$('#remove-element a').live('click', function(){
		var id = $(this).attr('id');
		$( 'fieldset#' + id ).fadeOut();
		return false;
	})
	
	/**
	 * edit address office
	 * ajax request url : /admin/ajax/office
	 * 		module : admin
	 * 		controller : ajax
	 * 		action : office
	 * 		param act : update
	 */
	$('button#editElement').click(function() {
		var action = $(this).closest('form').attr('action');
		var element = $(this).closest('fieldset');
		var elementId = element.attr('id');
		
		// ambil object input
		var data = {};
		$('#' + elementId + ' input[type=text]').each(function(){
			data[$(this).attr('name')] = $(this).val();			
		});
		
		// console.log( data );
		
		$.ajax({
			type : 'POST',
			url : action,
			// send POST elementId dan data
			data : { act:'update', elementId:elementId, data:data },
			success : function( response ) {
				// debug
				console.log( response );
				
				// show message
				showMessage( response.status, response.message );
			}
		});
		
		return false;
	});
	
	/**
	 * remove address office
	 * ajax request url : /admin/ajax/office
	 * 		module : admin
	 * 		controller : ajax
	 * 		action : office
	 * 		param act : remove
	 */
	$('button#removeElement').click(function() {
		var action = $(this).closest('form').attr('action');
		var element = $(this).closest('fieldset');
		var elementId = element.attr('id');
		
		$.ajax({
			type : 'POST',
			url : action,
			data : { act:'remove', elementId:elementId },
			success : function( response ) {
				// debug
				console.log( response );
				if( response.ok )
					element.remove(); // hilangkan element
				// show message
				showMessage( response.status, response.message );
			}
		});
		
		return false;
	});
	
	/**
	 * tambah contact office
	 * ajax request url : /admin/ajax/office
	 * 		module : admin
	 * 		controller : ajax
	 * 		action : office
	 * 		param act : add
	 */
	$('form#option_contact').submit(function() {

		var element = $( this ).find('fieldset');
		var action = $( this ).attr( 'action' );
		
		//console.log( $(this) );
		
		if( isValid['option_contact'] )
		{
			$.ajax({
				type : 'POST',
				url : action + '/act/add',
				data : $(this).serialize(),
				success : function(response) {
					// debug
					console.log( response );
					
					// jika success
					// buat animate ke #addresses
					// kemudian pindahkan element ke #addresses
					if( response.ok )
					{
						$( element ).animate_from_to( "#addresses", {
							//pixels_per_second: 200,
				       		initial_css: {
				        		'background': '#AE432E'
				       		},
							callback : function() {
								var elementId = element.attr('id');
								// tambahkan pada wrap #address
								$('#addresses').append( element );
							}
						});
					}
					// show message
					showMessage( response.status, response.message );
				}
			});
		} else {
			showMessage( 'attention', 'Form invalid !! Please check form below' );
		}
		

		return false;
	});
	
	
	/**
	 * doValidation
	 * proses ajax validasi
	 * 
	 * @param {string} id, element id
	 * @param {string} url
	 * @param {string} type
	 */
	function doValidation(id, url, type) 
	{
		var ajaxUrl = url + '/type/' + type 
		
		var data = {};
		$('form#'+type+' input').each(function() {
			data[$(this).attr('name')] = $(this).val();
		});
		
		$.post( ajaxUrl, data, function( resp ) 
		{
			var response = resp.messages;
			var geo = resp['geo'];
			// cek maps dan geo
			if( !response['msg'] && typeof(geo) != "undefined" )
			{
				// debug
				//console.log(response['geo']);
				// hapus
				$("#geo").remove();
				// buat element untuk latitude dan longtitude
				var geoElement = '<div id="geo">'+
				'<dt><label>Latitude</label></dt>'+
				'<dd><input type="text" class="small" value="'+geo['lat']+'" id="latitude" name="latitude"></dd>'+
				'<dt><label>Longtitude</label></dt>'+
				'<dd><input type="text" class="small" value="'+geo['long']+'" id="longtitude" name="longtitude"></dd>'+
				'</div>';
				// tampilkan
				$('#addElement-label').before(geoElement);
				
			} else{
				$("#geo").remove();
			}
			
			// hapus notifikasi
			$("#" + id).parent().find('.notification').remove();
			if (response != ''){
				// tambahkan class invalid
				//$("#" + id).addClass('invalid');
				// remove element span, agar tidak tampil berulang
				//$("#" + id).parent().find('span').remove();
				// tambahkan span invalid
				//$("#" + id).after('<span class="invalid-side-note"></span>');
				// append error html sesuai id
				$("#" + id).parent().append( getErrorHtml(response[id], id) );
				// set invalid form
				isValid[type] = false;
			} else {
				// tambahkan class valid
				//$("#" + id).addClass('valid');
				// remove element span, agar tidak tampil berulang
				//$("#" + id).parent().find('span').remove();
				// tambahkan span invalid
				//$("#" + id).after('<span class="valid-side-note"></span>');
				// set valid form
				isValid[type] = true;
			}
	
		}, 'json');
	}
	
	/**
	 * getErrorHtml
	 * tampilkan pesan error
	 * 
	 * @param {array} formErrors
	 * @param {Object} id
	 */
	function getErrorHtml(formErrors, id) {
		var html = '';
		for(key in formErrors) {
			html = '<div class="notification attention medium">';
			html += '<p>' + formErrors[key] + '</p>';
			html += '</div>';
		}
		return html;
	}
	
	/**
	 * showMessage
	 * tampilkan notifikasi ajax message
	 * 
	 * @param {String} status
	 * @param {String} text
	 */
	function showMessage( status, text ){
		var msg = $('#ajax-message');
		//msg.addClass('ajax-' + status);
	    msg.html( '<div class="ajax-' + status +'">'+ text +'</div>' );
	    msg.fadeIn('slow');
	    // fade out in 5 sec
		setTimeout(function(){
	    	msg.fadeOut('slow');
	    }, 3000);
	    // animate to ajax message
	    //$('html, body').animate({ scrollTop: $("#ajax-message").offset().top }, 500);
	};
});
	





