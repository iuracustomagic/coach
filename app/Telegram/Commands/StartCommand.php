<?php


namespace App\Telegram\Commands;

use Telegram\Bot\Commands\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class StartCommand extends Command
{

    protected $name = 'start';
    protected $description = 'Start Command to get you started';

    public function handle()
    {

//        $update = $this->getUpdate();
//        $name = $update['message']['from']['first_name'];
//        $this->replyWithMessage(['text' => 'Welcome '.$name]);
        $update = Telegram::getUpdates();
$lastKey = array_key_last($update);
        $name = $update[$lastKey]['message']['from']['first_name'];
        $chatId = $update[$lastKey]['message']['chat']['id'];
        Telegram::sendMessage([
           'chat_id' => $chatId,
           'text' => 'Добрый день! '.$name,
           'parse_mode' => 'html'
       ]);
    }
}
