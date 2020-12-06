const express = require('express');
const path = require('path');
const app = express();

app.get('/api', function(req, res) {
  const sql = require("mssql");
  const config = {
    user: 'laguna',
    password: 'goqkdtks.1234',
    server: 'localhost',
    database: 'meetfresh'
  };
  sql.connect(config, function (err) {

    if (err) console.log(err);

    // create Request object
    var request = new sql.Request();

    // query to the database and get the records
    request.query('select * from shops', function (err, recordset) {

        if (err) console.log(err)

        // send records as a response
        res.send(recordset);

    });
  });
});

// default Heroku PORT
app.listen(process.env.PORT || 3000, function(){
    console.log('App is running on http://localhost:3000')
});
