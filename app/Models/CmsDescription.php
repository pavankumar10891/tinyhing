<?php
namespace App\Models; 
use Eloquent;

/**
 * CmsDescription Model
 */
class CmsDescription extends Eloquent{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'cms_page_descriptions';
	
	/**
	 * function for find result from database 
	 *
	 * @param null
	 * 
	 * @return array
	 */		
	public static function showResult(){
		
		$currentLanguageId	=	Session::get('currentLanguageId');
		$result		=	 CmsDescription::select('source_col_name','source_col_description')->where('language_id' , $currentLanguageId);
		return $result;
	} //end showResult()
	
	
	public function getCmsBySlug(){
		$lang			=	App::getLocale();
		$cmsPagesDetail	=	DB::select(DB::raw("SELECT * FROM cms_page_descriptions WHERE foreign_key = (select id from cms_pages WHERE cms_pages.slug = '$slug') AND language_id = (select id from languages WHERE languages.lang_code = '$lang')"));
		if(empty($cmsPagesDetail)){
			return Redirect::to('/');
		} 
		$result	=	array();
		foreach($cmsPagesDetail as $cms){
			$key	=	$cms->source_col_name;
			$value	=	$cms->source_col_description;
			$result[$cms->source_col_name]	=		$cms->source_col_description;
		}
		
		return $result;
	}
	
}// end CmsDescription class