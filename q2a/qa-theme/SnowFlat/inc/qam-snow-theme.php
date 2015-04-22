<?php
/*
	Snow Theme for Question2Answer Package
	Copyright (C) 2014  Q2A Market <http://www.q2amarket.com>

	File:           inc/qam-snow-theme.php
	Version:        Snow 1.4
	Description:    Snow theme core class

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.
*/

/**
 * Snow theme loader class
 *
 * This class loads all required data for the Snow theme. This is written more
 * for future use and to keep untouched <code>qa_html_theme_base</code>
 *
 * @package Snow
 * @subpackage Loader
 * @category Theme
 * @since Snow 1.4
 * @version 1.0
 * @author Q2A Market <http://www.q2amarket.com>
 * @copyright (c) 2014, Q2A Market
 * @license http://www.gnu.org/copyleft/gpl.html
 */
class qam_snow_theme
{
	/**
	 * @var array Holds the data
	 */
	private $data;

	/**
	 * Snow instance
	 *
	 * @access public
	 * @since Snow 1.4
	 * @version 1.0
	 *
	 * @static $instance
	 *
	 * @uses qam_snow_theme::setup_globals() Setup require globals
	 * @uses qam_snow_theme::includes() Include require files
	 * @uses qam_snow_theme::heads() Setup <code><head></code> elements
	 * @uses qam_snow_theme::set_options() Setup dynamic options for Snow
	 * @uses qam_snow_theme::headers() Setup header elements
	 * @uses qam_snow_theme::footers() Setup footer elements
	 *
	 * @see qam_snow_theme()
	 * @return mixed all qam_snow_theme
	 *
	 * @author Q2A Market <http://www.q2amarket.com>
	 * @copyright (c) 2014, Q2A Market
	 * @license http://www.gnu.org/copyleft/gpl.html
	 */
	public static function instance()
	{

		// Store the instance locally to avoid private static replication
		static $instance = null;

		// Only run these methods if they haven't been run previously
		if (null === $instance) {
			$instance = new qam_snow_theme;
			$instance->setup_globals();
			$instance->includes();
			// $instance->heads();
			$instance->get_options();
			$instance->headers();
			// $instance->footers();
		}

		// Always return the instance
		return $instance;
	}

	/**
	 * Class construct
	 */
	private function __construct()
	{ /* Do nothing here */
	}

	/**
	 *
	 * @param type $key
	 * @return type
	 */
	public function __isset($key)
	{
		return isset($this->data[$key]);
	}

	/**
	 *
	 * @param type $key
	 * @return type
	 */
	public function __get($key)
	{
		return isset($this->data[$key]) ? $this->data[$key] : null;
	}

	/**
	 *
	 * @param type $key
	 * @param type $value
	 */
	public function __set($key, $value)
	{
		$this->data[$key] = $value;
	}

	/**
	 *
	 * @param type $key
	 */
	public function __unset($key)
	{
		if (isset($this->data[$key])) {
			unset($this->data[$key]);
		}
	}

	/**
	 *
	 * @param type $name
	 * @param type $args
	 * @return null
	 */
	public function __call($name = '', $args = array())
	{
		unset($name, $args);
		return null;
	}

	/**
	 * Snow theme globals
	 *
	 * @access private
	 * @since Snow 1.4
	 * @version 1.0
	 *
	 * @author Q2A Market <www.q2amarket.com>
	 * @copyright (c) 2014, Q2A Market
	 * @license http://www.gnu.org/copyleft/gpl.html
	 */
	private function setup_globals()
	{
		$this->theme        = qa_opt('site_theme');
		$this->author       = $this->qam_opt('snow_author', 'Q2A Market');
		$this->author_url   = $this->qam_opt('snow_author_url', 'http://www.q2amarket.com');
		$this->version      = $this->qam_opt('snow_version', '1.4-beta');
		$this->snow_version = strtolower($this->theme . '-' . $this->version);
		$this->opt_prefix   = 'qam_snow_';

		$this->js_dir   = 'js/';
		$this->css_dir  = 'css/';
		$this->img_url  = 'images/';
		$this->icon_url = $this->img_url . 'icons/';
	}

