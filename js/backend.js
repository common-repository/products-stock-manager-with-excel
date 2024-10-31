(function( $ ) {

	$("body").on("click", ".stockManagerWooCommerce  .proVersion", function (e) {
		if (e.target.disabled) { // or this.disabled
			$("#stockManagerWooCommerceModal").slideDown();
		}

	});

	$(".stockManagerWooCommerce  .proVersion, .stockManagerWooCommerce  select.proVersion").click(function(e){
		e.preventDefault();
		$("#stockManagerWooCommerceModal").slideDown();
	});

		$("#stockManagerWooCommerceModal .close").click(function(e){
			e.preventDefault();
			$("#stockManagerWooCommerceModal").fadeOut();
		});		

		var modal = document.getElementById('stockManagerWooCommerceModal');

		// When the user clicks anywhere outside of the modal, close it
		window.onclick = function(event) {
			if (event.target == modal) {
				modal.style.display = "none";
			}
		}
		
    $(function() {
        $('.stockManagerWooCommerce .color').wpColorPicker();
    });


		//EXTENSIONS
		$(".extendwp_extensions").click(function(e){
			e.preventDefault();
			
			if( $('#extendwp_extensions_popup').length > 0 ) {
			
				$(".stockManagerWooCommerce .get_ajax #extendwp_extensions_popup").fadeIn();
				
				$("#extendwp_extensions_popup .extendwp_close").click(function(e){
					e.preventDefault();
					$("#extendwp_extensions_popup").fadeOut();
				});		
				var extensions = document.getElementById('extendwp_extensions_popup');
				window.onclick = function(event) {
					if (event.target === extensions) {
						extensions.style.display = "none";
						localStorage.setItem('hideIntro', '1');
					}
				}					
			}else{
				var action = 'extensions';
				$.ajax({
					type: 'POST',
					url: stockManagerWooCommerce.ajax_url,
					data: { 
						"action": action
					},							
					 beforeSend: function(data) {								
						$("html, body").animate({ scrollTop: 0 }, "slow");
						$('.stockManagerWooCommerce').addClass('loading');
					},								
					success: function (response) {
						$('.stockManagerWooCommerce').removeClass('loading');
						if( response !='' ){
							$('.stockManagerWooCommerce .get_ajax' ).css('visibility','hidden');
							$('.stockManagerWooCommerce .get_ajax' ).append( response );
							$('.stockManagerWooCommerce .get_ajax #extendwp_extensions_popup' ).css('visibility','visible');
							$(".stockManagerWooCommerce .get_ajax #extendwp_extensions_popup").fadeIn();
							
							$("#extendwp_extensions_popup .extendwp_close").click(function(e){
								e.preventDefault();
								$("#extendwp_extensions_popup").fadeOut();
							});		
							var extensions = document.getElementById('extendwp_extensions_popup');
							window.onclick = function(event) {
								if (event.target === extensions) {
									extensions.style.display = "none";
									localStorage.setItem('hideIntro', '1');
								}
							}							
						}
					},
					error:function(response){
						console.log('ERROR');
					}
				});			
			}
		});

		$(".stockManagerWooCommerce .search").keyup(function () {
			var value = this.value.toLowerCase().trim();
			var table = $(this).parent();
			$(table).find('tr').each(function (index) {
				if (!index) return;
				$(this).find("td").each(function () {
					var id = $(this).text().toLowerCase().trim();
					var not_found = (id.indexOf(value) == -1);
					
					$(this).closest('tr').toggle(!not_found);
					return not_found;
				});
				
				if($(this).css('display') == 'none'){
					$(this).addClass("noExl");
				}else $(this).removeClass("noExl");
			});
		});	


	$('.stockManagerWooCommerce #upload').attr('disabled','disabled');
    $(".stockManagerWooCommerce .smwFile").on('change',function () {
        var smprofileExtension = ['xls', 'xlsx'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), smprofileExtension) === -1) {
            alert("Only format allowed: "+smprofileExtension.join(', '));	
			$(".stockManagerWooCommerce input[type='submit']").attr('disabled','disabled');
        }else{
			$(".stockManagerWooCommerce input[type='submit']").removeAttr('disabled');
			$(".stockManagerWooCommerce").find('form').submit();
		}
    });

	$(".stockManagerWooCommerce #product_update").on("submit", function (e) {
				e.preventDefault();
				if(confirm("Are you sure you want to update the products stock and prices ?")){
					var smwproData = new FormData();
					$.each($('.smwFile')[0].files, function(i, file) {
						smwproData.append('file', file);
					});	
					smwproData.append('_wpnonce',$("#_wpnonce").val());
					smwproData.append('update_products',$("#update_products").val() );
					$.ajax({
						url: $(this).attr('action'),
						data: smwproData,
						cache: false,
						contentType: false,
						processData: false,
						type: 'POST',
						beforeSend: function() {	
							$('.stockManagerWooCommerce').addClass('loading');	
							console.log(smwproData);
						},					
						success: function(response){
							$(".result").slideDown().html($(response).find(".result").html());
							$('.stockManagerWooCommerce').removeClass('loading');	
							$(".stockManagerWooCommerce").find('form').hide().delay(5000).fadeIn();
							$(".stockManagerWooCommerce form")[0].reset();
							
							$(".success, .warning, .error").delay(5000).fadeOut();
						}
					});	
				}		
	});

			$(".stockManagerWooCommerce .exportToggler").on('click',function(){
				$(".stockManagerWooCommerce #exportProductsForm").slideToggle();
				$(".stockManagerWooCommerce .exportTableWrapper").slideToggle();
				$(".stockManagerWooCommerce .downloadToExcel").slideToggle();
				$(".stockManagerWooCommerce #selectTaxonomy").slideToggle();
			});


			
			$(".stockManagerWooCommerce #exportProductsForm").on('submit',function(e) {
					e.preventDefault();
		

				$.ajax({
					url: $(this).attr('action'),
					data:  $(this).serialize(),
					type: 'POST',
					beforeSend: function() {									
						$('.stockManagerWooCommerce').addClass('loading');		
					},						
					success: function(response){


										
						$('.stockManagerWooCommerce').removeClass('loading');
						
						$(".stockManagerWooCommerce #exportProductsForm").hide();
						$(".stockManagerWooCommerce #selectTaxonomy").hide();	
						
						$(".resultExport").slideDown().html($(response).find(".resultExport").html());

									var i=0;
									$(".stockManagerWooCommerce input[name='total']").val($(".stockManagerWooCommerce .totalPosts").html());
									$(".stockManagerWooCommerce input[name='start']").val($(".stockManagerWooCommerce .startPosts").html());							
									var total = $(".stockManagerWooCommerce input[name='total']").val();	
									var start = $(".stockManagerWooCommerce input[name='start']").val();

								function smwExportProducts() {
									var total = $(".stockManagerWooCommerce input[name='total']").val();
									var start = $(".stockManagerWooCommerce input[name='start']").val() * i;
									
									if($(".stockManagerWooCommerce .totalPosts").html()  <=500){
											$(".stockManagerWooCommerce input[name='posts_per_page']").val($(".stockManagerWooCommerce .totalPosts").html() );
									}else $(".stockManagerWooCommerce input[name='posts_per_page']").val($(".stockManagerWooCommerce .startPosts").html());
									
									var dif = total- start;
									
									if( $('.stockManagerWooCommerce #toskuExport >tbody >tr').length >= total ){
																														
										$('.stockManagerWooCommerce #myProgress').delay(10000).hide('loading');

										$("body").find('.stockManagerWooCommerce #exportProductsForm').find("input[type='number'],input[type='text'], select, textarea").val('');
										$('.stockManagerWooCommerce .message').html('Job Done!');
										$('.stockManagerWooCommerce .message').addClass('success');
										$('.stockManagerWooCommerce .message').removeClass('error');
										
										$(".stockManagerWooCommerce #toskuExport").tableExport();
										
										
									}else{	
									
										var dif = total - start;
										if(total> 500 && dif <=500 ){
											$(".stockManagerWooCommerce  input[name='posts_per_page']").val(dif);
										} 									
										
										$.ajax({
											url: stockManagerWooCommerce.ajax_url,
											data: $(".stockManagerWooCommerce #exportProductsForm").serialize(),
											type: 'POST',
											beforeSend: function() {
												$("html, body").animate({ scrollTop: 0 }, "slow");	
												$('.stockManagerWooCommerce').removeClass('loading');
											},						
											success: function(response){	

												$(".stockManagerWooCommerce .tableExportAjax").append(response);
												i++;
												start = $(".stockManagerWooCommerce input[name='start']").val() * i;
												
												$(".stockManagerWooCommerce  input[name='offset']").val(start);
												
												var offset = $(".stockManagerWooCommerce  input[name='offset']").val();																									
											},complete: function(response){	
																					
												smwExportProducts();	
																								}
										});
									}
								}
								smwExportProducts();								
					}
					});	
										
			});	



		$("#stock_manager_signup").on('submit',function(e){
			e.preventDefault();	
			var dat = $(this).serialize();
			$.ajax({
				
				url:	"https://extend-wp.com/wp-json/signups/v2/post",
				data:  dat,
				type: 'POST',							
				beforeSend: function(data) {								
						console.log(dat);
				},					
				success: function(data){
					alert(data);
				},
				complete: function(data){
					dismissStockManager();
				}				
			});	
		});

		function dismissStockManager(){
			
				var ajax_options = {
					action: 'push_not',
					data: 'title=1',
					nonce: 'push_not',
					url: stockManagerWooCommerce.ajax_url,
				};			
				
				$.post( stockManagerWooCommerce.ajax_url, ajax_options, function(data) {
					$(".stock_manager_notification").fadeOut();
				});
		}
		
		$(".stock_manager_notification .dismiss").on('click',function(e){
				var ajax_options = {
					action: 'push_not',
					data: 'title=1',
					nonce: 'push_not',
					url: stockManagerWooCommerce.ajax_url,
				};			
				
				$.post( stockManagerWooCommerce.ajax_url, ajax_options, function(data) {
					$(".stock_manager_notification").fadeOut();
				});
		});
		
		
})( jQuery )	