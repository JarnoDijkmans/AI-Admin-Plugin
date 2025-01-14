<?php
	// Exit if accessed directly.
	defined( 'ABSPATH' ) || exit;

    add_filter( 'plugins_api', 'yooker_ai_admin_plugin_info', 20, 3);

    function yooker_ai_admin_plugin_info( $res, $action, $args ){
        if( 'plugin_information' !== $action ):
            return $res;
        endif;

        if( yai_slug !== $args->slug ):
            return $res;
        endif;

        $remote = wp_remote_get( 
            ''.yai_plugindata['PluginURI'].'', 
            array(
                'timeout' => 10,
                'headers' => array(
                    'Accept' => 'application/json'
                ) 
            )
        );

        if( is_wp_error( $remote ) || 200 !== wp_remote_retrieve_response_code( $remote ) || empty( wp_remote_retrieve_body( $remote ) )):
            return $res;	
        endif;

        $remote = json_decode( wp_remote_retrieve_body( $remote ) );
        
        $res = new stdClass();
        $res->name = $remote->name;
        $res->slug = $remote->slug;
        $res->author = $remote->author;
        $res->author_profile = $remote->author_profile;
        $res->version = $remote->version;
        $res->tested = $remote->tested;
        $res->requires = $remote->requires;
        $res->requires_php = $remote->requires_php;
        $res->download_link = $remote->download_url;
        $res->trunk = $remote->download_url;
        $res->last_updated = $remote->last_updated;
        $res->sections = array(
            'description' => $remote->sections->description,
            'installation' => $remote->sections->installation,
            'changelog' => $remote->sections->changelog
            // you can add your custom sections (tabs) here
        );
        // in case you want the screenshots tab, use the following HTML format for its content:
        // <ol><li><a href="IMG_URL" target="_blank"><img src="IMG_URL" alt="CAPTION" /></a><p>CAPTION</p></li></ol>
        if( ! empty( $remote->sections->screenshots ) ):
            $res->sections[ 'screenshots' ] = $remote->sections->screenshots;
        endif;

        $res->banners = array(
            'low' => $remote->banners->low,
            'high' => $remote->banners->high
        );
        
        return $res;

    }
?>