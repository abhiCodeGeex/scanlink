<?php

/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_User extends Model
{
	public function getlogin($post)
	{
		/*return DB::select('id','email', 'password','expire_at','client_id')
		  ->from('users')
		  ->where('email','=',$post['email'])
		  ->and_where('password','=',$post['password'])
		  ->execute()
		  ->as_array();
		  */

		$userdata = $this->getUsersByClientEmail($post['email']);

		if (!empty($userdata) && !empty($userdata['is_password_change'])) {

			$query = DB::query(Database::SELECT, "SELECT id,email,password,expire_at,client_id,is_sub_user,is_password_change FROM users WHERE email = '" . $post['email'] . "' AND password = '" . $post['password'] . "'");
		} else {

			$query = DB::query(Database::SELECT, "SELECT id,email,password,expire_at,client_id,is_sub_user,is_password_change FROM users WHERE email = '" . $post['email'] . "' AND password = '" . md5($post['password']) . "'");
		}

		$query = $query->execute()
			->as_array();

		return $query;
	}

	public function getpass($id, $pass)
	{
		return DB::select('id', 'password')
			->from('users')
			->where('id', '=', $id)
			->and_where('password', '=', $pass)
			->execute()
			->as_array();
	}

	public function editpass($id, $pass)
	{
		DB::update('users')->set($pass)->where('id', '=', $id)->execute();
	}


	public function getUserByURI($user_id)
	{
		$client = DB::select("clients.*")->from("clients")->join("users")->on("users.client_id", "=", "clients.id")->where("users.id", "=", $user_id)->execute()->as_array();

		if ($client) {
			return $client;
		} else {
			return array();
		}
	}


	public function getChecklistOption($client_id)
	{
		return  DB::select("checklist_option")->from("users")
			->where("client_id", "=", $client_id)
			->and_where("role", "=", 5)
			->execute()->as_array();
	}

	public function getUserByID($user_id)
	{
		$user = DB::select("*")->from("users")->where("id", "=", $user_id)->execute()->as_array();

		if ($user) {
			return $user[0];
		} else {
			return array();
		}
	}
	public function getUsersByClientEmail($email)
	{
		$user = DB::select("*")->from("users")->where("email", "=", $email)->execute()->as_array();

		if ($user) {
			return $user[0];
		} else {
			return array();
		}
	}


	public function getUsersByClientId($client_id)
	{
		return DB::select("*")->from("users")->where("client_id", "=", $client_id)
			->execute()
			->as_array();
	}

	public function getClientById($user_id)
	{
		$client = DB::select("clients.*")->from("clients")->join("users")->on("users.client_id", "=", "clients.id")->where("users.id", "=", $user_id)->execute()->as_array();

		if ($client) {
			return $client[0];
		} else {
			return array();
		}
	}



	public function getloginMobile($data)
	{
		$profile = $data["profile"];
		$pass = $data["password"];

		$profile = explode("/", $profile);
		$profile_id = $profile[1];
		$uri = $profile[0];


		return DB::select('id', 'email', 'password')
			->from('users')
			->where('email', '=', $post['email'])
			->and_where('password', '=', $post['password'])
			->execute()
			->as_array();

		if ($users) {
			return $users;
		} else {
			return array();
		}
	}
	public function getVideoPermissionByCid($cid)
	{
		$video_per = DB::select('video_upload')->from('users')->where('id', '=', $cid)
			->execute()
			->as_array();

		if ($video_per) {
			return $video_per[0]['video_upload'];
		} else {
			return '';
		}
	}

	public function getIdByEmail($email)
	{
		return DB::select('id', 'email', 'password')
			->from('users')
			->where('email', '=', $email)
			->execute()
			->as_array();
	}
	public function getUserDataByClientId($clientuserid)
	{
		$result =  DB::select('*')
			->from('users')
			->where('id', '=', $clientuserid)
			->execute()
			->as_array();

		if (!empty($result)) {
			return $result[0];
		}
	}

	public function getAllUsers()
	{
		return DB::select('*')
			->from('users')
			->execute()
			->as_array();
	}

	/*** Insert New user
	 **** @param data array
	 **** @return array
	 ***/

	public function insertUser($data, $clientid)
	{
		$client_id = DB::insert(
			'users',
			array(
				'client_id',
				'email',
				'password',
				'status',
				'role',
				'video_upload',
				'checklist_option',
				'customqr_option',
				'created_at',
				'updated_at',
				'notice',
				'expire_at',
				'first_name',
				'last_name',
				'company_name',
				'billing_address',
				'town',
				'phone',
				'postal_code',
				'pop_up_welcome',
				'client_reseller_code',
				'is_password_change'
			)
		)
			->values(
				array(
					$clientid,
					$data['email'],
					$data['password'],
					'1',
					'5',
					'0',
					'0',
					'0',
					date("Y-m-d H:i:s"),
					date("Y-m-d H:i:s"),
					'0',
					date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . "+1 year")),
					$data['first_name'],
					$data['last_name'],
					$data['company_name'],
					$data['billing_address'],
					$data['town'],
					$data['phone'],
					$data['postal_code'],
					'1',
					$data['client_reseller_code'],
					1,
				)
			)
			->execute();
		return $client_id;
	}


	/*** UPDATE USER DATA
	 **** @param dataFill array
	 **** @param user_id integer
	 **** 
	 ***/
	public function updateuser($dataFill, $user_id)
	{
		$dataFill['is_password_change'] = 1;
		$query = DB::update('users')->set($dataFill)->where('id', '=', $user_id);
		$query->execute();
	}



	/*** Get user reseller code
	 **** @param dataFill array
	 **** @param user_id integer
	 **** 
	 ***/
	public function getUserResellerCode($user_id)
	{
		$data =  DB::select("reseller_code")->from("users")->where("id", "=", $user_id)
			->execute()
			->as_array();
		return $data[0]['reseller_code'];
	}

	public function getalluser_by_client_id($client_id, $user_data)
	{

		return DB::select()
			->from('users')
			->where('client_id', '=', $client_id)
			->and_where('id', '<>', $user_data['id'])
			// ->and_where('enable_admin_access', '=', 1)
			->execute()
			->as_array();
	}

	public function deleteuser($user_id)
	{
		DB::delete('users')->where('id', '=', $user_id)->execute();
	}

	public function blockuser($user_id)
	{
		$cstatus = DB::select('status')
			->from('users')
			->where('id', '=', $user_id)
			->execute()
			->as_array();
		if ($cstatus[0]['status'] == 1) :
			$sflag = '0';
		else :
			$sflag = '1';
		endif;
		DB::update('users')->set(array('status' => $sflag))->where('id', '=', $user_id)->execute();
	}
}
