<?php
 
 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require plugin_dir_path( __FILE__ ) .'/Classes/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
 
 class StockManagerWooCommerceInit{
	
	public $tab;
	public $activeTab;
	public $importUrl = 'importUrl';
	public $importFrequency = 'importFrequency';
	public $lowStockValue = 'lowStockValue';
	public $autoimport	='autoimport';
	public $importTime	='importTime';
	public $numberOfRows=1;
	public $keyword='';
	public $posts_per_page='';
	public $sale_price='';
	public $regular_price='';
	public $price_selector='';
	public $sale_price_selector='';
	public $sku='';
	public $offset='';
	public $addStockNumber = 'addStockNumber';

	public $allowed_html = array(
            'a' => array(
                'style' => array(),
                'href' => array(),
                'title' => array(),
                'class' => array(),
                'id'=>array(),
				'target'=>array(),
            ),
			'i' => array('style' => array(),'class' => array(),'id'=>array() ),
            'br' => array('style' => array(),'class' => array(),'id'=>array() ),
            'em' => array('style' => array(),'class' => array(),'id'=>array() ),
            'strong' => array('style' => array(),'class' => array(),'id'=>array() ),
            'h1' => array('style' => array(),'class' => array(),'id'=>array() ),
            'h2' => array('style' => array(),'class' => array(),'id'=>array() ),
            'h3' => array('style' => array(),'class' => array(),'id'=>array() ),
            'h4' => array('style' => array(),'class' => array(),'id'=>array() ),
            'h5' => array('style' => array(),'class' => array(),'id'=>array() ),
            'h6' => array('style' => array(),'class' => array(),'id'=>array() ),
            'img' => array('style' => array(),'class' => array(),'id'=>array() ),
            'p' => array('style' => array(),'class' => array(),'id'=>array() ),
            'div' => array('style' => array(),'class' => array(),'id'=>array() ),
            'section' => array('style' => array(),'class' => array(),'id'=>array() ), 
            'ul' => array('style' => array(),'class' => array(),'id'=>array() ),
            'li' => array('style' => array(),'class' => array(),'id'=>array() ),
            'ol' => array('style' => array(),'class' => array(),'id'=>array() ),
            'video' => array('style' => array(),'class' => array(),'id'=>array() ),
            'blockquote' => array('style' => array(),'class' => array(),'id'=>array() ),
            'figure' => array('style' => array(),'class' => array(),'id'=>array() ),
            'figcaption' => array('style' => array(),'class' => array(),'id'=>array() ),
            'style' => array(),
            'button' => array(
                'class' => array(),            
            ),

            'input' => array(
                'type' => array(), 
				'class' => array(), 				
				'placeholder' => array(), 
				'disabled' => array(),		
            ),				
            'option' => array(
                'value' => array(),
                'stock' => array(),
                'quantity' => array(),
                'price' => array(),
                'id' => array(),              
            ),			
            'iframe' => array(
                'height' => array(),
                'src' => array(),
                'width' => array(),
                'allowfullscreen' => array(),
                'style' => array(),
                'class' => array(),
                'id'=>array()                
            ),             
            'img' => array(
                'alt' => array(),
                'src' => array(),
                'title' => array(),
                'style' => array(),
                'class' => array(),
				'width' => array(),
				'height' => array(),
                'id'=>array()
            ), 
            'video' => array(
                'width' => array(),
                'height' => array(),
                'controls'=>array(),
                'class' => array(),
                'id'=>array()
            ),  
            'source' => array(
                'src' => array(),
                'type' => array(),
                'class' => array(),
                'id'=>array()
            ),             
        );
		
	public function adminHeader(){
			
		print "<h1>".$this->name."</h1>";
	}

	public function adminTabs(){
			$this->tab = array( 'import'=>esc_html__( "Update", "smw" ),'export'=>esc_html__( "Export", "smw" ),'report'=>esc_html__( "Report", "smw" ),'settings' => esc_html__( "Settings", "smw" ),'more'=> esc_html__( "GO PRO", "smw" ) );
			if( isset( $_GET['tab'] ) ){
				$this->activeTab = esc_html( $_GET['tab'] ) ;
			}else $this->activeTab = 'import';
			echo '<h2 class="nav-tab-wrapper" >';
			foreach( $this->tab as $tab => $name ){
				$class = ( $tab == $this->activeTab ) ? ' nav-tab-active' : '';
				
				if($tab == 'more'){
					echo "<a class='nav-tab".esc_attr( $class )." proVersion' href='#'>".esc_attr( $name )."</a>";
				}else echo "<a class='nav-tab". esc_html( $class )." contant' href='?page=".esc_html( $this->slug )."&tab=".esc_html( $tab )."'>".esc_html( $name )."</a>";				
				
			}?>
			<?php
			echo '</h2>';		
	}
	
	public function adminSettings(){
			$this->adminTabs();	
			if( isset( $_GET['tab'] ) ){
				$this->activeTab = $_GET['tab'] ;
			}else $this->activeTab = 'import';			
		
			if(  $this->activeTab == 'settings' ) {
				
				?>
				<div class='result'><?php $this->adminProcessSettings(); ?> </div>
				<form method="post" id='<?php print esc_html( $this->plugin ); ?>Form'  >
				
				<?php
				
				settings_fields( esc_html( $this->plugin ).'general-options' );
				do_settings_sections( esc_html( $this->plugin ).'general-options' );

				wp_nonce_field( esc_html( $this->plugin ) );
				submit_button();				
				
				?></form>
				
				<?php
				
			}elseif( !isset( $_GET['tab'] ) || $this->activeTab == 'import' ){
				$this->importDisplay();
			}elseif( !isset( $_GET['tab'] ) || $this->activeTab == 'export' ){
				$this->exportProductsDisplay();				
			}elseif( $this->activeTab == 'report' ){
				 $this->LowStock(); 
			}

			
	}

	
	public function adminFooter(){ ?>	
		<hr>
		<div></div>
		<?php $this->rating(); ?>
		<button class='button-primary extendwp_extensions'><i class='fa fa-puzzle-piece'></i> <?php print esc_html__( "More Extensions", "smw" ) ; ?></button>
		<a target='_blank' class='web_logo' href='https://extend-wp.com/wordpress-premium-plugins/'>
			<img  src='<?php echo plugins_url( 'images/extendwp.png', __FILE__ ); ?>' alt='<?php print esc_html("Get more plugins by extendWP","smw"); ?>' title='<?php print esc_html("Get more plugins by extendWP","smw"); ?>' />
			
		</a><div class='get_ajax'></div>
		<?php 
	}

	public function rating(){
	?>
		<div class="notices notice-success rating is-dismissible">

			<?php esc_html_e( "You like this plugin? ", 'smw' ); ?><i class='fa fa-smile-o' ></i> <?php esc_html_e( "Then please give us ", 'smw' ); ?>
				<a target='_blank' href='https://wordpress.org/support/plugin/products-stock-manager-with-excel/reviews/#new-post'>
					<i class='fa fa-star' ></i><i class='fa fa-star' ></i><i class='fa fa-star' ></i><i class='fa fa-star' ></i><i class='fa fa-star' ></i>
				</a>

		</div> 	
	<?php	
	}
	
	public function lowStockValue(){
		?>
			<input type="number" name="<?php print esc_attr( $this->plugin )."lowStockValue";?>" id="<?php print esc_attr( $this->plugin )."lowStockValue";?>"  value="<?php if(!empty ($_POST[$this->plugin."lowStockValue"]) ){
				?><?php echo esc_attr( $_POST[$this->plugin."lowStockValue"] ); ?><?php
			}elseif(!empty(get_option($this->plugin."lowStockValue")) ){ ?><?php echo esc_attr(get_option($this->plugin."lowStockValue")); ?><?php } ?>" placeholder='<?php  esc_html_e("Low Stock Value","smw"); ?>' />

		<?php
	}

	public function importTime(){
		?>
			<input type="time" disabled class=' proVersion'  placeholder='<?php print esc_attr("Import time","smw"); ?>' />

		<?php
	}


	public function importUrl(){
		?>
			<input type="text" disabled class=' proVersion'  placeholder='<?php print esc_attr("Import URL","smw"); ?>' />

		<?php
	}

	public function autoimport(){
		
		?>
			<select disabled class='proVersion' name="<?php print  esc_attr( $this->plugin );?>autoimport" required  placeholder='full on mobile' >
				<option  value='yes' ><?php  esc_html_e("Yes","smw"); ?></option>
				<option  value='no' ><?php esc_html_e("No","smw"); ?></option>
			</select>		
		<?php
	}

	public function addStockNumber(){
		
		if( isset($_REQUEST[$this->plugin.'addStockNumber'] ) ){
			$addStockNumber =  sanitize_text_field($_REQUEST[$this->plugin.'addStockNumber']);
		}else $addStockNumber = get_option($this->plugin.'addStockNumber'); 		
		?>
		<input type='checkbox' name='<?php print $this->plugin."addStockNumber";?>' id='<?php print $this->plugin."addStockNumber";?>' value='yes' <?php if($addStockNumber === 'yes') print "checked"; ?> />
		<?php
	}
	
	public function importFrequency(){
		?>			
				<select  disabled class='proVersion' name="<?php print esc_attr( $this->plugin ).'importFrequency';?>" id="<?php print esc_attr( $this->plugin ).'importFrequency';?>"  >
						<option   value=''><?php  esc_html_e('Select...',"smw");?></option>
						<option value='1min'><?php  esc_html_e('Once every 1min',"smw");?></option>
						<option value='30min'><?php  esc_html_e('Once every 30min',"smw");?></option>
						<option value='1hour'><?php  esc_html_e('Once every 1 hour',"smw");?></option>
						<option value='6hours'><?php  esc_html_e('Once every 6 hours',"smw");?></option>
						<option value='12hours'><?php  esc_html_e('Once every 12 hours',"smw");?></option>
						<option value='daily'><?php  esc_html_e('Daily',"smw");?></option>
						<option value='weekly'><?php  esc_html_e('Weekly',"smw");?></option>
						<option value='monthly'><?php  esc_html_e('Monthly',"smw");?></option>
				</select>			
		<?php
	}	

	
	public function adminPanels(){
		add_settings_section( esc_html( $this->plugin )."general", "", null, esc_html( $this->plugin )."general-options" );		

		add_settings_field('addStockNumber',esc_html__( "Add Stock for same Product from multiple Excel Rows", "smwpro" ), array($this, 'addStockNumber'),  $this->plugin."general-options", $this->plugin."general");			
		register_setting($this->plugin."general", $this->plugin.$this->addStockNumber);
		
		add_settings_field( 'lowStockValue',esc_html__( "Low Stock Value Identifier for Reports", "smw" ), array($this, 'lowStockValue'),  esc_html( $this->plugin )."general-options", esc_html( $this->plugin )."general" );			
		register_setting( esc_html( $this->plugin )."general", esc_html( $this->plugin ).$this->lowStockValue );
		
		add_settings_field( 'autoimport',"<span class='proVersion'>".esc_html__( "Auto Import with Cron", "smw" )."<span>", array($this, 'autoimport'),  esc_html( $this->plugin )."general-options", $this->plugin."general" );			
		register_setting( esc_html( $this->plugin )."general", esc_html( $this->plugin ).$this->autoimport );

		add_settings_field( 'importUrl',"<span class='proVersion'>".esc_html__( "Auto Import Excel URL", "smw" )."<span>", array($this, 'importUrl'),  esc_html( $this->plugin )."general-options", esc_html( $this->plugin )."general");			
		register_setting($this->plugin."general", esc_html( $this->plugin ).$this->importUrl );


		add_settings_field('importFrequency',"<span class='proVersion'>".esc_html__( "Import Frequency", "smw" )."<span>", array($this, 'importFrequency'),  esc_html( $this->plugin )."general-options", $this->plugin."general");			
		register_setting( esc_html( $this->plugin )."general", esc_html( $this->plugin ).$this->importFrequency);	

		add_settings_field('importTime',"<span class='proVersion'>".esc_html__( "Import Time", "smw" )."<span>", array($this, 'importTime'),  $this->plugin."general-options", esc_html( $this->plugin )."general" );			
		register_setting( esc_html( $this->plugin )."general", esc_html( $this->plugin ).$this->importTime );
		
	}
	
	
	public function adminProcessSettings(){
		
		if($_SERVER['REQUEST_METHOD'] == 'POST' && current_user_can('administrator') ){
		
			check_admin_referer( esc_html( $this->plugin ) );
			check_ajax_referer( esc_html( $this->plugin ) );			

			if( isset( $_REQUEST[ esc_html( $this->plugin ).'lowStockValue'] ) ){
				update_option( esc_html( $this->plugin ).'lowStockValue',sanitize_text_field($_REQUEST[$this->plugin.'lowStockValue']));				
			}

			if( isset($_REQUEST[$this->plugin.'addStockNumber'])  && !empty($_REQUEST[$this->plugin.'addStockNumber']) ){
				update_option($this->plugin.'addStockNumber',sanitize_text_field($_REQUEST[$this->plugin.'addStockNumber']));				
			}else delete_option($this->plugin.'addStockNumber');				
		}
		
	}

	public function importDisplay(){?>
		<h3>
		<?php _e("UPDATE SIMPLE PRODUCTS stock,prices ","smw");?>
		<span class='proVersion'><i><?php esc_html_e("- variable products in pro","smw");?></i></span>
		</h3>	
			
		<p>
			<?php _e("Download the sample excel file, insert stock numbers and update using the form below","smw");?> 
			<a href='<?php echo plugins_url( '/example_excel/example.xlsx', __FILE__ ); ?>'>
				<?php _e("sample","smw");?>
			</a>
		
		</p>


		<div>			
			<form method="post" id='product_update' enctype="multipart/form-data" action= "<?php echo admin_url( 'admin.php?page=stock-manager-woocommerce&tab=import' ); ?>">

				<table class="form-table">
						<tr valign="top">
							<td>
								<?php wp_nonce_field('update_products'); ?>
								<input type="hidden"   id='update_products' name="update_products" value="1" />
								<div class="uploader">
									<img src="" class='userSelected'/>
									<input type="file"  required name="file" class="smwFile"  accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
								   <input type='hidden' name='start' value='2' />
								   <input type='hidden' name='action' value='update_products' />									
								</div>						
							</td>
						</tr>
				</table>
				<?php submit_button( __( 'Upload', 'smw' ) ,'primary','upload'); ?>
			</form>	
			<div class='result'>
				<?php  $this->update_products(); ?>
			</div>					
		</div>
	<?php
	}

	public function get_product_by_sku( $sku ) {
	  global $wpdb;

	  $product_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $sku ) );

	  if ( $product_id ) return $product_id;

	  return null;
	}

	public function update_products(){
		
		if(isset($_POST['update_products']) && current_user_can('administrator')){
			
			$time_start = microtime(true);

			check_admin_referer( 'update_products' );
			check_ajax_referer( 'update_products' );
			
			$filename = $_FILES["file"]["tmp_name"];
					
			if($_FILES["file"]["size"] > 0 ){
				if($_FILES["file"]["type"] === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'){
				
					$objPHPExcel = IOFactory::load($filename);
					
					$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
					$data = count($allDataInSheet);  // Here get total count of row in that Excel sheet	
					
					$stockUpdate = array();
					
					for($i=2;$i<=$data;$i++){	

						if ( is_numeric( $allDataInSheet[$i]['C'] ) && $allDataInSheet[$i]['C'] > 0 ) {
							
							$regular_price = sanitize_text_field($allDataInSheet[$i]['C']);
							
						}else $regular_price = '';

										
						if ( is_numeric( $allDataInSheet[$i]['D'] ) && $allDataInSheet[$i]['D'] >= 0  ) {
							$sale_price = sanitize_text_field($allDataInSheet[$i]['D']);
				
						}else $sale_price = '';					
						
									
						if ( is_numeric( $allDataInSheet[$i]['E'] ) && $allDataInSheet[$i]['E'] >= 0 ) {
							$stock = sanitize_text_field($allDataInSheet[$i]['E']);		
						}else $stock = '';	
						
										
						if ( !empty($allDataInSheet[$i]['F'] ) ) {
							$stock_status = sanitize_text_field($allDataInSheet[$i]['F']);	
						}else $stock_status = '';
						
						
						if( $allDataInSheet[$i]['C'] < 0 ) print "<p class='error'>".$id ." ".__( 'regular price cannot be less than 0', 'smw' ).".</p>";
						if( $allDataInSheet[$i]['D'] < 0 ) print "<p class='error'>".$id ." ".__( 'sale price cannot be less than 0', 'smw' ).".</p>";
						if( $allDataInSheet[$i]['E'] < 0 ) print "<p class='error'>".$id ." ".__( 'stock number cannot be less than 0', 'smw' ).".</p>";
						
						$post_id = (int)$allDataInSheet[$i]['A'];		
						$sku = sanitize_text_field( $allDataInSheet[$i]['B'] );					

						if ( !empty( $post_id ) ) { // POST ID NOT USED 
							$id = $post_id;

						}elseif( empty( $post_id ) && $sku != '' ){
							$id = $this->get_product_by_sku( $sku );

						}else {
							print "<p class='warning'>".esc_html__( 'ID or SKU not provided', 'smw' ).".</p>";
						}

						global $product;
						global $post;				
						global $woocommerce;
						$product = wc_get_product( $id );
						
						if ( $product ){
							if( $product->is_type( 'simple' ) ) {
								
								
								if( isset($stock) && $stock!==''){
									
									if( !empty(get_option($this->plugin.'addStockNumber')) ) {
										
																				
										$stockUpdate[] = array( "id"=> $id, "stock"=>$stock );

										$result = [];
										$map = [];
										$stock;
										foreach ($stockUpdate as $subarray) {
											$id = $subarray['id'];

											// First time we encounter the id thus we can safely push it into result
											if (!key_exists($id, $map)) {
												// array_push returns the number of elements
												// since we push one at a time we can directly get the index.
												$index = array_push($result, $subarray) - 1;
												$map[$id] = $index;

												continue;
											}

											// If the id is already present in our map we can simply
											// update the running sum for the values and concat the
											// product names.
											$index = $map[$id];
											$result[$index]['stock'] += $subarray['stock'];
											$stock = $result[$index]['stock'];
										}
										update_post_meta( $id, '_stock',$stock );
						
									}else update_post_meta( $id, '_stock',$stock );
									
								}								
								
								if($stock_status!=='')update_post_meta( $id, '_stock_status', $stock_status );
								if($regular_price!=='')update_post_meta( $id, '_regular_price', $regular_price );
								if($sale_price!=='')update_post_meta( $id, '_sale_price', $sale_price );
								if(isset($sale_price) && $sale_price!=='' && $sale_price != 0 ){
									update_post_meta( $id, '_price', $sale_price );	
								}elseif( isset($regular_price) && $regular_price!=='' && $regular_price != 0 ){
									update_post_meta( $id, '_price', $regular_price );
								}
								if( $sale_price == '0' ) update_post_meta( $id, '_sale_price', '' );
								
								// update the modified date on post 
								$postid = array(
									'ID' => $id,
								);								
								wp_update_post( $postid );	
								
								wc_delete_product_transients( $id );
							
								print "<p class='success'>".$id ." ".__( 'updated successfully', 'smw' ).".</p>";							
								
							}		
						}else print "<p class='success'>".$id ." ".__( 'Product does not exist', 'smw' ).".</p>";
						
						
					}
					
				}
			}
				
		}
	
	}

	
	public function exportProductsDisplay(){?>
		<h2>
			<?php esc_html_e( 'EXPORT SIMPLE PRODUCTS in the correct format for Update', 'smw' ) ?>
			<span class='proVersion'><i><?php esc_html_e("- variable products in pro","smw");?></i></span>
		</h2>
		<p>
			<i><?php esc_html_e( 'Important Note: always save the generated export file in xlsx format to a new excel for import use.', 'smw' ) ?></i>
		</p>
	   <div>	
			<?php  print "<div class='result'>". $this->exportProductsForm()."</div>"; ?>
	   </div>
	   <?php
	}
	
	public function exportProductsForm(){
	
		$query = new WP_Query( array(
			'post_type' => 'product',				
			'posts_per_page' => '-1',								
		) );
		if($query ->have_posts()){	
		?>
				<p class='exportToggler button button-secondary warning   btn btn-danger'><i class='fa fa-eye '></i> 
					<?php esc_html_e('Filter & Fields to Show', 'smw');?>
				</p>
				
				
				<form name='exportProductsForm' id='exportProductsForm' method='post' action= "<?php echo admin_url( 'admin.php?page=stock-manager-woocommerce&tab=export'); ?>" >	
					<table class='wp-list-table widefat fixed table table-bordered'>	

						<tr>
							<td>
								<?php esc_html_e('Keywords', 'smw');?>
							</td>
							<td>
								<input type='text' name='keyword' id='keyword' placeholder='<?php esc_html_e('Search term', 'smw');?>'/>
							</td>
							<td></td><td></td>
						</tr>
						<tr>
							<td><?php esc_html_e('SKU', 'smw');?></td>
							<td>
								<input type='text' name='sku' id='sku' placeholder='<?php esc_html_e('Search by SKU', 'smw');?>'/>
							</td>
							<td></td><td></td>
						</tr>
						<tr>
							<td>
								<?php esc_html_e('Regular Price', 'smw');?>
							</td>
							<td>
								<input type='number' name='regular_price' id='regular_price' placeholder='<?php esc_html_e('Regular Price', 'smw');?>'/>
							</td>
							<td>
								<?php esc_html_e('Regular Price Selector', 'smw');?>
							</td>
							<td>
								<select name='price_selector' id='price_selector'>
									<option value=">">></option>
									<option value=">=">>=</option>
									<option value="<="><=</option>
									<option value="<"><</option>
									<option value="==">==</option>
									<option value="!=">!=</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<?php esc_html_e('Sale Price', 'smw');?>
							</td>
							<td>
								<input type='number' name='sale_price' id='sale_price' placeholder='<?php esc_html_e('Sale Price', 'smw');?>'/>
							</td>
							
							<td>
								<?php esc_html_e('Sale Price Selector', 'smw');?>
							</td>
							<td>
								<select name='sale_price_selector' id='sale_price_selector' >
									<option value=">">></option>
									<option value=">=">>=</option>
									<option value="<="><=</option>
									<option value="<"><</option>
									<option value="==">==</option>
									<option value="!=">!=</option>						
								</select>	
							</td>
						</tr>

						<tr>
							<td>
							<?php esc_html_e('Limit Results', 'smw');?>
							</td>
							<td>
								<input type='number' min="1" max="100000" style='width:100%;'  name='posts_per_page' id='posts_per_page' placeholder='<?php esc_html_e('Number to display..', 'smw');?>' />
							</td>
							<input type='hidden' name='offset' style='width:100%;' id='offset' placeholder='<?php esc_html_e('Start from..', 'smw');?>' />
							<input type='hidden' name='start' /><input type='hidden' name='total' />
							
							<td></td><td></td>
						</tr>
						
					</table>
					
					<?php $taxonomy_objects = get_object_taxonomies( 'product', 'objects' ); ?>


					<table class='wp-list-table widefat fixed table table-bordered fields_checks'>
						<legend>
							<h2>
								<?php esc_html_e('FIELDS TO SHOW apart from stock, prices, id, sku and title  - PRO VERSION', 'smw');?>
							</h2>
						</legend>
					
						<?php
						$cols = array( "description","excerpt","post_name","_variation_description",'_weight','_width','_length','_height','_downloadable','_download_limit','_download_expiry','_virtual','_purchase_note','_upsell_ids','_crosssell_ids','_thumbnail_id','_product_image_gallery','_sold_individually','_backorders','_featured' );	?>
						
						<tr  class='proVersion'>
						
						<?php $checked = 'checked';
						foreach( $cols as $col){					
							print "<td style='float:left'>
								<input type='checkbox' disabled class='fieldsToShow'  id='toShow".$col."' name='toShow".$col."' value='1'/>
								<label for='".$col."'>". $col. "</label>
								</td>";
						} 
						
						foreach( $taxonomy_objects as $voc){
									
							print "<td style='float:left'>
							<input type='checkbox' disabled class='fieldsToShow' ".$checked." name='toShow".esc_attr( $voc->name )."' value='1'/>
							<label for='".str_replace( '_',' ',esc_attr( $voc->name ) )."'>". str_replace( '_',' ',esc_attr( $voc->name ) ). "</label>
							</td>";
						}
						?>
						
						</tr>
					</table>			
							
					<input type='hidden' name='columnsToShow' value='1'  />
					<input type='hidden' id='action' name='action' value='smw_exportProducts' />
					<?php wp_nonce_field( 'columnsToShow' ); ?>

					<?php submit_button( esc_html( 'Search', 'smw' ),'primary','Search' ); ?>

				</form>
			
			<div class='resultExport'>
				<?php $this->exportProducts(); ?>
			</div>
		<?php			
		}else print esc_html__("No Products to display...","smw"); //end of checking for products
	}	
	
	public function exportProducts(){

		if($_SERVER['REQUEST_METHOD'] === 'POST' && current_user_can('administrator') && $_REQUEST['columnsToShow'] ){
			
			check_admin_referer( 'columnsToShow' );
			check_ajax_referer( 'columnsToShow' );
		
			$cat_query='1';
			$meta_query = array();
			
			if(!empty($_POST['vocabularySelect']) && !empty($_POST['taxTerm'] ) ){
				$this->category = sanitize_text_field( $_POST['vocabularySelect'] );
				$this->term = sanitize_text_field( $_POST['taxTerm'] );			
					 $cat_query = array(
						'relation' => 'AND',
						array(
							'taxonomy' => 'product_type',
							'field'    => 'slug',
							'terms'    => 'simple', 
						),					 
						array(
						'taxonomy' => $this->category,
						'field' => 'id',
						'terms'    => $this->term,
						 ),
					);							
			}else{
					 $cat_query = array(
						array(
							'taxonomy' => 'product_type',
							'field'    => 'slug',
							'terms'    => 'simple', 
						),
					);
			}
			
			if(!empty($_POST['price_selector']))$this->price_selector = (int)$_POST['price_selector'] ;	
			if(!empty($_POST['sale_price_selector']))$this->sale_price_selector =  (int)$_POST['sale_price_selector'] ;			
			
			if(!empty($_POST['sku'])){ 
				$this->sku = sanitize_text_field( $_POST['sku'] );
				$sku = array('key'     => '_sku','value'   => $this->sku,'compare' => 'LIKE');
				
				array_push($meta_query,$sku );
			}else $this->sku='';
			
			if(!empty($_POST['keyword']))  $this->keyword = sanitize_text_field( $_POST['keyword'] );
			
			if(!empty($_POST['regular_price'])){ 
				$this->regular_price = (float) $_POST['regular_price'];
				$regular = array('key'     => '_regular_price','value'   => $this->regular_price,'type' => 'numeric','compare' => $this->price_selector);
				array_push($meta_query,$regular );
			}else $this->regular_price='';	
			
			if(!empty($_POST['sale_price'])){
				$this->sale_price = (float)$_POST['sale_price'] ;
				$price = array('key'     => '_sale_price','value'   => $this->sale_price,'type' => 'numeric','compare' => $this->sale_price_selector);				
			}else $this->sale_price='';
			
			if(!empty($_POST['posts_per_page'])){
				$this->posts_per_page = (int)$_POST['posts_per_page'] ;
			}else $this->posts_per_page = '-1';
			
			if(!empty($_POST['offset'])){
				$this->offset = (int)$_POST['offset'];
			}else $this->offset = '-1';
			
			$query = new WP_Query( array(
				'post_type' => 'product',
				's' => $this->keyword,
				'meta_and_tax' => TRUE,
				'tax_query'  => $cat_query,			
				'meta_query' => $meta_query,				
				'posts_per_page' => (int)$this->posts_per_page,	
				'offset' => (int)$this->offset,
				) );
				
				
			if ( $query ->have_posts() ){
				
				$query->the_post();
			
				$i=0;
				?>
				<p class='message error'>
					<?php esc_html_e( 'Wait... Download is loading...', 'smw' );?>
					<b class='totalPosts'  ><?php print esc_html($query->post_count);?></b>					
				</p>

				<?php		
				if($query->post_count <= 500){
					$start=0;
				}else $start=500;
				print " <b class='startPosts' style='display:none'>".esc_html($start)."</b>";
			
			}

			?>

			<div class='exportTableWrapper'>
				<table id='toskuExport'>
					<thead>
						<tr> 
							<th>
								<?php esc_html_e('ID', 'smw');?>
							</th>
							<th>
								<?php esc_html_e('SKU', 'smw');?>
							</th>
							<th>
								<?php esc_html_e('REGULAR PRICE', 'smw');?>
							</th>
							<th>
								<?php esc_html_e('SALE PRICE', 'smw');?>
							</th>
							<th>
								<?php esc_html_e('STOCK', 'smw');?>
							</th>
							<th>
								<?php esc_html_e('STOCK STATUS', 'smw');?>
							</th>
							<th>
								<?php esc_html_e('PRODUCT TITLE', 'smw');?>
							</th>								
						</tr>
					</thead>
					<tbody class='tableExportAjax'>
					</tbody>	
				</table>
			</div>	
			
			<?php	
			
		}//check request						
	}


	public function smw_exportProducts(){

		if($_SERVER['REQUEST_METHOD'] === 'POST' && current_user_can('administrator') ){
					
			check_admin_referer( 'columnsToShow' );
			check_ajax_referer( 'columnsToShow' );
			
			$cat_query ='';
			$meta_query = array();
			
			if(!empty($_POST['vocabularySelect']) && !empty($_POST['taxTerm'] ) ){
				$this->category = sanitize_text_field( $_POST['vocabularySelect'] );
				$this->term = sanitize_text_field( $_POST['taxTerm'] );			
					 $cat_query = array(
						'relation' => 'AND',
						array(
							'taxonomy' => 'product_type',
							'field'    => 'slug',
							'terms'    => 'simple', 
						),					 
						array(
						'taxonomy' => $this->category,
						'field' => 'id',
						'terms'    => $this->term,
						 ),
					);							
			}else{
					 $cat_query = array(
						array(
							'taxonomy' => 'product_type',
							'field'    => 'slug',
							'terms'    => 'simple', 
						),
					);
			}
			
			$price_selector = (int)$_POST['price_selector'];	
			$sale_price_selector =  (int)$_POST['sale_price_selector'] ;			
				
			if(!empty($_POST['sku'])){ 
				$sku = sanitize_text_field( $_POST['sku'] );
				$sku = array('key'     => '_sku','value'   => $sku,'compare' => '=');
				
				array_push($meta_query,$sku );
			}else $sku='';
			if(!empty($_POST['keyword'])) {
				$keyword = sanitize_text_field( $_POST['keyword'] );
			}
			if(!empty($_POST['regular_price'])){ 
				$regular_price = (int) $_POST['regular_price'];
				$regular = array('key'     => '_regular_price','value'   => $regular_price,'type' => 'numeric','compare' => $price_selector);
				array_push($meta_query,$regular );
			}else $regular_price='';	
			if(!empty($_POST['sale_price'])){
				$sale_price = (int)$_POST['sale_price'] ;
				$price = array('key'     => '_sale_price','value'   => $sale_price,'type' => 'numeric','compare' => $sale_price_selector);			
			}else $sale_price='';
				
			if(!empty($_POST['posts_per_page'])){
				$posts_per_page = (int)$_POST['posts_per_page'];
			}else $posts_per_page = '-1';
				
			if(!empty($_POST['offset'])){
				$offset = (int)$_POST['offset'] ;
			}else $offset = '0';
				
			$query = new WP_Query( 
				array(
					'post_type' => 'product',
					's' => $keyword,
					'meta_and_tax' => TRUE,
					'tax_query'  => $cat_query,			
					'meta_query' => $meta_query,				
					'posts_per_page' => (int)$posts_per_page,	
					'offset' => (int)$offset,
				) 
			);				
				
				
				
			if ( $query ->have_posts() ){
										
				while ( $query->have_posts() ){
									
					$query->the_post();
					global $product;
					global $post;				
					global $woocommerce;
					$product = wc_get_product( get_the_ID() );
					
					if ( $product->is_type( 'simple' ) ) { ?>						
						<tr>
							<td><?php print esc_html( get_the_ID()) ;?></td>
							<td><?php print esc_html( get_post_meta(get_the_ID(), "_sku", true ) ); ?></td>
							<td><?php print esc_html( get_post_meta(get_the_ID(),  "_regular_price", true ) ); ?></td>
							<td><?php print esc_html( get_post_meta(get_the_ID(),  "_sale_price", true ) ); ?></td>
							<td><?php print esc_html( get_post_meta(get_the_ID(),  "_stock", true ) ); ?></td>
							<td><?php print esc_html( get_post_meta(get_the_ID(),  "_stock_status", true ) ); ?></td>
							<td><?php print esc_html( get_the_title() ) ;?></td>
										
						<?php print "</tr>";					
					}
				}//end while

				die;												
			}else print "<p class='warning' >".esc_html__('No Product Found', 'smw')."</p>";//end if						
		}//check request						
	}
	
	public function LowStock(){


			$message='';
			
			$args = array(
				'post_type' => 'product',
				'meta_key' => '_stock',
				'orderby' => 'meta_value_num',
				'order' => 'ASC',
				'posts_per_page' => -1,
				
			);
			$loop = new WP_Query( $args );
			
			if( !empty(get_option( esc_html( $this->plugin ).'lowStockValue')) ) {
				$lowStockValue = esc_html( get_option( esc_html( $this->plugin ).'lowStockValue') );
			}else $lowStockValue = 5;
			
			if($loop->have_posts()){
			?>
			<div id='noselling'>
			<h3 class='text-center title'><i class='fa fa-pie-chart' ></i> <?php  esc_html_e('General Stock Monitoring','smw' );?></h3>
			<p><i><?php  esc_html_e('Low stock is considered the number: '. $lowStockValue,'smw' );?></i></p>
			
			<table class='widefat'>
			<thead>
				<tr>
					<th><?php  esc_html_e('Title','smw' );?></th>
					<th><?php  esc_html_e('Stock','smw' );?></th>
					<th><?php  esc_html_e('Sales','smw' );?></th>
				</tr>
			</thead>
			<tbody>
			<?php
			while ( $loop->have_posts() ) : $loop->the_post(); 
			global $product; 
			global $post;



			$stockManage = get_post_meta( get_the_ID(), '_manage_stock', true );
			if($stockManage =='yes' ){

				if($this->get_product_total_sales(get_the_ID())!='' ){	
					?><tr>
					<td>
						<a href="<?php esc_url( the_permalink() ); ?>"  title="<?php the_title(); ?>"><?php the_title(); ?></a>
					</td>
					<td>
					<?php
					if ( $product->get_stock_quantity()  ) { // if manage stock is enabled 
						if ( number_format( $product->get_stock_quantity(),0,'','' ) < $lowStockValue && number_format( $product->get_stock_quantity(),0,'','' ) >0 ) { // if stock is low
						  echo number_format($product->get_stock_quantity(),0,'','');
						}
						elseif ( number_format( $product->get_stock_quantity(),0,'','' ) <=0 ) { // if stock is low
							echo '0';
						}else {
							echo '<div class="remaining">' . number_format($product->get_stock_quantity(),0,'','') . '</div>'; 
						}
					}
					?>
					</td>
					<td><?php print wc_price( $this->get_product_total_sales( get_the_ID() ) ); ?>
					</td>
					
					</tr>
					<?php	
					//}
				}else{
					if ( number_format( $product->get_stock_quantity(),0,'','' ) <= $lowStockValue ) { // if stock is low
					?><tr>
					<td>
						<a href="<?php esc_url( the_permalink() ); ?>"  title="<?php the_title(); ?>"><?php the_title(); ?></a>
					</td>
					<td>
					<?php
					if (  $product->get_stock_quantity() >0 ) { // if manage stock is enabled 
						echo  number_format($product->get_stock_quantity(),0,'','' );
					}
					if ( $product->get_stock_quantity() <=0 ) { // if manage stock is enabled 
						echo '0';
					}			
					?>
					</td>
					<td><?php print "-"; ?>
					</td>
					</tr>	
					<?php
					}
				}
			
			}
	?>
	
	<?php endwhile; ?>
	<?php wp_reset_query(); ?>	
	
		</tbody> 
		<input type="text" class="search" placeholder="<?php  esc_html_e('Search...','smw' );?>"></input>
		</table>

		<?php print "<h3 class='text-center'>". esc_html( $message )."</h3>"; ?>
		</div>
	<?php
	
		}else print "<h3 class='text-center'>".__('No products','smw' )."</h3>";
		
	}

	public function get_product_total_sales( $productid ) {
		 
		global $wpdb;
		global $product;
		 
		$total_sales = $wpdb->get_var( "SELECT SUM( order_item_meta__line_total.meta_value) as order_item_amount 
			FROM {$wpdb->posts} AS posts
			INNER JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON posts.ID = order_items.order_id
			INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta__line_total ON (order_items.order_item_id = order_item_meta__line_total.order_item_id)
				AND (order_item_meta__line_total.meta_key = '_line_total')
			INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta__product_id_array ON order_items.order_item_id = order_item_meta__product_id_array.order_item_id 
			WHERE posts.post_type IN ( 'shop_order' )
			AND posts.post_status IN ( 'wc-completed' ) AND ( ( order_item_meta__product_id_array.meta_key IN ('_product_id','_variation_id') 
			AND order_item_meta__product_id_array.meta_value IN ('{$productid}') ) );" );
		 
		return $total_sales ;
	}	

	public function extensions(){
		
		if( is_admin() && current_user_can( 'administrator' ) ){
			
			$response = wp_remote_get( "https://extend-wp.com/wp-json/products/v2/product/category/excel" );
			if( is_wp_error( $response ) ) {
				return;
			}		
			$posts = json_decode( wp_remote_retrieve_body( $response ) );

			if( empty( $posts ) ) {
				return;
			}

			if( !empty( $posts ) ) {
				echo "<div id='extendwp_extensions_popup'>";
					echo "<div class='extendwp_extensions_content '>";	
						?>
						<span class="extendwp_close">&times;</span>
						<h2><i><?php esc_html_e( 'More Plugins to Make your life easier by ExtendWP!','smw' ); ?></i></h2>
						<hr/>
						<?php
						print "<div class='extend_flex'>";
						foreach( $posts as $post ) {
							
							if( $post->class == 'StockManagerWooCommercePro'   ){
								
								/*
								echo "<div class='columns3 extendwp_opacity'><img src='".esc_url( $post->image )."' />
								<h3>". esc_html( $post->title ) . "</h3>
								<div>". wp_kses( $post->excerpt, $this->allowed_html )."</div>
								<h4><i>". esc_html__( 'installed', 'smw' ) . "</i> <i class='fa fa-2x fa-check'></i></h4>
								</div>";
								*/
							}else{
								
								echo "<div class='columns3'><a target='_blank' href='".esc_url( $post->url )."' /><img src='".esc_url( $post->image )."' /></a>
								<h3><a target='_blank' href='".esc_url( $post->url )."' />". esc_html( $post->title ) . "</a></h3>
								<div>". wp_kses( $post->excerpt, $this->allowed_html )."</div>
								<a class='button_extensions button-primary' target='_blank' href='".esc_url( $post->url )."' />". esc_html__( 'Get it here', 'smw' ) . " <i class='fa fa-angle-double-right'></i></a>
								</div>";								
							}
							

						}
						print "</div>";
					echo '</div>';
				echo '</div>';	
			}
		
		}
	}	
 }