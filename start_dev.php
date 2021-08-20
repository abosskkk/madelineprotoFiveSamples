<?php
require_once 'vendor/autoload.php';

use danog\MadelineProto\Stream\Proxy\SocksProxy;

$settings['app_info']['api_id'] = 1821270;


$settings['app_info']['api_hash'] = '76546754b936d09e79a3c1bb7f879e1d';


$settings['connection_settings']['all']['proxy'] = SocksProxy::getName();
$settings['connection_settings']['all']['proxy_extra'] = [
    'address'  => '127.0.0.1',
    'port'     =>  9090,
];



$MadelineProto = new \danog\MadelineProto\API('botSession/session.madeline', $settings);

$MadelineProto->botLogin('1907905521:AAHs5oQcSwS5bwLTkKbjkKECYWDYk5gjGec');


$self = $MadelineProto->getSelf();
echo json_encode($self, JSON_PRETTY_PRINT);

class EventHandler extends \danog\MadelineProto\EventHandler
{

    public $from_id = null;
    public function onUpdateNewChannelMessage(array $update)
    {
        yield $this->onUpdateNewMessage($update);
    }

    public function onUpdateNewMessage(array $update)
    {
        if ($update['message']['_'] === 'messageEmpty') {
            return;
        }
        $this->from_id = $update['message']['from_id'];
        $update_json =  json_encode($update, JSON_PRETTY_PRINT);
        echo $update_json;
        try{
            $this->send_message_to_from_id('hello world!');
        }
        catch (Exception $e) {
            switch ($e->getMessage()) {
                case 'USER_IS_BOT': {
                    }
                default: {
                        echo 'new exception';
                    }
            }
            echo $e;
        }

        return;
    }

    public function send_message($peer, $message, $reply = false){
        $message_object = [
            'peer' => $peer,
            'message' => $message,
            'parse_mode' => 'html',
        ];
        if ($reply) {
            array_push($message_object, ['reply_to_msg_id' => $reply,]);
        }
        return $this->messages->sendMessage($message_object);
    }

    public function send_message_to_from_id($message){
        return $this->send_message($this->from_id,$message);
    }



}

$MadelineProto->loop(function () use ($MadelineProto) {
    yield $MadelineProto->start();
    yield $MadelineProto->setEventHandler(EventHandler::class);
});
$MadelineProto->loop();
