<?php
namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

class WallService extends BaseService
{
    public function postMessageInWall($from_id, $message)  {

        $VK_GROUP_MY = $this->container->getParameter('myGroups');

        $user = $this->vk->api('users.get', [
            'user_ids' => $from_id,
            'access_token' => $this->appToken,
        ], 'array', 'POST');


        $user = $user['response'][0];
        $this->vk->api('messages.send', [
            'message' => $user['first_name'] . " Я размещу твое сообщение на стене группы после того как оно пройдет модерацию. Обычно модерация не занимает более получаса.",
            'peer_id' => $from_id,
            'access_token' => $this->groupToken,
            'v' => '5.103',
            'random_id' => time().rand(1,10000)
        ], 'array', 'POST');

        $this->vk->api('wall.post', [
            'owner_id' => $this->myGroupId,
            'from_group' => 1,
            'message'=> sprintf('%s %s%sот пользователя: %s[id%s|%s %s]',
                $message,
                PHP_EOL,
                PHP_EOL,
                PHP_EOL,
                $from_id,
                $user['first_name'],
                $user['last_name']
            ),
            'publish_date' => time() + (60 * rand(3, 30)),
            'access_token' => $this->appToken,
            'count' => 10,
        ], 'array', 'POST');
    }

}



