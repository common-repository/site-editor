<?php

if( !class_exists( 'SEDFile' ) )
    require_once dirname( __FILE__ ) . DS . 'app_file.class.php';

class SEDPageBuilderModules extends SiteEditorModules{

    public $sed_skin;
    public static $modules_base_rel;

	function __construct( $args = array() ) {
        $args = wp_parse_args( $args, array(
            'app_name' => 'pagebuilder'
        ) );

		parent::__construct( $args );

        if( !class_exists( 'SEDPageBuilderModuleSkins' ) )
            require_once dirname( __FILE__ ) . DS . 'app_pb_module_skins.class.php';

        $this->sed_skin = new SEDPageBuilderModuleSkins;

        self::$modules_base_rel = 'plugins/' . SED_PLUGIN_NAME . '/editor/extensions/pagebuilder/modules/';

        if( ! is_site_editor() && ! defined( 'DOING_AJAX' ) ){
            add_action("admin_init" , array( $this , "check_exist_modules" ) );
        }

	}

    function get_modules_base(){

        return array(
            'columns'           =>  'columns/columns.php' ,
            'content-layout'    =>  'content-layout/content-layout.php' ,
            'icons'             =>  'icons/icons.php' ,
            'image'             =>  'image/image.php' ,
            'module'            =>  'module/module.php' ,
            'paragraph'         =>  'paragraph/paragraph.php' ,
            'row'               =>  'row/row.php' ,
            'row-container'     =>  'row-container/row-container.php' ,
            'separator'         =>  'separator/separator.php' ,
            'sidebar'           =>  'sidebar/sidebar.php' ,
            'single-icon'       =>  'single-icon/single-icon.php' ,
            'single-image'      =>  'single-image/single-image.php' ,
            'title'             =>  'title/title.php' ,
            'wp-text-editor'    =>  'wp-text-editor/wp-text-editor.php'
        );

    }

    function get_core_modules(){ 
        return parent::get_modules();
    }

    function is_module_base( $module ){

        $module_name = $this->get_module_name( $module );

        $base_modules = ( array ) $this->get_modules_base();

        return isset( $base_modules[$module_name] );

    }

    function is_install( $module ){
        /*$module_name = $this->get_module_name( $module );

        $module_info = (array) sed_get_setting("module_info");
        return isset( $module_info[$module_name] );*/

        return true;
    }

    function is_module( $module ){
        $module = $this->module_basename( trim( $module ) );
        $modules = $this->get_modules();

        if( in_array( $module , array_keys( $modules ) ) ){
            return true;
        }else{
            return false;
        }

    }

    function is_module_active( $module ) {
        $site_editor_settings = get_option('site-editor-settings');
        $activate_modules = isset( $site_editor_settings['live_module'] ) ? $site_editor_settings['live_module'] : array() ;
        $activate_modules = array_values( $activate_modules );

        return in_array( $module, $activate_modules );
    }

    function check_exist_modules(){

        $modules = $this->get_modules();
        $modules = array_keys( $modules );

        $modules = array_map( array( $this , 'get_module_name' ) , $modules );

        //$modules_info = (array) sed_get_setting("module_info");

        $live_module = sed_get_setting( "live_module" );

        if( !empty( $live_module ) ){
            $active_update_needle = false;

            foreach( $live_module AS $module_name => $module_file ){

                if( !in_array( $module_name , $modules ) && isset( $live_module[ $module_name ] ) ){

                    unset( $live_module[$module_name] );
                    $active_update_needle = true;

                }

            }

            if( $active_update_needle ){
                sed_update_setting( "live_module" , $live_module );
            }
        }

        /*if( !empty( $modules_info ) ){
            $update_needle = false;

            foreach( $modules_info AS $module_name => $info ){

                if( !in_array( $module_name , $modules ) ){

                    unset( $modules_info[$module_name] );
                    $update_needle = true;
                }

            }

            if( $update_needle ){
                sed_update_setting( "module_info" , $modules_info );
            }

        }*/

    }

