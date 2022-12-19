<?php

require 'vendor/autoload.php';
include 'bootstrap.php';

use Chatter\Models\Message;
use Chatter\Middleware\Logging as ChatterLogging;
use Chatter\Middleware\Authentication as ChatterAuth;
use Chatter\Middleware\FileFilter;
use Chatter\Middleware\FileMove;
use Chatter\Middleware\ImageRemoveExif;

$app = new \Slim\App();
$app->add(new ChatterAuth());
$app->add(new ChatterLogging());

$app->group('/v1' , function() {
    $app->group('/messages', function () {
        $this->map(['GET'], '', function ($request, $response, $args) {
            $_message = new Message();

            $messages = $_message->all();

            $payload = [];
            foreach($messages as $_msg) {
                $payload[$_msg->id] = $_msg->output();
            }

            return $response->withStatus(200)->withJson($payload);
        })->setName('get_messages');
    });
});

$app->group('/v2' , function() {
    $app->group('/messages', function () {
        $this->map(['GET'], '', function ($request, $response, $args) {
            $_message = new Message();

            $messages = $_message->all();

            $payload = [];
            foreach($messages as $_msg) {
                $payload[$_msg->id] = $_msg->output();
            }

            return $response->withStatus(200)->withJson($payload);
        })->setName('get_messages');
    });
});

    $filter = new FileFilter();
    $removeExif = new ImageRemoveExif();
    $move   = new FileMove();
    
    $this->map(['POST'], '', function ($request, $response, $args) {
        $_message = $request->getParsedBodyParam('message', '');

        $message = new Message();
        $message->body = $_message;
        $message->user_id = $request->getAttribute('user_id');
        $message->image_url = $request->getAttribute('filename');
        $message->save();

        if ($message->id) {
            $payload = ['message_id' => $message->id,
                'message_uri' => '/messages/' . $message->id,
                'image_url' => $message->image_url
            ];
            return $response->withStatus(201)->withJson($payload);
        } else {
            return $response->withStatus(400);
        }
    })->add($filter)->add($removeExif)->add($move)->setName('create_messages');

    $this->delete('/{message_id}', function ($request, $response, $args) {
        $message = Message::find($args['message_id']);
        $message->delete();

        if ($message->exists) {
            return $response->withStatus(400);
        } else {
            return $response->withStatus(204);
        }
    })->setName('delete_messages');


// Run app
$app->run();