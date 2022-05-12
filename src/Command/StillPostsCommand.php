<?php

namespace App\Command;
use App\Entity\InvatedUsers;
use App\Entity\StillPosts;
use App\Service\SpamFilter;
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
    private $spamFilter;
    public function __construct(EntityManagerInterface $em, SpamFilter $spamFilter)
    {
        parent::__construct();
        $this->spamFilter = $spamFilter;
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

                if (in_array($post['from_id'], $publicUsers)) {
                    continue;
                }
                $signature = $this->spamFilter->filterPost($post);
                $rsUser = $vk->api('users.get', [
                    'user_ids' => $post['from_id'],
                    'access_token' => $vk->getAddedAccessToken(),
                    'count'=> 1000,
                    'fields' => 'online,blacklisted_by_me,bdate',
                    'v' => '5.131'
                ], 'array', 'POST');


                if (!empty($rsUser['response'][0]['deactivated'])) {
                    $signature = 'deactivated ' . $signature;
                }
                if (!empty($rsUser['response'][0]['blacklisted_by_me'])) {
                    $signature = 'blacklisted_by_me ' . $signature;
                }


                if ($signature !== false) {
                    $stillPosts = new StillPosts();
                    $stillPosts->setBotId($users[0]['u_id']);
                    $stillPosts->setDate($post['date']);
                    $stillPosts->setGroupId($group);
                    $stillPosts->setPostId($post['id']);
                    $stillPosts->setText($post['text']);
                    $stillPosts->setUserId($post['from_id']);
                    $stillPosts->setError(9999);
                    $stillPosts->setErrorTxt($signature);
                    $this->em->persist($stillPosts);

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
                $iter++;
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
