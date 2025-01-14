<?php
    // Exit if accessed directly.
    defined( 'ABSPATH' ) || exit;
    function yookerai_admin_notice() {
        global $message;
        global $messagestyle;
        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $messagestyle ), esc_html( $message ) );
    }
    function yookerai_admin_site_transient_update_plugins($transient) {
       
        $cached_update_data = get_transient('yookerai_admin_update_data');
        if ($cached_update_data !== false) {
            return $cached_update_data;
        }
       
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, esc_html( yai_plugindata['PluginURI'] ));
        curl_setopt($ch, CURLOPT_USERAGENT,'Awesome-Octocat-App');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json'));
        $fetch = curl_exec($ch);
        curl_close($ch);
        $jsondata = json_decode($fetch, true);

        if(isset($jsondata->message)){
            global $message;
            global $messagestyle;
            $message = 'There is an conflict detected for '.esc_html( yai_plugindata['Name'] ).' message '.$jsondata->message.' for more information read the GitHub documentation '.$jsondata->documentation_url.'.';
            $messagestyle = 'notice notice-error';
            add_action( 'admin_notices', 'yookerai_admin_notice');
            $item = array(
                'slug'          => yai_slug,
                'plugin'        => yai_basename,
                'new_version'   => ''.esc_html( yai_plugindata['Version'] ).'',
                'url'           => '',
                'package'       => '',
                'requires'      => '',
                'requires_php'  => '',
                'icons'         => array(),
                'banners'       => array(),
                'banners_rtl'   => array(),
                'tested'        => '',
                'compatibility' => new stdClass()
            );
            if($transient):
                $transient->response[''.yai_basename.''] = $item;
            endif;
            return $transient;
        }
        elseif(isset($jsondata['version'])){
            if(version_compare( (''.$jsondata['version'].''), (''.esc_html( yai_plugindata['Version'] ).''), '>' )) {
                $item = new stdClass();
                $item->slug = yai_slug;
                $item->plugin = yai_basename;
                $item->requires = $jsondata['requires'];
                $item->new_version = $jsondata['version'];
                $item->requires_php = $jsondata['requires_php'];
                $item->tested = $jsondata['tested'];
                $item->package = $jsondata['download_url'];
                if($transient):
                    $transient->response[''.yai_basename.''] = $item;
                endif;
            }

            set_transient('yookerai_admin_update_data', $transient, 1 * HOUR_IN_SECONDS);

            return $transient;
        }
    }
    add_filter('site_transient_update_plugins', 'yookerai_admin_site_transient_update_plugins');
?>