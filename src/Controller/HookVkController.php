<?php

namespace App\Controller;

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
    public function index(Request $request, VK $vk)//: Response
    {
        return new Response('ok');
        $vk->setApiVersion(5.131);
        $VK_GROUP_MY = $this->getParameter('myGroups');
        $vk->setAccessToken($this->getParameter('group_access_token'));

        $data = json_decode($request->getContent(),true);

        if ($data['type'] ==='message_new') {
            $user = $data['object']['message']['from_id'];
            $text = $data['object']['message']['text'];
            $rsPost = $vk->api('messages.send', [
                'user_id' => $user,
                //'peer_id' =>$user,
                'message'=> $text,
                'access_token' => $vk->getAddedAccessToken(),
//                'keyboard' => '{
//  "one_time": true,
//  "buttons": [
//    [
//      {
//        "action": {
//          "type": "location",
//          "payload": "{\"button\": \"1\"}"
//        }
//      }
//    ],
//    [
//      {
//        "action": {
//          "type": "open_app",
//          "app_id": 6232540,
//          "owner_id": -157525928,
//          "hash": "123",
//          "label": "LiveWidget"
//        }
//      }
//    ],
//    [
//      {
//        "action": {
//          "type": "vkpay",
//          "hash": "action=transfer-to-group&group_id=181108510&aid=10"
//        }
//      }
//    ],
//    [
//      {
//        "action": {
//          "type": "text",
//          "payload": "{\"button\": \"1\"}",
//          "label": "Red"
//        },
//        "color": "negative"
//      },
//      {
//        "action": {
//          "type": "text",
//          "payload": "{\"button\": \"2\"}",
//          "label": "Green"
//        },
//        "color": "positive"
//      },
//      {
//        "action": {
//          "type": "text",
//          "payload": "{\"button\": \"2\"}",
//          "label": "Blue"
//        },
//        "color": "primary"
//      },
//      {
//        "action": {
//          "type": "text",
//          "payload": "{\"button\": \"2\"}",
//          "label": "White"
//        },
//        "color": "secondary"
//      }
//    ]
//  ]
//}',
                'random_id' => rand(0,99999)
            ], 'array', 'POST');

            dump($rsPost,$data);
        }

        return new Response('ok');
        /*return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/HookVkController.php',
        ]);*/
    }
}
