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
            $sql = $sql . " WHERE description IN (" . $shop_name . ")";
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
                $sql = $sql . " AND s.description IN (" . $shop_name . ")";
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
                $sql = $sql . " AND s.description IN (" . $shop_name . ")";
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
            $sql = $sql . " AND s.description IN (" . $shop_name . ")";
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
            $sql = $sql . " AND s.description IN (" . $shop_name . ")";
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
            $sql = $sql . " AND s.description IN (" . $shop_name . ")";
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
                $sql = $sql . " AND s.description IN (" . $shop_name . ")";
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
            $sql = $sql . " AND s.description IN (" . $shop_name . ")";
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
                AND s.description IN (" . $shop_name . ")
            GROUP BY g.description
        ";
        return $this->run_query($conn, $sql);
    }
    function get_transaction_detail($conn, $date, $shop_name){
        $sql = "
            SELECT DATEPART(hour, t.trans_date) h, COUNT(*) transaction_count
            FROM transactions t WITH (INDEX(idx_transactions_bookdate))
            LEFT JOIN shops s ON s.id = t.shop_id
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description IN (" . $shop_name . ")
            GROUP BY DATEPART(hour, t.trans_date)
            ORDER BY h
        ";
        return $this->run_query($conn, $sql);
    }

    function get_payment_total($conn, $date, $shop_name){
        $sql = "
            SELECT sum(tp.amount) total
            FROM transactions t WITH (INDEX(idx_transactions_bookdate))
            LEFT JOIN shops s ON s.id = t.shop_id
            LEFT JOIN trans_payments tp ON tp.transaction_id = t.id
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                ";
        if($shop_name != 'All'){
            $sql = $sql . " AND s.description IN (" . $shop_name . ")";
        }
        return $this->run_query($conn, $sql);
    }
    function get_payment_detail($conn, $date, $shop_name){
        $sql = "
            SELECT sum(COALESCE(tp.amount, 0)) amount, p.description pd
            FROM transactions t WITH (INDEX(idx_transactions_bookdate))
            LEFT JOIN shops s ON s.id = t.shop_id
            LEFT JOIN trans_payments tp ON tp.transaction_id = t.id
            INNER JOIN payments p ON p.id = tp.payment_id
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description IN (" . $shop_name . ")
            GROUP BY p.description
        ";
        return $this->run_query($conn, $sql);
    }

    function get_daily_turnover($conn, $date, $shop_name){
        $sql = "
            SELECT
            DATEPART(DY, t.bookkeeping_date) d,";
        if($date['length'] <= 3){
            $sql = $sql . "s.description shop_name,";
        }
        $sql = $sql . "
            SUM(ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) as netsale
            FROM transactions t
            INNER JOIN shops s ON s.id = t.shop_id
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id AND tk.in_statistics=1
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            INNER JOIN articles a ON (a.id = ta.article_id) AND a.article_type Not In(2,3)
            INNER JOIN measure_units mu ON (mu.id = a.measure_unit_id)
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
            AND s.description IN (" . $shop_name . ")
            GROUP BY DATEPART(DY, t.bookkeeping_date)";
        if($date['length'] <= 3){
            $sql = $sql . ", s.description";
        }
        $sql = $sql . "
            ORDER BY DATEPART(DY, t.bookkeeping_date)
        ";
        return $this->run_query($conn, $sql);
    }
    function get_weekly_turnover($conn, $date, $shop_name){
        $sql = "
            SELECT
            DATEPART(week, t.bookkeeping_date) w,";
        if($date['length'] <= 3){
            $sql = $sql . "s.description shop_name,";
        }
        $sql = $sql . "
            SUM(ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) as netsale
            FROM transactions t
            INNER JOIN shops s ON s.id = t.shop_id
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id AND tk.in_statistics=1
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            INNER JOIN articles a ON (a.id = ta.article_id) AND a.article_type Not In(2,3)
            INNER JOIN measure_units mu ON (mu.id = a.measure_unit_id)
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
            AND s.description IN (" . $shop_name . ")
            GROUP BY DATEPART(week, t.bookkeeping_date)";
        if($date['length'] <= 3){
            $sql = $sql . ", s.description";
        }
        $sql = $sql . "
            ORDER BY DATEPART(week, t.bookkeeping_date)
        ";
        return $this->run_query($conn, $sql);
    }
    function get_monthly_turnover($conn, $date, $shop_name){
        $sql = "
            SELECT
            DATEPART(month, t.bookkeeping_date) m,";
        if($date['length'] <= 3){
            $sql = $sql . "s.description shop_name,";
        }
        $sql = $sql . "
            SUM(ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) as netsale
            FROM transactions t
            INNER JOIN shops s ON s.id = t.shop_id
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id AND tk.in_statistics=1
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            INNER JOIN articles a ON (a.id = ta.article_id) AND a.article_type Not In(2,3)
            INNER JOIN measure_units mu ON (mu.id = a.measure_unit_id)
            WHERE t.delete_operator_id IS NULL
            AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
            AND s.description IN (" . $shop_name . ")
            GROUP BY DATEPART(month, t.bookkeeping_date)";
        if($date['length'] <= 3){
            $sql = $sql . ", s.description";
        }
        $sql = $sql . "
            ORDER BY DATEPART(month, t.bookkeeping_date)
        ";
        return $this->run_query($conn, $sql);
    }

    function get_yearly_turnover($conn, $shop_name){
        $sql = "
            SELECT
            DATEPART(year, t.bookkeeping_date) y,
            SUM(ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) as netsale
            FROM transactions t
            INNER JOIN shops s ON s.id = t.shop_id
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id AND tk.in_statistics=1
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            INNER JOIN articles a ON (a.id = ta.article_id) AND a.article_type Not In(2,3)
            INNER JOIN measure_units mu ON (mu.id = a.measure_unit_id)
            WHERE t.delete_operator_id IS NULL
            AND s.description IN (" . $shop_name . ")
            GROUP BY DATEPART(year, t.bookkeeping_date)";
        if($date['length'] <= 3){
            $sql = $sql . ", s.description";
        }
        $sql = $sql . "
            ORDER BY DATEPART(year, t.bookkeeping_date)
        ";
        return $this->run_query($conn, $sql);
    }

    function detail_comparison_article($conn, $date, $shop_name){
        $sql = "
            SELECT g.id group_id, CONCAT(CONCAT(g.code, '_'), g.description) group_name, a.description article_name, COALESCE(sub_result.amount, 0) amount, COALESCE(sub_result.price, 0) price, COALESCE(sub_result_last_week.amount, 0) last_week_amount, COALESCE(sub_result_last_week.price, 0) last_week_price
            FROM groups g
            INNER JOIN articles a ON g.id = a.group_a_id
            LEFT JOIN (SELECT
                SUM(ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) as price,
            	count(ta.price) amount,
                a.id as article_id
            FROM transactions t WITH (INDEX(idx_transactions_bookdate))
            INNER JOIN shops s ON s.id = t.shop_id
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id AND tk.in_statistics=1
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            INNER JOIN articles a ON (a.id = ta.article_id) AND a.article_type Not In(2,3)
            INNER JOIN measure_units mu ON (mu.id = a.measure_unit_id)
            INNER JOIN groups g ON g.id = a.group_a_id
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
            AND s.description IN (" . $shop_name . ")
            GROUP BY a.id) sub_result ON a.id = sub_result.article_id
            LEFT JOIN (SELECT
            SUM(ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) as price,
            count(ta.price) amount,
            a.id as article_id
            FROM transactions t WITH (INDEX(idx_transactions_bookdate))
            INNER JOIN shops s ON s.id = t.shop_id
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id AND tk.in_statistics=1
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            INNER JOIN articles a ON (a.id = ta.article_id) AND a.article_type Not In(2,3)
            INNER JOIN measure_units mu ON (mu.id = a.measure_unit_id)
            INNER JOIN groups g ON g.id = a.group_a_id
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['last_week_start'] . "' AND '" . $date['last_week_end'] . "'
            ";
            if($shop_name != 'All'){
                $sql = $sql . " AND s.description IN (" . $shop_name . ")";
            }
            $sql = $sql . "
            GROUP BY a.id) sub_result_last_week ON sub_result_last_week.article_id = a.id
            ORDER BY g.id
            ";
        return $this->run_query($conn, $sql);
    }
    function detail_comparison_discount($conn, $date, $shop_name){
        $sql = "
            SELECT d.description discount_description, COALESCE(this_week.quantity, 0) this_week_quantity, COALESCE(this_week.amount, 0) this_week_amount, COALESCE(last_week.quantity, 0) last_week_quantity, COALESCE(last_week.amount, 0) last_week_amount
            FROM discounts d
            LEFT JOIN (SELECT d.description discount_description, sum(td.quantity) quantity, sum(td.amount) amount
            FROM discounts d
            LEFT JOIN trans_discounts td ON td.discount_id = d.id
            INNER JOIN transactions t ON t.id = td.transaction_id
            INNER JOIN shops s ON s.id = t.shop_id
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
            AND s.description IN (" . $shop_name . ")
            GROUP BY d.description) this_week ON d.description = this_week.discount_description
            LEFT JOIN (SELECT d.description discount_description, sum(td.quantity) quantity, sum(td.amount) amount
            FROM discounts d
            LEFT JOIN trans_discounts td ON td.discount_id = d.id
            INNER JOIN transactions t ON t.id = td.transaction_id
            INNER JOIN shops s ON s.id = t.shop_id
            WHERE t.delete_operator_id IS NULL
            AND t.bookkeeping_date BETWEEN '" . $date['last_week_start'] . "' AND '" . $date['last_week_end'] . "'
        AND s.description IN (" . $shop_name . ")
            GROUP BY d.description) last_week ON d.description = last_week.discount_description
        ";
        return $this->run_query($conn, $sql);
    }
    function detail_comparison_payment($conn, $date, $shop_name){
        $sql = "
            SELECT p.description, COALESCE(this_week_payment.amount, 0) this_week_amount, COALESCE(this_week_payment.qty, 0) this_week_qty, COALESCE(last_week_payment.amount, 0) last_week_amount, COALESCE(last_week_payment.qty, 0) last_week_qty
            FROM payments p
            LEFT JOIN (SELECT p.description payment_detail, sum(COALESCE(tp.amount, 0)) amount, count(tp.transaction_id) qty
            FROM transactions t WITH (INDEX(idx_transactions_bookdate))
            LEFT JOIN shops s ON s.id = t.shop_id
            LEFT JOIN trans_payments tp ON tp.transaction_id = t.id
            INNER JOIN payments p ON p.id = tp.payment_id
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
            AND s.description IN (" . $shop_name . ")
            GROUP BY p.description) this_week_payment ON p.description = this_week_payment.payment_detail
            LEFT JOIN (SELECT p.description payment_detail, sum(COALESCE(tp.amount, 0)) amount, count(tp.transaction_id) qty
            FROM transactions t WITH (INDEX(idx_transactions_bookdate))
            LEFT JOIN shops s ON s.id = t.shop_id
            LEFT JOIN trans_payments tp ON tp.transaction_id = t.id
            INNER JOIN payments p ON p.id = tp.payment_id
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['last_week_start'] . "' AND '" . $date['last_week_end'] . "'
        AND s.description IN (" . $shop_name . ")
            GROUP BY p.description) last_week_payment on p.description = last_week_payment.payment_detail
        ";
        return $this->run_query($conn, $sql);
    }
    function shop_article_details($conn, $date, $shop_name){
        $sql = "
            SELECT
            s.description shop_name,
            SUM(ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) as price,
            a.description as article_name
            FROM transactions t WITH (INDEX(idx_transactions_bookdate))
            INNER JOIN shops s ON s.id = t.shop_id
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id AND tk.in_statistics=1
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            INNER JOIN articles a ON (a.id = ta.article_id) AND a.article_type Not In(2,3)
            INNER JOIN measure_units mu ON (mu.id = a.measure_unit_id)
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description IN (" . $shop_name . ")
            GROUP BY a.description, s.description
            ORDER BY s.description, price DESC
        ";
        return $this->run_query($conn, $sql);
    }
    function get_shops($conn, $shop_name){
        $sql = "
            SELECT s.id, s.description
            FROM shops s";
        if($shop_name != 'All'){
            $sql = $sql . " WHERE s.description IN (" . $shop_name . ")";
        }
        return $this->run_query($conn, $sql);
    }
    function get_tills($conn, $shop_name){
        $sql = "
            SELECT t.id, t.description
            FROM tills t
            INNER JOIN shops s ON s.id = t.shop_id";
        if($shop_name != 'All'){
            $sql = $sql . " WHERE s.description IN (" . $shop_name . ")";
        }
        return $this->run_query($conn, $sql);
    }
    function get_operators($conn, $shop_name){
        $sql = "
            SELECT DISTINCT(o.id), o.description, o.code
            FROM operators o
            INNER JOIN presence_operations p ON p.operator_id = o.id
            INNER JOIN shops s ON s.id = p.shop_id
            ORDER BY o.id";
        // if($shop_name != 'All'){
        //     $sql = $sql . " WHERE s.description IN (" . $shop_name . ")";
        // }
        return $this->run_query($conn, $sql);
    }
    function get_presence($conn, $data){
        $sql = "
            SELECT o.id operator_id, o.code operator_code, o.description operator_name, p.till_id, p.shop_id, p.timestamp t_stamp, p.operation_type o_type
            FROM operators o
            INNER JOIN presence_operations p ON o.id = p.operator_id
            INNER JOIN tills t ON t.id = p.till_id
            INNER JOIN shops s ON s.id = p.shop_id
            WHERE p.timestamp BETWEEN '" . $data['start'] . "' AND '" . $data['end'] . "'
                AND t.id IN (" . $data['till_id'] . ")
                AND s.id IN (" . $data['shop_id'] . ")
                AND o.id IN (" . $data['operator_id'] . ")
            ORDER BY operator_id, t_stamp";

        return $this->run_query($conn, $sql);
    }
}
?>
