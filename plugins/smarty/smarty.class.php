<?php
Class Plugins_Smarty_Smarty {
	
	static public $_smarty = NULL;
	
	const PLUGIN_FILEPATH = '/libs/Smarty.class.php';
	
	static public function GetInst() {
		//Static cache
		if (self::$_smarty instanceof Smarty) {
			return self::$_smarty;
		}

		$pluginFile = dirname( __FILE__ ) . '/' . self::PLUGIN_FILEPATH;

		if ( file_exists( $pluginFile ) ) {
			require_once( $pluginFile );
			
			if ( class_exists( 'Smarty' ) ) {
				self::$_smarty = new Smarty;
			}
			
		}
		
		return self::$_smarty;
	}
	
}