    function check_reapet_module_name( $module ){
        
        $module_name = $this->get_module_name( $module );

        $modules_info = (array) sed_get_setting("module_info");

        if( !empty( $modules_info ) ){
            $modules_names = array_keys( $modules_info );

            if( in_array( $module_name , $modules_names ) ){
                return true;
            }

        }

        return false;
    }

    function get_modules(){
        $sed_modules = ( array ) $this->get_core_modules();

        $modules = array();

        foreach( (array) $sed_modules AS $module_file => $module_data ){
            $module_file = self::$modules_base_rel . $module_file;
            $modules[ $module_file ] = $module_data;
        } 

        return apply_filters( 'sed_modules', $modules );
    }

    function remove_module_info( $module ){

        $module_info = (array) sed_get_setting("module_info");
        $module_name = $this->get_module_name( $module );

        if( isset( $module_info[$module_name] ) ){
            unset( $module_info[$module_name] );
            sed_update_setting( "module_info" , $module_info );
        }

    }

    function validate_module( $module ) {

        $module_main_file = WP_CONTENT_DIR . "/" . $module;

        $module_path = dirname( $module_main_file );

        if( !is_dir( $module_path  ) )
            return new WP_Error('module_not_found', sprintf( __("module %s not found","site-editor" ) , $module ) );

        if ( validate_file($module) )
            return new WP_Error('module_invalid', __('Invalid module path.' , 'site-editor'));

        if ( ! file_exists( $module_main_file ) )
            return new WP_Error('module_file_not_found', __('Module Main file does not exist.' , 'site-editor'));

        $installed_modules = $this->get_modules();

        if ( ! isset($installed_modules[$module]) )
            return new WP_Error('no_module_header', __('The module does not have a valid header.' , 'site-editor'));

        $module_skins = glob( $module_path . DS . "skins" . DS . "default" . "*" );

        if( empty( $module_skins ) )
            return new WP_Error( 'skin_not_found' , sprintf( __("We can not find default skin in module %s","site-editor" ) , $module ) );

        return 0;
    }


    function activate_module( $module, $redirect = '' ) {
        $module = $this->module_basename( trim( $module ) );

        if( !$this->is_install( $module ) ){
            $module_name = $this->get_module_name( $module );
            return new WP_Error( 'module_not_installed' , sprintf( __("The %s module not installed.","site-editor" ) , $module_name ) );
        }

        $site_editor_settings = get_option('site-editor-settings');
        $activate_modules = isset( $site_editor_settings['live_module'] ) ? $site_editor_settings['live_module'] : array() ;

        $current = array_values( $activate_modules );

        $valid = $this->validate_module($module);
        if ( is_wp_error($valid) )
            return $valid;

        if ( !in_array( $module, $current ) ) {

            if ( !empty($redirect) )
                wp_redirect(add_query_arg('_error_nonce', wp_create_nonce('module-activation-error_' . $module), $redirect)); // we'll override this later if the plugin can be included without fatal error

            $module_name = $this->get_module_name( $module );

            //TODO : Remove Save Modules & Skins Info
            $this->save_module_info( $module , array( "skins"  => array() ) );

            if( !$this->install_skins( $module ) ){
                $this->print_message( sprintf( __("Module %s not installed","site-editor" ) , $module_name ) , "error" );
                return false;
            }

            $activate_modules[$module_name] = $module;

            sed_update_setting( "live_module" , $activate_modules );

            do_action( 'sed_pb_activated_module', $module_name );

            return true;

        }

        return null;
    }


