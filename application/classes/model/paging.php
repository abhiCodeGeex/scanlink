<?php

class Model_Paging extends Model
{

    public $entity = "";
    public $perpage = "";
    public $count = 0;
    public $pages = 0;
    public $filter = "";
    public $filter_on = "";
    public $filter_by = "asc";
    public $group_by = '';
    public $results = null;
    public $curpage = 1;
    public $showlinks = 5;
    public $params = null;
    public $links = null;
    public $results_answer = null;
    public $show_profiles_code = null;

    public function getFilteredResult($entiry, $filter = "", $filter_on, $curpage = 1, $perpage = 10, $params = array(), $filter_by = 'asc', $show_profiles_code = "")
    {
        $this->entity = $entiry;
        $this->filter = $filter;
        $this->perpage = $perpage;
        $this->curpage = $curpage;
        $this->filter_on = 'profiles.' . $filter_on;
        $this->params = $params;

        $this->show_profiles_code = $show_profiles_code;
        if ($filter_by == 'asc' || $filter_by == 'desc') {
            $this->filter_by = $filter_by;
        }

        $this->countResult();
        $this->countPages();

        $this->getResults();
        $this->getLinks();

        return array('results' => $this->results, 'links' => $this->links);
    }

    public function getFilteredResultVoc($entiry, $filter = "", $filter_on, $curpage = 1, $perpage = 10, $params = array(), $filter_by = 'asc')
    {
        $this->entity = $entiry;
        $this->filter = $filter;
        $this->perpage = $perpage;
        $this->curpage = $curpage;
        $this->filter_on = 'profiles.' . $filter_on;
        $this->params = $params;
        if ($filter_by == 'asc' || $filter_by == 'desc') {
            $this->filter_by = $filter_by;
        }

        $this->countResultVoc();
        $this->countPages();

        $this->getResultsVoc();
        $this->getLinks();

        return array('results' => $this->results, 'links' => $this->links);
    }

    public function get_form_result($entiry, $filter = "", $filter_on, $curpage = 1, $perpage = 10, $params = array(), $filter_by = 'asc', $group_by = '')
    {
        $this->entity = $entiry;
        $this->filter = $filter;
        $this->perpage = $perpage;
        $this->curpage = $curpage;
        $this->group_by = $group_by;

        $new_filter = "";

        if (is_array($filter) && count($filter) > 0) {
            if (isset($filter["from_date"]) && $filter["from_date"] != "") {
                $new_filter .= " and DATE_FORMAT(date_time,'%Y-%m-%d')>=STR_TO_DATE('" . $filter["from_date"] . "', '%d/%m/%Y' )";
            }

            if (isset($filter["to_date"]) && $filter["to_date"] != "") {
                $new_filter .= " and DATE_FORMAT(date_time,'%Y-%m-%d')<=STR_TO_DATE('" . $filter["to_date"] . "', '%d/%m/%Y' )";
            }
        }

        if ($new_filter != "") {
            $this->filter = urlencode($new_filter);
        }

        $this->filter_on = $filter_on;
        $this->params = $params;

        if ($filter_by == 'asc' || $filter_by == 'desc') {
            $this->filter_by = $filter_by;
        }

        $this->count_result_form();
        $this->countPages();

        $this->get_results_for_form();
        $this->getLinks_forms();

        for ($i = 0; $i < count($this->results); $i++) {
            $this->get_answer_result($this->results[$i], $i);
        }
        return array('results' => $this->results, 'links' => $this->links, 'answers' => $this->results_answer);
    }

    public function getFilteredResultCodeBalance($entiry, $filter = "", $filter_on, $curpage = 1, $perpage = 10, $params = array(), $filter_by = 'asc')
    {
        $this->entity = $entiry;
        $this->filter = $filter;
        $this->perpage = $perpage;
        $this->curpage = $curpage;
        $this->filter_on = 'profiles.' . $filter_on;
        $this->params = $params;
        if ($filter_by == 'asc' || $filter_by == 'desc') {
            $this->filter_by = $filter_by;
        }

        $this->countResultCodeBalance();
        $this->countPages();

        $this->getResultsCodeBalance();
        $this->getLinks_for_code_balance();

        return array('results' => $this->results, 'links' => $this->links);
    }

