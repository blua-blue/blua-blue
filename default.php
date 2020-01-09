<?php
// default route (homepage)
define('default_ctrl','home');

// optional: custom 404
define('default_404','notFound');

\Neoan3\Core\Event::listen('Core\\Api::incoming', function (){
    header('Access-Control-Allow-Origin: http://localhost');
});
