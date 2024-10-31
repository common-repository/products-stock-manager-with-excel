<?php
/*
 * Plugin Name: Products Stock Manager with Excel for WooCommerce Inventory
 * Description: Update your WooCommerce Products Stock and Prices with the power of Excel, get stock reports - go pro & automate
 * Version: 2.0
 * Author: extendWP
 * Author URI: https://extend-wp.com
 *
 * WC requires at least: 2.2
 * WC tested up to: 8.4
 *  
 * Requires PHP: 7.1 
 * License: GPL2
 * Created On: 07-07-2020
 * Updated On: 29-12-2023
 * Text Domain: smw
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

include_once( plugin_dir_path(__FILE__) ."/class-main.php");

			
class StockManagerWooCommerce extends StockManagerWooCommerceInit{
	
		public $plugin = 'stockManagerWooCommerce';		
		public $name = 'Products Stock Manager with Excel for WooCommerce';
		public $shortName = 'Stock Manager';
		public $slug = 'stock-manager-woocommerce';
		public $dashicon = 'dashicons-cart';
		public $proUrl = 'https://extend-wp.com/product/products-stock-manager-excel-woocommerce';
		public $menuPosition ='50';
		public $localizeBackend;
		public $localizeFrontend;
		public $description = 'Update your WooCommerce Products Stock and Prices with the power of Excel, get stock reports - go pro & automate';
 
		public function __construct() {		
			
			add_action('plugins_loaded', array($this, 'translate') );
			add_action("admin_init", array($this, 'adminPanels') );	

			add_action('admin_enqueue_scripts', array($this, 'BackEndScripts') );
			
			add_action('admin_menu', array($this, 'SettingsPage') );
			add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array($this, 'Links') );

			add_action( 'wp_ajax_nopriv_update_products', array($this,'update_products') );
			add_action( 'wp_ajax_update_products', array($this,'update_products') );
			
			add_action( 'wp_ajax_smw_exportProducts',  array($this,'smw_exportProducts') );
			add_action( 'wp_ajax_nopriv_smw_exportProducts',   array($this,'smw_exportProducts') );

			add_action( 'wp_ajax_nopriv_extensions', array( $this,'extensions' ) );
			add_action( 'wp_ajax_extensions', array( $this,'extensions' ) );
			
			add_action("admin_footer", array($this,"proModal" ) );
			
			register_activation_hook( __FILE__,  array($this, 'onActivation') );
			register_deactivation_hook( __FILE__,  array($this, 'onDeactivation') );
			
			// HPOS compatibility declaration
			
			add_action( 'before_woocommerce_init', function() {
				if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
					\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
				}
			} );


			// deactivation survey 

			include( plugin_dir_path(__FILE__) .'/lib/codecabin/plugin-deactivation-survey/deactivate-feedback-form.php');	
			add_filter('codecabin_deactivate_feedback_form_plugins', function($plugins) {

				$plugins[] = (object)array(
					'slug'		=> 'products-stock-manager-excel',
					'version'	=> '2.0'
				);

				return $plugins;

			});	
			
			register_activation_hook( __FILE__, array($this,'notification_hook'  )  );
			
			add_action( 'admin_notices', array( $this,'notification' ) );
			add_action( 'wp_ajax_nopriv_push_not',array( $this, 'push_not'  ) );
			add_action( 'wp_ajax_push_not', array( $this, 'push_not' ) );

		
		}


		public function onActivation(){
			require_once(ABSPATH .'/wp-admin/includes/plugin.php');
			$pro = "/stock-manager-woocommerce-pro/stock-manager-woocommerce-pro.php";
			deactivate_plugins($pro);				
		}
		function onDeactivation() {
		}
		
	    public function print_scripts() {
	               //if want to print some inline script
	    }		

		public function translate() {
	         load_plugin_textdomain( $this->plugin, false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
	    }
		
		public function BackEndScripts( $hook ){
			
			//$screen = get_current_screen();
			//var_dump( $screen );
			//if ( 'woocommerce_page_stock-manager-woocommerce'  !== $screen->base )
				//return;
	
			wp_enqueue_style( esc_html( $this->plugin )."adminCss", plugins_url( "/css/backend.css?v=adj", __FILE__ ) );	
			wp_enqueue_style( esc_html( $this->plugin )."adminCss");	
			
			wp_enqueue_script('jquery');
			wp_enqueue_media();
			wp_enqueue_style( 'wp-color-picker' ); 
			wp_enqueue_script("jquery-ui-tabs");
			wp_enqueue_script( esc_html( $this->plugin ).'xlsx', plugins_url( "/js/xlsx.js", __FILE__ ), array('jquery') , null, true );	
			wp_enqueue_script( esc_html( $this->plugin ).'xlsx');			

			wp_enqueue_script( esc_html( $this->plugin ).'filesaver', plugins_url( "/js/filesaver.js", __FILE__ ), array('jquery') , null, true );	
			wp_enqueue_script( esc_html( $this->plugin ).'filesaver');

			wp_enqueue_script( esc_html( $this->plugin ).'tableexport', plugins_url( "/js/tableexport.js", __FILE__ ), array('jquery') , null, true );	
			wp_enqueue_script( esc_html( $this->plugin ).'tableexport');
			
			if( ! wp_script_is( esc_html( $this->plugin )."_fa", 'enqueued' ) ) {
				wp_enqueue_style( esc_html( $this->plugin )."_fa", plugins_url( '/css/font-awesome.min.css', __FILE__ ) );
			}
			
			wp_enqueue_script( $this->plugin."adminJs", plugins_url( "/js/backend.js?v=1fss" , __FILE__ ) , array('jquery','wp-color-picker','jquery-ui-tabs') , null, true);	
			

			$this->localizeBackend = array( 
				'RestRoot' => esc_url_raw( rest_url() ),
				'plugin_url' => plugins_url( '', __FILE__ ),
				'siteUrl'	=>	site_url(),
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'wp_rest' ),
				'plugin_wrapper'=> esc_html( $this->plugin ),
				'exportfile' => plugins_url( '/js/tableexport.js', __FILE__ )
			);		
			wp_localize_script( esc_html( $this->plugin )."adminJs", $this->plugin , $this->localizeBackend );
			wp_enqueue_script( esc_html( $this->plugin )."adminJs" );

		}	
		

		public function SettingsPage(){
			add_submenu_page( 'woocommerce', esc_html( $this->shortName ), esc_html( $this->shortName ), 'manage_options', esc_html( $this->slug ), array($this, 'init') );			
		}		
		
		public function Links($links){
			$mylinks[] =  '<a href="' . admin_url( "admin.php?page=".$this->slug ) . '">'.esc_html__( "Settings", "smw" ).'</a>';
			return array_merge( $links, $mylinks );			
		}


		
		public function init(){
			print "<div class='".esc_html( $this->plugin )."'>";

			
				$this->adminHeader();
				$this->adminSettings();
				$this->adminFooter();
			print "</div>";		
		}
		
		
		public function proModal(){ ?>
			<div style='display:none' id="<?php print esc_html( $this->plugin ).'Modal'; ?>">
			  <!-- Modal content -->
			  <div class="modal-content">
				<div class='<?php print esc_html( $this->plugin ); ?>clearfix'><span class="close">&times;</span></div>
				<div class='<?php print esc_html( $this->plugin ); ?>clearfix'>
					<div class='<?php print esc_html( $this->plugin ); ?>columns2'>
						<center>
							<img style='width:90%' src='<?php echo esc_url( plugins_url( 'images/'.esc_html( $this->slug ).'-pro.png', __FILE__ ) ); ?>' style='width:100%' />
						</center>
					</div>
					
					<div class='<?php print esc_html( $this->plugin ); ?>columns2'>
						<h3><?php esc_html_e('Go PRO and get more important features!','cwcb' ); ?></h3>
						<p><i class='fa fa-check'></i> <?php esc_html_e('Update stock & prices with excel for product variations with Excel','smw' ); ?></p>
						<p><i class='fa fa-check'></i> <?php esc_html_e('Export to Excel extra product fields','smw' ); ?></p>
						<p><i class='fa fa-check'></i> <?php esc_html_e('Automatically update with Cron from remote location','smw' ); ?></strong></p>
						<p><i class='fa fa-check'></i> <?php esc_html_e('Automatically update from Google Spreadsheet','smw' ); ?></strong></p>
						<p><i class='fa fa-check'></i> <?php esc_html_e('Predefine Update Mapping fields in settings','smw' ); ?></strong></p>
						<p><i class='fa fa-check'></i> <?php esc_html_e('.. and a lot more!','smw' ); ?></p>
						<p class='bottomToUp'><center><a target='_blank' class='proUrl' href='<?php print esc_url( $this->proUrl ); ?>'><?php esc_html_e('GET IT HERE', 'smw' ); ?></a></center></p>
					</div>
				</div>
			  </div>
			</div>		
			<?php
		}			




		// Email notification form
			
		public function notification_hook() {
			set_transient( 'stock_manager_notification', true );
		}

		

		public function notification(){

			/* Check transient, if available display notice */
			if( get_transient( 'stock_manager_notification' ) ){
				?>
				<div class="updated notice  stock_manager_notification">
				<a href="#" class='dismiss' style='float:right;padding:4px' >close</a>
					<h3>Products Stock Manager | <?php esc_html_e( "Add your Email below & get ", 'smw' ); ?><strong>10%</strong><?php esc_html_e( " in our pro plugins! ", 'smw' ); ?></h3>
					<p><i><?php esc_html_e( "By adding your email you will be able to use your email as coupon to a future purchase at ", 'smw' ); ?><a href='https://extend-wp.com' target='_blank' >extend-wp.com</a></i></p>
					<form method='post' id='stock_manager_signup'>
						<input required type='email' name='woopei_email' />
						<?php submit_button(__( 'Sign up!', 'smw' ),'primary','Sign up!'); ?>
					</form>
				</div>
				<?php
		
			}
		}
		

		public function push_not(){
			
			delete_transient( 'stock_manager_notification' );
					
		}



		
		
}
$instantiate = new StockManagerWooCommerce();