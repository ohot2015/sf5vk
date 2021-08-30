<?php

namespace App\Command;
use App\Entity\InvatedUsers;
use App\Entity\StillPosts;
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
    private function filterPost(array $post):bool
    {
        if (strval($post['from_id'])[0]  === '-' ){
            return false;
        }
        if ( ($post['date'] + (60 * 60 * 24)) < time()) {
            return false;
        }
        if (empty($post['text'])) {
            return false;
        }

        $signatures = [
            'мп',
            'МП',
            '$',
            'деньги',
            'материальную',
            'поддержку',
            'подержку',
            'подершку',
            'поддершку',
            'подержу',
            'поддержу',
            'вознаграждение',
            'вознагрождение',
            'награда',
            'бабки',
            'заплачу',
            'билеты',
            'переезд',
            "денег",
            "деньг",
            "плата",
            "плач",
            "обеспеч",
            "содерж",
            "групп",
            "паблик",
            "сайт",
            "сообщество",
            "канал",
            "купи",
            "купай",
            "прода",
            "клуб",
            "http",
            "регистри",
            "регестри",
            "заходи",
            "переходи",
            "приобрет",
            "приобрит"
        ];

        foreach ($signatures as $s) {
            if (strpos(strtolower($post['text']), $s) !== false) {
                return false;
            }
        }
        return true;
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
        $VK_GROUP_MY = $container->getParameter('myGroups');
        $groups = explode(',',$container->getParameter('groupsStillPosts'));
        $users = [
            ['u_id' => $container->getParameter('mypage')],
        ];

        $repo = $this->em->getRepository(StillPosts::class);
        $resp=[];
        $iter=0;
        $publicUsers = [];
        foreach ($groups as $group) {
            $rsWall = $vk->api('wall.get', [
                'owner_id' => $group,
                'access_token' => $vk->getAddedAccessToken(),
                'count' => 10,
                'extended' =>1,
                'fields'=> 'name'
            ], 'array', 'POST');
            if (empty($rsWall['response'])) {
                continue;
            }

            foreach($rsWall['response']['items'] as $post) {
                $posted = $repo->findBy(['groupId'=> $group, 'postId' => $post['id']]);
                if (!empty($posted)) {
                    continue;
                }
                if (!$this->filterPost($post)) {
                    continue;
                }
                if (in_array($post['from_id'], $publicUsers)) {
                    continue;
                }
                $publicUsers[] = $post['from_id'];
                $profileUser = [];
                foreach($rsWall['response']['profiles'] as $profile){
                    if ($profile['id'] === $post['from_id']) {
                        $profileUser = $profile;
                    }
                }
                if (empty($profileUser)) {
                    continue;
                }

                $iter++;
                $rsPost = $vk->api('wall.post', [
                    'owner_id' => $VK_GROUP_MY,
                    'from_group' => 1,
                    'message'=> sprintf('%s %s%sот пользователя: %s[id%s|%s %s]',
                        $post['text'],
                        PHP_EOL,
                        PHP_EOL,
                        PHP_EOL,
                        $post['from_id'],
                        $profileUser['first_name'],
                        $profileUser['last_name']
                    ),
                //    'message'=> $post['text'] .'  '. PHP_EOL . PHP_EOL . 'от пользователя: '. PHP_EOL . '@id'.$post['from_id'],
                    'publish_date' => time() + (60 * 30 * $iter),
                    'copyright' => '@vk.com/club' . $group,
                    'access_token' => $vk->getAddedAccessToken(),
                    'count' => 10,
                ], 'array', 'POST');

                $stillPosts = new StillPosts();
                $stillPosts->setBotId($users[0]['u_id']);
                $stillPosts->setDate($post['date']);
                $stillPosts->setGroupId($group);
                $stillPosts->setPostId($post['id']);
                $stillPosts->setText($post['text']);
                $stillPosts->setUserId($post['from_id']);
                if (!empty($rsPost['error'])) {
                    $stillPosts->setError($rsPost['error']['error_code']);
                    $stillPosts->setErrorTxt($rsPost['error']['error_msg']);
                }
                $this->em->persist($stillPosts);
                $resp[] = $rsPost;
                sleep(rand(1,18));
            }
        }
        $this->em->flush();
        $io->success('success'. $iter);

        return Command::SUCCESS;
    }
}
