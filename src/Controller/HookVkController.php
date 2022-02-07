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
        dump($data);
        switch ($data['type']) {
            case 'confirmation':
                return new Response('d26cdef1');
            case 'message_new':
                $message = $data['object']['message'];
                if (empty($message['text']) || $message['peer_id'] === 2000000001){
                    dump('break');
                    break;
                }

                if ($spamFilter->filterText($message['text'])){
                    dump('filtert spam detected');
                    return new Response('ok');
                }
                $user = $vk->api('users.get', [
                    'user_ids' => $message['from_id'],
                    'access_token' => $this->getParameter('app_token'),
                ], 'array', 'POST');


                $user = $user['response'][0];
                $vk->api('messages.send', [
                    'message' => $user['first_name'] . " Я размещу твое сообщение на стене группы после того как оно пройдет модерацию. Обычно модерация не занимает более получаса.",
                    'peer_id' => $message['from_id'],
                    'access_token' => $this->getParameter('group_access_token'),
                    'v' => '5.103',
                    'random_id' => time()
                ], 'array', 'POST');

                $vk->api('wall.post', [
                    'owner_id' => $VK_GROUP_MY,
                    'from_group' => 1,
                    'message'=> sprintf('%s %s%sот пользователя: %s[id%s|%s %s]',
                        $message['text'],
                        PHP_EOL,
                        PHP_EOL,
                        PHP_EOL,
                        $message['from_id'],
                        $user['first_name'],
                        $user['last_name']
                    ),
                    'publish_date' => time() + (60 * rand(3, 30)),
                    'access_token' => $this->getParameter('app_token'),
                    'count' => 10,
                ], 'array', 'POST');

                break;
        }

        return new Response('ok');

    }

}