    public function getResults()
    {


        $ul = ($this->curpage - 1) * $this->perpage;

        if ($this->show_profiles_code != "") {
            //LEFT JOIN users on profiles.user_id = users.id

            if ($this->filter !== "") {
                if (isset($this->params['type_id'])) {

                    $query = DB::query(
                        Database::SELECT,
                        "SELECT
							profiles.*,
							qrimage.id as 'qr_id',
							qrimage.qrimg_name,
							DATE_FORMAT(profiles.expired_at,'%d/%m/%Y') as expired_at,
							DATE_FORMAT(profiles.created_at,'%d/%m/%Y') as created_at,
							DATEDIFF( `activation_start_date` , curdate( ) ) AS interval_from_start_date,
							DATEDIFF( `activation_end_date` , curdate( ) ) AS interval_from_end_date,
							if(`expired_at` > now(),if(`expired_at` > DATE_ADD(now(),INTERVAL 30 DAY),'green','orange'),'red') as myclass
							FROM profiles LEFT JOIN qrimage ON qrimage.profile_id = profiles.id LEFT JOIN users on profiles.user_id = users.id where profiles.type_id = '" . $this->params['type_id'] . "' and
								profiles.client_id = '" . $this->params['client_id'] . "' and
								update_or_not = '1'
								and
								( code_profile_name LIKE '%" . $this->filter . "%' OR  profiles.id = '" . $this->filter . "' )  and profiles.id in (" . $this->show_profiles_code . ")
								GROUP By profiles.id
								order by " . $this->filter_on . " " . $this->filter_by .
                            " limit " . $ul . "," . $this->perpage
                    );
                } else {
                    $query = DB::query(
                        Database::SELECT,
                        "SELECT
							profiles.*,
							qrimage.id as 'qr_id',
							qrimage.qrimg_name,
							DATE_FORMAT(profiles.expired_at,'%d/%m/%Y') as expired_at,
							DATE_FORMAT(profiles.created_at,'%d/%m/%Y') as created_at,
							DATEDIFF( `activation_start_date` , curdate( ) ) AS interval_from_start_date,
							DATEDIFF( `activation_end_date` , curdate( ) ) AS interval_from_end_date,
							if(`expired_at` > now(),if(`expired_at` > DATE_ADD(now(),INTERVAL 30 DAY),'green','orange'),'red') as myclass
							FROM profiles LEFT JOIN qrimage ON qrimage.profile_id = profiles.id LEFT JOIN users on profiles.user_id = users.id
							where profiles.client_id = '" . $this->params['client_id'] . "' and
								update_or_not = '1' and
								profiles.type_id >0
								and
								( code_profile_name LIKE '%" . $this->filter . "%' OR  profiles.id = '" . $this->filter . "' )  and profiles.id in (" . $this->show_profiles_code . ")
								GROUP By profiles.id
								order by " . $this->filter_on . " " . $this->filter_by .
                            " limit " . $ul . "," . $this->perpage
                    );
                }
            } else {


                if (isset($this->params['type_id'])) {

                    $query = DB::query(Database::SELECT, "SELECT profiles.*, (select count(*) from voc_documents where profile_id=profiles.id and (DATEDIFF(expiry_date,curdate()))<=30 and (DATEDIFF(expiry_date,curdate()))>0 ) as voc_doc_near_expiry_count,(select count(*) from voc_documents where profile_id=profiles.id and (DATEDIFF(expiry_date,curdate()))<=0  ) as voc_doc_expired,
									qrimage.id as 'qr_id',
									qrimage.qrimg_name,
									DATE_FORMAT(profiles.expired_at,'%d/%m/%Y') as expired_at ,
									DATE_FORMAT(profiles.created_at,'%d/%m/%Y') as created_at,
									DATEDIFF( `activation_start_date` , curdate( ) ) AS interval_from_start_date,
									DATEDIFF( `activation_end_date` , curdate( ) ) AS interval_from_end_date,
									if(`expired_at` > now(),if(`expired_at` > DATE_ADD(now(),INTERVAL 30 DAY),'green','orange'),'red') as myclass
									FROM profiles LEFT JOIN qrimage ON qrimage.profile_id = profiles.id LEFT JOIN users on profiles.user_id = users.id
									where profiles.type_id = '" . $this->params['type_id'] . "' and
									profiles.client_id = '" . $this->params['client_id'] . "' and
									update_or_not = '1'  and profiles.id in (" . $this->show_profiles_code . ")  
									GROUP By profiles.id
									order by " . $this->filter_on . " " . $this->filter_by .
                        " limit " . $ul . "," . $this->perpage);
                } else {


                    $query = DB::query(
                        Database::SELECT,
                        "SELECT profiles.*,
									qrimage.id as 'qr_id',
									qrimage.qrimg_name,
									DATE_FORMAT(profiles.expired_at,'%d/%m/%Y') as expired_at ,
									DATE_FORMAT(profiles.created_at,'%d/%m/%Y') as created_at,
									DATEDIFF( `activation_start_date` , curdate( ) ) AS interval_from_start_date,
									DATEDIFF( `activation_end_date` , curdate( ) ) AS interval_from_end_date,
									if(`expired_at` > now(),if(`expired_at` > DATE_ADD(now(),INTERVAL 30 DAY),'green','orange'),'red') as myclass
									FROM profiles LEFT JOIN qrimage ON qrimage.profile_id = profiles.id LEFT JOIN users on profiles.user_id = users.id
									where profiles.client_id = '" . $this->params['client_id'] . "' and
									update_or_not = '1'  and
									profiles.type_id >0  and profiles.id in (" . $this->show_profiles_code . ")
									GROUP By profiles.id
									order by " . $this->filter_on . " " . $this->filter_by .
                            " limit " . $ul . "," . $this->perpage
                    );
                }
            }
        } else {

            if ($this->filter !== "") {

                if (isset($this->params['type_id'])) {

                    $query = DB::query(
                        Database::SELECT,
                        "SELECT
							profiles.*,
							qrimage.id as 'qr_id',
							qrimage.qrimg_name,
							DATE_FORMAT(profiles.expired_at,'%d/%m/%Y') as expired_at,
							DATE_FORMAT(profiles.created_at,'%d/%m/%Y') as created_at,
							DATEDIFF( `activation_start_date` , curdate( ) ) AS interval_from_start_date,
							DATEDIFF( `activation_end_date` , curdate( ) ) AS interval_from_end_date,
							if(`expired_at` > now(),if(`expired_at` > DATE_ADD(now(),INTERVAL 30 DAY),'green','orange'),'red') as myclass
							FROM profiles LEFT JOIN qrimage ON qrimage.profile_id = profiles.id
							where profiles.type_id = '" . $this->params['type_id'] . "' and
								client_id = '" . $this->params['client_id'] . "' and
								update_or_not = '1'
								and
								( code_profile_name LIKE '%" . $this->filter . "%' OR  profiles.id = '" . $this->filter . "' )
								GROUP By profiles.id
								order by " . $this->filter_on . " " . $this->filter_by .
                            " limit " . $ul . "," . $this->perpage
                    );
                } else {

                    $query = DB::query(
                        Database::SELECT,
                        "SELECT
							profiles.*,
							qrimage.id as 'qr_id',
							qrimage.qrimg_name,
							DATE_FORMAT(profiles.expired_at,'%d/%m/%Y') as expired_at,
							DATE_FORMAT(profiles.created_at,'%d/%m/%Y') as created_at,
							DATEDIFF( `activation_start_date` , curdate( ) ) AS interval_from_start_date,
							DATEDIFF( `activation_end_date` , curdate( ) ) AS interval_from_end_date,
							if(`expired_at` > now(),if(`expired_at` > DATE_ADD(now(),INTERVAL 30 DAY),'green','orange'),'red') as myclass
							FROM profiles LEFT JOIN qrimage ON qrimage.profile_id = profiles.id
							where client_id = '" . $this->params['client_id'] . "' and
								update_or_not = '1' and
								profiles.type_id >0
								and
								( code_profile_name LIKE '%" . $this->filter . "%' OR  profiles.id = '" . $this->filter . "' )
								GROUP By profiles.id
								order by " . $this->filter_on . " " . $this->filter_by .
                            " limit " . $ul . "," . $this->perpage
                    );
                }
            } else {

                if (isset($this->params['type_id'])) {

                    $query = DB::query(Database::SELECT, "SELECT profiles.*, (select count(*) from voc_documents where profile_id=profiles.id and (DATEDIFF(expiry_date,curdate()))<=30 and (DATEDIFF(expiry_date,curdate()))>0 ) as voc_doc_near_expiry_count,(select count(*) from voc_documents where profile_id=profiles.id and (DATEDIFF(expiry_date,curdate()))<=0  ) as voc_doc_expired,
									qrimage.id as 'qr_id',
									qrimage.qrimg_name,
									DATE_FORMAT(profiles.expired_at,'%d/%m/%Y') as expired_at ,
									DATE_FORMAT(profiles.created_at,'%d/%m/%Y') as created_at,
									DATEDIFF( `activation_start_date` , curdate( ) ) AS interval_from_start_date,
									DATEDIFF( `activation_end_date` , curdate( ) ) AS interval_from_end_date,
									if(`expired_at` > now(),if(`expired_at` > DATE_ADD(now(),INTERVAL 30 DAY),'green','orange'),'red') as myclass
									FROM profiles LEFT JOIN qrimage ON qrimage.profile_id = profiles.id
									where profiles.type_id = '" . $this->params['type_id'] . "' and
									client_id = '" . $this->params['client_id'] . "' and
									update_or_not = '1'
									GROUP By profiles.id
									order by " . $this->filter_on . " " . $this->filter_by .
                        " limit " . $ul . "," . $this->perpage);
                } else {

                    $query = DB::query(Database::SELECT, "SELECT profiles.*,
									qrimage.id as 'qr_id',
									qrimage.qrimg_name,
									DATE_FORMAT(profiles.expired_at,'%d/%m/%Y') as expired_at ,
									DATE_FORMAT(profiles.created_at,'%d/%m/%Y') as created_at,
									DATEDIFF( `activation_start_date` , curdate( ) ) AS interval_from_start_date,
									DATEDIFF( `activation_end_date` , curdate( ) ) AS interval_from_end_date,
									if(`expired_at` > now(),if(`expired_at` > DATE_ADD(now(),INTERVAL 30 DAY),'green','orange'),'red') as myclass
									FROM profiles LEFT JOIN qrimage ON qrimage.profile_id = profiles.id
									where client_id = '" . $this->params['client_id'] . "' and
									update_or_not = '1'  and
									profiles.type_id >0
									GROUP By profiles.id
									order by " . $this->filter_on . " " . $this->filter_by .
                        " limit " . $ul . "," . $this->perpage);
                }
            }
        }




        // echo $query;exit;
        /* foreach($this->params as $key => $val){
        $query->and_where($key,"=",$val);
        } */



        $this->results = $query->execute()->as_array();
    }

