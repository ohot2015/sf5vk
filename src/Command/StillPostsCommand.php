<?php

namespace App\Command;
use App\Entity\InvatedUsers;
use App\Service\VK;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Container;

class StillPostsCommand extends Command
{
    protected static $defaultName = 'stillPosts';
    protected static $defaultDescription = 'Add a short description for your command';
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }
    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        /** @var Container container */
        $container = $this->getApplication()->getKernel()->getContainer();
        /** @var VK $vk */
        $vk = $container->get('vk');
        $vk->setApiVersion(5.131);
        $VK_GROUP_BIG = '-201078167';//179635329;
        $VK_GROUP_MY = '-205719869';

        $users = [
            ['u_id' => '523544221'],
        ];

        $rsWall = $vk->api('wall.get', [
            'owner_id' => $VK_GROUP_BIG,
            'access_token' => $vk->getAddedAccessToken(),
            'count' => 4,

        ], 'array', 'POST');
        $posts =[];
        foreach($rsWall['response']['items'] as $post) {
            $rsPost = $vk->api('wall.post', [
                'owner_id' => $VK_GROUP_MY,
                'from_group' => 1,
                'message'=> $post['text'] .'  '. PHP_EOL . PHP_EOL . 'от пользователя: '. PHP_EOL . 'https://vk.com/id'.$post['from_id'],
                'publish_date' => time() + (60 * 60 * 24),
                'copyright' => 'http://vk.com/club' . '201078167',
                'access_token' => $vk->getAddedAccessToken(),
                'count' => 10,
            ], 'array', 'POST');
            $rsPost[] = $rsPost['response'];
            sleep(rand(1,18));
        }


        $io->success('success '. count([]));

        return Command::SUCCESS;
    }
}
