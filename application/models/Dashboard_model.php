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
    // Tax
    function get_tax($conn, $date, $shop_name){
        $sql = "
            SELECT
            t.shop_id as shop_id,
            SUM(t.tax_amount) as tax
            FROM transactions t WITH (INDEX(idx_transactions_bookdate))
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
            LEFT JOIN shops s ON s.id = t.shop_id
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
    function get_discount_detail($conn, $date, $shop_name){
        $sql = "
            SELECT d.description discount_description, sum(td.quantity) quantity, sum(td.amount) amount
            FROM discounts d
            LEFT JOIN trans_discounts td ON td.discount_id = d.id
            INNER JOIN transactions t ON t.id = td.transaction_id
            INNER JOIN shops s ON s.id = t.shop_id
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
            	AND s.description IN (" . $shop_name . ")
            GROUP BY d.description, s.description
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
        if($date['length'] <= 5){
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
        if($date['length'] <= 5){
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
        if($date['length'] <= 5){
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
        if($date['length'] <= 5){
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
        if($date['length'] <= 5){
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
        if($date['length'] <= 5){
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
        if($date['length'] <= 5){
            $sql = $sql . ", s.description";
        }
        $sql = $sql . "
            ORDER BY DATEPART(year, t.bookkeeping_date)
        ";
        return $this->run_query($conn, $sql);
    }

    function detail_comparison_article($conn, $date, $shop_name){
        $sql = "
            SELECT g.id group_id, g.description group_name, a.description article_name, COALESCE(sub_result.amount, 0) amount, COALESCE(sub_result.price, 0) price, COALESCE(sub_result_last_week.amount, 0) last_week_amount, COALESCE(sub_result_last_week.price, 0) last_week_price
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
            WHERE t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
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
            WHERE t.bookkeeping_date BETWEEN '" . $date['last_week_start'] . "' AND '" . $date['last_week_end'] . "'
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
    function get_all_operators($conn){
        $sql = "
            SELECT *
            FROM operators";
        return $this->run_query($conn, $sql);
    }
    function get_operators($conn, $shop_name){
        $sql = "
            SELECT DISTINCT(o.id), o.description, o.code, s.id shop_id
            FROM operators o
            INNER JOIN presence_operations p ON p.operator_id = o.id
            INNER JOIN shops s ON s.id = p.shop_id
            WHERE s.description IN (" . $shop_name . ")
            ORDER BY o.id";
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

                AND s.id IN (" . $data['shop_id'] . ")
                AND o.id IN (" . $data['operator_id'] . ")
            ORDER BY operator_id, t_stamp";

        return $this->run_query($conn, $sql);
    }

    function get_p_netsale($conn, $date, $shop_name){
        $sql = "
            SELECT
            	DATEPART(day, t.bookkeeping_date) d,
                SUM(ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) as netsale
            FROM transactions t WITH (INDEX(idx_transactions_bookdate))
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id AND tk.in_statistics=1
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            INNER JOIN articles a ON (a.id = ta.article_id) AND a.article_type Not In(2,3)
            INNER JOIN measure_units mu ON (mu.id = a.measure_unit_id)
            INNER JOIN shops s ON s.id = t.shop_id
            WHERE t.delete_operator_id IS NULL
        		AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description IN ('" . $shop_name . "')
            GROUP BY DATEPART(day, t.bookkeeping_date)
            ORDER BY DATEPART(day, t.bookkeeping_date)
        ";
        return $this->run_query($conn, $sql);
    }
    function get_p_tax($conn, $date, $shop_name){
        $sql = "
            SELECT
                DATEPART(day, t.bookkeeping_date) d,
                SUM(t.tax_amount) as tax
            FROM transactions t WITH (INDEX(idx_transactions_bookdate))
            INNER JOIN shops s ON s.id = t.shop_id
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description IN ('" . $shop_name . "')
            GROUP BY DATEPART(day, t.bookkeeping_date)
            ORDER BY DATEPART(day, t.bookkeeping_date)
        ";
        return $this->run_query($conn, $sql);
    }
    function get_p_detail($conn, $date, $shop_name){
        $sql = "
            SELECT DATEPART(day, t.bookkeeping_date) d, p.description payment_description, sum(COALESCE(tp.amount, 0)) amount
            FROM transactions t WITH (INDEX(idx_transactions_bookdate))
            LEFT JOIN shops s ON s.id = t.shop_id
            LEFT JOIN trans_payments tp ON tp.transaction_id = t.id
            INNER JOIN payments p ON p.id = tp.payment_id
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description IN ('" . $shop_name . "')
            GROUP BY p.description, DATEPART(day, t.bookkeeping_date)
        ";
        return $this->run_query($conn, $sql);
    }

    function get_m_sale($conn, $date, $shop_name){
        $sql = "
            SELECT
            	DATEPART(YEAR, t.bookkeeping_date) y, DATEPART(MONTH, t.bookkeeping_date) m, DATEPART(day, t.bookkeeping_date) d, DATEPART(WEEKDAY, t.bookkeeping_date) w,
                SUM(ta.price) as sale,
                SUM(ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) as netsale
            FROM transactions t WITH (INDEX(idx_transactions_bookdate))
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id AND tk.in_statistics=1
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            INNER JOIN articles a ON (a.id = ta.article_id) AND a.article_type Not In(2,3)
            INNER JOIN measure_units mu ON (mu.id = a.measure_unit_id)
            INNER JOIN shops s ON s.id = t.shop_id
            WHERE t.delete_operator_id IS NULL
        		AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description IN ('" . $shop_name . "')
                GROUP BY DATEPART(day, t.bookkeeping_date), DATEPART(WEEKDAY, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(YEAR, t.bookkeeping_date)
                ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date)
        ";
        return $this->run_query($conn, $sql);
    }
    function get_m_count($conn, $date, $shop_name){
        $sql = "
            SELECT
                DATEPART(YEAR, t.bookkeeping_date) y, DATEPART(MONTH, t.bookkeeping_date) m, DATEPART(day, t.bookkeeping_date) d, DATEPART(WEEKDAY, t.bookkeeping_date) w,
                COUNT(*) transaction_count
            FROM transactions t WITH (INDEX(idx_transactions_bookdate))
            LEFT JOIN shops s ON s.id = t.shop_id
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id
            WHERE t.delete_operator_id IS NULL
        		AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description IN ('" . $shop_name . "')
                GROUP BY DATEPART(day, t.bookkeeping_date), DATEPART(WEEKDAY, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(YEAR, t.bookkeeping_date)
                ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date)
        ";
        return $this->run_query($conn, $sql);
    }
    function get_m_cups($conn, $date, $shop_name){
        $sql = "
            SELECT
                DATEPART(YEAR, t.bookkeeping_date) y, DATEPART(MONTH, t.bookkeeping_date) m, DATEPART(day, t.bookkeeping_date) d, DATEPART(WEEKDAY, t.bookkeeping_date) w,
                COUNT(*) cups
            FROM trans_articles ta
            LEFT JOIN transactions t ON t.id=ta.transaction_id
            LEFT JOIN shops s ON s.id = t.shop_id
            WHERE t.delete_operator_id IS NULL
        		AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description IN ('" . $shop_name . "')
                GROUP BY DATEPART(day, t.bookkeeping_date), DATEPART(WEEKDAY, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(YEAR, t.bookkeeping_date)
                ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date)
        ";
        return $this->run_query($conn, $sql);
    }
    function get_m_ac($conn, $date, $shop_name){
        $sql = "
            SELECT tb.y, tb.m, tb.d, sum(tb.ac) ac
            FROM (SELECT
            	g.id group_id,
            	DATEPART(YEAR, t.bookkeeping_date) y, DATEPART(MONTH, t.bookkeeping_date) m, DATEPART(day, t.bookkeeping_date) d, DATEPART(WEEKDAY, t.bookkeeping_date) w,
            	(SUM(ta.price) / count(g.id)) ac
            FROM trans_articles ta
            LEFT JOIN transactions t ON t.id=ta.transaction_id
            LEFT JOIN shops s ON s.id = t.shop_id
            LEFT JOIN articles a ON a.id = ta.article_id
            LEFT JOIN groups g ON a.group_a_id = g.id
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description IN ('" . $shop_name . "')
            	AND g.description NOT LIKE '%add_on%'
                AND g.description NOT LIKE '%drink%'
            GROUP BY g.id, DATEPART(day, t.bookkeeping_date), DATEPART(WEEKDAY, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(YEAR, t.bookkeeping_date)) tb
            GROUP BY tb.y, tb.m, tb.d
            ORDER BY tb.y, tb.m, tb.d
        ";
        return $this->run_query($conn, $sql);
    }
    function get_m_drinks($conn, $date, $shop_name){
        $sql = "
            SELECT
                DATEPART(YEAR, t.bookkeeping_date) y, DATEPART(MONTH, t.bookkeeping_date) m, DATEPART(day, t.bookkeeping_date) d, DATEPART(WEEKDAY, t.bookkeeping_date) w,
                SUM(ta.price) drinks
            FROM trans_articles ta
            LEFT JOIN transactions t ON t.id=ta.transaction_id
            LEFT JOIN shops s ON s.id = t.shop_id
            LEFT JOIN articles a ON a.id = ta.article_id
            LEFT JOIN groups g ON a.group_a_id = g.id
            WHERE t.delete_operator_id IS NULL
        		AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description IN ('" . $shop_name . "')
                AND g.description LIKE '%drink%'
                AND g.description NOT LIKE '%coffee%'
                GROUP BY DATEPART(day, t.bookkeeping_date), DATEPART(WEEKDAY, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(YEAR, t.bookkeeping_date)
                ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date)
        ";
        return $this->run_query($conn, $sql);
    }

    function get_y_sale($conn, $date, $shop_name){
        $sql = "
            SELECT
                DATEPART(YEAR, t.bookkeeping_date) y, DATEPART(MONTH, t.bookkeeping_date) m,
                SUM(ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) as netsale
            FROM transactions t
            INNER JOIN shops s ON s.id = t.shop_id
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id AND tk.in_statistics=1
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            INNER JOIN articles a ON (a.id = ta.article_id) AND a.article_type Not In(2,3)
            INNER JOIN measure_units mu ON (mu.id = a.measure_unit_id)
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description IN ('" . $shop_name . "')
            GROUP BY DATEPART(MONTH, t.bookkeeping_date), DATEPART(YEAR, t.bookkeeping_date)
            ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date)
        ";
        return $this->run_query($conn, $sql);
    }
    function get_y_dinein_count($conn, $date, $shop_name){
        $sql = "
            SELECT
                DATEPART(YEAR, t.bookkeeping_date) y, DATEPART(MONTH, t.bookkeeping_date) m,
                COUNT(a.id) dinein_count
            FROM transactions t
            INNER JOIN shops s ON s.id = t.shop_id
            LEFT JOIN trans_articles ta ON t.id = ta.transaction_id
            LEFT JOIN articles a ON a.id = ta.article_id
            LEFT JOIN transaction_causals tc ON tc.id = t.transaction_causal_id
            WHERE t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description IN ('" . $shop_name . "')
            	AND tc.description = 'Dine In'
            GROUP BY DATEPART(MONTH, t.bookkeeping_date), DATEPART(YEAR, t.bookkeeping_date)
            ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date)
        ";
        return $this->run_query($conn, $sql);
    }
    function get_y_dinein_amount($conn, $date, $shop_name){
        $sql = "
            SELECT
                DATEPART(YEAR, t.bookkeeping_date) y, DATEPART(MONTH, t.bookkeeping_date) m,
                SUM(t.total_amount) dinein_amount
            FROM transactions t
            INNER JOIN shops s ON s.id = t.shop_id
            LEFT JOIN transaction_causals tc ON tc.id = t.transaction_causal_id
            WHERE t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description IN ('" . $shop_name . "')
            	AND tc.description = 'Dine In'
            GROUP BY DATEPART(MONTH, t.bookkeeping_date), DATEPART(YEAR, t.bookkeeping_date)
            ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date)
        ";
        return $this->run_query($conn, $sql);
    }
    function get_y_togo_count($conn, $date, $shop_name){
        $sql = "
            SELECT
                DATEPART(YEAR, t.bookkeeping_date) y, DATEPART(MONTH, t.bookkeeping_date) m,
                COUNT(a.id) togo_count
            FROM transactions t
            INNER JOIN shops s ON s.id = t.shop_id
            LEFT JOIN trans_articles ta ON t.id = ta.transaction_id
            LEFT JOIN articles a ON a.id = ta.article_id
            LEFT JOIN transaction_causals tc ON tc.id = t.transaction_causal_id
            WHERE t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description IN ('" . $shop_name . "')
                AND tc.description = 'To Go'
            GROUP BY DATEPART(MONTH, t.bookkeeping_date), DATEPART(YEAR, t.bookkeeping_date)
            ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date)
        ";
        return $this->run_query($conn, $sql);
    }
    function get_y_togo_amount($conn, $date, $shop_name){
        $sql = "
            SELECT
                DATEPART(YEAR, t.bookkeeping_date) y, DATEPART(MONTH, t.bookkeeping_date) m,
                SUM(t.total_amount) togo_amount
            FROM transactions t
            INNER JOIN shops s ON s.id = t.shop_id
            LEFT JOIN transaction_causals tc ON tc.id = t.transaction_causal_id
            WHERE t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description IN ('" . $shop_name . "')
                AND tc.description = 'To Go'
            GROUP BY DATEPART(MONTH, t.bookkeeping_date), DATEPART(YEAR, t.bookkeeping_date)
            ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date)
        ";
        return $this->run_query($conn, $sql);
    }
    function get_y_delivery_count($conn, $date, $shop_name){
        $sql = "
            SELECT
                DATEPART(YEAR, t.bookkeeping_date) y, DATEPART(MONTH, t.bookkeeping_date) m,
                COUNT(a.id) delivery_count
            FROM transactions t
            INNER JOIN shops s ON s.id = t.shop_id
            LEFT JOIN trans_articles ta ON t.id = ta.transaction_id
            LEFT JOIN articles a ON a.id = ta.article_id
            LEFT JOIN transaction_causals tc ON tc.id = t.transaction_causal_id
            WHERE t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description IN ('" . $shop_name . "')
                AND tc.description = 'DELIVERY'
            GROUP BY DATEPART(MONTH, t.bookkeeping_date), DATEPART(YEAR, t.bookkeeping_date)
            ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date)
        ";
        return $this->run_query($conn, $sql);
    }
    function get_y_delivery_amount($conn, $date, $shop_name){
        $sql = "
            SELECT
                DATEPART(YEAR, t.bookkeeping_date) y, DATEPART(MONTH, t.bookkeeping_date) m,
                SUM(t.total_amount) delivery_amount
            FROM transactions t
            INNER JOIN shops s ON s.id = t.shop_id
            LEFT JOIN transaction_causals tc ON tc.id = t.transaction_causal_id
            WHERE t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description IN ('" . $shop_name . "')
                AND tc.description = 'DELIVERY'
            GROUP BY DATEPART(MONTH, t.bookkeeping_date), DATEPART(YEAR, t.bookkeeping_date)
            ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date)
        ";
        return $this->run_query($conn, $sql);
    }
    function get_y_transaction_count($conn, $date, $shop_name){
        $sql = "
            SELECT
                DATEPART(YEAR, t.bookkeeping_date) y, DATEPART(MONTH, t.bookkeeping_date) m,
                COUNT(*) transaction_count
            FROM transactions t
            INNER JOIN shops s ON s.id = t.shop_id
            WHERE t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description IN ('" . $shop_name . "')
            GROUP BY DATEPART(MONTH, t.bookkeeping_date), DATEPART(YEAR, t.bookkeeping_date)
            ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date)
        ";
        return $this->run_query($conn, $sql);
    }
    function get_y_article_count($conn, $date, $shop_name){
        $sql = "
            SELECT
                DATEPART(YEAR, t.bookkeeping_date) y, DATEPART(MONTH, t.bookkeeping_date) m,
                COUNT(*) article_count
            FROM transactions t
            INNER JOIN shops s ON s.id = t.shop_id
            LEFT JOIN trans_articles ta ON t.id = ta.transaction_id
            LEFT JOIN articles a ON a.id = ta.article_id
            LEFT JOIN groups g ON g.id = a.group_a_id
            WHERE t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description IN ('" . $shop_name . "')
            GROUP BY DATEPART(MONTH, t.bookkeeping_date), DATEPART(YEAR, t.bookkeeping_date)
            ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date)
        ";
        return $this->run_query($conn, $sql);
    }

    // API models redefine v3
    // Net sale
    function _get_sale($conn, $date, $shop_name){
        $sql = "
            SELECT
                cast(DATEPART(YEAR, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(MONTH, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(day, t.bookkeeping_date) as varchar) d,
                SUM(ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) as netsale,
                count(*) as article_count
            FROM transactions t
            INNER JOIN shops s ON s.id = t.shop_id
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id AND tk.in_statistics=1
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            INNER JOIN articles a ON (a.id = ta.article_id) AND a.article_type Not In(2,3)
            INNER JOIN measure_units mu ON (mu.id = a.measure_unit_id)
            WHERE t.delete_operator_id IS NULL
                    AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                    AND s.description = '" . $shop_name . "'
                    GROUP BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date)
                    ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date)
        ";
        return $this->run_query($conn, $sql);
    }
    // Discount
    function _get_discount($conn, $date, $shop_name){
        $sql = "
            SELECT COALESCE(SUM(ta.discount),0) as discount, cast(DATEPART(YEAR, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(MONTH, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(day, t.bookkeeping_date) as varchar) d
            FROM transactions t INNER JOIN trans_articles ta ON ta.transaction_id = t.id
            INNER JOIN shops s ON s.id = t.shop_id
            WHERE t.delete_operator_id IS NULL
                    AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                    AND s.description = '" . $shop_name . "'
                    GROUP BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date)
                    ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date)
        ";
        return $this->run_query($conn, $sql);
    }
    // Tax
    function _get_tax($conn, $date, $shop_name){
        $sql = "
            SELECT
            SUM(t.tax_amount) as tax, cast(DATEPART(YEAR, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(MONTH, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(day, t.bookkeeping_date) as varchar) d
            FROM transactions t
            INNER JOIN shops s ON s.id = t.shop_id
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description = '" . $shop_name . "'
                GROUP BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date)
                ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date)
        ";
        return $this->run_query($conn, $sql);
    }
    // average item price | exclude toppings
    function _get_avg($conn, $date, $shop_name){
        $sql = "
            SELECT
                COALESCE(SUM(ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) / count(ta.price), 0) as avg_per_item
            FROM transactions t
            INNER JOIN shops s ON s.id = t.shop_id
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id
            LEFT JOIN trans_articles ta ON (ta.transaction_id = t.id)
            LEFT JOIN articles a ON (a.id = ta.article_id)
            LEFT JOIN groups g ON g.id = a.group_a_id
            WHERE
                a.article_type = 1
                AND t.bookkeeping_date BETWEEN '" . $date['end'] . "' AND '" . $date['end'] . "'
                AND s.description = '" . $shop_name . "'
                AND g.description NOT LIKE '%topping%'
            ORDER BY avg_per_item DESC
        ";
        return $this->run_query($conn, $sql);
    }
    // Transaction numbers
    function _get_transaction($conn, $date, $shop_name){
        $sql = "
            SELECT COUNT(*) transaction_count,
                cast(DATEPART(YEAR, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(MONTH, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(day, t.bookkeeping_date) as varchar) d,
                SUM(t.total_amount - COALESCE(t.tax_amount, 0)) / COUNT(*) as average_bill
            FROM transactions t
            LEFT JOIN shops s ON s.id = t.shop_id
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description = '" . $shop_name . "'
                GROUP BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date)
                ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date)
        ";
        return $this->run_query($conn, $sql);
    }

    // Promotions
    function _get_promotion($conn, $date, $shop_name){
        $sql = "
            SELECT sum(coalesce(tp.discount,0))+ sum(coalesce(tp.amount,0))- sum(coalesce(tp.offered_amount,0)) + sum(tp.articles_amount) + sum(coalesce(tp.discount,0))+ sum(coalesce(tp.amount,0))- sum(CASE WHEN tp.offered_amount <> 0 then tp.articles_amount ELSE 0 END) as promotion,cast(DATEPART(YEAR, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(MONTH, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(day, t.bookkeeping_date) as varchar) d
            FROM transactions t
            INNER JOIN shops s ON s.id = t.shop_id
            LEFT JOIN trans_promotions tp on tp.transaction_id = t.id
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description = '" . $shop_name . "'
                GROUP BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date)
                ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date)
        ";
        return $this->run_query($conn, $sql);
    }
    // Tip
    function _get_tip($conn, $date, $shop_name){
        $sql = "
            SELECT SUM(ta.price ) + SUM (COALESCE(ta.discount, 0)) tip, cast(DATEPART(YEAR, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(MONTH, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(day, t.bookkeeping_date) as varchar) d
            FROM transactions t
            INNER JOIN shops s ON s.id = t.shop_id
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            INNER JOIN articles a ON (a.id = ta.article_id) AND a.article_type In(2)
            INNER JOIN measure_units mu ON (mu.id = a.measure_unit_id)
            LEFT JOIN transaction_causals tcau ON tcau.id = t.transaction_causal_id
            LEFT JOIN groups g ON g.id = a.group_a_id AND a.group_a_id IS NOT NULL
            LEFT JOIN article_causals ac ON ta.causal_id = ac.id
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description = '" . $shop_name . "'
                GROUP BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date)
                ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date)
        ";
        return $this->run_query($conn, $sql);
    }
    function _get_division_sale($conn, $date, $shop_name, $division){
        $sql = "
            SUM(ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) as netsale
            FROM transactions t
            INNER JOIN shops s ON s.id = t.shop_id
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id AND tk.in_statistics=1
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            INNER JOIN articles a ON (a.id = ta.article_id) AND a.article_type Not In(2,3)
            INNER JOIN measure_units mu ON (mu.id = a.measure_unit_id)
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description = '" . $shop_name . "'
        ";
        if($division == '7'){
           $sql = "
               SELECT cast(DATEPART(WEEKDAY, t.bookkeeping_date) as varchar) d,
           " . $sql . "
               GROUP BY DATEPART(WEEKDAY, t.bookkeeping_date)
               ORDER BY DATEPART(WEEKDAY, t.bookkeeping_date)
           ";
       }else if($division == '10'){
            $sql = "
                SELECT (case when day(t.bookkeeping_date) <= 10 then 'first' when day(t.bookkeeping_date) <= 20 then 'second' else 'third' end) as d,
            " . $sql . "
                GROUP BY (case when day(t.bookkeeping_date) <= 10 then 'first' when day(t.bookkeeping_date) <= 20 then 'second' else 'third' end)
                ORDER BY d
            ";
        }
        else if($division == '15'){
            $sql = "
                SELECT cast(DATEPART(day, t.bookkeeping_date) as varchar) d,
            " . $sql . "
                GROUP BY DATEPART(day, t.bookkeeping_date)
                ORDER BY DATEPART(day, t.bookkeeping_date)
            ";
        }else if($division == '30'){
            $sql = "
                SELECT cast(DATEPART(week, t.bookkeeping_date) as varchar) d,
            " . $sql . "
                GROUP BY DATEPART(WEEK, t.bookkeeping_date)
                ORDER BY DATEPART(WEEK, t.bookkeeping_date)
            ";
        }else{
            $sql = "
                SELECT cast(DATEPART(MONTH, t.bookkeeping_date) as varchar) d,
            " . $sql . "
                GROUP BY DATEPART(MONTH, t.bookkeeping_date)
                ORDER BY DATEPART(MONTH, t.bookkeeping_date)
            ";
        }
        return $this->run_query($conn, $sql);
    }
    function _get_hourly_sale($conn, $date, $shop_name){
        $sql = "
            SELECT DATEPART(hour, t.trans_date) h, COUNT(*) transaction_count
            FROM transactions t
            LEFT JOIN shops s ON s.id = t.shop_id
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description = '" . $shop_name . "'
            GROUP BY DATEPART(hour, t.trans_date)
            ORDER BY h
        ";
        return $this->run_query($conn, $sql);
    }
    function _get_payment_detail($conn, $date, $shop_name){
        $sql = "
            SELECT p.description payment_description, sum(COALESCE(tp.amount, 0)) amount
            FROM transactions t
            LEFT JOIN shops s ON s.id = t.shop_id
            LEFT JOIN trans_payments tp ON tp.transaction_id = t.id
            INNER JOIN payments p ON p.id = tp.payment_id
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description = '" . $shop_name . "'
            GROUP BY p.description
        ";
        return $this->run_query($conn, $sql);
    }
    function _get_today_items($conn, $date, $shop_name){
        $sql = "
            SELECT
            SUM(ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) as price,
            a.description as article_name, count(a.description) qty
            FROM transactions t
            INNER JOIN shops s ON s.id = t.shop_id
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id AND tk.in_statistics=1
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            INNER JOIN articles a ON (a.id = ta.article_id) AND a.article_type Not In(2,3)
            INNER JOIN measure_units mu ON (mu.id = a.measure_unit_id)
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description = '" . $shop_name . "'
            GROUP BY a.description, s.description
            ORDER BY s.description, price DESC
        ";
        return $this->run_query($conn, $sql);
    }

    function _get_causals($conn){
        $sql = "
            SELECT id, description
            FROM transaction_causals
        ";
        return $this->run_query($conn, $sql);
    }

    function _get_sale_details($conn, $date, $shop_name, $d){
        $sql = "
                COALESCE(tk.description, 'Other') as causal_desc,
                SUM(ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) as netsale
            FROM transactions t
            INNER JOIN shops s ON s.id = t.shop_id
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            INNER JOIN articles a ON (a.id = ta.article_id)
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description = '" . $shop_name . "'
                AND a.article_type = 1
            GROUP BY tk.description,
        ";
        if($d == 'hour'){
            $sql = "
                SELECT
                cast(DATEPART(YEAR, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(MONTH, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(day, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(hour, t.beginning_timestamp) as varchar) d,
            " . $sql . "
                DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date), DATEPART(hour, t.beginning_timestamp)
                ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date), DATEPART(hour, t.beginning_timestamp)
            ";
        }else if($d == 'day'){
            $sql = "
                SELECT
                cast(DATEPART(YEAR, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(MONTH, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(day, t.bookkeeping_date) as varchar) d,
            " . $sql . "
                DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date)
                ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date)
            ";
        }else if($d == 'weekday'){
            $sql = "
                SELECT
                cast(DATEPART(YEAR, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(MONTH, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(day, t.bookkeeping_date) as varchar) d,
            " . $sql . "
                DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date)
                ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date)
            ";
        }else if($d == 'week'){
            $sql = "
                SELECT
                cast(DATEPART(YEAR, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(week, t.bookkeeping_date) as varchar) d,
            " . $sql . "
                DATEPART(YEAR, t.bookkeeping_date), DATEPART(week, t.bookkeeping_date)
                ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(week, t.bookkeeping_date)
            ";
        }else if($d == '10days'){
            $sql = "
                SELECT
                (case when day(t.bookkeeping_date) <= 10 then 'first' when day(t.bookkeeping_date) <= 20 then 'second' else 'third' end) as d,
            " . $sql . "
                (case when day(t.bookkeeping_date) <= 10 then 'first' when day(t.bookkeeping_date) <= 20 then 'second' else 'third' end)
                ORDER BY d
            ";
        }else if($d == 'month'){
            $sql = "
                SELECT
                cast(DATEPART(YEAR, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(MONTH, t.bookkeeping_date) as varchar) d,
            " . $sql . "
                DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date)
                ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date)
            ";
        }else if($d == 'year'){
            $sql = "
                SELECT
                cast(DATEPART(YEAR, t.bookkeeping_date) as varchar) d,
            " . $sql . "
                DATEPART(YEAR, t.bookkeeping_date)
                ORDER BY DATEPART(YEAR, t.bookkeeping_date)
            ";
        }else{

        }
        return $this->run_query($conn, $sql);
    }
    function _get_discount_details($conn, $date, $shop_name){
        $sql = "
            SELECT d.description description, sum(td.quantity) qty, sum(td.amount) amount
            FROM discounts d
            LEFT JOIN trans_discounts td ON td.discount_id = d.id
            INNER JOIN transactions t ON t.id = td.transaction_id
            INNER JOIN shops s ON s.id = t.shop_id
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description = '" . $shop_name . "'
            GROUP BY d.description
        ";
        return $this->run_query($conn, $sql);
    }
    function _get_other_details($conn, $date, $shop_name){
        $sql = "
            SELECT
            'tax' as description, SUM(t.tax_amount) as amount
            FROM transactions t
            INNER JOIN shops s ON s.id = t.shop_id
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description = '" . $shop_name . "'
            UNION
            SELECT
            'promotion' as description, sum(coalesce(tp.discount,0))+ sum(coalesce(tp.amount,0))- sum(coalesce(tp.offered_amount,0)) + sum(tp.articles_amount) + sum(coalesce(tp.discount,0))+ sum(coalesce(tp.amount,0))- sum(CASE WHEN tp.offered_amount <> 0 then tp.articles_amount ELSE 0 END) as amount
            FROM transactions t
            INNER JOIN shops s ON s.id = t.shop_id
            LEFT JOIN trans_promotions tp on tp.transaction_id = t.id
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description = '" . $shop_name . "'
            UNION
            SELECT
            'tip' as description, SUM(ta.price ) + SUM (COALESCE(ta.discount, 0)) as amount
            FROM transactions t
            INNER JOIN shops s ON s.id = t.shop_id
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            INNER JOIN articles a ON (a.id = ta.article_id)
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description = '" . $shop_name . "'
                AND a.article_type = 2
        ";
        return $this->run_query($conn, $sql);
    }

    function _get_trans_details($conn, $date, $shop_name, $d){
        $sql = "
            COUNT(*) qty, SUM(t.total_amount - COALESCE(t.tax_amount, 0)) / COUNT(*) as average_bill
            FROM transactions t
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id
            LEFT JOIN shops s ON s.id = t.shop_id
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description = '" . $shop_name . "'
            GROUP BY
        ";
        if($d == 'hour'){
            $sql = "
                SELECT
                cast(DATEPART(YEAR, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(MONTH, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(day, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(hour, t.beginning_timestamp) as varchar) d,
            " . $sql . "
                DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date), DATEPART(hour, t.beginning_timestamp)
                ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date), DATEPART(hour, t.beginning_timestamp)
            ";
        }else if($d == 'day'){
            $sql = "
                SELECT
                cast(DATEPART(YEAR, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(MONTH, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(day, t.bookkeeping_date) as varchar) d,
            " . $sql . "
                DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date)
                ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date)
            ";
        }else if($d == 'weekday'){
            $sql = "
                SELECT
                cast(DATEPART(YEAR, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(MONTH, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(day, t.bookkeeping_date) as varchar) d,
            " . $sql . "
                DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date)
                ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date)
            ";
        }else if($d == 'week'){
            $sql = "
                SELECT
                cast(DATEPART(YEAR, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(week, t.bookkeeping_date) as varchar) d,
            " . $sql . "
                DATEPART(YEAR, t.bookkeeping_date), DATEPART(week, t.bookkeeping_date)
                ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(week, t.bookkeeping_date)
            ";
        }else if($d == '10days'){
            $sql = "
                SELECT
                (case when day(t.bookkeeping_date) <= 10 then 'first' when day(t.bookkeeping_date) <= 20 then 'second' else 'third' end) as d,
            " . $sql . "
                (case when day(t.bookkeeping_date) <= 10 then 'first' when day(t.bookkeeping_date) <= 20 then 'second' else 'third' end)
                ORDER BY d
            ";
        }else if($d == 'month'){
            $sql = "
                SELECT
                cast(DATEPART(YEAR, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(MONTH, t.bookkeeping_date) as varchar) d,
            " . $sql . "
                DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date)
                ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date)
            ";
        }else if($d == 'year'){
            $sql = "
                SELECT
                cast(DATEPART(YEAR, t.bookkeeping_date) as varchar) d,
            " . $sql . "
                DATEPART(YEAR, t.bookkeeping_date)
                ORDER BY DATEPART(YEAR, t.bookkeeping_date)
            ";
        }else{

        }
        return $this->run_query($conn, $sql);
    }
    function _get_payment_descriptions($conn){
        $sql = "
            SELECT description FROM payments
        ";
        return $this->run_query($conn, $sql);
    }
    function _get_payment_details($conn, $date, $shop_name, $d){
        $sql = "
            p.description payment_detail, sum(COALESCE(tp.amount, 0)) amount, count(tp.transaction_id) qty
            FROM transactions t
            LEFT JOIN shops s ON s.id = t.shop_id
            LEFT JOIN trans_payments tp ON tp.transaction_id = t.id
            INNER JOIN payments p ON p.id = tp.payment_id
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description = '" . $shop_name . "'
            GROUP BY p.description,
        ";
        if($d == 'hour'){
            $sql = "
                SELECT
                cast(DATEPART(YEAR, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(MONTH, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(day, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(hour, t.beginning_timestamp) as varchar) d,
            " . $sql . "
                DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date), DATEPART(hour, t.beginning_timestamp)
                ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date), DATEPART(hour, t.beginning_timestamp)
            ";
        }else if($d == 'day'){
            $sql = "
                SELECT
                cast(DATEPART(YEAR, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(MONTH, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(day, t.bookkeeping_date) as varchar) d,
            " . $sql . "
                DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date)
                ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date)
            ";
        }else if($d == 'weekday'){
            $sql = "
                SELECT
                cast(DATEPART(YEAR, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(MONTH, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(day, t.bookkeeping_date) as varchar) d,
            " . $sql . "
                DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date)
                ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date)
            ";
        }else if($d == 'week'){
            $sql = "
                SELECT
                cast(DATEPART(YEAR, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(week, t.bookkeeping_date) as varchar) d,
            " . $sql . "
                DATEPART(YEAR, t.bookkeeping_date), DATEPART(week, t.bookkeeping_date)
                ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(week, t.bookkeeping_date)
            ";
        }else if($d == '10days'){
            $sql = "
                SELECT
                (case when day(t.bookkeeping_date) <= 10 then 'first' when day(t.bookkeeping_date) <= 20 then 'second' else 'third' end) as d,
            " . $sql . "
                (case when day(t.bookkeeping_date) <= 10 then 'first' when day(t.bookkeeping_date) <= 20 then 'second' else 'third' end)
                ORDER BY d
            ";
        }else if($d == 'month'){
            $sql = "
                SELECT
                cast(DATEPART(YEAR, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(MONTH, t.bookkeeping_date) as varchar) d,
            " . $sql . "
                DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date)
                ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date)
            ";
        }else if($d == 'year'){
            $sql = "
                SELECT
                cast(DATEPART(YEAR, t.bookkeeping_date) as varchar) d,
            " . $sql . "
                DATEPART(YEAR, t.bookkeeping_date)
                ORDER BY DATEPART(YEAR, t.bookkeeping_date)
            ";
        }else{

        }
        return $this->run_query($conn, $sql);
    }

    function _get_article_details($conn, $date, $shop_name, $d, $group_id){
        $sql = "
            SELECT
                g.id as group_id,
                g.description as group_description,
                a.id as article_id,
            	a.description as article_description,
                SUM(ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) as price,
                count(ta.price) amount
            FROM transactions t
            INNER JOIN shops s ON s.id = t.shop_id
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            INNER JOIN articles a ON (a.id = ta.article_id)
            INNER JOIN measure_units mu ON (mu.id = a.measure_unit_id)
            INNER JOIN groups g ON g.id = a." . $group_id . "
            WHERE
                  a.article_type = 1
                  AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                  AND s.description = '" . $shop_name . "'
            GROUP BY a.id, a.description, g.id, g.description
            ORDER BY g.description, amount DESC, price DESC
        ";
        return $this->run_query($conn, $sql);
    }

    function _get_weekly_group_detail($conn, $date, $shop_name, $d, $group_id){
        $sql = "
            SELECT
            cast(DATEPART(YEAR, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(MONTH, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(day, t.bookkeeping_date) as varchar) d,
            g.id as group_id,
            g.description as group_description,
            a.id as article_id,
            a.description as article_description,
            SUM(ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) as price,
            count(ta.price) amount
            FROM transactions t
            INNER JOIN shops s ON s.id = t.shop_id
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            INNER JOIN articles a ON (a.id = ta.article_id)
            INNER JOIN measure_units mu ON (mu.id = a.measure_unit_id)
            INNER JOIN groups g ON g.id = a." . $group_id . "
            WHERE
                a.article_type = 1
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description = '" . $shop_name . "'
            GROUP BY a.id, a.description, g.id, g.description, DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date)
            ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date), g.description, amount DESC, price DESC
        ";
        return $this->run_query($conn, $sql);
    }
    function _get_weekly_sale_detail($conn, $date, $shop_name, $d, $group_id){
        return 'sale detail';
    }
    function _get_weekly_payment_detail($conn, $date, $shop_name, $d, $group_id){
        return 'payment detail';
    }

    function _get_sale_compare($conn, $date, $shops){
        $sql = "
            SELECT
            	cast(DATEPART(YEAR, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(MONTH, t.bookkeeping_date) as varchar) + '-' + cast(DATEPART(day, t.bookkeeping_date) as varchar) d,
                s.description shop_name,
                SUM(ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) as netsale,
                SUM(ta.price) as grossale
            FROM transactions t
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            INNER JOIN articles a ON (a.id = ta.article_id)
            LEFT JOIN shops s ON s.id = t.shop_id
            WHERE t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
            AND s.description IN ('" . $shops . "')
            	AND a.article_type = 1
            GROUP BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date), s.description
            ORDER BY DATEPART(YEAR, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(day, t.bookkeeping_date), s.description
        ";
        return $this->run_query($conn, $sql);
    }
    function _get_trans_compare($conn, $date, $shops){
        $sql = "
            SELECT
                s.description shop_name,
                COUNT(*) transaction_count,
                SUM(t.total_amount - COALESCE(t.tax_amount, 0)) / COUNT(*) as average_bill
            FROM transactions t WITH (INDEX(idx_transactions_bookdate))
            LEFT JOIN shops s ON s.id = t.shop_id
            WHERE t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description IN ('" . $shops . "')
            GROUP BY s.description
            ORDER BY s.description
        ";
        return $this->run_query($conn, $sql);
    }
    function _get_discount_compare($conn, $date, $shops){
        $sql = "
            SELECT
                s.description shop_name,
                d.description discount_description,
                sum(td.quantity) qty,
                sum(td.amount) price
            FROM discounts d
            LEFT JOIN trans_discounts td ON td.discount_id = d.id
            INNER JOIN transactions t ON t.id = td.transaction_id
            INNER JOIN shops s ON s.id = t.shop_id
            WHERE t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description IN ('" . $shops . "')
            GROUP BY s.description, d.description
            ORDER BY s.description, d.description
        ";
        return $this->run_query($conn, $sql);
    }
    function _get_tax_compare($conn, $date, $shops){
        $sql = "
            SELECT
                s.description shop_name,
                SUM(t.tax_amount) as tax
            FROM transactions t WITH (INDEX(idx_transactions_bookdate))
            INNER JOIN shops s ON s.id = t.shop_id
            WHERE t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description IN ('" . $shops . "')
            GROUP BY s.description
            ORDER BY s.description
        ";
        return $this->run_query($conn, $sql);
    }
    function _get_promotion_compare($conn, $date, $shops){
        $sql = "
            SELECT
                s.description shop_name,
                sum(coalesce(tp.discount,0))+ sum(coalesce(tp.amount,0))- sum(coalesce(tp.offered_amount,0)) + sum(tp.articles_amount) + sum(coalesce(tp.discount,0))+ sum(coalesce(tp.amount,0))- sum(CASE WHEN tp.offered_amount <> 0 then tp.articles_amount ELSE 0 END) promotion
            FROM transactions t LEFT JOIN trans_promotions tp on tp.transaction_id = t.id
            LEFT JOIN shops s ON s.id = t.shop_id
            WHERE t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description IN ('" . $shops . "')
            GROUP BY s.description
            ORDER BY s.description
        ";
        return $this->run_query($conn, $sql);
    }
    function _get_tip_compare($conn, $date, $shops){
        $sql = "
            SELECT
                s.description shop_name,
                SUM(ta.price ) + SUM (COALESCE(ta.discount, 0)) tip
            FROM transactions t
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            INNER JOIN articles a ON (a.id = ta.article_id)
            LEFT JOIN groups g ON g.id = a.group_a_id AND a.group_a_id IS NOT NULL
            LEFT JOIN shops s ON s.id = t.shop_id
            WHERE t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description IN ('" . $shops . "')
                AND a.article_type = 2
            GROUP BY s.description
            ORDER BY s.description
        ";
        return $this->run_query($conn, $sql);
    }
    function _get_article_compare($conn, $date, $shops){
        $sql = "
            SELECT
                s.description shop_name,
                g.description as group_description,
                a.description as article_description,
                SUM(ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) as price,
                count(ta.price) amount
            FROM transactions t
            INNER JOIN shops s ON s.id = t.shop_id
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            INNER JOIN articles a ON (a.id = ta.article_id)
            INNER JOIN measure_units mu ON (mu.id = a.measure_unit_id)
            INNER JOIN groups g ON g.id = a.group_a_id
            WHERE a.article_type = 1
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description IN ('" . $shops . "')
            GROUP BY a.description, g.description, s.description
            ORDER BY s.description, g.description, amount DESC, price DESC
        ";
        return $this->run_query($conn, $sql);
    }
    function _get_payment_compare($conn, $date, $shops){
        $sql = "
            SELECT
                s.description shop_name,
                p.description payment_detail,
                sum(COALESCE(tp.amount, 0)) price,
                count(tp.transaction_id) qty
            FROM transactions t
            LEFT JOIN shops s ON s.id = t.shop_id
            LEFT JOIN trans_payments tp ON tp.transaction_id = t.id
            INNER JOIN payments p ON p.id = tp.payment_id
            WHERE t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description IN ('" . $shops . "')
            GROUP BY s.description, p.description
            ORDER BY s.description, p.description
        ";
        return $this->run_query($conn, $sql);
    }
    function _get_sale_date_compare($conn, $date, $shop_name){
        $sql = "
            SELECT
                'f' as od,
                SUM(ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) as netsale,
                SUM(ta.price) as grossale
            FROM transactions t
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            INNER JOIN articles a ON (a.id = ta.article_id)
            LEFT JOIN shops s ON s.id = t.shop_id
                WHERE t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description = '" . $shop_name . "'
                AND a.article_type = 1
            GROUP BY s.description
            UNION
            SELECT
                's' as od,
                SUM(ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) as netsale,
                SUM(ta.price) as grossale
            FROM transactions t
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            INNER JOIN articles a ON (a.id = ta.article_id)
            LEFT JOIN shops s ON s.id = t.shop_id
                WHERE t.bookkeeping_date BETWEEN '" . $date['start_secondary'] . "' AND '" . $date['end_secondary'] . "'
                AND s.description = '" . $shop_name . "'
                AND a.article_type = 1
            GROUP BY s.description
        ";
        return $this->run_query($conn, $sql);
    }
    function _get_trans_date_compare($conn, $date, $shop_name){
        $sql = "
            SELECT
                'f' as od,
                COUNT(*) transaction_count,
                SUM(t.total_amount - COALESCE(t.tax_amount, 0)) / COUNT(*) as average_bill
            FROM transactions t WITH (INDEX(idx_transactions_bookdate))
            LEFT JOIN shops s ON s.id = t.shop_id
            WHERE t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description = '" . $shop_name . "'
            GROUP BY s.description
            UNION
            SELECT
                's' as od,
                COUNT(*) transaction_count,
                SUM(t.total_amount - COALESCE(t.tax_amount, 0)) / COUNT(*) as average_bill
            FROM transactions t WITH (INDEX(idx_transactions_bookdate))
            LEFT JOIN shops s ON s.id = t.shop_id
            WHERE t.bookkeeping_date BETWEEN '" . $date['start_secondary'] . "' AND '" . $date['end_secondary'] . "'
                AND s.description = '" . $shop_name . "'
            GROUP BY s.description
        ";
        return $this->run_query($conn, $sql);
    }
    function _get_discount_date_compare($conn, $date, $shop_name){
        $sql = "
            SELECT
                'f' as od,
                d.description discount_description,
                sum(td.quantity) qty,
                sum(td.amount) price
            FROM discounts d
            LEFT JOIN trans_discounts td ON td.discount_id = d.id
            INNER JOIN transactions t ON t.id = td.transaction_id
            INNER JOIN shops s ON s.id = t.shop_id
            WHERE t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description = '" . $shop_name . "'
            GROUP BY d.description
            UNION
            SELECT
                's' as od,
                d.description discount_description,
                sum(td.quantity) qty,
                sum(td.amount) price
            FROM discounts d
            LEFT JOIN trans_discounts td ON td.discount_id = d.id
            INNER JOIN transactions t ON t.id = td.transaction_id
            INNER JOIN shops s ON s.id = t.shop_id
            WHERE t.bookkeeping_date BETWEEN '" . $date['start_secondary'] . "' AND '" . $date['end_secondary'] . "'
                AND s.description = '" . $shop_name . "'
            GROUP BY d.description
        ";
        return $this->run_query($conn, $sql);
    }
    function _get_tax_date_compare($conn, $date, $shop_name){
        $sql = "
            SELECT
                'f' as od,
                SUM(t.tax_amount) as tax
            FROM transactions t WITH (INDEX(idx_transactions_bookdate))
            INNER JOIN shops s ON s.id = t.shop_id
            WHERE t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description = '" . $shop_name . "'
            GROUP BY s.description
            UNION
            SELECT
                's' as od,
                SUM(t.tax_amount) as tax
            FROM transactions t WITH (INDEX(idx_transactions_bookdate))
            INNER JOIN shops s ON s.id = t.shop_id
            WHERE t.bookkeeping_date BETWEEN '" . $date['start_secondary'] . "' AND '" . $date['end_secondary'] . "'
                AND s.description = '" . $shop_name . "'
            GROUP BY s.description
        ";
        return $this->run_query($conn, $sql);
    }
    function _get_promotion_date_compare($conn, $date, $shop_name){
        $sql = "
            SELECT
                'f' as od,
                sum(coalesce(tp.discount,0))+ sum(coalesce(tp.amount,0))- sum(coalesce(tp.offered_amount,0)) + sum(tp.articles_amount) + sum(coalesce(tp.discount,0))+ sum(coalesce(tp.amount,0))- sum(CASE WHEN tp.offered_amount <> 0 then tp.articles_amount ELSE 0 END) promotion
            FROM transactions t LEFT JOIN trans_promotions tp on tp.transaction_id = t.id
            LEFT JOIN shops s ON s.id = t.shop_id
            WHERE t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description = '" . $shop_name . "'
            GROUP BY s.description
            UNION
            SELECT
                's' as od,
                sum(coalesce(tp.discount,0))+ sum(coalesce(tp.amount,0))- sum(coalesce(tp.offered_amount,0)) + sum(tp.articles_amount) + sum(coalesce(tp.discount,0))+ sum(coalesce(tp.amount,0))- sum(CASE WHEN tp.offered_amount <> 0 then tp.articles_amount ELSE 0 END) promotion
            FROM transactions t LEFT JOIN trans_promotions tp on tp.transaction_id = t.id
            LEFT JOIN shops s ON s.id = t.shop_id
            WHERE t.bookkeeping_date BETWEEN '" . $date['start_secondary'] . "' AND '" . $date['end_secondary'] . "'
                AND s.description = '" . $shop_name . "'
            GROUP BY s.description
        ";
        return $this->run_query($conn, $sql);
    }
    function _get_tip_date_compare($conn, $date, $shop_name){
        $sql = "
            SELECT
                'f' as od,
                SUM(ta.price ) + SUM (COALESCE(ta.discount, 0)) tip
            FROM transactions t
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            INNER JOIN articles a ON (a.id = ta.article_id)
            LEFT JOIN groups g ON g.id = a.group_a_id AND a.group_a_id IS NOT NULL
            LEFT JOIN shops s ON s.id = t.shop_id
            WHERE t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description = '" . $shop_name . "'
                AND a.article_type = 2
            GROUP BY s.description
            UNION
            SELECT
                's' as od,
                SUM(ta.price ) + SUM (COALESCE(ta.discount, 0)) tip
            FROM transactions t
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            INNER JOIN articles a ON (a.id = ta.article_id)
            LEFT JOIN groups g ON g.id = a.group_a_id AND a.group_a_id IS NOT NULL
            LEFT JOIN shops s ON s.id = t.shop_id
            WHERE t.bookkeeping_date BETWEEN '" . $date['start_secondary'] . "' AND '" . $date['end_secondary'] . "'
                AND s.description = '" . $shop_name . "'
                AND a.article_type = 2
            GROUP BY s.description
        ";
        return $this->run_query($conn, $sql);
    }
    function _get_article_date_compare($conn, $date, $shop_name){
        $sql = "
            SELECT
                'f' as od,
                g.description as group_description,
                a.description as article_description,
                SUM(ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) as price,
                count(ta.price) amount
            FROM transactions t
            INNER JOIN shops s ON s.id = t.shop_id
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            INNER JOIN articles a ON (a.id = ta.article_id)
            INNER JOIN measure_units mu ON (mu.id = a.measure_unit_id)
            INNER JOIN groups g ON g.id = a.group_a_id
            WHERE a.article_type = 1
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description = '" . $shop_name . "'
            GROUP BY a.description, g.description
            UNION
            SELECT
                's' as od,
                g.description as group_description,
                a.description as article_description,
                SUM(ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) as price,
                count(ta.price) amount
            FROM transactions t
            INNER JOIN shops s ON s.id = t.shop_id
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            INNER JOIN articles a ON (a.id = ta.article_id)
            INNER JOIN measure_units mu ON (mu.id = a.measure_unit_id)
            INNER JOIN groups g ON g.id = a.group_a_id
            WHERE a.article_type = 1
                AND t.bookkeeping_date BETWEEN '" . $date['start_secondary'] . "' AND '" . $date['end_secondary'] . "'
                AND s.description = '" . $shop_name . "'
            GROUP BY a.description, g.description
        ";
        return $this->run_query($conn, $sql);
    }
    function _get_payment_date_compare($conn, $date, $shop_name){
        $sql = "
            SELECT
                'f' as od,
                p.description payment_detail,
                sum(COALESCE(tp.amount, 0)) price,
                count(tp.transaction_id) qty
            FROM transactions t
            LEFT JOIN shops s ON s.id = t.shop_id
            LEFT JOIN trans_payments tp ON tp.transaction_id = t.id
            INNER JOIN payments p ON p.id = tp.payment_id
            WHERE t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description = '" . $shop_name . "'
            GROUP BY p.description
            UNION
            SELECT
                's' as od,
                p.description payment_detail,
                sum(COALESCE(tp.amount, 0)) price,
                count(tp.transaction_id) qty
            FROM transactions t
            LEFT JOIN shops s ON s.id = t.shop_id
            LEFT JOIN trans_payments tp ON tp.transaction_id = t.id
            INNER JOIN payments p ON p.id = tp.payment_id
            WHERE t.bookkeeping_date BETWEEN '" . $date['start_secondary'] . "' AND '" . $date['end_secondary'] . "'
                AND s.description = '" . $shop_name . "'
            GROUP BY p.description
        ";
        return $this->run_query($conn, $sql);
    }
    function _get_hourly_detail($conn, $date, $shop_name){
        $sql = "
            SELECT a.h, a.article_count article_count, b.trans_count trans_count, a.netsale netsale
            FROM (SELECT DATEPART(hour, t.trans_date) h, COUNT(t.id) article_count, SUM(ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) as netsale
            FROM transactions t
            LEFT JOIN shops s ON s.id = t.shop_id
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            LEFT JOIN articles ar ON ar.id = ta.article_id
            WHERE t.delete_operator_id IS NULL
            	AND ar.article_type = 1
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description = '" . $shop_name . "'
            GROUP BY DATEPART(hour, t.trans_date)
            ) a
            LEFT JOIN (SELECT DATEPART(hour, t.trans_date) h, COUNT(t.id) trans_count
            FROM transactions t
            LEFT JOIN shops s ON s.id = t.shop_id
            WHERE t.delete_operator_id IS NULL
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description = '" . $shop_name . "'
            GROUP BY DATEPART(hour, t.trans_date)
            ) b ON a.h = b.h
            ORDER BY a.h
        ";
        return $this->run_query($conn, $sql);
    }
    function _get_hourly_detail_article($conn, $date, $shop_name, $group_id, $h){
        $sql = "
            SELECT
            g.id as group_id,
            g.description as group_description,
            a.id as article_id,
            a.description as article_description,
            SUM(ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) as price,
            count(ta.price) amount
            FROM transactions t
            INNER JOIN shops s ON s.id = t.shop_id
            LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id
            INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
            INNER JOIN articles a ON (a.id = ta.article_id)
            INNER JOIN measure_units mu ON (mu.id = a.measure_unit_id)
            INNER JOIN groups g ON g.id = a." . $group_id . "
            WHERE
                a.article_type = 1
                AND t.bookkeeping_date BETWEEN '" . $date['start'] . "' AND '" . $date['end'] . "'
                AND s.description = '" . $shop_name . "'
                AND DATEPART(hour, t.trans_date) = '" . $h . "'
            GROUP BY DATEPART(hour, t.trans_date), a.id, a.description, g.id, g.description
            ORDER BY DATEPART(hour, t.trans_date), g.description, amount DESC, price DESC
        ";
        return $this->run_query($conn, $sql);
    }
}
?>