    public function getResultsVoc()
    {
        $ul = ($this->curpage - 1) * $this->perpage;

        $session = Session::instance();
        $profile_id_array = implode(",", $session->get('profile_id_array'));

        if ($this->filter !== "") {

            if (isset($this->params['type_id'])) {

                $query = DB::query(
                    Database::SELECT,
                    "SELECT
							profiles.*,
							qrimage.id as 'qr_id',
							qrimage.qrimg_name,
							DATE_FORMAT(profiles.expired_at,'%d/%m/%Y') as expired_at,
							DATE_FORMAT(profiles.created_at,'%d/%m/%Y') as created_at,
							DATEDIFF( `activation_start_date` , curdate( ) ) AS interval_from_start_date,
							DATEDIFF( `activation_end_date` , curdate( ) ) AS interval_from_end_date,
							if(`expired_at` > now(),if(`expired_at` > DATE_ADD(now(),INTERVAL 30 DAY),'green','orange'),'red') as myclass
							FROM profiles LEFT JOIN qrimage ON qrimage.profile_id = profiles.id
							where profiles.type_id = '" . $this->params['type_id'] . "' and
								profiles.id in(" . $profile_id_array . ") and
								update_or_not = '1'
								and
								( code_profile_name LIKE '%" . $this->filter . "%' OR  profiles.id = '" . $this->filter . "' )
								GROUP By profiles.id
								order by " . $this->filter_on . " " . $this->filter_by .
                        " limit " . $ul . "," . $this->perpage
                );
            }
        } else {

            if (isset($this->params['type_id'])) {

                $query = DB::query(Database::SELECT, "SELECT profiles.*, (select count(*) from voc_documents where profile_id=profiles.id and (DATEDIFF(expiry_date,curdate()))<=30 and (DATEDIFF(expiry_date,curdate()))>0 ) as voc_doc_near_expiry_count,(select count(*) from voc_documents where profile_id=profiles.id and (DATEDIFF(expiry_date,curdate()))<=0  ) as voc_doc_expired,
									qrimage.id as 'qr_id',
									qrimage.qrimg_name,
									DATE_FORMAT(profiles.expired_at,'%d/%m/%Y') as expired_at ,
									DATE_FORMAT(profiles.created_at,'%d/%m/%Y') as created_at,
									DATEDIFF( `activation_start_date` , curdate( ) ) AS interval_from_start_date,
									DATEDIFF( `activation_end_date` , curdate( ) ) AS interval_from_end_date,
									if(`expired_at` > now(),if(`expired_at` > DATE_ADD(now(),INTERVAL 30 DAY),'green','orange'),'red') as myclass
									FROM profiles LEFT JOIN qrimage ON qrimage.profile_id = profiles.id
									where profiles.type_id = '" . $this->params['type_id'] . "' and
									profiles.id in(" . $profile_id_array . ") and
									update_or_not = '1'
									GROUP By profiles.id
									order by " . $this->filter_on . " " . $this->filter_by .
                    " limit " . $ul . "," . $this->perpage);
            }
        }

        $this->results = $query->execute()->as_array();
    }

