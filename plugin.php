<?php
/*
Plugin Name: Top Panel
Plugin URI: http://wordpress.org/extend/plugins/muloqot-top-panel/
Author: Shukhrat Ermatov
Depends: OauthClient
Version: 2.1
Description: The plugin enables the top panel of links from muloqot networking sites 
License: GPLv2 or later
*/
/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/


// Default Links

define('TP_DEFAULT_LINKS', <<<LINKS
	<a href="http://muloqot.uz/">Muloqot</a>
	<a href="http://daryo.uz/">Daryo</a>
	<a href="http://takliflar.uz/">Takliflar</a>
	<a href="http://intervyu.uz/">Intervyu</a>
	<a href="http://ob-havo.muloqot.uz/">Ob-havo</a>
	<a href="http://muloqot.uz/kafe">Kafe</a>

	<group name="Bloglar">
		<a href="http://xushnudbek.uz/">Xushnudbek</a>
		<a href="http://englishclubpro.com/">English Club Pro</a>
		<a href="http://iqtisodchi.uz/">Iqtisodchi</a>
		<a href="http://doim-onlayn.uz/">Doim Onlayn</a>
		<a href="http://turfaolam.com/">Turfa Olam</a>
		<a href="http://voqea.uz/">Voqea</a>
	</group>
LINKS
);

// Links file

define('TP_LINKS_FILE', plugin_dir_path( __FILE__ ).'links');


// Functions

function tp_render() {
	$links = tp_getlinks();
	include_once(plugin_dir_path( __FILE__ ).'top-panel.inc.php');
}

function tp_head() {
	echo '<link rel="stylesheet" type="text/css" media="all" href="'.plugin_dir_url( __FILE__ ).'style.css" />';
}

function tp_fetchlinks() {
	
	if (file_exists(TP_LINKS_FILE)) 
		$links = file_get_contents(TP_LINKS_FILE);
	else
		file_put_contents(TP_LINKS_FILE, TP_DEFAULT_LINKS);
		
	if (empty($links)) $links = TP_DEFAULT_LINKS;

	return $links;
}

function tp_getlinks() {
	$links = tp_fetchlinks();

	// Mark current site
	$links = preg_replace('/<a\s+href="http:\/\/'.$_SERVER['HTTP_HOST'].'(.uz)?\/"[^>]*?>([^<]+)<\/a>/', '<span>$2</span>', $links);

	if (TP_SHORT_PANEL) { // without drop-down
		if (($p1 = strpos($links, '<group')) !== false)
			return substr($links, 0, $p1);
	}

	// Fast Parsing
	if (($p1 = strpos($links, '<group name="')) !== false) {
		
		$a = substr($links, 0, $p1);
		$b = substr($links, $p1+13);
		$p2 = strpos($b, '">');
		$group_name = substr($b, 0, $p2);
		$b = substr($b, $p2+2);

		$links = $a.'<group>'.$b;
	}

	if (($p1 = strpos($links, '<group>')) !== false) {
		$p2 = strpos($links, '</group>');
		$group = substr($links, $p1+7, $p2-$p1-7);
		$links = substr($links, 0, $p1);

		if (($p1 = strpos($group, '<span>')) !== false) {
			$replace = substr($group, $p1, strpos($group, '</span>')-$p1);
			$span = $replace.' <b>&#x25BE;</b></span>';
			$replace .= '</span>';
			$group = str_replace($replace, '', $group);
		}
		else
			$span = '<a>'.$group_name.' <b>&#x25BE;</b></a>';


		$group = '<div id="tp_linksgroup" class="linksgroup">'.$span.'<div>'.$group.'</div></div>';
		
		$links = $links.$group;
	}
	
	return $links;
}

function muloqot_toppanel_plugin_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	if ($config) {
		$client_id = $config['client_id'];
		$client_secret = $config['client_secret'];
	}

	if (isset($_POST['links'])) {
		$links = trim(stripslashes($_POST['links']));
		file_put_contents(TP_LINKS_FILE, $links);
	}
	else
		$links = tp_fetchlinks();
	?>
	<div class="wrap">
		<h2>Top Panel Links</h2>
		<br/>
		<form action="" method="post">
			<textarea name="links" cols="120" rows="20" spellcheck="false" style="font-size: 13px;font-family: monospace;"><?php echo $links; ?></textarea>
			<br/>		
			<br/>
			<input type="submit" value="Save Changes" />		
		</form>
	</div>
	<?php
}

function muloqot_toppanel_plugin_menu() {
	add_options_page( 'Top Panel', 'Top Panel Links', 'manage_options', 'toppanel', 'muloqot_toppanel_plugin_options' );
}


// Actions

add_action( 'admin_menu', 'muloqot_toppanel_plugin_menu' );

// Define Mobile Library

if (class_exists('Mobile_Detect'))
	$detect = new Mobile_Detect();
elseif (class_exists('_Mobile_Detect'))
	$detect = new _Mobile_Detect();
else {
	include_once(plugin_dir_path( __FILE__ ).'Mobile_Detect.php');
	$detect = new _Mobile_Detect();
}

if ($detect):

	define('TP_NO_PANEL', ($detect->isMobile() && (!$detect->is('AndroidOS') || $detect->is('Opera')) && (!$detect->is('iOS') || $detect->is('Opera'))));
	define('TP_SHORT_PANEL', $detect->isMobile());

	if (!TP_NO_PANEL) {
		add_action( 'wp_head', 'tp_head');
		add_action( 'wp_footer', 'tp_render');
	}
	
endif;






