<?php
/**
 * Created by PhpStorm.
 * User: Sasha
 * Date: 01.02.2019
 * Time: 15:28
 */

namespace App\Command;

use App\Entity\Post;
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
    /** @var string */
    protected static $defaultName = 'app:execute-post';

    /** @var PostRepository  */
    protected $postRepository;

    public function __construct(PostRepository $postRepository, ?string $name = null)
    {
        parent::__construct($name);
        $this->postRepository = $postRepository;
    }

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
            'rabbitmq',
            'rabbitmq');
        $channel = $connection->channel();

        $channel->queue_declare('posts', false, false, false, false);

        $callback = function ($msg) {
            $message = json_decode($msg->body);
            try {
                $ch = curl_init($message->{'0'}->{'url'});
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $message->{'0'}->{'method'});
                if(($message->{'0'}->{'method'}) === "POST" || ($message->{'0'}->{'method'}) === "PUT"){
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $message->{'0'}->{'body'});
                    curl_setopt($ch, CURLOPT_HTTPHEADER,
                        array(  'Content-type: application/json',
                                'Content-Length: ' . strlen($message->{'0'}->{'body'}
                                )));
                }
                curl_exec($ch);
                $this->postRepository->setState($message->{'id'}, Post::STATUS_COMPLETED);
            }catch (\Exception $e){
                $this->postRepository->setState($message->{'id'}, Post::STATUS_ERROR);
            }
        };

        $channel->basic_consume('posts', '',
            false, true, false, false,
            $callback);

        $shutdown = function ($channel, $connection) {
            $channel->close();
            $connection->close();
        };

        register_shutdown_function($shutdown, $channel, $connection);

        while (count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}
