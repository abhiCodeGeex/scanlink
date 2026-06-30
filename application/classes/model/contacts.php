<?php

/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Contacts extends Model {

  public function addContact($array) {
    $contact = DB::insert('contacts', array('id_profile', 'name', 'surname' , 'mobile', 'email', 'created_at'))
            ->values(array($array['id_profile'], $array['name'], $array['surname'], $array['mobile'], $array['email'], $array['created_at']))
            ->execute();

    return $contact[0];
  }
  
  
  public function getContactsById($id){
		$profile=DB::select('*')->from('contacts')
		->where('contacts.id_profile', '=', $id)
		->execute()
		->as_array();
		
		if($profile):
			return $profile;
		else:
			return array();
		endif;
	}

}

?>