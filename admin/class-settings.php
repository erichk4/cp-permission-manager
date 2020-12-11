<?php
	/**
	 * Handles the settings screen.
	 *
	 * @package    Members
	 * @subpackage Admin
	 * @author     Justin Tadlock <justintadlock@gmail.com>
	 * @copyright  Copyright (c) 2009 - 2018, Justin Tadlock
	 * @link       https://themehybrid.com/plugins/members
	 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
	 */

	namespace Members\Admin;

	/**
	 * Sets up and handles the plugin settings screen.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	final class Settings_Page
	{

		/**
		 * Admin page name/ID.
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $name = 'members-settings';

		/**
		 * Settings page name.
		 *
		 * @since  1.0.0
		 * @access public
		 * @var    string
		 */
		public $settings_page = '';

		/**
		 * Holds an array the settings page views.
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    array
		 */
		public $views = array();

		/**
		 * Constructor method.
		 *
		 * @return void
		 * @since  1.0.0
		 * @access public
		 */
		private function __construct()
		{
		}

		/**
		 * Returns the instance.
		 *
		 * @return object
		 * @since  1.0.0
		 * @access public
		 */
		public static function get_instance()
		{

			static $instance = null;

			if ( is_null( $instance ) ) {
				$instance = new self;
				$instance->includes();
				$instance->setup_actions();
			}

			return $instance;
		}

		/**
		 * Loads settings files.
		 *
		 * @return void
		 * @since  2.0.0
		 * @access private
		 */
		private function includes()
		{

			// Include the settings functions.
			require_once( members_plugin()->dir . 'admin/functions-settings.php' );

			// Load settings view classes.
			require_once( members_plugin()->dir . 'admin/views/class-view.php' );
			require_once( members_plugin()->dir . 'admin/views/class-view-general.php' );
			require_once( members_plugin()->dir . 'admin/views/class-view-roles.php' );
		}

		/**
		 * Sets up initial actions.
		 *
		 * @return void
		 * @since  2.0.0
		 * @access private
		 */
		private function setup_actions()
		{

			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		}

		/**
		 * Register a view.
		 *
		 * @param object $view
		 *
		 * @return void
		 * @since  2.0.0
		 * @access public
		 */
		public function register_view( $view )
		{

			if ( ! $this->view_exists( $view->name ) ) {
				$this->views[ $view->name ] = $view;
			}
		}

		/**
		 * Check if a view exists.
		 *
		 * @param string $name
		 *
		 * @return bool
		 * @since  2.0.0
		 * @access public
		 */
		public function view_exists( $name )
		{

			return isset( $this->views[ $name ] );
		}

		/**
		 * Unregister a view.
		 *
		 * @param string $name
		 *
		 * @return void
		 * @since  2.0.0
		 * @access public
		 */
		public function unregister_view( $name )
		{

			if ( $this->view_exists( $name ) ) {
				unset( $this->view[ $name ] );
			}
		}

		/**
		 * Sets up custom admin menus.
		 *
		 * @return void
		 * @since  1.0.0
		 * @access public
		 */
		public function admin_menu()
		{

			// Create the settings page.
			$this->settings_page = add_security_page(
				esc_html_x( 'Roles and Capabilities', 'admin screen', 'members' ),
				esc_html_x( 'Roles and Capabilities', 'admin screen', 'members' ),
				'cp-permission-manager',
				array( $this, 'settings_page' )
			);

			if ( $this->settings_page ) {

				do_action( 'members_register_settings_views', $this );

				uasort( $this->views, 'members_priority_sort' );

				// Register setings.
				add_action( 'admin_init', array( $this, 'register_settings' ) );

				// Page load callback.
				add_action( "load-{$this->settings_page}", array( $this, 'load' ) );

				// Enqueue scripts/styles.
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
			}
		}

		/**
		 * Runs on page load.
		 *
		 * @return void
		 * @since  2.0.0
		 * @access public
		 */
		public function load()
		{

			// Print custom styles.
			add_action( 'admin_head', array( $this, 'print_styles' ) );

			// Add help tabs for the current view.
			$view = $this->get_view( members_get_current_settings_view() );

			if ( $view ) {
				$view->load();
				$view->add_help_tabs();
			}
		}

		/**
		 * Get a view object
		 *
		 * @param string $name
		 *
		 * @return object
		 * @since  2.0.0
		 * @access public
		 */
		public function get_view( $name )
		{

			return $this->view_exists( $name ) ? $this->views[ $name ] : false;
		}

		/**
		 * Print styles to the header.
		 *
		 * @return void
		 * @since  2.0.0
		 * @access public
		 */
		public function print_styles()
		{ ?>

            <style type="text/css">
                .settings_page_members-settings .wp-filter {
                    margin-bottom: 15px;
                }
            </style>
		<?php }

		/**
		 * Enqueue scripts/styles.
		 *
		 * @param string $hook_suffix
		 *
		 * @return void
		 * @since  1.0.0
		 * @access public
		 */
		public function enqueue( $hook_suffix )
		{

			if ( $this->settings_page !== $hook_suffix ) {
				return;
			}

			$view = $this->get_view( members_get_current_settings_view() );

			if ( $view ) {
				$view->enqueue();
			}
		}

		/**
		 * Registers the plugin settings.
		 *
		 * @return void
		 * @since  1.0.0
		 * @access public
		 */
		function register_settings()
		{

			foreach ( $this->views as $view ) {
				$view->register_settings();
			}
		}

		/**
		 * Renders the settings page.
		 *
		 * @return void
		 * @since  1.0.0
		 * @access public
		 */
		public function settings_page()
		{ ?>

            <div class="wrap">
                <h1><?php echo esc_html_x( 'Permission Manager', 'admin screen', 'members' ); ?></h1>

                <div class="wp-filter">
					<?php $this->filter_links(); ?>
                </div>

				<?php $this->get_view( members_get_current_settings_view() )->template(); ?>

            </div><!-- wrap -->
		<?php }

		/**
		 * Outputs the list of views.
		 *
		 * @return void
		 * @since  2.0.0
		 * @access public
		 */
		private function filter_links()
		{
		    ?>
            <ul class="filter-links">
				<?php foreach ( $this->views as $view ) :

					// Determine current class.
					$class = $view->name === members_get_current_settings_view() ? 'class="current"' : '';

					// Get the URL.
					$url = members_get_settings_view_url( $view->name );

					if ( 'general' === $view->name ) {
						$url = remove_query_arg( 'view', $url );
					} ?>

                    <li class="<?php echo sanitize_html_class( $view->name ); ?>">
                        <a href="<?php echo esc_url( $url ); ?>" <?php echo $class; ?>><?php echo esc_html( $view->label ); ?></a>
                    </li>
				<?php endforeach; ?>
            </ul>
		<?php }

		/**
		 * Adds help tabs.
		 *
		 * @return     void
		 * @deprecated 2.0.0
		 * @access     public
		 * @since      1.0.0
		 */
		public function add_help_tabs()
		{
		}
	}

	Settings_Page::get_instance();
