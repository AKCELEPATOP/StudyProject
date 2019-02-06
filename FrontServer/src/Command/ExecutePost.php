<?php
/**
 * Created by PhpStorm.
 * User: Sasha
 * Date: 01.02.2019
 * Time: 15:28
 */

namespace App\Command;

use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ExecutePost extends Command
{
    /** @var string  */
    protected static $defaultName = 'app:execute-post';

    protected function configure()
    {
        $this->setDescription('Execute post')
            ->setHelp('This command execute post from queue');

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \ErrorException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = new AMQPStreamConnection('rabbitmq', 5672,
            '%env(string:RABBITMQ_USER_NAME)%',
            '%env(string:RABBITMQ_PASSWORD)%');
        $channel = $connection->channel();

        $channel->queue_declare('posts', false,false,false,false);

        $callback = function ($msg){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'back.test');
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST,           1 );
            curl_setopt($ch, CURLOPT_POSTFIELDS, $msg->body);

            $response = curl_exec($ch);

//            $data = json_decode($response);
        };

        $channel->basic_consume('posts', '',
            false,true,false,false,
            $callback);

        while (count($channel->callbacks)){
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}