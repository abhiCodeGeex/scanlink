<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Model_Covidapisearch extends Model
{
	public function getCovidApiSearchData($where)
	{
		$visitationApiMode = VISITAION_API_MODE;

		$form_builder_answer_table = 'form_builder_answers';
		$form_builder_question_table = 'form_builder_question';
		$profile_table = 'profiles';
		$clients_table = 'clients';

		if ($visitationApiMode == 'testing') {
			$form_builder_answer_table = 'testing_form_builder_answers';
			$form_builder_question_table = 'testing_form_builder_question';
			$profile_table = 'testing_profiles';
			$clients_table = 'testing_clients';
		}

		$period_start_date = $where['period_start_datetime'];
		$period_end_date = $where['period_end_datetime'];

		unset($where['period_start_datetime']);
		unset($where['period_end_datetime']);
		unset($where['search_type']);
		unset($where['gateway_request_id']);

		$join_query = "SELECT client_name, " . $clients_table . ".address, " . $clients_table . ".telephone, " . $clients_table . ".email, " . $form_builder_answer_table . ".profile_id, DATE_FORMAT( date_time, '%Y-%m-%dT%H:%i:%s+00:00' ) AS date_time, question_answer, question_text, " . $form_builder_question_table . ".profile_id AS profile_id, question_type_id  FROM `" . $form_builder_answer_table . "` JOIN " . $form_builder_question_table . " ON " . $form_builder_question_table . ".question_id = " . $form_builder_answer_table . ".question_id JOIN " . $profile_table . " on " . $form_builder_answer_table . ".profile_id = " . $profile_table . ".id JOIN " . $clients_table . " ON " . $clients_table . ".id = " . $profile_table . ".client_id ";

		$join_query .= " WHERE date_time >='" . $period_start_date . "' AND date_time <= '" . $period_end_date . "'";

		foreach ($where as $key => $search_params) {
			if (is_array($search_params)) {
				if ($search_params != "") {
					$counter = 0;
					foreach ($search_params as $k => $search_param) {
						if ($search_param != "") {
							if ($counter != 0) {
								$join_query .= ' OR  ';
							} else {
								$join_query .= ' AND ( ';
							}
							$join_query .= " question_answer like '%" . $search_param . "%'";
							$counter++;
						}
					}
					$join_query .= " ) ";
				}
			} else {
				if ($search_params != "") {
					$join_query .= " AND question_answer like '%" . $search_params . "%'";
				}
			}
		}

		if (!empty($where['checkin_minutes_prior'])) {
			$join_query .= " AND  MINUTE(date_time) <= " . $where['checkin_minutes_prior'];
		}

		if (!empty($where['checkin_minutes_after'])) {
			$join_query .= " AND MINUTE(date_time) >= " . $where['checkin_minutes_after'];
		}

		$join_query .= ' AND question_type_id = 25';

		$query = DB::query(Database::SELECT, $join_query);
		$result = $query->execute()->as_array();
		return $result;
	}

	public function generateRandomString($string)
	{
		return "TEST_" . str_shuffle($string) . rand(000, 999);
	}

	function generateRandomNumber()
	{
		return rand(0000000000, 9999999999);
	}
	function generateRandomEmail()
	{
		return uniqid() . "@gmail.com";
	}

	public function generateTestingData()
	{
		//$this->generateClientData();
		$this->generateFormBuilderAnswerData();
	}

	public function generateClientData()
	{
		$query = "SELECT id,client_name,address,telephone,email FROM `testing_clients`";
		$clients =	DB::query(Database::SELECT, $query)->execute()->as_array();

		foreach ($clients as $key => $client) {

			$update_records = array(
				'client_name' => $this->generateRandomString($client['client_name']),
				'address' => $this->generateRandomString($client['address']),
				'telephone' => $this->generateRandomNumber(),
				'email' => $this->generateRandomEmail(),
			);

			$query = DB::update('testing_clients')->set($update_records)->where('id', '=', $client['id']);
			$query->execute();
		}
		die('Client update done');
	}

	public function generateFormBuilderAnswerData()
	{
		$formbuilder_answer_query = "SELECT client_name, testing_clients.address, testing_clients.telephone, testing_clients.email, form_builder_answers.profile_id,form_builder_answers.form_builder_answer_id, DATE_FORMAT( date_time, '%Y-%m-%dT%H:%i:%s+00:00' ) AS date_time, question_answer, question_text, testing_form_builder_question.profile_id AS profile_id, question_type_id  FROM `form_builder_answers` JOIN testing_form_builder_question ON testing_form_builder_question.question_id = form_builder_answers.question_id JOIN testing_profiles on form_builder_answers.profile_id = testing_profiles.id JOIN testing_clients ON testing_clients.id = testing_profiles.client_id WHERE question_type_id = 25 ";

		$formbuilder_answer = DB::query(Database::SELECT, $formbuilder_answer_query)->execute()->as_array();

		foreach ($formbuilder_answer as $key => $ans) {
			$new_generated_string = $ans['question_answer'];

			if (strpos($ans['question_answer'], ":::") != false) {
				$new_ans_string = explode(":::", $ans['question_answer']);
				$visitor_name1 = explode(' ', $new_ans_string[0]);
				$visitor_name = $visitor_name1[0];
				// $visitor_name = //str_shuffle($new_ans_string[0]);
				$telephone_no = '04' . rand(99999999, 00000000);
				echo $visitor_name . '<br>' . $key;
				unset($new_ans_string[0]);
				unset($new_ans_string[1]);
				array_unshift($new_ans_string, $visitor_name);
				array_splice($new_ans_string, 1, 0, $telephone_no);
				$new_generated_string = implode(':::', $new_ans_string);
			}

			$update_records = array(
				'question_answer' => $new_generated_string,
			);

			// DB::delete('testing_form_builder_answers')->where('form_builder_answer_id', '>', '0')->execute();


			$query = DB::update('testing_form_builder_answers')->set($update_records)->where('form_builder_answer_id', '=', $ans['form_builder_answer_id']);
			$query->execute();
		}
		die('Form builder answer update done');
	}
	public function generateRandomStringTest($length)
	{
		$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < strlen($length); $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
}
