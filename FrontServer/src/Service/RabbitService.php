<?php
/**
 * Created by PhpStorm.
 * User: Sasha
 * Date: 14.02.2019
 * Time: 15:34
 */

namespace App\Service;


use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RabbitService
{
    private $connection;

    private $channel;

    public function __construct(ContainerInterface $container)
    {
        $this->connection = new AMQPStreamConnection(
            $container->getParameter('rabbit.host'),
            $container->getParameter('rabbit.port'),
            $container->getParameter('rabbit.login'),
            $container->getParameter('rabbit.password'));

        $this->channel =$this->connection->channel();

        $this->channel->queue_declare('posts', false, false, false, false);
    }

    public function sendMessage($body){
        $msg = new AMQPMessage($body);

        $this->channel->basic_publish($msg, '', 'posts');
    }

    public function getMessages(callable $callback)
    {
        $this->channel->basic_consume('posts', '',
            false, true, false, false,
            $callback);
        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }
    }

    public function __destruct()
    {
        $this->connection->close();
        $this->channel->close();
    }
}
