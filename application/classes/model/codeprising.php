<?php
/**
 * Description of code_prising
 * 
 * @author Megha shah
 */
 class Model_codeprising extends Model{
	 
	 /**** get code amount by code quantity
	 ***** @param qty integer
	 ***** @return array
	 ****/
	 public function getAmountByQuantity($qty){
		return DB::select('id','amount','reseller_amount')
		  ->from('code_prising')
		  ->where('code_min_qty','<=',$qty)
		  ->and_where('code_max_qty','>=',$qty)
		  ->execute()
		  ->as_array();
	}
 }