<?php
/**
 * 	Controller for Document actions
 */
class DocumentApiController extends ApiController{

	public function __construct(){
		parent::__construct();

		$this->beforeFilter('auth', array('on' => array('post','put', 'delete')));
	}	
	
	public function getRecent($query = null){
		$recent = 10;

		if(isset($query)){
			$recent = $query;
		}

		$docs = Doc::take(10)->orderBy('updated_at', 'DESC')->get();

		foreach($docs as $doc){
			$doc->setActionCount();
		}

		return Response::json($docs);
	}

	public function getCategories($doc = null){
		if(!isset($doc)){
			$categories = Category::all();	
		}else{
			$doc = Doc::find($doc);
			$categories = $doc->categories()->get();
		}

		return Response::json($categories);
	}

	public function postCategories($doc){
		$doc = Doc::find($doc);

		$categories = Input::get('categories');
		$categoryIds = array();

		foreach($categories as $category){
			$toAdd = Category::where('name', $category)->first();
			
			if(!isset($toAdd)){
				$toAdd = new Category();
			}
			
			$toAdd->name = $category;
			$toAdd->save();

			array_push($categoryIds, $toAdd->id);
		}

		$doc->categories()->sync($categoryIds);

		return Response::json($categoryIds);	
	}
}
