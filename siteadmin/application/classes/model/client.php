<?php

/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Client extends Model
{
	/*
	   * Insert Client
	   */
	public function addclient($client)
	{
		// Create a new client record in the database
		$checklist_option = $client['checklist_option'];
		$customqr_option = $client['customqr_option'];
		unset($client['checklist_option']);
		unset($client['customqr_option']);
		$client['is_password_change'] = 1;
		$insert_id = DB::insert('clients', array_keys($client))
			->values($client)
			->execute();

		DB::insert('users', array('client_id', 'email', 'password', 'role', 'video_upload', 'checklist_option', 'customqr_option', 'created_at', 'is_password_change', 'expire_at'))
			->values(array($insert_id[0], $client['email'], $client['password'], 5, 1, $checklist_option, $customqr_option, $client['created_at'], 1, date('Y-m-d H:i:s', strtotime('+1 years', strtotime($client['created_at'])))))
			->execute();

		return $insert_id;
	}
	public function adduser($user)
	{
		$user['is_password_change'] = 1;
		// Create a new user record in the database
		$insert_id = DB::insert('users', array_keys($user))
			->values($user)
			->execute();
		return $insert_id;
	}
	public function getdupurl($client)
	{
		// Select id for check email and url duplication   
		$result = DB::select('id')
			->from('clients')
			->where('url', '=', $client['url']);
		if (isset($client['mode']) && $client['mode'] == 'edit') {
			$result->and_where('id', '!=', $client['id']);
		}
		return $result->execute()
			->as_array();
	}

	public function isChecklistAllowedToClient($client_id)
	{

		$result = DB::select('users.checklist_option')
			->from('clients')
			->join('users')->on('clients.id', '=', 'users.client_id')
			->where('clients.id', "=", $client_id)
			->and_where('users.role', "=", 5);

		return $result->execute()->as_array();
	}

	public function isCustomqrEnabledToClient($client_id)
	{

		$result = DB::select('users.customqr_option')
			->from('clients')
			->join('users')->on('clients.id', '=', 'users.client_id')
			->where('clients.id', "=", $client_id)
			->and_where('users.role', "=", 5);

		return $result->execute()->as_array();
	}

	public function checkDuplicateEmail($email)
	{

		$result = DB::select('id')->from('users')->where('email', '=', $email)->execute()->as_array();

		if (!empty($result)) {
			return false;
		} else {
			return true;
		}
	}


	public function getUsersByClientId($id)
	{
		return DB::select()->from('users')->where('client_id', '=', $id)->execute()->as_array();
	}

	public function getdupemail($client)
	{
		// Select id for check email and url duplication

		$result = DB::select('id')
			->from('users')
			->where('email', '=', $client['email']);
		if (isset($client['mode']) && $client['mode'] == 'edit') {
			$subselect = DB::select('id')
				->from('users')
				->where('client_id', '=', $client['id'])
				->and_where('role', '=', 5);
			$result->and_where('id', '!=', $subselect);
		}
		return $result->execute()
			->as_array();
	}
	public function getuseremail($email)
	{
		// Select id for check email duplication
		return DB::select('id')
			->from('users')
			->where('email', '=', $email)
			->execute()
			->as_array();
	}

	public function changeChecklistOption($client_id)
	{
		$checklist_option = $this->isChecklistAllowedToClient($client_id);
		$checklist = $checklist_option[0]['checklist_option'];
		if ($checklist == 0) {
			DB::update('users')->set(array('checklist_option' => '1'))
				->where('client_id', '=', $client_id)
				->and_where('role', '=', 5)->execute();
		} else {
			DB::update('users')->set(array('checklist_option' => '0'))
				->where('client_id', '=', $client_id)
				->and_where('role', '=', 5)->execute();
		}
	}

	public function changeCustomqrOption($client_id)
	{
		$customqr_option = $this->isCustomqrEnabledToClient($client_id);
		$customqr = $customqr_option[0]['customqr_option'];
		if ($customqr == 0) {
			DB::update('users')->set(array('customqr_option' => '1'))
				->where('client_id', '=', $client_id)
				->and_where('role', '=', 5)->execute();
		} else {
			DB::update('users')->set(array('customqr_option' => '0'))
				->where('client_id', '=', $client_id)
				->and_where('role', '=', 5)->execute();
		}
	}

	public function getallclient()
	{
		// Select All client
		return DB::select('clients.*', 'users.checklist_option', 'users.customqr_option')->from('clients')
			->join('users')->on('clients.id', '=', 'users.client_id')
			->and_where('users.role', "=", 5)
			->order_by('client_name', 'ASC')
			->execute()
			->as_array();
	}





	public function deleteclient($id)
	{
		// Delete client
		DB::delete('clients')->where('id', '=', $id)->execute();
		DB::delete('users')->where('client_id', '=', $id)->execute();
	}
	public function blockclient($id)
	{
		// block client
		$cstatus = DB::select('approve')
			->from('clients')
			->where('id', '=', $id)
			->execute()
			->as_array();
		if ($cstatus[0]['approve'] == 1) :
			$sflag = '0';
		else :
			$sflag = '1';
		endif;
		DB::update('clients')->set(array('approve' => $sflag))->where('id', '=', $id)->execute();
		//DB::update('users')->set(array('status' => $sflag))->where('client_id', '=', $id)->execute();		    
	}
	public function getclientbyid($id)
	{
		// Select id for check email duplication
		return DB::select('clients.*', 'users.checklist_option', 'users.customqr_option', 'users.client_reseller_code')
			->from('clients')
			->join('users')->on('users.client_id', '=', 'clients.id')
			->and_where('users.role', "=", 5)
			->and_where('clients.id', '=', $id)
			->execute()
			->as_array();
	}
	public function editclient($id, $client, $user)
	{
		$client['is_password_change'] = 1;
		$user['is_password_change'] = 1;
		DB::update('clients')->set($client)->where('id', '=', $id)->execute();
		DB::update('users')->set($user)->where('client_id', '=', $id)->and_where('role', '=', 5)->execute();
	}

	public function editclientResellerData($id, $client, $user)
	{
		$client['is_password_change'] = 1;
		$user['is_password_change'] = 1;

		DB::update('clients')->set($client)->where('id', '=', $id)->execute();
		/**Previous code start*/
		// DB::update('users')->set($user)->where('client_id', '=', $id)->execute();
		/**Previous code end*/
		DB::update('users')->set($user)->where('client_id', '=', $id)->and_where('role', '=', 5)->execute();
	}

	public function geturl($proid)
	{
		$client_url = DB::select('clients.url')->from('clients')->join('profiles')->on('clients.id', '=', 'profiles.client_id')->where('profiles.id', '=', $proid)
			->execute()
			->as_array();
		return $client_url[0]['url'];
	}

	public function getResllerCodeByClientId($client_id)
	{
		$result = DB::select('reseller_code')
			->from('clients')
			->where('id', '=', $client_id);
		$data = $result->execute()->as_array();
		if (isset($data) && is_array($data) && !empty($data)) {
			return $data[0]['reseller_code'];
		} else {
			return '';
		}
	}

	public function is_reseller_code_exist($client_id, $resellerCode)
	{
		// Select id for check email and url duplication   
		$result = DB::select('id')
			->from('clients')
			->where('reseller_code', '=', $resellerCode)
			->and_where('id', '!=', $client_id);
		$data =  $result->execute()->as_array();
		if (isset($data) && is_array($data) && !empty($data)) {
			return true;
		} else {
			return false;
		}
	}

	/*** Get client name using reseller code
	 **** @param resellercode string
	 **** 
	 ***/
	public function get_client_name_using_reseller_code($resellercode)
	{
		$data =  DB::select("client_name")->from("clients")->where("reseller_code", "=", $resellercode)
			->execute()
			->as_array();
		if (isset($data[0]['client_name'])) {
			return $data[0]['client_name'];
		} else {
			return;
		}
	}

	/*** Get client id using reseller code
	 **** @param resellercode string
	 **** 
	 ***/
	public function get_client_id_using_reseller_code($resellercode)
	{
		$data =  DB::select("id")->from("clients")->where("reseller_code", "=", $resellercode)
			->execute()
			->as_array();
		if (isset($data[0]['id'])) {
			return $data[0]['id'];
		} else {
			return;
		}
	}

	public function get_user_by_clientid_and_role($id, $role = "5")
	{
		return DB::select()->from('users')
			->where('client_id', '=', $id)
			->where('role', '=', $role)
			->execute()->as_array();
	}
}
