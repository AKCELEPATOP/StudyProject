<?php
/**
 * Created by PhpStorm.
 * User: Sasha
 * Date: 01.02.2019
 * Time: 12:11
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
use Symfony\Component\Serializer\SerializerInterface;

class SendPosts extends Command
{
    /** @var string  */
    protected static $defaultName = 'app:send-posts';

    /** @var PostRepository  */
    private $postRepository;

    /** @var SerializerInterface  */
    private $serializer;

    /**
     * SendPosts constructor.
     * @param PostRepository $postRepository
     * @param SerializerInterface $serializer
     * @param string|null $name
     */
    public function __construct(PostRepository $postRepository, SerializerInterface $serializer, ?string $name = null)
    {
        parent::__construct($name);

        $this->postRepository = $postRepository;

        $this->serializer = $serializer;

    }

    protected function configure()
    {
        $this->setDescription('Send posts to queue')
            ->setHelp('This command allows you to send posts')
//            ->addArgument('postId', InputArgument::REQUIRED, 'Post id')
//            ->addArgument('method', InputArgument::REQUIRED, 'Method')
//            ->addArgument('url', InputArgument::REQUIRED, 'Url post')
//            ->addArgument('body', InputArgument::OPTIONAL, 'Request body')
//            ->addArgument('time', InputArgument::REQUIRED, 'Time of execution')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $posts = $this->postRepository->getReadyPosts();
        $connection = new AMQPStreamConnection('rabbitmq', 5672,
            '%env(string:RABBITMQ_USER_NAME)%',
            '%env(string:RABBITMQ_PASSWORD)%');
        $channel = $connection->channel();

        $channel->queue_declare('posts', false,false,false,false);

        foreach ($posts as $post){
            $msg = new AMQPMessage($this->serializer->serialize($post,'json',['groups' => Post::GROUP_SEND]));
            $channel->basic_publish($msg,'','posts');
        }

        $channel->close();
        $connection->close();
    }
}