    function deactivate_module( $module, $redirect = '' ) {
        $module = $this->module_basename( trim( $module ) );

        if( !$this->is_install( $module ) ){
            $module_name = $this->get_module_name( $module );
            return new WP_Error( 'module_not_installed' , sprintf( __("The %s module not installed.","site-editor" ) , $module_name ) );
        }

        if( $this->is_module_base( $module ) ){
            $module_name = $this->get_module_name( $module );
            return new WP_Error( 'module_is_base' , sprintf( __("The %s module is a base module. you don't deactivate base modules.","site-editor" ) , $module_name ) );
        }

        $site_editor_settings = get_option('site-editor-settings');
        $activate_modules = isset( $site_editor_settings['live_module'] ) ? $site_editor_settings['live_module'] : array() ;

        $current = array_values( $activate_modules );

        if ( in_array( $module, $current ) ) {
            if ( !empty($redirect) )
                wp_redirect(add_query_arg('_error_nonce', wp_create_nonce('module-deactivation-error_' . $module), $redirect)); // we'll override this later if the plugin can be included without fatal error

            //$live_module = sed_get_setting( "live_module" );
            $module_name = $this->get_module_name( $module );

            unset( $activate_modules[$module_name] );

            sed_update_setting( "live_module" , $activate_modules );


            do_action( 'sed_pb_deactivated_module', $module_name );

            return true;
        }

        return null;
    }

    private function save_module_info( $module , $curr_module_info = array() ){

        $module_name = $this->get_module_name( $module );

        $module_info = (array) sed_get_setting("module_info");

        $module_info[$module_name] = isset( $module_info[$module_name] ) ? $module_info[$module_name] : array();

        $module_info[$module_name] = array_merge(  (array) $module_info[$module_name] , $curr_module_info );

        sed_update_setting( "module_info" , $module_info );

    }

    private function install_skins( $module ){

        $module_main_file = WP_CONTENT_DIR . "/" . $module;

        $module_path = dirname( $module_main_file );

        $skins_path     = array_filter( glob( $module_path . DS . 'skins' . DS . '*' ) , 'is_dir' );
        $result         = array();

        $module_name = $this->get_module_name( $module );

        if( empty( $skins_path ) ){
            $this->print_message( sprintf( __("skin not found for module %s.","site-editor" ) , $module_name )  , "error" );
            return false;
        }

        $this->print_message( sprintf( __("begin install skins for module %s.","site-editor" ) , $module_name )  , "info" );

        $result_skins_install = true;

        foreach ( $skins_path as $skin_path ){
            $result = $this->sed_skin->install_skin( $module , basename( $skin_path ) );

            if( !$result || is_wp_error( $result ) ){
                $this->print_message( sprintf( __( "An error occurred in the installation skin %s" ,"site-editor" ) , $skin_path )  , "error" );
                $result_skins_install = false;
            }
        }

        return $result_skins_install;
    }

