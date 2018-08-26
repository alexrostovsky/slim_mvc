<?php

$app->get('/', 'GameController:index');
$app->get('/list', 'GameController:getlist');
$app->get('/{id}', 'GameController:getGame');
