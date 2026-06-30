<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Logout extends Controller_Template_Master
{

	public function action_index()
	{
		if (isset($_REQUEST['profile_id'])) {
			$profile_id = $_REQUEST['profile_id'];

			// delete logo, documents, pictures which temp uploaded
			//delete logo file
			$logo = DB::select('*')->from('logo')
				->where('logo.profile_id', '=', $profile_id)
				->where('is_temp', '=', 1)
				->execute()
				->as_array();
			foreach ($logo as $profile_logo) {
				unlink($profile_logo['logo_name']);
			}


			//delete document file
			$documents = DB::select('*')->from('documents')
				->where('profile_id', '=', $profile_id)
				->where('is_temp', '=', 1)
				->execute()
				->as_array();
			foreach ($documents as $profile_documents) {
				unlink($profile_documents['doc_name']);
			}

			//delete picture file
			$picture = DB::select('*')->from('picture')
				->where('profile_id', '=', $profile_id)
				->where('is_temp', '=', 1)
				->execute()
				->as_array();
			foreach ($picture as $profile_picture) {
				unlink($profile_picture['picture_name']);
			}


			// Delete Logo
			DB::delete('logo')->where('profile_id', '=', $profile_id)->where('is_temp', '=', 1)->execute();

			// Delete picture
			DB::delete('picture')->where('profile_id', '=', $profile_id)->where('is_temp', '=', 1)->execute();

			// Delete documents
			DB::delete('documents')->where('profile_id', '=', $profile_id)->where('is_temp', '=', 1)->execute();
		}



		$session = Session::instance();
		$session->delete('clientuserid');
		$session->delete('clientusername');
		$session->delete('is_sub_user');
		$session->delete('voc_user_id');
		$session->delete('profile_id_array');
		session_unset();
		$this->request->redirect(URL::base() . 'welcome/index');
	}
} // End Welcome
