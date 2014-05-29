<?php
Class Common_Utility_Img {
	
	CONST IMG_MARK_BIG = 'big';
	
	CONST IMG_MARK_SMALL = 'small';
	
	
	/**
	 * 获取课程各尺寸图片数组
	 * 
	 * @param string $url
	 * @return mixed array|null - 图片数组
	 */
	static public function GetClassImgArrary( $url='' ) {
		$outImgs = array();
		
		if ( $url ) {
			$outImgs['thumb_big']   = self::GetClassReSizeImg( $url );
			$outImgs['thumb_small'] = self::GetClassReSizeImg( $url, 2 );
			
			if ( !empty( $outImgs['thumb_big'] )  &&  !empty( $outImgs['thumb_small'] ) ) {
				return $outImgs;
			}
		}
		
		return NULL;
	}
	
	
	/**
	 * 课件原图转缩率图方法
	 * 
	 * @param string $url - 原图url
	 * @param int $type - 转换图片类型,1-大图,2-小图
	 * @return mixed string | null
	 */
	static public function GetClassReSizeImg( $url='', $type=1 ) {
		
		if ( $url ) {
			$orgMark = '/avatar_origin/';
			
			if ( 1 == $type ) {	//选择转换大小图
				$newMark = self::IMG_MARK_BIG;
			}
			else {
				$newMark = self::IMG_MARK_SMALL;
			}
			
			$newMark = '/' . $newMark . '/';
			
			$outUrl = str_replace( $orgMark, $newMark, $url );
			
			return $outUrl;
		}
		
		return NULL;
	}
}