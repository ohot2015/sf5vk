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

class ClearTrashMyGroupCommand extends Command
{
    protected static $defaultName = 'clearTrashMyGroup';
    protected static $defaultDescription = 'Add a short description for your command';
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
        $myGroup = $container->getParameter('myGroups');
        $users = [
            ['u_id' => $container->getParameter('mypage')],
        ];

        $rsGetGroups = $vk->api('groups.getMembers', [
            'group_id' => substr($myGroup,1),
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

        shuffle($rs['response']);
        $prepare_delete_users_ids = [];
        foreach ($rs['response'] as $user) {
            if (!empty($user['deactivated'])) {
                array_push($prepare_delete_users_ids, $user['id']);
            }
            if (!empty($user['blacklisted_by_me'])) {
                array_push($prepare_delete_users_ids, $user['id']);
            }
        }

        foreach ($prepare_delete_users_ids as $user) {
            $vk->api('groups.removeUser', [
                'group_id' => substr($myGroup,1),
                'user_id' => $user,
                'access_token' => $vk->getAddedAccessToken(),
                'v' => '5.131'
            ], 'array', 'POST');
            sleep(rand(3,5));
        }
        $io->success('delete users '. implode(', ',$prepare_delete_users_ids));

        return Command::SUCCESS;
    }
}
