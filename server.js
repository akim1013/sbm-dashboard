const express = require('express');
const path = require('path');
const app = express();

// // Serve static files....
// app.use(express.static(__dirname + '/'));
//
// // Send all requests to index.html
app.get('/api', function(req, res) {
    res.send('Nodejs API server is live');
});

// default Heroku PORT
app.listen(process.env.PORT || 3000, function(){
    console.log('App is running on http://localhost:3000')
});
