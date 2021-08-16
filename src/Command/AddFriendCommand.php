<?php

namespace App\Command;
use App\Service\VK;
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
        $this->container = $this->getApplication()->getKernel()->getContainer();
        /** @var VK $vk */
        $vk = $this->container->get('vk');
        $vk->setApiVersion(5.131);

        $VK_GROUP_BIG = 179635329;
        $VK_GROUP_MY = 205719869;

        $users = [
            ['u_id' => '523544221', 'token' => 'cd3369824bc184f658b0c8d6adfa07272feee21dfb0d92d4cddba92310dfab7363c6ef7045c6efb97fb58'],
        ];

        $rs = $vk->api('groups.getMembers', [
            'group_id' => $VK_GROUP_BIG,
            'access_token' => $users[0]['token'],
            'count'=> 1000,
        ], 'array', 'POST');

        $ids = $rs['response']['items'];

        $rs = $vk->api('users.get', [
            'user_ids' => substr(implode(',',$ids),0,-1),
            'access_token' => $users[0]['token'],
            'count'=> 1000,
            'fields' => 'online,blacklisted_by_me,bdate',
            'v' => '5.131'
        ], 'array', 'POST');


        $json = file_get_contents('./invaitedUserIds.json');
        if (!empty($json)) {
            $oldInvaitedUserIds = json_decode($json,true);
        }

        $validUsers = [];
        foreach ($rs['response'] as $user) {
            if (!empty($user['deactivated'])) {
                continue;
            }
            if ($user['online'] == 0) {
                continue;
            }
            if (in_array($user['id'],$oldInvaitedUserIds)) {
                continue;
            }
            $validUsers[] = $user;
        }
        $iter = 0;
        $invaitedUserIds = [];
        $results = [];
        foreach ($validUsers as $user) {
            $results[] = $vk->api('friends.add', [
                'user_id' => $user['id'],
                'access_token' => $users[0]['token'],
                'v' => '5.131'
            ], 'array', 'POST');

            $invatedUserIds[] = $user['id'];
            $iter++;
            sleep(rand(3,19));
            if (!empty($rs['error'])) {
                break;
            }
            if ($iter >= 3 ) {
                break;
            }
        }

        file_put_contents('./invaitedUserIds.json', json_encode(array_merge($oldInvaitedUserIds, $invatedUserIds)));

        echo '<pre>';
        print_r($results);
        echo '</pre>';
        echo PHP_EOL;





        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
