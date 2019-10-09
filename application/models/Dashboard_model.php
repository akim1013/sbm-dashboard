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
    function get_shop_list($conn, $shop_name){
        $sql = "
            SELECT id, description
            FROM shops";
        if($shop_name != 'All'){
            $sql = $sql . " WHERE description = '" . $shop_name . "'";
        }
        return $this->run_query($conn, $sql);
    }

    // Net sale
    function get_sale($conn, $date, $shop_name){
        $sql = "
            SELECT
                t.shop_id as shop_id,
                SUM(ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) as netsale
            FROM transactions t WITH (INDEX(idx_transactions_bookdate))
            INNER JOIN shops s ON s.id = t.shop_id
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id AND tk.in_statistics=1
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            INNER JOIN articles a ON (a.id = ta.article_id) AND a.article_type Not In(2,3)
            INNER JOIN measure_units mu ON (mu.id = a.measure_unit_id)
            WHERE t.delete_operator_id IS NULL
                    AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                    ";
            if($shop_name != 'All'){
                $sql = $sql . " AND s.description = '" . $shop_name . "'";
            }
                $sql = $sql . "
            GROUP BY t.shop_id
        ";
        return $this->run_query($conn, $sql);
    }
    // Discount
    function get_discount($conn, $date, $shop_name){
        $sql = "
            SELECT COALESCE(SUM(ta.discount),0) as discount, t.shop_id as shop_id
            FROM transactions t INNER JOIN trans_articles ta ON ta.transaction_id = t.id
            INNER JOIN shops s ON s.id = t.shop_id
            WHERE t.delete_operator_id IS NULL
                    AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                    ";
            if($shop_name != 'All'){
                $sql = $sql . " AND s.description = '" . $shop_name . "'";
            }
                $sql = $sql . "
            GROUP BY t.shop_id
        ";
        return $this->run_query($conn, $sql);
    }
    // Transaction numbers
    function get_transaction($conn, $date, $shop_name){
        $sql = "
            SELECT COUNT(*) transaction_count,
                SUM(t.total_amount - COALESCE(t.tax_amount, 0)) / COUNT(*) as average_bill,
                t.shop_id as shop_id
            FROM transactions t WITH (INDEX(idx_transactions_bookdate))
            LEFT JOIN shops s ON s.id = t.shop_id
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                ";
        if($shop_name != 'All'){
            $sql = $sql . " AND s.description = '" . $shop_name . "'";
        }
            $sql = $sql . "
            GROUP BY t.shop_id
        ";
        return $this->run_query($conn, $sql);
    }

    // Promotions
    function get_promotion($conn, $date, $shop_name){
        $sql = "
            SELECT sum(coalesce(tp.discount,0))+ sum(coalesce(tp.amount,0))- sum(coalesce(tp.offered_amount,0)) + sum(tp.articles_amount) + sum(coalesce(tp.discount,0))+ sum(coalesce(tp.amount,0))- sum(CASE WHEN tp.offered_amount <> 0 then tp.articles_amount ELSE 0 END) as promotion,
                t.shop_id as shop_id
            FROM transactions t WITH (INDEX(idx_transactions_bookdate))
            INNER JOIN shops s ON s.id = t.shop_id
            LEFT JOIN trans_promotions tp on tp.transaction_id = t.id
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                ";
        if($shop_name != 'All'){
            $sql = $sql . " AND s.description = '" . $shop_name . "'";
        }
            $sql = $sql . "
            GROUP BY t.shop_id
        ";
        return $this->run_query($conn, $sql);
    }

    // Tip
    function get_tip($conn, $date, $shop_name){
        $sql = "
            SELECT SUM(ta.price ) + SUM (COALESCE(ta.discount, 0)) tip,
                t.shop_id as shop_id
            FROM transactions t WITH (INDEX(idx_transactions_bookdate))
            INNER JOIN shops s ON s.id = t.shop_id
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            INNER JOIN articles a ON (a.id = ta.article_id) AND a.article_type In(2)
            INNER JOIN measure_units mu ON (mu.id = a.measure_unit_id)
            LEFT JOIN transaction_causals tcau ON tcau.id = t.transaction_causal_id
            LEFT JOIN groups g ON g.id = a.group_a_id AND a.group_a_id IS NOT NULL
            LEFT JOIN article_causals ac ON ta.causal_id = ac.id
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                ";
        if($shop_name != 'All'){
            $sql = $sql . " AND s.description = '" . $shop_name . "'";
        }
            $sql = $sql . "
            GROUP BY t.shop_id
        ";
        return $this->run_query($conn, $sql);
    }

    // Daily data
    function get_daily_sale($conn, $date, $shop_name){
        $sql = "
            SELECT
                t.shop_id as shop_id,
            	t.bookkeeping_date transaction_date,
                SUM(ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) as netsale
            FROM transactions t WITH (INDEX(idx_transactions_bookdate))
            INNER JOIN shops s ON s.id = t.shop_id
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id AND tk.in_statistics=1
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            INNER JOIN articles a ON (a.id = ta.article_id) AND a.article_type Not In(2,3)
            INNER JOIN measure_units mu ON (mu.id = a.measure_unit_id)
            WHERE t.delete_operator_id IS NULL
                    AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                    ";
            if($shop_name != 'All'){
                $sql = $sql . " AND s.description = '" . $shop_name . "'";
            }
                $sql = $sql . "
            GROUP BY t.bookkeeping_date, t.shop_id
            ORDER BY t.shop_id ASC, t.bookkeeping_date ASC
        ";
        return $this->run_query($conn, $sql);
    }
    function get_daily_transaction($conn, $date, $shop_name){
        $sql = "
            SELECT COUNT(*) transaction_count,
                t.bookkeeping_date transaction_date,
                t.shop_id as shop_id
            FROM transactions t WITH (INDEX(idx_transactions_bookdate))
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id
            LEFT JOIN shops s ON s.id = t.shop_id AND tk.in_statistics=1
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                ";
        if($shop_name != 'All'){
            $sql = $sql . " AND s.description = '" . $shop_name . "'";
        }
            $sql = $sql . "
            GROUP BY t.bookkeeping_date, t.shop_id
            ORDER BY t.shop_id ASC, t.bookkeeping_date ASC
        ";
        return $this->run_query($conn, $sql);
    }

    function get_sale_detail($conn, $date, $shop_name){
        $sql = "
            SELECT
                SUM(ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) as price,
                g.description as group_name
            FROM transactions t WITH (INDEX(idx_transactions_bookdate))
            INNER JOIN shops s ON s.id = t.shop_id
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id AND tk.in_statistics=1
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            INNER JOIN articles a ON (a.id = ta.article_id) AND a.article_type Not In(2,3)
            INNER JOIN measure_units mu ON (mu.id = a.measure_unit_id)
            INNER JOIN groups g ON g.id = a.group_a_id
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                ";
        if($shop_name != 'All'){
            $sql = $sql . " AND s.description = '" . $shop_name . "'";
        }
        $sql = $sql . "
            GROUP BY g.description
        ";
        return $this->run_query($conn, $sql);
    }
    function get_transaction_detail($conn, $date, $shop_name){
        $sql = "
            SELECT
                SUM(ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) as price,
                g.description as group_name
            FROM transactions t WITH (INDEX(idx_transactions_bookdate))
            INNER JOIN shops s ON s.id = t.shop_id
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id AND tk.in_statistics=1
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            INNER JOIN articles a ON (a.id = ta.article_id) AND a.article_type Not In(2,3)
            INNER JOIN measure_units mu ON (mu.id = a.measure_unit_id)
            INNER JOIN groups g ON g.id = a.group_a_id
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                ";
        if($shop_name != 'All'){
            $sql = $sql . " AND s.description = '" . $shop_name . "'";
        }
        $sql = $sql . "
            GROUP BY g.description
        ";
        return $this->run_query($conn, $sql);
    }
}
?>
