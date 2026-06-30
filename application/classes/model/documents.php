<?php
/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Documents extends Model{
    public function getDocumentsByProfileId($id){
        $documents = DB::select('*')->from('documents')->where('documents.profile_id', '=', $id)
            //->where('is_temp','=',0)
            ->order_by('sort_order','asc')
            ->execute()
            ->as_array();

        if($documents){
            return $documents;
        }else{
            return array();
        }
    }

    public function get_documents_by_id_for_autosave($id){
        $documents = DB::select('*')->from('documents')->where('documents.profile_id', '=', $id)
            ->execute()
            ->as_array();

        if($documents){
            return $documents;
        }else{
            return array();
        }
    }

    public function deleteDocumentByProfileId($profile_id){
        DB::delete('documents')->where('profile_id', '=', $profile_id)->execute();
    }

    public function getDocumentById($id){
        $document = DB::select('*')->from('documents')->where('documents.id', '=', $id)
            ->where('is_temp','=',0)
            ->execute()
            ->as_array();

        if($document){
            return $document[0];
        }else{
            return array();
        }
    }

    public function deleteDocumentById($id){
        DB::delete('documents')->where('id', '=', $id)->execute();
    }

    public function saveTempDocument($userid,$name,$btn_color,$path,$txt_align,$profile_id,$is_temp){
        $client_id = DB::select('client_id')->from('users')->where('id','=',$userid)->execute()->as_array();
        $clientid = $client_id[0]['client_id'];
        $query = DB::insert('documents', array('client_id','user_id','profile_id','name','btn_color','doc_name','txt_align','created_at','is_temp','sort_order'))
            ->values(array($clientid, $userid, $profile_id, $name,$btn_color, $path,$txt_align, date("Y-m-d H:i:s"),$is_temp,time()))
            ->execute();
        return $query;
    }

    public function saveDocumentOrder($order){
        $orders = explode(",", $order);
        $index = 0;
        foreach ($orders as $order){
            DB::update('documents')->set(array("sort_order"=>$index))->where('id', '=', $order)->execute();
            $index++;
        }
    }

    public function update_temp_field($profile_id)
    {
        $query = DB::update('documents')->set(array("is_temp"=>'0'))->where('profile_id', '=', $profile_id)->execute();
    }

    public function saveDocFiles($files,$profile_id){
        foreach($files as $key=>$file){
            $document = DB::select('*')->from('documents')->where('documents.id', '=', $file)->execute()->as_array();

            if($document){
                $old="images/tmp_files/".$document[0]["doc_name"];
                $new= "images/doc/".$document[0]["doc_name"];

                if(!empty($old) && file_exists($old)){
                    if (copy($old,$new)) {
                        @unlink(realpath($old));
                        //$document = DB::update('*')->from('documents')->where('documents.id', '=', $id);
                        $query = DB::update('documents')->set(array("doc_name"=>$new,"profile_id"=>$profile_id))->where('id', '=', $file)->execute();
                    }
                }
            }
        }
    }

    public function getTempDocuments(){
        $documents = DB::select('*')->from('documents')
            ->where('profile_id', '=', "0")
            ->and_where('created_at', '<',date("Y-m-d H:i:s", mktime(date("H"),date("i"),date("s"),date("m"),date("d") - 1, date("Y"))))->execute()->as_array();

        if($documents){
            return $documents;
        }else{
            return array();
        }
    }
}
?>