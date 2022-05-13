<?php

namespace App\Controller;
use App\Service\SpamFilter;
use App\Service\WallService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HookVkController extends AbstractController
{

    /**
     * @Route("/", name="hook_vk")
     */
    public function index(Request $request, SpamFilter $spamFilter, WallService $wall)//: Response
    {
      //  return new Response('ok');
        $data = json_decode($request->getContent(), true);

        if (empty($data['type'])) {
            dump('no data');
            return new Response('ok');
        }
        switch ($data['type']) {
            case 'confirmation':
                return new Response('d26cdef1');
            case 'message_new':
                $message = $data['object']['message'];
                // 20000... сообщение из беседы
                if (empty($message['text']) || $message['peer_id'] === 2000000001){
                    dump('break');
                    break;
                }
//                if ($spamFilter->filterText($message['text'])){
//                    dump('filtert spam detected');
//                    return new Response('ok');
//                }

                $mes = 'test';
                //,
                //$wall->postMessageInWall($message['from_id'],$message['text']);
                $wall->himSelfSend('test');

                break;
        }

        return new Response('ok');

    }

}
