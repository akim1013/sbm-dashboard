const express = require('express')
const path = require('path')
const app = express()
const bodyParser = require('body-parser')
const router = express.Router()
const jsonParser = bodyParser.json()

const { getCustomerInfo } = require('./ns/model')

app.get('/api', function(req, res) {
  res.send('You have reached API endpoint.')
});

app.post('/api/crm/get_customer_info', jsonParser, async function(req, res){
  await getCustomerInfo(req.body.offset, req.body.limit).then(result => {
    res.send(result.recordsets)
  }).catch(err => {
    res.status(500).send('Server error. Please try again later.')
  })
});

// default Heroku PORT
app.listen(process.env.PORT || 3001, function(){
    console.log('App is running on http://localhost:3001')
});
