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

class AddFriendCommand extends Command
{
    protected static $defaultName = 'addFriend';
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
        $VK_GROUP_BIG = 179635329;
        $VK_GROUP_MY = 205719869;

        $users = [
            ['u_id' => '523544221'],
        ];

        $rsGetGroups = $vk->api('groups.getMembers', [
            'group_id' => $VK_GROUP_BIG,
            'access_token' => $vk->getAddedAccessToken(),
            'count'=> 1000,
        ], 'array', 'POST');

        $ids = $rsGetGroups['response']['items'];

        $rs = $vk->api('users.get', [
            'user_ids' => substr(implode(',',$ids),0,-1),
            'access_token' => $vk->getAddedAccessToken(),
            'count'=> 1000,
            'fields' => 'online,blacklisted_by_me,bdate',
            'v' => '5.131'
        ], 'array', 'POST');

        $repo = $this->em->getRepository(InvatedUsers::class);
        $invatedUsers = $repo->createQueryBuilder('i')
            ->select('i')
            ->where('i.inviter = :myUser')
            ->andWhere('i.type = \'myFriend\'')
            ->setParameters(['myUser'=> $users[0]['u_id']])
            ->getQuery()
            ->getArrayResult();
        $invatedUsers = array_column($invatedUsers,'invitation');
        $validUsers = [];
        shuffle($rs['response']);
        $qwe=[1=>0,2=>0,3=>0,4=>0,5=>0];
        foreach ($rs['response'] as $user) {
            if (!empty($user['deactivated'])) {
                $qwe[1]++;
                continue;
            }
            if (!empty($user['blacklisted_by_me'])) {
                $qwe[2]++;
                continue;
            }
            if ($user['online'] == 0) {
                $qwe[3]++;
                continue;
            }
//            if (in_array($user['id'], $ids)){
//                $qwe[3]++;
//                continue;
//            }
            if (in_array($user['id'], $invatedUsers)) {
                $qwe[4]++;
                continue;
            }
            $qwe[5]++;
            $validUsers[] = $user;
        }
        //dump($qwe);exit;

        $iter = 0;
        $results = [];
        foreach ($validUsers as $user) {
            $results[] = $vk->api('friends.add', [
                'user_id' => $user['id'],
                'access_token' => $vk->getAddedAccessToken(),
                'v' => '5.131'
            ], 'array', 'POST');

            $iu = new InvatedUsers();
            $iu->setInviter($users[0]['u_id']);
            $iu->setInvitation($user['id']);
            $iu->setType('myFriend');
            $iter++;

            if (!empty($results[$iter - 1]['error'])) {
                $iu->setErrorCode($results[$iter - 1]['error']['error_code']);
                $iu->setErrorTxt($results[$iter - 1]['error']['error_msg']);
                if ($results[$iter - 1]['error']['error_code'] == 14) {
                    $this->em->persist($iu);
                    break;
                }
            }

            if ($iter >= 3 ) {
                $this->em->persist($iu);
                break;
            }
            $this->em->persist($iu);
            sleep(rand(3,19));
        }
        $this->em->flush();
        $io->success('success '. count($validUsers));

        return Command::SUCCESS;
    }
}
