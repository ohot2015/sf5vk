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

class ClearTrashMyProfileCommand extends Command
{
    protected static $defaultName = 'clearTrashMyProfile';
    protected static $defaultDescription = 'Remove dogs in profile';
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

        $mypage =  $container->getParameter('mypage');

        $rsGetUsers = $vk->api('friends.get', [
            'user_id' => $mypage,
            'access_token' => $vk->getAddedAccessToken(),
            'count'=> 1000,
        ], 'array', 'POST');

        $ids = $rsGetUsers['response']['items'];

        $rs = $vk->api('users.get', [
            'user_ids' => substr(implode(',',$ids),0,-1),
            'access_token' => $vk->getAddedAccessToken(),
            'count'=> 1000,
            'fields' => 'blacklisted_by_me,bdate',
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
            $vk->api('friends.delete', [
                'user_id' => $user,
                'access_token' => $vk->getAddedAccessToken(),
                'v' => '5.131'
            ], 'array', 'POST');
            sleep(rand(3,5));
        }
        // deleted dogs
//        686695587, 686489478, 673392290, 605295598, 550158505,
//      513663965, 532188721, 325148453, 575013514, 557440074, 376515566,
//      633141865, 353212130, 530603044, 561418870, 422690138, 353122902,
//      650056459, 619266365, 541959615, 565149230, 692685245, 614177154,
//      597342836, 655147257, 574898533, 406895824, 581184845, 676038257,
//      624949928, 547722415, 680743802, 646971556, 574872354, 701166320,
//      535971744, 427074276, 378743457, 684871892, 539878051, 566622016,
//      288327824, 516204797, 643889618, 346304640, 521921215, 472704622,
//      556081037, 605145043, 699530274, 620391574, 721689401, 565241800,
//      608580270, 563174934, 697121544, 574856635, 658655644, 668788130,
//      657435001, 526211342, 271949890, 365834695, 11461372, 586805290,
//      533986790, 488604088, 673579922, 444096381, 618618383, 444507438,
//      556902988, 148062277, 602872988, 520101921, 455661022, 679805682,
//      655195818, 680704272, 556940822, 714189443, 420392382, 670847470,
//      582753397, 343609729, 523219997, 664165711, 578152830, 517746364,
//      387755590, 639982306, 392613512, 659156114, 507257249, 373539759


        $io->success('delete users '. implode(', ',$prepare_delete_users_ids));

        return Command::SUCCESS;
    }
}
