// Net sale
SELECT
    t.shop_id as shop_id,
    SUM(ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) as netsale
FROM transactions t WITH (INDEX(idx_transactions_bookdate))
LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id AND tk.in_statistics=1
INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
INNER JOIN articles a ON (a.id = ta.article_id) AND a.article_type Not In(2,3)
INNER JOIN measure_units mu ON (mu.id = a.measure_unit_id)
WHERE t.bookkeeping_date BETWEEN '2019-09-20' AND '2019-09-20'
		AND t.delete_operator_id IS NULL
		AND shop_id = 13
GROUP BY t.shop_id


// Num transactions and average bill
SELECT COUNT(*) transaction_count,
    SUM(t.total_amount - COALESCE(t.tax_amount, 0)) / COUNT(*) as average_bill,
    t.shop_id as shop_id
FROM transactions t WITH (INDEX(idx_transactions_bookdate))
LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id
LEFT JOIN shops s ON s.id = t.shop_id AND tk.in_statistics=1
WHERE t.delete_operator_id IS NULL
    AND t.bookkeeping_date BETWEEN '2019-09-20' AND '2019-09-20'
	AND shop_id = 13
GROUP BY t.shop_id

// Discount
SELECT COALESCE(SUM(ta.discount),0)
FROM transactions t INNER JOIN trans_articles ta ON ta.transaction_id = t.id
WHERE t.bookkeeping_date BETWEEN '2019-09-20' AND '2019-09-20'
		AND t.delete_operator_id IS NULL
		AND shop_id = 13

// Promotions
SELECT sum(coalesce(tp.discount,0))+ sum(coalesce(tp.amount,0))- sum(coalesce(tp.offered_amount,0)) + sum(tp.articles_amount) + sum(coalesce(tp.discount,0))+ sum(coalesce(tp.amount,0))- sum(CASE WHEN tp.offered_amount <> 0 then tp.articles_amount ELSE 0 END)
FROM transactions t LEFT JOIN trans_promotions tp on tp.transaction_id = t.id
WHERE t.bookkeeping_date BETWEEN '2019-09-20' AND '2019-09-20'
		AND t.delete_operator_id IS NULL
		AND shop_id = 13

//
SELECT SUM(ta.price ) + SUM (ta.discount) total_amount
FROM transactions t
JOIN transaction_causals tk ON tk.id = t.transaction_causal_id
JOIN trans_articles ta ON ta.transaction_id=t.id
JOIN articles a ON a.id=ta.article_id AND a.article_type Not In(2,3)   AND tk.in_statistics=1

WHERE t.bookkeeping_date BETWEEN '2019-09-20' AND '2019-09-20'
		AND t.delete_operator_id IS NULL
		AND shop_id = 13

// Tips
SELECT SUM(ta.price ) + SUM (COALESCE(ta.discount, 0)) total_amount
FROM transactions t
INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
INNER JOIN articles a ON (a.id = ta.article_id) AND a.article_type In(2)
INNER JOIN measure_units mu ON (mu.id = a.measure_unit_id)
LEFT JOIN transaction_causals tcau ON tcau.id = t.transaction_causal_id
LEFT JOIN groups g ON g.id = a.group_a_id AND a.group_a_id IS NOT NULL
LEFT JOIN article_causals ac ON ta.causal_id = ac.id  AND tcau.in_statistics=1
WHERE t.bookkeeping_date BETWEEN '2019-09-20' AND '2019-09-20'
		AND t.delete_operator_id IS NULL
		AND shop_id = 13
// Netsale detail
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
        AND t.bookkeeping_date BETWEEN '2019-09-20' AND '2019-09-20'
		AND t.shop_id = '1'
GROUP BY g.description




// Articles details
SELECT g.id group_id, g.description group_name, a.description article_name, sub_result.amount amount, sub_result.price price, sub_result_last_week.amount last_week_amount, sub_result_last_week.price last_week_price
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
    AND t.bookkeeping_date BETWEEN '2019-11-07' AND '2019-11-07'
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
    AND t.bookkeeping_date BETWEEN '2019-11-06' AND '2019-11-06'
GROUP BY a.id) sub_result_last_week ON sub_result_last_week.article_id = a.id
ORDER BY g.id
// Discount details
SELECT d.description discount_desctiption, this_week.quantity this_week_quantity, this_week.amount this_week_amount, last_week.quantity last_week_quantity, last_week.amount last_week_amount
FROM discounts d
LEFT JOIN (SELECT d.description discount_description, sum(td.quantity) quantity, sum(td.amount) amount
FROM discounts d
LEFT JOIN trans_discounts td ON td.discount_id = d.id
INNER JOIN transactions t ON t.id = td.transaction_id
INNER JOIN shops s ON s.id = t.shop_id
WHERE t.delete_operator_id IS NULL
    AND t.bookkeeping_date BETWEEN '2019-11-07' AND '2019-11-07'
GROUP BY d.description) this_week ON d.description = this_week.discount_description
LEFT JOIN (SELECT d.description discount_description, sum(td.quantity) quantity, sum(td.amount) amount
FROM discounts d
LEFT JOIN trans_discounts td ON td.discount_id = d.id
INNER JOIN transactions t ON t.id = td.transaction_id
INNER JOIN shops s ON s.id = t.shop_id
WHERE t.delete_operator_id IS NULL
    AND t.bookkeeping_date BETWEEN '2019-11-06' AND '2019-11-06'
GROUP BY d.description) last_week ON d.description = last_week.discount_description

