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
