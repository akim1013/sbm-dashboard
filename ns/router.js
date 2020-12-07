var express = require('express');
var router = express.Router();

const bodyParser = require('body-parser')
const jsonParser = bodyParser.json()

router.get('/', function(req, res) {
  res.send('Root')
});

router.get('/api', function(req, res) {
  res.send('API')
});

// export your router
module.exports = router;