	/**
	 * Incldue require files
	 *
	 * @access private
	 * @since Snow 1.4
	 * @version 1.0
	 *
	 * @author Q2A Market <www.q2amarket.com>
	 * @copyright (c) 2014, Q2A Market
	 * @license http://www.gnu.org/copyleft/gpl.html
	 */
	private function includes()
	{ //do nothing now
	}

	/**
	 * Get theme options for customization.
	 *
	 * @access private
	 * @since Snow 1.4
	 * @version 1.0
	 * @return array|mixed theme options value
	 *
	 * @author Q2A Market <http://www.q2amarket.com>
	 * @copyright (c) 2014, Q2A Market
	 * @license http://www.gnu.org/copyleft/gpl.html
	 */
	private function get_options()
	{
		$this->data['ask_search_box_color']  = $this->qam_opt('ask_search_box_color');
		$this->data['welcome_widget_color']  = $this->qam_opt('welcome_widget_color');
		$this->data['fixed_topbar']          = (($this->qam_opt('fixed_topbar')) ? 'fixed' : null);
		$this->data['header_custom_content'] = $this->qam_opt('header_custom_content');
		$this->data['footer_custom_content'] = $this->qam_opt('above_footer_custom_content');

		return $this->data;
	}

	/**
	 * Get header items
	 *
	 * @access private
	 * @since Snow 1.4
	 * @version 1.0
	 * @return array|mixed various header items (e.g. user account, scripts)
	 *
	 * @author Q2A Market <http://www.q2amarket.com>
	 * @copyright (c) 2014, Q2A Market
	 * @license http://www.gnu.org/copyleft/gpl.html
	 */
	private function headers()
	{
		$this->data['headers'] = array(
			'user_points'         => $this->user_points(),
			'ask_button'          => $this->ask_button(),
		);

		return $this->data;
	}

	/**
	 * Get logged in user's points
	 *
	 * @access private
	 * @since Snow 1.4
	 * @version 1.0
	 * @return string|null LoggedIn user's total points, null for guest
	 *
	 * @author Q2A Market <http://www.q2amarket.com>
	 * @copyright (c) 2014, Q2A Market
	 * @license http://www.gnu.org/copyleft/gpl.html
	 */
	private function user_points()
	{
		if (qa_is_logged_in()) {
			$userpoints = qa_get_logged_in_points();
			$pointshtml = ($userpoints == 1) ? qa_lang_html_sub('main/1_point', '1', '1') : qa_html(number_format($userpoints));
			$points     = '<DIV CLASS="qam-logged-in-points">' . $pointshtml . '</DIV>';

			return $points;
		}

		return null;
	}

	/**
	 * Custom ask button for medium and small screen
	 *
	 * @access private
	 * @since Snow 1.4
	 * @version 1.0
	 * @return string Ask button html markup
	 *
	 * @author Q2A Market <http://www.q2amarket.com>
	 * @copyright (c) 2014, Q2A Market
	 * @license http://www.gnu.org/copyleft/gpl.html
	 */
	private function ask_button()
	{
		$html = '<div class="qam-ask-search-box">';
		$html .= '<div class="qam-ask-mobile"><a href="' . qa_path('ask', null, qa_path_to_root()) . '" class="' . $this->qam_opt('ask_search_box_color') . '">' . qa_lang_html('main/nav_ask') . '</a></div>';
		$html .= '<div class="qam-search-mobile ' . $this->qam_opt('ask_search_box_color') . '" id="qam-search-mobile"></div>';
		$html .= '</div>';

		return $html;
	}
}


/* ---------------------------------------------------------------------------- */

// create a function to instanciate the class
if (!function_exists('qam_snow_theme')) {

	/**
	 * Return <code>qam_snow_theme</code> class instance
	 *
	 * @access public
	 * @since Snow 1.4
	 * @version 1.0
	 * @return array
	 *
	 * @author Q2A Market <http://www.q2amarket.com>
	 * @copyright (c) 2014, Q2A Market
	 * @license http://www.gnu.org/copyleft/gpl.html
	 */
	function qam_snow_theme()
	{
		return qam_snow_theme::instance();
	}

}

// Declare global variable
if (class_exists('qam_snow_theme')) {
	$GLOBALS['qam_snow'] = qam_snow_theme();
}
