<?php
/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Testimonial extends Model{
	public function insertTestimonial($data){
		
		$testimonial_id = DB::insert('testimonial', array('title','video'))
          ->values(array($data['txttitle'], $data['txtvideo']))
          ->execute();
	
      return $testimonial_id[0];
  }
  
  
	public function updateTestimonial($id, $data){
		
		$dataFill = array( "title" => $data["txttitle"], "video" => $data["txtvideo"]);
		
		$query = DB::update('testimonial')->set($dataFill)->where('id', '=', $id);
		$query->execute();
		
	}

	public function getAllTestimonial(){
		$testimonial=array();
		
		if($eq_type==NULL){
			$testimonial = DB::select('*')->from('testimonial')
			->execute()
			->as_array();
		}		
		return $testimonial;
	}

	  public function gettestimonialbyid($id) {
        return DB::select('*')
          ->from('testimonial')
		  ->where('id','=',$id)
		  ->execute()
		  ->as_array();
     }	
		 
	 public function deleteTestimonial($id) {
			DB::delete('testimonial')->where('id', '=', $id)->execute();
     }	

}
?>