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

class AddFriendInGroupCommand extends Command
{
    protected static $defaultName = 'addFriendGroup';
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
        $VK_GROUP_MY =  $container->getParameter('myGroups');
        $users = [
            ['u_id' => $container->getParameter('mypage')],
        ];

        $rs = $vk->api('friends.get', [
            'user_id' => $users[0]['u_id'],
            'access_token' =>  $vk->getAddedAccessToken(),
            'count'=> 1000,
            'v' => '5.131'
        ], 'array', 'POST');
        $ids = $rs['response']['items'];

        $rs = $vk->api('groups.getMembers', [
            'group_id' => $VK_GROUP_MY,
            'access_token' =>  $vk->getAddedAccessToken(),
            'count'=> 1000,
            'v' => '5.131'
        ], 'array', 'POST');

        $repo = $this->em->getRepository(InvatedUsers::class);
        $invatedUsers = $repo->createQueryBuilder('i')
            ->select('i')
            ->where('i.inviter = :myUser')
            ->andWhere('i.type = \'groupUsers\'')
            ->setParameters(['myUser'=>  $users[0]['u_id']])
            ->getQuery()
            ->getArrayResult();

        $oldInvaitedUserIds = array_column($invatedUsers,'invitation');
        $idsMyGroup = $rs['response']['items'];

//filtred users
        $i =0 ;
        foreach ($ids as $key => $id) {
            foreach ($idsMyGroup as $mg) {
                if ($id == $mg) {

                    unset($ids[$key]);
                }
            }
        }
        foreach ($ids as $key => $id) {
            foreach ($oldInvaitedUserIds as $mg) {
                if ($id == $mg) {
                    unset($ids[$key]);
                }
            }
        }

        $iter = 0;
        $invatedUsers=[];
        foreach ($ids as $user) {
            $invatedUsers[] = $vk->api('groups.invite', [
                'group_id' => $VK_GROUP_MY,
                'user_id' => $user,
                'access_token' =>  $vk->getAddedAccessToken(),
                'v' => '5.131'
            ], 'array', 'POST');

            $iter++;
            $iu = new InvatedUsers();
            $iu->setInviter($users[0]['u_id']);
            $iu->setInvitation($user);
            $iu->setType('groupUsers');

            if (!empty($invatedUsers[$iter - 1]['error'])) {
                $iu->setErrorCode($invatedUsers[$iter - 1]['error']['error_code']);
                $iu->setErrorTxt($invatedUsers[$iter - 1]['error']['error_msg']);
                if ($invatedUsers[$iter - 1]['error']['error_code'] == 14) {
                    $this->em->persist($iu);
                    break;
                }
                if ($invatedUsers[$iter - 1]['error']['error_code'] == 15) {
                    $iter--;
                }
            }

            if ($iter >= 3 ) {
                $this->em->persist($iu);
                break;
            }
            $this->em->persist($iu);
            sleep(rand(3,12));
        }
        $this->em->flush();
        $io->success('success group  '. count($ids));

        return Command::SUCCESS;
    }
}
