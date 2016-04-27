<?php
//Routes

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/users', function ($request, $response, $args) {
    $apiSettings = $this->get('settings')['api'];
    
    return $this->renderer->render($response, 'users.php',[
        'settings' => $apiSettings
    ]);
});


$app->get('/student/{guid}', function (Request $request, Response $response,$args) {
        $guid = $request->getAttribute('guid');
        $apiSettings = $this->get('settings')['api'];
    
    return $this->renderer->render($response, 'student.php', [
        'guid' => $request->getAttribute('guid'),
        'settings' => $apiSettings,
    ]);
   
});

$app->get('/student_test_time/{guid}', function (Request $request, Response $response,$args) {
        $guid = $request->getAttribute('guid');
        $apiSettings = $this->get('settings')['api'];
    
    return $this->renderer->render($response, 'student_test_time.php', [
        'guid' => $request->getAttribute('guid'),
        'settings' => $apiSettings,
    ]);
   
});