    public function getResults_old()
    {

        $ul = ($this->curpage - 1) * $this->perpage;

        if ($this->filter !== "") {

            if (isset($this->params['type_id'])) {

                $query = DB::query(
                    Database::SELECT,
                    "SELECT
							profiles.*,
							qrimage.id as 'qr_id',
							qrimage.qrimg_name,
							DATE_FORMAT(profiles.expired_at,'%d/%m/%Y') as expired_at,
							DATE_FORMAT(profiles.created_at,'%d/%m/%Y') as created_at,
							if(`expired_at` > now(),if(`expired_at` > DATE_ADD(now(),INTERVAL 30 DAY),'green','orange'),'red') as myclass
							FROM profiles LEFT JOIN qrimage ON qrimage.profile_id = profiles.id
							where profiles.type_id = '" . $this->params['type_id'] . "' and
								client_id = '" . $this->params['client_id'] . "' and
								deleted = 0 And
								update_or_not = '1'
								and
								( code_profile_name LIKE '%" . $this->filter . "%' OR  profiles.id = '" . $this->filter . "' )
								GROUP By profiles.id
								order by " . $this->filter_on . " " . $this->filter_by .
                        " limit " . $ul . "," . $this->perpage
                );
            } else {

                $query = DB::query(
                    Database::SELECT,
                    "SELECT
							profiles.*,
							qrimage.id as 'qr_id',
							qrimage.qrimg_name,
							DATE_FORMAT(profiles.expired_at,'%d/%m/%Y') as expired_at,
							DATE_FORMAT(profiles.created_at,'%d/%m/%Y') as created_at,
							if(`expired_at` > now(),if(`expired_at` > DATE_ADD(now(),INTERVAL 30 DAY),'green','orange'),'red') as myclass
							FROM profiles LEFT JOIN qrimage ON qrimage.profile_id = profiles.id
							where client_id = '" . $this->params['client_id'] . "' and
								deleted = 0 And
								update_or_not = '1'
								and
								( code_profile_name LIKE '%" . $this->filter . "%' OR  profiles.id = '" . $this->filter . "' )
								GROUP By profiles.id
								order by " . $this->filter_on . " " . $this->filter_by .
                        " limit " . $ul . "," . $this->perpage
                );
            }

            /* $query = DB::select()->from($this->entity)
        ->where($this->filter_on, 'LIKE', $this->filter.'%')
        ->order_by($this->filter_on, 'ASC')->limit($this->perpage)->offset($ul); */
        } else {

            if (isset($this->params['type_id'])) {
                $query = DB::query(Database::SELECT, "SELECT profiles.*,
									qrimage.id as 'qr_id',
									qrimage.qrimg_name,
									DATE_FORMAT(profiles.expired_at,'%d/%m/%Y') as expired_at ,
									DATE_FORMAT(profiles.created_at,'%d/%m/%Y') as created_at,
									if(`expired_at` > now(),if(`expired_at` > DATE_ADD(now(),INTERVAL 30 DAY),'green','orange'),'red') as myclass
									FROM profiles LEFT JOIN qrimage ON qrimage.profile_id = profiles.id
									where
									profiles.type_id = '" . $this->params['type_id'] . "' and
									client_id = '" . $this->params['client_id'] . "' and
									deleted = 0 and
									update_or_not = '1'
									GROUP By profiles.id
									order by " . $this->filter_on . " " . $this->filter_by .
                    " limit " . $ul . "," . $this->perpage);
            } else {
                $query = DB::query(Database::SELECT, "SELECT profiles.*,
									qrimage.id as 'qr_id',
									qrimage.qrimg_name,
									DATE_FORMAT(profiles.expired_at,'%d/%m/%Y') as expired_at ,
									DATE_FORMAT(profiles.created_at,'%d/%m/%Y') as created_at,
									if(`expired_at` > now(),if(`expired_at` > DATE_ADD(now(),INTERVAL 30 DAY),'green','orange'),'red') as myclass
									FROM profiles LEFT JOIN qrimage ON qrimage.profile_id = profiles.id
									where client_id = '" . $this->params['client_id'] . "' and
									deleted = 0 and
									update_or_not = '1'
									GROUP By profiles.id
									order by " . $this->filter_on . " " . $this->filter_by .
                    " limit " . $ul . "," . $this->perpage);
            }
        }

        //echo $query; exit;
        /* foreach($this->params as $key => $val){
        $query->and_where($key,"=",$val);
        } */

        $this->results = $query->execute()->as_array();
    }

