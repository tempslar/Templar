<?php
Class Common_Utility_Json {
	
	static public function jsonOut( $datas ) {
		header( 'Content-Type: application/json; charset=utf-8' );
		echo json_encode( $datas );
		
		Common_Tool::End();
	}
	
}