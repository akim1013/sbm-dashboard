const express = require('express')
const path = require('path')
const app = express()
const sql = require("mssql")

const router = express.Router()

require('dotenv').config()
//const utiles = require('ns/utiles')

const CONFIG = {
  user: process.env.DB_USER,
  password: process.env.DB_PASS,
  server: process.env.DB_HOST,
  database: 'meetfresh',
  options: {
    enableArithAbort: true
  }
};

let runQuery = (query) => {

}

app.get('/api', function(req, res) {
  sql.connect(CONFIG).then(() => {
    //return sql.query`select * from shops`
  }).then(result => {
    res.send(`Connection established successfully`)
  }).catch(err => {
    res.send(`Connection is not established.`)
  })

  sql.on('error', err => {

  })
});

// default Heroku PORT
app.listen(process.env.PORT || 3001, function(){
    console.log('App is running on http://localhost:3001')
});