// Payment detail
SELECT p.description, this_week_payment.amount this_week_amount, this_week_payment.qty this_week_qty, last_week_payment.amount last_week_amount, last_week_payment.qty last_week_amount
FROM payments p
LEFT JOIN (SELECT p.description payment_detail, sum(COALESCE(tp.amount, 0)) amount, count(tp.transaction_id) qty
FROM transactions t WITH (INDEX(idx_transactions_bookdate))
LEFT JOIN shops s ON s.id = t.shop_id
LEFT JOIN trans_payments tp ON tp.transaction_id = t.id
INNER JOIN payments p ON p.id = tp.payment_id
WHERE t.delete_operator_id IS NULL
    AND t.bookkeeping_date BETWEEN '2019-11-07' AND '2019-11-07'
GROUP BY p.description) this_week_payment ON p.description = this_week_payment.payment_detail
LEFT JOIN (SELECT p.description payment_detail, sum(COALESCE(tp.amount, 0)) amount, count(tp.transaction_id) qty
FROM transactions t WITH (INDEX(idx_transactions_bookdate))
LEFT JOIN shops s ON s.id = t.shop_id
LEFT JOIN trans_payments tp ON tp.transaction_id = t.id
INNER JOIN payments p ON p.id = tp.payment_id
WHERE t.delete_operator_id IS NULL
    AND t.bookkeeping_date BETWEEN '2019-11-06' AND '2019-11-06'
GROUP BY p.description) last_week_payment on p.description = last_week_payment.payment_detail

// Dataset for David
SELECT d.m month, d.w week, d.h hour, d.shop_name shop, g.description group_name, a.description article_name, d.amount qty, d.price price
FROM articles a
INNER JOIN (SELECT
	s.description shop_name,
	DATEPART(month, t.bookkeeping_date) m,
	DATEPART(week, t.bookkeeping_date) w,
	DATEPART(hour, t.beginning_timestamp) h,
	a.id as article_id,
    SUM(ta.price + COALESCE(ta.discount, 0) + COALESCE(ta.promotion_discount, 0)) as price,
    count(ta.price) amount
FROM transactions t
INNER JOIN shops s ON s.id = t.shop_id
LEFT JOIN transaction_causals tk ON tk.id = t.transaction_causal_id AND tk.in_statistics=1
INNER JOIN trans_articles ta ON (ta.transaction_id = t.id)
INNER JOIN articles a ON (a.id = ta.article_id) AND a.article_type Not In(2,3)
INNER JOIN measure_units mu ON (mu.id = a.measure_unit_id)
INNER JOIN groups g ON g.id = a.group_a_id
WHERE t.delete_operator_id IS NULL
    AND t.bookkeeping_date BETWEEN '2019-01-01' AND '2019-12-31'
GROUP BY a.id, DATEPART(month, t.bookkeeping_date), DATEPART(week, t.bookkeeping_date), DATEPART(hour, t.beginning_timestamp), s.description
) d ON d.article_id = a.id
LEFT JOIN groups g ON g.id = a.group_a_id
ORDER BY shop, month, week, hour

// Detail comparison article part by shops
SELECT s.description shop_name, g.id group_id, g.description group_name, a.description article_name, sub_result.amount amount, sub_result.price price, sub_result_last_week.amount last_week_amount, sub_result_last_week.price last_week_price
FROM groups g
INNER JOIN articles a ON g.id = a.group_a_id
LEFT JOIN (SELECT
	s.description shop_name,
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
    AND t.bookkeeping_date BETWEEN '2019-11-07' AND '2019-11-07'
GROUP BY a.id, s.description) sub_result ON a.id = sub_result.article_id
LEFT JOIN (SELECT
	s.description shop_name,
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
    AND t.bookkeeping_date BETWEEN '2019-11-06' AND '2019-11-06'
GROUP BY a.id, s.description) sub_result_last_week ON sub_result_last_week.article_id = a.id
INNER JOIN shops s ON s.description = sub_result.shop_name AND s.description = sub_result_last_week.shop_name
ORDER BY s.description, g.id