    public function get_results_for_form()
    {
        $ul = ($this->curpage - 1) * $this->perpage;

        /*$query = DB::query(Database::SELECT, "SELECT session_id,app_user_firstname,app_user_lastname,app_user_email,app_user_mobile,
        DATE_FORMAT(date_time,'%d/%m/%Y %H:%i') as date_time_formated
        FROM form_builder_answers
        where profile_id = '".$this->params['profile_id'] ."' ".urldecode($this->filter)."
        GROUP BY ".$this->group_by."
        ORDER BY " . $this->filter_on . " " . $this->filter_by .
        " limit " . $ul . "," . $this->perpage);*/

        // Ravi: no idea why self join is used, its making query slow

        /*if ($this->group_by != '') {
            $query = DB::query(Database::SELECT, "SELECT  session_id,app_user_firstname,app_user_lastname,app_user_email,app_user_mobile,
									DATE_FORMAT(date_time,'%d/%m/%Y %H:%i') as date_time_formated
									FROM (

									SELECT *
									FROM form_builder_answers
									ORDER BY " . $this->filter_on . " " . $this->filter_by . "
									) AS records
									where profile_id = '" . $this->params['profile_id'] . "' " . urldecode($this->filter) . "
									GROUP BY " . $this->group_by . "
									ORDER BY " . $this->filter_on . " " . $this->filter_by . "
                         limit " . $ul . "," . $this->perpage);
        } else {
            $query = DB::query(Database::SELECT, "SELECT  session_id,app_user_firstname,app_user_lastname,app_user_email,app_user_mobile,
									DATE_FORMAT(date_time,'%d/%m/%Y %H:%i') as date_time_formated
									FROM (

									SELECT *
									FROM form_builder_answers
									ORDER BY " . $this->filter_on . " " . $this->filter_by . "
									) AS records
									where profile_id = '" . $this->params['profile_id'] . "' " . urldecode($this->filter) . "
									ORDER BY " . $this->filter_on . " " . $this->filter_by . "
                         limit " . $ul . "," . $this->perpage);
        }*/

        if ($this->group_by != '') {
            $query = DB::query(Database::SELECT, "SELECT  session_id,app_user_firstname,app_user_lastname,app_user_email,app_user_mobile,
									DATE_FORMAT(date_time,'%d/%m/%Y %H:%i') as date_time_formated

									FROM form_builder_answers

									where profile_id = '" . $this->params['profile_id'] . "' " . urldecode($this->filter) . "
									GROUP BY " . $this->group_by . "
									ORDER BY " . $this->filter_on . " " . $this->filter_by . "
                         limit " . $ul . "," . $this->perpage);
        } else {
            $query = DB::query(Database::SELECT, "SELECT  session_id,app_user_firstname,app_user_lastname,app_user_email,app_user_mobile,
									DATE_FORMAT(date_time,'%d/%m/%Y %H:%i') as date_time_formated

									FROM form_builder_answers

									where profile_id = '" . $this->params['profile_id'] . "' " . urldecode($this->filter) . "
									ORDER BY " . $this->filter_on . " " . $this->filter_by . "
                         limit " . $ul . "," . $this->perpage);
        }

        $this->results = $query->execute()->as_array();
    }

    public function getResultsCodeBalance()
    {

        $ul = ($this->curpage - 1) * $this->perpage;

        if ($this->filter != "") {
            $query = DB::query(
                Database::SELECT,
                "SELECT
							profiles.*,
							DATE_FORMAT(profiles.expired_at,'%d/%m/%Y') as expired_at,
							DATE_FORMAT(profiles.created_at,'%d/%m/%Y') as created_at,
							if(`expired_at` > now(),if(`expired_at` > DATE_ADD(now(),INTERVAL 30 DAY),'green','orange'),'red') as myclass
							FROM profiles
							where
								client_id = '" . $this->params['client_id'] . "' and
								update_or_not = '0' and
								id='" . $this->filter . "'
								GROUP By profiles.id
								order by " . $this->filter_on . " " . $this->filter_by .
                    " limit " . $ul . "," . $this->perpage
            );
        } else {
            $query = DB::query(
                Database::SELECT,
                "SELECT
							profiles.*,
							DATE_FORMAT(profiles.expired_at,'%d/%m/%Y') as expired_at,
							DATE_FORMAT(profiles.created_at,'%d/%m/%Y') as created_at,
							if(`expired_at` > now(),if(`expired_at` > DATE_ADD(now(),INTERVAL 30 DAY),'green','orange'),'red') as myclass
							FROM profiles
							where
								client_id = '" . $this->params['client_id'] . "' and
								update_or_not = '0'
								GROUP By profiles.id
								order by " . $this->filter_on . " " . $this->filter_by .
                    " limit " . $ul . "," . $this->perpage
            );
        }

        /* $arr = $query->execute()->as_array();
        $id_arr= array();
        foreach($arr as $val)
        {
        array_push($id_arr,$val["id"]);
        }
        echo array_search('980', $id_arr);*/

        $this->results = $query->execute()->as_array();
    }

    public function countResult()
    {
        $query = "";
        if ($this->show_profiles_code != "") {

            if ($this->filter !== "" && $this->filter_on !== "") {
                if (isset($this->params['type_id'])) {
                    $query = DB::query(Database::SELECT, "SELECT profiles.id FROM profiles LEFT JOIN qrimage ON qrimage.profile_id = profiles.id LEFT JOIN users on profiles.user_id = users.id  where profiles.type_id = '" . $this->params['type_id'] . "' and profiles.client_id = '" . $this->params['client_id'] . "' and deleted = 0 and  update_or_not = '1' and ( name LIKE '%" . $this->filter . "%' OR  profiles.id = '" . $this->filter . "' ) and profiles.id in (" . $this->show_profiles_code . ")  GROUP By profiles.id ");
                } else {
                    $query = DB::query(Database::SELECT, "SELECT profiles.id FROM profiles LEFT JOIN qrimage ON qrimage.profile_id = profiles.id LEFT JOIN users on profiles.user_id = users.id  where profiles.client_id = '" . $this->params['client_id'] . "' and deleted = 0 and  update_or_not = '1' and ( name LIKE '%" . $this->filter . "%' OR  profiles.id = '" . $this->filter . "' ) and profiles.id in (" . $this->show_profiles_code . ") GROUP By profiles.id ");
                }
            } else {
                if (isset($this->params['type_id'])) {
                    $query = DB::query(Database::SELECT, "SELECT profiles.id FROM profiles LEFT JOIN qrimage ON qrimage.profile_id = profiles.id LEFT JOIN users on profiles.user_id = users.id  where profiles.type_id = '" . $this->params['type_id'] . "' and profiles.client_id = '" . $this->params['client_id'] . "' and deleted = 0 AND update_or_not = '1' and profiles.id in (" . $this->show_profiles_code . ") GROUP By profiles.id  ");
                } else {
                    $query = DB::query(Database::SELECT, "SELECT profiles.id FROM profiles LEFT JOIN qrimage ON qrimage.profile_id = profiles.id LEFT JOIN users on profiles.user_id = users.id   where profiles.client_id = '" . $this->params['client_id'] . "' and deleted = 0 AND update_or_not = '1' and profiles.id in (" . $this->show_profiles_code . ") GROUP By profiles.id  ");
                }
            }
        } else {
            if ($this->filter !== "" && $this->filter_on !== "") {
                if (isset($this->params['type_id'])) {
                    $query = DB::query(Database::SELECT, "SELECT profiles.id FROM profiles LEFT JOIN qrimage ON qrimage.profile_id = profiles.id  where profiles.type_id = '" . $this->params['type_id'] . "' and client_id = '" . $this->params['client_id'] . "' and deleted = 0 and  update_or_not = '1' and ( name LIKE '%" . $this->filter . "%' OR  profiles.id = '" . $this->filter . "' ) GROUP By profiles.id ");
                } else {
                    $query = DB::query(Database::SELECT, "SELECT profiles.id FROM profiles LEFT JOIN qrimage ON qrimage.profile_id = profiles.id  where client_id = '" . $this->params['client_id'] . "' and deleted = 0 and  update_or_not = '1' and ( name LIKE '%" . $this->filter . "%' OR  profiles.id = '" . $this->filter . "' ) GROUP By profiles.id ");
                }
            } else {
                if (isset($this->params['type_id'])) {
                    $query = DB::query(Database::SELECT, "SELECT profiles.id FROM profiles LEFT JOIN qrimage ON qrimage.profile_id = profiles.id  where profiles.type_id = '" . $this->params['type_id'] . "' and client_id = '" . $this->params['client_id'] . "' and deleted = 0 AND update_or_not = '1' GROUP By profiles.id  ");
                } else {
                    $query = DB::query(Database::SELECT, "SELECT profiles.id FROM profiles LEFT JOIN qrimage ON qrimage.profile_id = profiles.id  where client_id = '" . $this->params['client_id'] . "' and deleted = 0 AND update_or_not = '1' GROUP By profiles.id  ");
                }
            }
        }
        $this->count = count($query->execute()->as_array());
    }

    public function countResultVoc()
    {
        $query = "";

        $session = Session::instance();
        $profile_id_array = implode(",", $session->get('profile_id_array'));

        if ($this->filter !== "" && $this->filter_on !== "") {
            if (isset($this->params['type_id'])) {
                $query = DB::query(Database::SELECT, "SELECT profiles.id FROM profiles LEFT JOIN qrimage ON qrimage.profile_id = profiles.id  where profiles.id in(" . $profile_id_array . ") and deleted = 0 and  update_or_not = '1' and ( name LIKE '%" . $this->filter . "%' OR  profiles.id = '" . $this->filter . "' ) GROUP By profiles.id ");
            }
        } else {
            if (isset($this->params['type_id'])) {
                $query = DB::query(Database::SELECT, "SELECT profiles.id FROM profiles LEFT JOIN qrimage ON qrimage.profile_id = profiles.id  where profiles.id in(" . $profile_id_array . ") and deleted = 0 AND update_or_not = '1' GROUP By profiles.id  ");
            }
        }

        $this->count = count($query->execute()->as_array());
    }

    public function count_result_form()
    {
        if ($this->group_by != '') {
            $query = DB::query(Database::SELECT, "SELECT form_builder_answer_id FROM form_builder_answers where profile_id =" . $this->params['profile_id'] . " " . urldecode($this->filter) . " GROUP By " . $this->group_by . "  ");
        } else {
            $query = DB::query(Database::SELECT, "SELECT form_builder_answer_id FROM form_builder_answers where profile_id =" . $this->params['profile_id'] . " " . urldecode($this->filter) . " ");
        }

        $this->count = count($query->execute()->as_array());
    }

    public function get_answer_result($session_id, $k)
    {

        $query = DB::query(Database::SELECT, "SELECT question_id,question_answer FROM form_builder_answers where profile_id =" . $this->params['profile_id'] . " AND session_id ='" . $session_id['session_id'] . "' ");
        $this->results_answer[$k] = $query->execute()->as_array();
    }
    public function countResultCodeBalance()
    {
        $query = "";

        if ($this->filter != "") {
            $query = DB::query(Database::SELECT, "SELECT profiles.id FROM profiles where  client_id = '" . $this->params['client_id'] . "' and  update_or_not = '0' and id = '" . $this->filter . "'  GROUP By profiles.id ");
        } else {
            $query = DB::query(Database::SELECT, "SELECT profiles.id FROM profiles where  client_id = '" . $this->params['client_id'] . "' and  update_or_not = '0'  GROUP By profiles.id ");
        }

        $this->count = count($query->execute()->as_array());
    }

    public function countPages()
    {
        $pages = $this->count / $this->perpage;
        $this->pages = is_real($pages) ? (int) $pages + 1 : $pages;
    }

    public function getLinks()
    {
        $links = array();
        if ($this->filter_on == 'profiles.id') {
            $filter_on = 'code';
        } else if ($this->filter_on == 'profiles.created_at' || $this->filter_on == 'profiles.expired_at') {
            $filter_on = 'exiprydate';
        } else {
            $filter_on = 'code_profile_name';
        }

        if ($this->pages > 1) {
            if ($this->curpage != 1) {
                $first = array('label' => '&lt;&lt;', 'href' => 'getResults(1,\'' . $this->filter . '\',\'' . $filter_on . '\',\'' . $this->filter_by . '\')', 'myClass' => 'pagelink');
                array_push($links, $first);

                $prev = array('label' => '&lt;', 'href' => 'getResults(' . ($this->curpage - 1) . ',\'' . $this->filter . '\',\'' . $filter_on . '\',\'' . $this->filter_by . '\')', 'myClass' => 'pagelink');
                array_push($links, $prev);
            }

            for ($i = 1; $i <= $this->pages; $i++) {
                $class = "";
                if ($this->curpage == $i) {
                    $class = "pagelink current";
                } else {
                    $class = "pagelink";
                }
                array_push($links, array('label' => $i, 'href' => 'getResults(' . $i . ',\'' . $this->filter . '\',\'' . $filter_on . '\',\'' . $this->filter_by . '\')', 'myClass' => $class));
            }

            if ($this->curpage != $this->pages) {
                $next = array('label' => '&gt;', 'href' => 'getResults(' . ($this->curpage + 1) . ',\'' . $this->filter . '\',\'' . $filter_on . '\',\'' . $this->filter_by . '\')', 'myClass' => 'pagelink');
                array_push($links, $next);

                $last = array('label' => '&gt;&gt;', 'href' => 'getResults(' . $this->pages . ',\'' . $this->filter . '\',\'' . $filter_on . '\',\'' . $this->filter_by . '\')', 'myClass' => 'pagelink');
                array_push($links, $last);
            }
        }
        $this->links = $links;
    }

    public function getLinks_forms()
    {
        $links = array();

        if ($this->filter != "") {
            $this->filter = "date";
        }

        if ($this->pages > 1) {
            if ($this->curpage != 1) {
                $first = array('label' => '&lt;&lt;', 'href' => 'getResults(1,\'' . $this->filter . '\',\'' . $this->filter_on . '\',\'' . $this->filter_by . '\')', 'myClass' => 'pagelink');
                array_push($links, $first);

                $prev = array('label' => '&lt;', 'href' => 'getResults(' . ($this->curpage - 1) . ',\'' . $this->filter . '\',\'' . $this->filter_on . '\',\'' . $this->filter_by . '\')', 'myClass' => 'pagelink');
                array_push($links, $prev);
            }

            for ($i = 1; $i <= $this->pages; $i++) {
                $class = "";
                if ($this->curpage == $i) {
                    $class = "pagelink current";
                } else {
                    $class = "pagelink";
                }
                array_push($links, array('label' => $i, 'href' => 'getResults(' . $i . ',\'' . $this->filter . '\',\'' . $this->filter_on . '\',\'' . $this->filter_by . '\')', 'myClass' => $class));
            }

            if ($this->curpage != $this->pages) {
                $next = array('label' => '&gt;', 'href' => 'getResults(' . ($this->curpage + 1) . ',\'' . $this->filter . '\',\'' . $this->filter_on . '\',\'' . $this->filter_by . '\')', 'myClass' => 'pagelink');
                array_push($links, $next);

                $last = array('label' => '&gt;&gt;', 'href' => 'getResults(' . $this->pages . ',\'' . $this->filter . '\',\'' . $this->filter_on . '\',\'' . $this->filter_by . '\')', 'myClass' => 'pagelink');
                array_push($links, $last);
            }
        }
        $this->links = $links;
    }

    public function getLinks_for_code_balance()
    {
        $links = array();
        if ($this->filter_on == 'profiles.id') {
            $filter_on = 'code';
        } else if ($this->filter_on == 'profiles.created_at' || $this->filter_on == 'profiles.expired_at') {
            $filter_on = 'exiprydate';
        } else {
            $filter_on = 'code_profile_name';
        }

        if ($this->pages > 1) {
            if ($this->curpage != 1) {
                $first = array('label' => '&lt;&lt;', 'href' => 'getResults(1,\'' . $this->filter . '\',\'' . $filter_on . '\',\'' . $this->filter_by . '\')', 'myClass' => 'pagelink');
                array_push($links, $first);

                $prev = array('label' => '&lt;', 'href' => 'getResults(' . ($this->curpage - 1) . ',\'' . $this->filter . '\',\'' . $filter_on . '\',\'' . $this->filter_by . '\')', 'myClass' => 'pagelink');
                array_push($links, $prev);
            }

            for ($i = 1; $i <= $this->pages; $i++) {
                $class = "";
                if ($this->curpage == $i) {
                    $class = "pagelink current";
                } else {
                    $class = "pagelink";
                }
                //array_push($links, array('label' => $i, 'href' => 'getResults(' . $i . ',\'' . $this->filter . '\',\'' . $filter_on . '\',\'' . $this->filter_by . '\')', 'myClass' => $class));
            }

            array_push($links, array('current_page' => $this->curpage, 'total_pages' => $this->pages));

            if ($this->curpage != $this->pages) {
                $next = array('label' => '&gt;', 'href' => 'getResults(' . ($this->curpage + 1) . ',\'' . $this->filter . '\',\'' . $filter_on . '\',\'' . $this->filter_by . '\')', 'myClass' => 'pagelink');
                array_push($links, $next);

                $last = array('label' => '&gt;&gt;', 'href' => 'getResults(' . $this->pages . ',\'' . $this->filter . '\',\'' . $filter_on . '\',\'' . $this->filter_by . '\')', 'myClass' => 'pagelink');
                array_push($links, $last);
            }
        }

        $this->links = $links;
    }

    public function get_expired_profile_result($entiry, $filter = "", $filter_on, $curpage = 1, $perpage = 10, $params = array(), $filter_by = 'asc')
    {
        $this->entity = $entiry;
        $this->filter = $filter;
        $this->perpage = $perpage;
        $this->curpage = $curpage;
        $this->filter_on = 'profiles.' . $filter_on;
        $this->params = $params;
        if ($filter_by == 'asc' || $filter_by == 'desc') {
            $this->filter_by = $filter_by;
        }

        $this->countResult_expired_profile();
        $this->countPages();

        $this->getResults_expired_profile();
        $this->getLinks();

        return array('results' => $this->results, 'links' => $this->links);
    }

    public function countResult_expired_profile()
    {
        $query = "";
        $user_id = $this->params['user_id'];

        if ($this->filter !== "" && $this->filter_on !== "") {

            $query = DB::query(Database::SELECT, "SELECT code_profile_name, id,user_id, type_id, expired_at,DATE_FORMAT(expired_at,'%d/%m/%Y') as expired_at_min, DATEDIFF(expired_at,now()) AS date_diff	FROM profiles where user_id = '" . $user_id . "' and ( code_profile_name LIKE '%" . $this->filter . "%' OR  profiles.id = '" . $this->filter . "' ) and (free_code=0 or (free_code=1 and renewal_required=1) ) and  expired_at!='0000-00-00 00:00:00' and (DATEDIFF(expired_at,now()))<=30  ");
        } else {
            $query = DB::query(Database::SELECT, "SELECT code_profile_name, id,user_id, type_id, expired_at,DATE_FORMAT(expired_at,'%d/%m/%Y') as expired_at_min, DATEDIFF(expired_at,now()) AS date_diff	FROM profiles where user_id = '" . $user_id . "' and (free_code=0 or (free_code=1 and renewal_required=1) ) and expired_at!='0000-00-00 00:00:00' and (DATEDIFF(expired_at,now()))<=30  ");
        }

        $this->count = count($query->execute()->as_array());
    }

    public function getResults_expired_profile()
    {

        if ($this->filter_on == "profiles.exiprydate") {
            $this->filter_on = "profiles.expired_at";
        }

        if ($this->filter_on == "profiles.code") {
            $this->filter_on = "profiles.id";
        }

        $ul = ($this->curpage - 1) * $this->perpage;
        $user_id = $this->params['user_id'];

        if ($this->filter !== "") {
            $query = DB::query(
                Database::SELECT,
                "SELECT
							code_profile_name, id,user_id,
							DATEDIFF(expired_at,now()) AS date_diff,
							DATE_FORMAT(profiles.expired_at,'%d/%m/%Y') as expired_at
							FROM profiles
							where user_id = '" . $user_id . "'
								and
								( code_profile_name LIKE '%" . $this->filter . "%' OR  profiles.id = '" . $this->filter . "' )
								and expired_at!='0000-00-00 00:00:00'  and (free_code=0 or (free_code=1 and renewal_required=1) )
								and (DATEDIFF(expired_at,now()))<=30
								order by " . $this->filter_on . " " . $this->filter_by .
                    " limit " . $ul . "," . $this->perpage
            );
        } else {

            $query = DB::query(Database::SELECT, "SELECT code_profile_name, id,user_id,
									DATEDIFF(expired_at,now()) AS date_diff,
									DATE_FORMAT(profiles.expired_at,'%d/%m/%Y') as expired_at
									FROM profiles
									where user_id = '" . $user_id . "'
									and expired_at!='0000-00-00 00:00:00'  and (free_code=0 or (free_code=1 and renewal_required=1) )
									and (DATEDIFF(expired_at,now()))<=30
									order by " . $this->filter_on . " " . $this->filter_by .
                " limit " . $ul . "," . $this->perpage);
        }

        $this->results = $query->execute()->as_array();
    }

    public function get_form_result_for_export($entiry, $filter = "", $filter_on, $curpage = 1, $perpage = 10, $params = array(), $filter_by = 'asc', $group_by = '')
    {
        $this->entity = $entiry;
        $this->filter = $filter;
        $this->perpage = $perpage;
        $this->curpage = $curpage;
        $this->group_by = $group_by;

        $new_filter = "";

        if (is_array($filter) && count($filter) > 0) {
            if (isset($filter["from_date"]) && $filter["from_date"] != "") {
                $new_filter .= " and DATE_FORMAT(date_time,'%Y-%m-%d')>=STR_TO_DATE('" . $filter["from_date"] . "', '%d/%m/%Y' )";
            }

            if (isset($filter["to_date"]) && $filter["to_date"] != "") {
                $new_filter .= " and DATE_FORMAT(date_time,'%Y-%m-%d')<=STR_TO_DATE('" . $filter["to_date"] . "', '%d/%m/%Y' )";
            }
        }

        if ($new_filter != "") {
            $this->filter = urlencode($new_filter);
        }

        $this->filter_on = $filter_on;
        $this->params = $params;

        if ($filter_by == 'asc' || $filter_by == 'desc') {
            $this->filter_by = $filter_by;
        }

        $this->count_result_form();
        $this->countPages();

        //$this->get_results_for_form();
        $this->get_results_for_form_updated();
        $this->getLinks_forms();

        return array('results' => $this->results, 'links' => $this->links, 'answers' => $this->results_answer);
    }

    public function get_results_for_form_updated()
    {
        $ul = ($this->curpage - 1) * $this->perpage;

        // $query = DB::query(Database::SELECT, "SELECT  session_id,question_id,question_answer,app_user_firstname,app_user_lastname,app_user_email,app_user_mobile,
        //                         DATE_FORMAT(date_time,'%d/%m/%Y %H:%i') as date_time_formated
        //                         FROM (

        //                         SELECT *
        //                         FROM form_builder_answers
        //                         ORDER BY " . $this->filter_on . " " . $this->filter_by . "
        //                         ) AS records
        //                         where profile_id = '" . $this->params['profile_id'] . "' " . urldecode($this->filter) . "
        //                         ORDER BY " . $this->filter_on . " " . $this->filter_by . "
        //                 limit " . $ul . "," . $this->perpage);

        $query = DB::query(Database::SELECT, "SELECT session_id, question_id, question_answer, app_user_firstname, app_user_lastname, app_user_email, app_user_mobile, DATE_FORMAT( date_time, '%d/%m/%Y %H:%i' ) AS date_time_formated
FROM form_builder_answers where profile_id = '" . $this->params['profile_id'] . "' " . urldecode($this->filter) . " ORDER BY " . $this->filter_on . " " . $this->filter_by . "
                        limit " . $ul . "," . $this->perpage);

        $this->results = $query->execute()->as_array();
        // Simplify result                                        
        $res = array(); // $this->results variable
        $result = array();
        for ($i = 0; $i < count($this->results); $i++) {
            //$this->get_answer_result($this->results[$i], $i);            
            $res[$this->results[$i]['session_id']] = array(
                'session_id' => $this->results[$i]['session_id'],
                'date_time_formated' => $this->results[$i]['date_time_formated']
            );
            $result[$this->results[$i]['session_id']][] = $this->results[$i];
        }
        $revised_res = array();
        foreach ($res as $res_row) {
            $revised_res[] = $res_row;
        }

        $this->results = $revised_res; // $this->results variable
        // Get answer key
        $answer = array();
        foreach ($result as $key => $answer_row) {
            //$answer[$key] = $answer_row;
            $answer[] = $answer_row;
        }

        $this->results_answer = $answer;
    }
}
