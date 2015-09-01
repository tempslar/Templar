<?php
Class Common_Utility_Json {
	
	public static function jsonOut( $datas ) {
		header( 'Content-Type: application/json; charset=utf-8' );
		echo json_encode( $datas );
		
		Common_Tool::End();
	}
	
}