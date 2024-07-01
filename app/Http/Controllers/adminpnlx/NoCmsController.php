<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\NoCms;
use App\Model\SeoDescription;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Illuminate\Http\Request;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Redirect,Response,Session,URL,View,Validator;
/**
* NoCmsController Controller
*
* Add your methods in the class below
*
* This file will render views from views/adminpnlxNoCms
*/
	class NoCmsController extends BaseController {
/**
* Function for display all Document 
*
* @param null
*
* @return view page. 
*/
	public function listDoc(Request $request){	
		$DB							=	NoCms::query();
		$searchVariable				=	array(); 
		$inputGet					=	$request->all();
		if ($request->all()){
			$searchData				=	$request->all();
			unset($searchData['display']);
			unset($searchData['_token']);

			if(isset($searchData['order'])){
				unset($searchData['order']);
			}
			if(isset($searchData['sortBy'])){
				unset($searchData['sortBy']);
			}
			
			if(isset($searchData['page'])){
				unset($searchData['page']);
			}
			foreach($searchData as $fieldName => $fieldValue){
				if(!empty($fieldValue)){
					$DB->where("$fieldName",'like','%'.$fieldValue.'%');
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
			}
		}
		$sortBy 					= ($request->input('sortBy')) ? $request->input('sortBy') : 'updated_at';
	    $order  					= ($request->input('order')) ? $request->input('order')   : 'DESC';
		$result 					= $DB->orderBy($sortBy, $order)->paginate(Config::get("Reading.records_per_page"));
		
		$complete_string			=	$request->query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string				=	http_build_query($complete_string);
		$result->appends($request->all())->render();
		return  View::make('admin.nocms.index',compact('result','searchVariable','sortBy','order','query_string'));
	}// end listDoc()
/**
* Function for display page  for add new seo
*
* @param null
*
* @return view page. 
*/
	public function addDoc(){
		$languages					=	DB::select("CALL GetAcitveLanguages(1)");
		$default_language			=	Config::get('default_language');
		$language_code 				=   $default_language['language_code'];
		return  View::make('admin.nocms.add',compact('languages' ,'language_code'));
	} //end addDoc()
/**
* Function for save document
*
* @param null
*
* @return redirect page. 
*/
	function saveDoc(Request $request){
		$request->replace($this->arrayStripTags($request->all()));
		$thisData					=	$request->all();
		$default_language			=	Config::get('default_language');
		$language_code 				=   $default_language['language_code'];
		$dafaultLanguageArray		=	$thisData['data'][$language_code];
		
		$validator = Validator::make(
			array(
				'page_id' 			=> $request->input('page_id'),
				'page_name' 		=> $request->input('page_name'),
				'title' 			=> $dafaultLanguageArray['title'],
				//'meta_description'  => $dafaultLanguageArray['meta_description'],
				//'meta_keywords' 	=> $dafaultLanguageArray['meta_keywords']
			),
			array(
				'page_id' 			=> 'required',
				'title' 			=> 'required',
				'page_name' 		=> 'required',
				//'meta_description' 	=> 'required',
				//'meta_keywords' 	=> 'required'
			),
			array(
				"page_id.required"				=>	trans("The page id field is required."),
				"title.required"				=>	trans("The title field is required."),				
				"page_name.required"			=>	trans("The page name field is required."),
			)
		);
		
		if ($validator->fails()){	
			return Redirect::back()
			->withErrors($validator)->withInput();
		}else{
			DB::beginTransaction();
			$seo 					= new NoCms;
			$seo->page_id    		= $request->input('page_id');
			$seo->page_name    		= $request->input('page_name');
			$seo->title   			= $dafaultLanguageArray['title'];
			$seo->meta_description  = $dafaultLanguageArray['meta_description'];
			$seo->meta_keywords   	= $dafaultLanguageArray['meta_keywords'];
			
			$seo->twitter_card   	= $dafaultLanguageArray['twitter_card'];
			$seo->twitter_site   	= $dafaultLanguageArray['twitter_site'];
			$seo->og_url   			= $dafaultLanguageArray['og_url'];
			$seo->og_type   		= $dafaultLanguageArray['og_type'];
			$seo->og_title   		= $dafaultLanguageArray['og_title'];
			$seo->og_description   	= $dafaultLanguageArray['og_description'];
			if($request->hasFile('og_image')){
				$extension 			=	$request->file('og_image')->getClientOriginalExtension();
				$fileName			=	time().'-og-image.'.$extension;
				if($request->file('og_image')->move(SEO_PAGE_IAMGE_ROOT_PATH, $fileName)){
					$seo->og_image   =  	$fileName;
				} 
			}
			
			$seopags				= $seo->save();
			if(!$seopags) {
				DB::rollback();
				Session::flash('error', trans("Something went wrong.")); 
				return Redirect::back()->withInput();
			}
			$seo_page_id			=	$seo->id;
			foreach ($thisData['data'] as $language_id => $seo) {
				if (is_array($seo))
					foreach ($seo as $key => $value) {
						$SeoDescription		=	SeoDescription::insert(
					
												array(
													'language_id'				=>	$language_id,
													'foreign_key'				=>	$seo_page_id,
													'source_col_name'			=>	$key,
													'source_col_description'	=>	$value,
													'created_at'				=>	DB::raw('NOW()'),
													'updated_at'				=>	DB::raw('NOW()')
												)
											);
						if(!$SeoDescription) {
							DB::rollback();
							Session::flash('error', trans("Something went wrong.")); 
							return Redirect::back()->withInput();
						}
					}
			}
			DB::commit();
			Session::flash('flash_notice', trans("Seo page added successfully")); 
			return Redirect::to('adminpnlx/no-cms-manager');
		}
	}//end saveBlock()
/**
* Function for display page  for edit seo
*
* @param $Id ad id 
*
* @return view page. 
*/	
	public function editDoc($Id){
		$docs				=	NoCms::find($Id);
		if(empty($docs)) {
			return Redirect::to('adminpnlx/no-cms-manager');
		}
		
		$SeoDescription		=	SeoDescription::where('foreign_key','=',$Id)->get();
		$multiLanguage		=	array();
		if(!empty($SeoDescription)){
			foreach($SeoDescription as $description) {
				$multiLanguage[$description->language_id][$description ->source_col_name]	=	$description->source_col_description;						
			}
		}
		$languages			=	DB::select("CALL GetAcitveLanguages(1)");
		$default_language	=	Config::get('default_language');
		$language_code 		=   $default_language['language_code'];
		return  View::make('admin.nocms.edit',array('doc' => $docs,'languages' => $languages,'language_code' => $language_code,'multiLanguage' => $multiLanguage));
	}// end editBlock()
/**
* Function for update seo 
*
* @param $Id ad id of seo 
*
* @return redirect page. 
*/
	function updateDoc($Id,Request $request){
		$docs				=	NoCms::find($Id);
		if(empty($docs)) {
			return Redirect::to('adminpnlxno-cms-manager');
		}
		
		$request->replace($this->arrayStripTags($request->all()));
		$this_data				=	$request->all();
		$doc 					= 	NoCms:: find($Id);
		$default_language		=	Config::get('default_language');
		$language_code 			=   $default_language['language_code'];
		$dafaultLanguageArray	=	$this_data['data'][$language_code];
		
		$validator = Validator::make(
			array(
				'page_id' 			=> $request->input('page_id'),
				'page_name' 		=> $request->input('page_name'),
				'title' 			=> $dafaultLanguageArray['title'],
				/* 'meta_description'  => $dafaultLanguageArray['meta_description'],
				'meta_keywords' 	=> $dafaultLanguageArray['meta_keywords'] */
			),
			array(
				'page_id' 			=> 'required',
				'page_name' 		=> 'required',
				'title' 			=> 'required',
				/* 'meta_description' 	=> 'required',
				'meta_keywords' 	=> 'required' */
			),
			array(
				"page_id.required"				=>	trans("The page id field is required."),
				"title.required"				=>	trans("The title field is required."),				
				"page_name.required"			=>	trans("The page name field is required."),
			)
		);
		if ($validator->fails()){	
			return Redirect::back()
				->withErrors($validator)->withInput();
		}else{
			DB::beginTransaction();
				$og_image	=	$docs->og_image;
				if($request->hasFile('og_image')){
					$extension 			=	$request->file('og_image')->getClientOriginalExtension();
					$fileName			=	time().'-og-image.'.$extension;
					if($request->file('og_image')->move(SEO_PAGE_IAMGE_ROOT_PATH, $fileName)){
						$og_image   =  	$fileName;
					} 
				}
				
				$Seo_response		=	NoCms::where('id', $Id)->update(array(
				'page_id'   	 	=>  $request->input('page_id'),
				'page_name'   	 	=>  $request->input('page_name'),
				'title' 			=>  $dafaultLanguageArray['title'],
				'meta_description' 	=>  $dafaultLanguageArray['meta_description'],
				'meta_keywords' 	=>  $dafaultLanguageArray['meta_keywords'],
				'twitter_card' 		=>  $dafaultLanguageArray['twitter_card'],
				'twitter_site' 		=>  $dafaultLanguageArray['twitter_site'],
				'og_url' 			=>  $dafaultLanguageArray['og_url'],
				'og_type' 			=>  $dafaultLanguageArray['og_type'],
				'og_title' 			=>  $dafaultLanguageArray['og_title'],
				'og_description' 	=>  $dafaultLanguageArray['og_description'],
				'og_image' 			=>  $og_image,
				'updated_at' 		=> DB::raw('NOW()')
			));
			
			if(!$Seo_response) {
				DB::rollback();
				Session::flash('error', trans("Something went wrong.")); 
				return Redirect::back()->withInput();
			}
			
			$seo_page_id		=	$Id;
			$Seo_response		=	DB::table('seo_page_descriptions')->where('foreign_key',$Id)->delete();
		
			if(!$Seo_response) {
				DB::rollback();
				Session::flash('error', trans("Something went wrong.")); 
				return Redirect::back()->withInput();
			}
			
			foreach ($this_data['data'] as $language_id => $seo) {
				if (is_array($seo))
					foreach ($seo as $key => $value) {
						$SeoDescription		=	 SeoDescription::insert(
							array(
								'language_id'				=>	$language_id,
								'foreign_key'				=>	$seo_page_id,
								'source_col_name'			=>	$key,
								'source_col_description'	=>	$value,
								'created_at'				=>	DB::raw('NOW()'),
								'updated_at'				=>	DB::raw('NOW()')
							)
						);
						if(!$SeoDescription) {
							DB::rollback();
							Session::flash('error', trans("Something went wrong.")); 
							return Redirect::back()->withInput();
						}
					}
			}
			DB::commit();
			Session::flash('flash_notice',  trans("Seo page updated successfully")); 
			return Redirect::intended('adminpnlx/no-cms-manager');
		}
	}// end updateNoCms()
/**
* Function for update seo  status
*
* @param $Id as id of seo 
* @param $Status as status of seo 
*
* @return redirect page. 
*/	
	public function updateDocStatus($Id = 0, $Status = 0){
		/* $model					=	NoCms::find($Id);
		$model->is_active		=	$Status;
		$model->save(); */
		if($Status == 0	){
			$statusMessage	=	trans("Seo page deactivated successfully");
		}else{
			$statusMessage	=	trans("Seo page activated successfully");
		}
		$this->_update_all_status('seos',$Id,$Status);
		Session::flash('flash_notice', $statusMessage); 
		return Redirect::to('adminpnlx/no-cms-manager');
	}// end updateNoCmsStatus()
/**
* Function for delete seo 
*
* @param $Id as id of seo 
*
* @return redirect page. 
*/	
	public function deleteDoc($Id = 0){
		if($Id){
			$doc				=	NoCms::find($Id) ;
			if(!empty($doc)){
				$this->_delete_table_entry('seos',$Id,'id');
			}
			/* $doc->delete();	 */
		}
		Session::flash('flash_notice',trans("Seo page removed successfully"));  
		return Redirect::to('adminpnlx/no-cms-manager');
	}// end deleteNoCms()
/**
* Function for delete multiple seo
*
* @param null
*
* @return view page. 
*/
	public function performMultipleAction(Request $request){
		if(Request::ajax()){
			$actionType 		=	(($request->input('type'))) ? $request->input('type') : '';
			if(!empty($actionType) && !empty($request->input('ids'))){
				if($actionType	==	'delete'){
					NoCms::whereIn('id', $request->input('ids'))->delete();
					Session::flash('flash_notice',trans("messages.management.doc_all_delete_msg")); 
				}
			}
		}
	}//end performMultipleAction()
}// end BlockController	