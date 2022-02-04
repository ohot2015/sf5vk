<?php

namespace App\Controller;
use App\Service\SpamFilter;
use App\Service\VK;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HookVkController extends AbstractController
{

    /**
     * @Route("/", name="hook_vk")
     */
    public function index(Request $request, VK $vk, SpamFilter $spamFilter)//: Response
    {
      //  return new Response('ok');
        $data = json_decode($request->getContent(), true);

        $vk->setApiVersion(5.131);
        $VK_GROUP_MY = $this->getParameter('myGroups');
        $vk->setAccessToken($this->getParameter('group_access_token'));
        if (empty($data['type'])) {
            dump('no data');
            return new Response('ok');
        }
        switch ($data['type']) {
            case 'confirmation':
                return new Response('d26cdef1');
            case 'message_new':
                $message = $data['object']['message'];
                dump($message['text']);
                if (empty($message['text'])){
                    dump('break');
                    break;
                }

                if ($spamFilter->filterText($message['text'])){
                    dump('filtert spam detected');
                    return new Response('ok');
                }
                dump( $message['text'] .'  '. PHP_EOL . PHP_EOL . 'от пользователя: '. PHP_EOL . '@id'.$message['from_id']);

                $rsPost = $vk->api('wall.post', [
                    'owner_id' => $VK_GROUP_MY,
                    'from_group' => 1,
                    'message'=> $message['text'] .'  '. PHP_EOL . PHP_EOL . 'от пользователя: '. PHP_EOL . '@id'.$message['from_id'],
                    'publish_date' => time() + (60 * 30 ),
                    'access_token' => $vk->getAddedAccessToken(),
                    'count' => 10,
                ], 'array', 'POST');

                dump($rsPost);
                break;
        }

        return new Response('ok');

    }

}
