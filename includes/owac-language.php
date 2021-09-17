<?php 
/**
 * Check link
 */
if(!function_exists('owac_language')){
	function owac_language() {
		
		$language = array(
			'english' => array('name'=>'English','code'=>'en'),
			'french' => array('name'=>'French','code'=>'fr'),
			'german' => array('name'=>'German','code'=>'de'),
			'spanish' => array('name'=>'Spanish','code'=>'es'),
			'Hungarian' => array('name'=>'Hungarian','code'=>'HU')
		);
		
		return $language;
	}
}
?>