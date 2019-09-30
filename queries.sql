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
