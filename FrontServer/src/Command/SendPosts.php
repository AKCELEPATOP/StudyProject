<?php
/**
 * Created by PhpStorm.
 * User: Sasha
 * Date: 01.02.2019
 * Time: 12:11
 */

namespace App\Command;

use App\Command\Base\BaseCommand;
use App\Entity\Post;
use App\Repository\PostRepository;
use App\Service\RabbitService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\SerializerInterface;

class SendPosts extends BaseCommand
{
    /** @var string */
    protected static $defaultName = 'app:send-posts';

    /** @var PostRepository */
    private $postRepository;

    /** @var SerializerInterface */
    private $serializer;

    /** @var RabbitService */
    protected $rabbitService;

    /**
     * SendPosts constructor.
     * @param PostRepository $postRepository
     * @param SerializerInterface $serializer
     * @param string|null $name
     */
    public function __construct(PostRepository $postRepository,
                                SerializerInterface $serializer,
                                RabbitService $rabbitService,
                                ?string $name = null)
    {
        parent::__construct($name);

        $this->postRepository = $postRepository;
        $this->serializer = $serializer;
        $this->rabbitService = $rabbitService;
    }

    protected function configure()
    {
        $this->setDescription('Send posts to queue')
            ->setHelp('This command allows you to send posts');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $posts = $this->postRepository->getReadyPosts();

        foreach ($posts as $post) {
            try {
                $body = $this->serializer->serialize($post, 'json', ['groups' => 'send']);
                for ($i = 0; $i < $post[0]->getCount(); $i++) {
                    $this->rabbitService->sendMessage($body);
                }
                $this->info($output,'Sent ' . $post[0]->getCount() . ' messages to query');
                $this->postRepository->setState($post['id'], Post::STATUS_PROCESSED);
            } catch (\Exception $ex) {
                $this->postRepository->setState($post['id'], Post::STATUS_ERROR);
                $this->error($output, 'Error: ' . $ex->getMessage());
            }
        }
    }
}
