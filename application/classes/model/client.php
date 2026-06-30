<?php

/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Client extends Model
{

  public function geturl($proid)
  {
    $client_url = DB::select('clients.url')->from('clients')->join('profiles')->on('clients.id', '=', 'profiles.client_id')->where('profiles.id', '=', $proid)
      ->execute()
      ->as_array();
    return $client_url[0]['url'];
  }

  public function editpass($id, $pass)
  {
    DB::update('clients')->set($pass)->where('id', '=', $id)->execute();
  }

  public function getClientById($client_id)
  {
    $client = DB::select('clients.*')->from('clients')->where('clients.id', '=', $client_id)
      ->execute()
      ->as_array();

    if ($client) {
      return $client[0];
    } else {
      return array();
    }
  }

  public function getClientByURI($url)
  {
    $client = DB::select('clients.*')->from('clients')->where('clients.url', '=', $url)
      ->execute()
      ->as_array();

    if ($client) {
      return $client[0];
    } else {
      return array();
    }
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

  /*   * * Insert New client
   * *** @param data array
   * *** @return array
   * * */

  public function insertClient($data)
  {

    $client_id = DB::insert(
      'clients',
      array(
        'client_name',
        'address',
        'telephone',
        'contact_person',
        'regi_date',
        'email',
        'password',
        'url',
        'created_at',
        'updated_at',
        'is_password_change'
      )
    )
      ->values(
        array(
          $data['first_name'] . " " . $data['last_name'],
          $data['billing_address'],
          $data['phone'],
          $data['first_name'],
          date("Y-m-d"),
          $data['email'],
          $data['password'],
          $data['client_url'],
          date("Y-m-d H:i:s"),
          date("Y-m-d H:i:s"),
          1,
        )
      )
      ->execute();
    return $client_id;
  }

  /*   * * UPDATE USER DATA
   * *** @param dataFill array
   * *** @param client_id integer
   * *** 
   * * */

  public function updateclient($dataFill, $client_id)
  {
    $dataFill['is_password_change'] = 1;
    $query = DB::update('clients')->set($dataFill)->where('id', '=', $client_id);
    $query->execute();
  }

  /*** Get user reseller code
   **** @param resellercode string
   **** 
   ***/
  public function isResellerCodeExit($resellercode)
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


  public function adduser($user)
  {
    // Create a new user record in the database
    $user['is_password_change'] = 1;
    $insert_id = DB::insert('users', array_keys($user))
      ->values($user)
      ->execute();
    return $insert_id;
  }
}
