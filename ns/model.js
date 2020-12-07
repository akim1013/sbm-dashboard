const sql = require("mssql")
require('dotenv').config()

const CONFIG = {
  user: process.env.DB_USER,
  password: process.env.DB_PASS,
  server: process.env.DB_HOST,
  database: 'meetfresh'
}

exports.getCustomerInfo = async function(offset, limit) {
  try{
    let pool = await sql.connect(CONFIG)
    let result = await pool.request()
      .input('offset', sql.Int, offset)
      .input('limit', sql.Int, limit)
      .query(`
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
       OFFSET @offset ROWS
       FETCH NEXT @limit ROWS ONLY
      `)
    return result
  }catch(err){
    return err
  }
}
