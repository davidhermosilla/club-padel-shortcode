<?php
/**
 * Plugin name: Club Padel API Shortcode
 * Plugin URI: https://github.com/davidhermosilla/club-padel-api
 * Description: Get information from external APIs in WordPress
 * Author: David Hermosilla
 * Author URI: https://github.com/davidhermosilla
 * version: 0.1.0
 * License: GPL2 or later.
 * text-domain: prefix-plugin-name
 */


defined('ABSPATH')  || die('Unauthorized Access');

// Action when user logs into admin panel
add_shortcode('club_padel_api', 'callback_function_club_api');

function callback_function_club_api( $atts ) {

	if ( is_admin() ) {
		return '<p>This is where the shortcode [club_padel_api] will show up.</p>';
	}

    $defaults = [
      'title'  => 'Table title'
    ];
    
	$operation = $atts['operation'];
    
	$atts = shortcode_atts(
        $defaults,
        $atts,
        'club_padel_api'
    );
    
    if (strcmp($operation, 'get_roles') == 0) {
        $html = get_roles($atts);
    } 
	
	$salida=user_has_role(wp_get_current_user()->id,'administrator');
	echo $salida;
	
    return $html;
}

function get_roles($atts) {
    $url = 'http://club-padel-api-af5b1de46c88.herokuapp.com/club-padel/roles';
    
    $arguments = array(
        'method' => 'GET' 
    );

    $response = wp_remote_get($url, $arguments);

    if (is_wp_error($response) ) {
        $error_message = $response->get_error_message();
        return "Something went wrong: $error_message";
    } 
    
    $results = json_decode( wp_remote_retrieve_body($response) );
 
    $html = '<h1 style="color:#1e73be;" class="has-text-align-center has-link-color has-text-color wp-block-post-title wp-elements-91b3a2fc69ee9e0aba4d3e593d56e5c1">' . $atts['title'] . '</h1>
		<figure class="wp-block-table is-style-stripes">
        <table class="has-text-color has-link-color" style="color:#1e73be">
            <thead>
			<tr>
                <th class="has-text-align-center" data-align="center">Id</th>
                <th class="has-text-align-center" data-align="center">Rol</th>
			</tr>
        </thead><tbody>';
    
    foreach( $results as $result ) {
		$html .= '<tr>' ;
		$html .= '<td class="has-text-align-center" data-align="center">'  .  $result->id . '</td>' ;
		$html .= '<td class="has-text-align-center" data-align="center">'  .  $result->rolType . '</td>' ;
		$html .= '</ tr>' ;
    }

    $html .= '</tbody></table>
              <figcaption class="wp-element-caption">Tabla de roles</figcaption>
              </figure>' ;


	
	
    return $html;    
}    

function user_has_role($user_id, $role_name)
{
    $user_meta = get_userdata($user_id);
    $user_roles = $user_meta->roles;
	print_r($user_roles);
    return in_array($role_name, $user_roles);
}
