<?php

/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Settings extends Model
{
	/*
	   * Insert Client
	   */
	public function getValueByTitle($title)
	{
		$row = DB::select('values')
			->from('settings')
			->where('title', '=', $title)
			->execute()
			->as_array();
		return $row[0]['values'];
	}
	public function getallsettings()
	{
		return DB::select()
			->from('settings')
			->execute()
			->as_array();
	}
	public function updateVisitationWebhookData($data)
	{
		DB::update('settings')->set($data)
			->where('title', '=', 'visitation_webhook_response')
			->execute();
	}
	public function updateVisitationWebhookData2($data)
	{
		DB::update('settings')->set($data)
			->where('title', '=', 'visitation_webhook_response2')
			->execute();
	}
}
