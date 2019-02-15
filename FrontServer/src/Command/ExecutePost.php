<?php
/**
 * Created by PhpStorm.
 * User: Sasha
 * Date: 01.02.2019
 * Time: 15:28
 */

namespace App\Command;

use App\Command\Base\BaseCommand;
use App\Entity\Post;
use App\Repository\PostRepository;
use App\Service\HttpService;
use App\Service\RabbitService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ExecutePost extends BaseCommand
{
    /** @var string */
    protected static $defaultName = 'app:execute-post';

    /** @var PostRepository */
    protected $postRepository;

    /** @var RabbitService */
    protected $rabbitService;

    /** @var HttpService */
    protected $httpService;

    public function __construct(PostRepository $postRepository,
                                RabbitService $rabbitService,
                                HttpService $httpService,
                                ?string $name = null)
    {
        parent::__construct($name);
        $this->postRepository = $postRepository;
        $this->rabbitService = $rabbitService;
        $this->httpService = $httpService;
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
        $callback = function ($msg) use ($output) {
            $message = json_decode($msg->body);
            try {
                $console_message = $this->httpService->sendGuzzleRequest($message->{'0'}->{'url'},
                    $message->{'0'}->{'method'},
                    $message->{'0'}->{'body'});
                $this->postRepository->setState($message->{'id'}, Post::STATUS_COMPLETED);
                $this->info($output, $console_message);
            } catch (\Exception $e) {
                $this->postRepository->setState($message->{'id'}, Post::STATUS_ERROR);
                $this->error($output,'Error: ' . $e->getMessage());
            }
        };

        $this->rabbitService->getMessages($callback);
    }
}