    private function less_module_compile( $module , $check_compile = true ){

        $module = $this->module_basename( trim( $module ) );

        $less_info      = array();

        $module_main_file = WP_CONTENT_DIR . "/" . $module;

        $module_path = dirname( $module_main_file );

        $less_files     = SEDFile::list_files( $module_path , 'skins' , '' , array( 'less' ) );

        foreach ( $less_files as $file ) {

            $data_file = SEDFile::get_file_data( $file , "less_info" );

            if( $data_file !== false && $data_file['handle'] ){

                $module_name = $this->get_module_name( $module );

                $this->print_message( sprintf( __("Start less compilation for %s","site-editor" ) , $module_name ) );


                if( !class_exists( 'SEDAppLess' ) )
                    require_once dirname( __FILE__ ) . DS . 'sed_app_less.class.php';

                $css_path = SEDAppLess::relative_path( $file , $module , "abs" );

                $uri_css_file = str_replace( DS , '/' , $css_path);

                $abs_css_path = SEDAppLess::upload_path( $file , $module );

                $handle     = $data_file['handle'];
                $deps       = isset( $data_file['deps'] )   ? $data_file['deps']    : array();
                $ver        = isset( $data_file['ver'] )    ? $data_file['ver']     : '1.0.0';
                $media      = isset( $data_file['media'] )  ? $data_file['media']   : 'all';

                $import     = isset( $data_file['import'] )  ? $data_file['import']   : array();

                $less = array(
                    "handle"        => $handle,
                    "src"           => $uri_css_file, //SED_UPLOAD_URL .
                    "deps"          => $deps,
                    "ver"           => $ver,
                    "media"         => $media ,
                    "import"        => $import ,
                    "src_rel"       => str_replace( DS , '/' , SEDAppLess::relative_path( $file , $module) )
                );

                $filename = basename($file);
                $css_filename = substr( $filename , 0 , -4 ) . 'css';
                $css_file = dirname( $file ) . DS . $css_filename;

                if( file_exists( $css_file ) ){
                    global $wp_filesystem;
                    if( empty( $wp_filesystem ) ) {
                        require_once( sed_get_wp_admin_path() .'includes/file.php' );
                        WP_Filesystem();
                    }
                    // create directory when not exists
                    if( !is_dir( dirname( $abs_css_path ) ) ){
                        wp_mkdir_p( dirname( $abs_css_path ) );
                        @chmod( dirname( $abs_css_path ) ,0777);
                    }

                    if( $wp_filesystem ) {
                        if( !$wp_filesystem->move( $css_file , $abs_css_path , true ) )
                            $this->print_message( __("Error : Css File Move Failed","site-editor" ) , 'error' );
                        else
                            $this->print_message( sprintf( __("Css File Move successful : %s.","site-editor" ) , $handle ) );
                    }else{
                        $this->print_message( __("Error : Css File Move Failed","site-editor" ) , 'error' );
                    }

                }else{
                    $result_compile = SEDAppLess::compile_file( $file , $abs_css_path );

                    if( $result_compile === true ){
                        $this->print_message( sprintf( __("less %s is compiled.","site-editor" ) , $handle ) );
                        $less_info[$less['handle']] = $less;

                    }
                    else
                        $this->print_message( sprintf( __("Error LESS : %s","site-editor" ) , $result_compile ) );
                }
            }

        }
        return $less_info;
    }

    function get_module_name( $module ){
        $module = $this->module_basename( $module );
        $pos = strrpos( $module , "/" );
        if( $pos > 0 ){
            $module = substr( $module , $pos + 1 );
        }

        $strleng = strlen( $module ) - 4;

        $module_name = substr( $module , 0 ,  $strleng );

        return $module_name;
    }


    public static function all_less_compile ( ){
        $modules_info = (array) sed_get_setting("module_info");
        if( !empty($modules_info) ){
            foreach( $modules_info AS $module => $module_info ){
                $skins = $module_info['skins'];
                foreach( $skins AS $skin => $skin_info ){

                    if( isset( $skin_info['less'] ) && !empty( $skin_info['less'] ) )
                        self::group_compile( $module , $skin_info['less'] );

                }

                if( isset( $module_info['less'] ) && !empty( $module_info['less'] ) )
                    self::group_compile(  $module , $module_info['less']  );
            }
        }
    }

    public static function group_compile ( $module , $lesses ){
        foreach( $lesses AS $handle => $less_info ){

            $base_path = substr( str_replace('/' , DS , $less_info["src"] ) , 0 , -4) . ".less";
            $file = WP_CONTENT_DIR . $base_path;

            if( !class_exists( 'SEDAppLess' ) )
                require_once SED_INC_DIR . DS . 'sed_app_less.class.php';

            $abs_css_path = SEDAppLess::upload_path( $file , $module );

            $result_compile = SEDAppLess::compile_file( $file , $abs_css_path );

            if( $result_compile === true )
                sed_print_message( sprintf( __("less %s is compiled.","site-editor" ) , $handle ) );
            else
                sed_print_message( sprintf( __("Error LESS : %1s in %2s","site-editor" ) , $result_compile , $base_path ) , "error"  );
        }
    }

}