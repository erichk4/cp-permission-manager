<?php
	/**
	 * Handles the add-ons settings view.
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
	 * Sets up and handles the add-ons settings view.
	 *
	 * @since  2.0.0
	 * @access public
	 */
	class View_Roles extends View
	{
		/**
		 * Renders the settings page.
		 *
		 * @return void
		 * @since  2.0.0
		 * @access public
		 */
		public function template()
		{
			$roles_url = add_query_arg( array( 'page' => 'roles' ), admin_url( 'users.php' ) );
			?>
            <div class="card" style="padding: 40px;">
                <div style="margin-bottom: 20px;">Click the button below to go to the user role editor:</div>
                    <a class="button" href="<?= $roles_url ?>"><?= esc_html__( 'Edit User Roles', 'members' ) ?></a>
            </div><!-- .widefat -->
			<?php
		}
	}
