const express = require('express')
const path = require('path')
const app = express()
const sql = require("mssql")
const bodyParser = require('body-parser')
const router = express.Router()
const jsonParser = bodyParser.json()
require('dotenv').config()

const { getPool } = require('./ns/pools') // connection pools
//const utiles = require('ns/utiles')

const CONFIG = {
  user: process.env.DB_USER,
  password: process.env.DB_PASS,
  server: process.env.DB_HOST,
  database: 'meetfresh'
}

// run a query
async function runQuery(query, config) {
  // pool will always be connected when the promise has resolved - may reject if the connection config is invalid
  const pool = await getPool('default', config)
  const result = await pool.request().query(query)
  return result
}

// app.get('/api', function(req, res) {
//   runQuery(`select * from shops`, CONFIG).then(result => {
//     res.send(result)
//   }).catch(error => {
//     res.send('Something went wrong..')
//   })
// });

app.post('/api/crm/get_customer_info', jsonParser, function(req, res) {
  runQuery(`
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
    OFFSET ${req.body.offset} ROWS
    FETCH NEXT ${req.body.offset} ROWS ONLY
  `, CONFIG).then(result => {
    res.send(result)
  }).catch(error => {
    res.send('Something went wrong...')
  })
});

// default Heroku PORT
app.listen(process.env.PORT || 3001, function(){
    console.log('App is running on http://localhost:3001')
});
