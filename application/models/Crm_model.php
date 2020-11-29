<?php

class Crm_model extends CI_model{

  function run_query($conn, $sql){
    $query = sqlsrv_query( $conn, $sql );
    $ret = array();
    while( $row = sqlsrv_fetch_array( $query, SQLSRV_FETCH_ASSOC) ) {
      array_push($ret, $row);
    }
    return $ret;
  }

  public function get_customer_info($conn, $limit, $offset){
    $sql = "
      select
        tsm.clover_id spoonity_id,
        tsm.email email,
        tsm.phone phone,
        max(bookkeeping_date) last_visit,
        concat(max(tsm.first_name),' ',max(tsm.last_name)) name,
        count(*) visit_count
      from transactions t
        join trans_spoonity_member tsm on t.id = tsm.transaction_id
      where t.delete_timestamp is null and (select count(price) from trans_articles where trans_articles.transaction_id = t.id and trans_articles.delete_timestamp is null) >0
        and t.bookkeeping_date >'2020-10-01'
      group by tsm.clover_id,tsm.phone,tsm.email
      order by visit_count desc, max(bookkeeping_date) desc
      OFFSET ".$offset." ROWS
	    FETCH NEXT ".$limit." ROWS ONLY
    ";
    return $this->run_query($conn, $sql);
  }
}