// Detail comparison discount by shops
SELECT s.description shop_name, d.description discount_desctiption, this_week.quantity this_week_quantity, this_week.amount this_week_amount, last_week.quantity last_week_quantity, last_week.amount last_week_amount
FROM discounts d
LEFT JOIN (SELECT s.description shop_name, d.description discount_description, sum(td.quantity) quantity, sum(td.amount) amount
FROM discounts d
LEFT JOIN trans_discounts td ON td.discount_id = d.id
INNER JOIN transactions t ON t.id = td.transaction_id
INNER JOIN shops s ON s.id = t.shop_id
WHERE t.delete_operator_id IS NULL
    AND t.bookkeeping_date BETWEEN '2019-11-07' AND '2019-11-07'
GROUP BY d.description, s.description) this_week ON d.description = this_week.discount_description
LEFT JOIN (SELECT s.description shop_name, d.description discount_description, sum(td.quantity) quantity, sum(td.amount) amount
FROM discounts d
LEFT JOIN trans_discounts td ON td.discount_id = d.id
INNER JOIN transactions t ON t.id = td.transaction_id
INNER JOIN shops s ON s.id = t.shop_id
WHERE t.delete_operator_id IS NULL
    AND t.bookkeeping_date BETWEEN '2019-11-06' AND '2019-11-06'
GROUP BY d.description, s.description) last_week ON d.description = last_week.discount_description
INNER JOIN shops s ON s.description = this_week.shop_name AND s.description = last_week.shop_name
ORDER BY s.description

// Detail comparison payment by shops
SELECT s.description shop_name, p.description, this_week_payment.amount this_week_amount, this_week_payment.qty this_week_qty, last_week_payment.amount last_week_amount, last_week_payment.qty last_week_amount
FROM payments p
LEFT JOIN (SELECT s.description shop_name, p.description payment_detail, sum(COALESCE(tp.amount, 0)) amount, count(tp.transaction_id) qty
FROM transactions t WITH (INDEX(idx_transactions_bookdate))
LEFT JOIN shops s ON s.id = t.shop_id
LEFT JOIN trans_payments tp ON tp.transaction_id = t.id
INNER JOIN payments p ON p.id = tp.payment_id
WHERE t.delete_operator_id IS NULL
    AND t.bookkeeping_date BETWEEN '2019-11-07' AND '2019-11-07'
GROUP BY p.description, s.description) this_week_payment ON p.description = this_week_payment.payment_detail
LEFT JOIN (SELECT s.description shop_name, p.description payment_detail, sum(COALESCE(tp.amount, 0)) amount, count(tp.transaction_id) qty
FROM transactions t WITH (INDEX(idx_transactions_bookdate))
LEFT JOIN shops s ON s.id = t.shop_id
LEFT JOIN trans_payments tp ON tp.transaction_id = t.id
INNER JOIN payments p ON p.id = tp.payment_id
WHERE t.delete_operator_id IS NULL
    AND t.bookkeeping_date BETWEEN '2019-11-06' AND '2019-11-06'
GROUP BY p.description, s.description) last_week_payment on p.description = last_week_payment.payment_detail
INNER JOIN shops s ON s.description = this_week_payment.shop_name AND s.description = last_week_payment.shop_name
ORDER BY s.description

// Presence Control
SELECT o.id operator_id, o.code operator_code, o.description operator_name, p.till_id, p.shop_id, p.timestamp t_stamp, p.operation_type o_type
FROM operators o
INNER JOIN presence_operations p ON o.id = p.operator_id
INNER JOIN tills t ON t.id = p.till_id
INNER JOIN shops s ON s.id = p.shop_id
WHERE p.timestamp BETWEEN '2019-12-01' AND '2019-12-31'
	AND t.id IN ('1', '2', '3')
	AND s.id IN ('1')
	AND o.id IN ('1', '2', '39', '40', '42')
ORDER BY operator_id, shop_id, t_stamp

// tax
SELECT
    t.shop_id as shop_id,
    SUM(t.tax_amount) as tax
FROM transactions t WITH (INDEX(idx_transactions_bookdate))
INNER JOIN shops s ON s.id = t.shop_id
WHERE t.delete_operator_id IS NULL
    AND t.bookkeeping_date BETWEEN '2019-09-20' AND '2019-09-20'
GROUP BY t.shop_id


// AC
SELECT tb.*
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
    AND t.bookkeeping_date BETWEEN '2019-11-01' AND '2019-12-31'
    AND s.description IN ('SUNMERRY_NJ')
	AND g.description NOT LIKE '%add_on%'
GROUP BY g.id, DATEPART(day, t.bookkeeping_date), DATEPART(WEEKDAY, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(YEAR, t.bookkeeping_date)) tb
ORDER BY tb.y, tb.m, tb.d



SELECT tb.y, tb.m, tb.d, sum(tb.ac)
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
    AND t.bookkeeping_date BETWEEN '2019-11-01' AND '2019-12-31'
    AND s.description IN ('SUNMERRY_NJ')
	AND g.description NOT LIKE '%add_on%'
GROUP BY g.id, DATEPART(day, t.bookkeeping_date), DATEPART(WEEKDAY, t.bookkeeping_date), DATEPART(MONTH, t.bookkeeping_date), DATEPART(YEAR, t.bookkeeping_date)) tb
GROUP BY tb.y, tb.m, tb.d
ORDER BY tb.y, tb.m, tb.d

//
