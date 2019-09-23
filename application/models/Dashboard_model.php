<?php
class Dashboard_model extends CI_Model{
    function run_query($conn, $sql){
        $query = sqlsrv_query( $conn, $sql );
        $ret = array();
        while( $row = sqlsrv_fetch_array( $query, SQLSRV_FETCH_ASSOC) ) {
			  array_push($ret, $row);
		}
        return $ret;
    }
    // Shop list
    function get_shop_list($conn){
        $sql = "
            SELECT id, description
            FROM shops
        ";
        return $this->run_query($conn, $sql);
    }

    // Net sale
    function get_sale($conn, $date){
        $sql = "
            SELECT
                t.shop_id as shop_id,
                SUM(ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) as netsale,
                SUM((ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) * COALESCE(t.currency_ratio, 1)) as real_netsale,
                SUM(ta.price) as grossale,
                SUM(ta.price * ta.vat_percent / 100) as vat,
                SUM(ta.price * (100 - ta.vat_percent) / 100 + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) as realsale,
                SUM(t.tax_amount) as tax
            FROM shops s
            LEFT JOIN transactions t on t.shop_id = s.id
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id AND tk.in_statistics=1
            LEFT JOIN trans_articles ta ON (ta.transaction_id = t.id)
            LEFT JOIN articles a ON (a.id = ta.article_id) AND a.article_type Not In(2,3)
            LEFT JOIN measure_units mu ON (mu.id = a.measure_unit_id)

            WHERE t.delete_operator_id IS NULL
                    AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
            GROUP BY t.shop_id
        ";
        return $this->run_query($conn, $sql);
    }

    // Transaction numbers
    function get_transaction_count($conn, $date){
        $sql = "
            SELECT COUNT(*) trans ,
                t.shop_id as shop_id
            FROM transactions t
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id
            LEFT JOIN shops s ON s.id = t.shop_id AND tk.in_statistics=1
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
            GROUP BY t.shop_id
        ";
        return $this->run_query($conn, $sql);
    }
}
?>
