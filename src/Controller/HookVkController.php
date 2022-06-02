<?php

namespace App\Controller;
use App\Service\SpamFilter;
use App\Service\WallService;
use Google\Cloud\Dialogflow\V2\EntityTypesClient;
use Google\Cloud\Dialogflow\V2\IntentsClient;
use Google\Cloud\Dialogflow\V2\QueryInput;
use Google\Cloud\Dialogflow\V2\SessionsClient;
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
        putenv('GOOGLE_APPLICATION_CREDENTIALS='.$this->getParameter('kernel.project_dir').'/service-account.json');

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

                //$projectId = 'vk-bot-350515';
                //$entityTypeId = $message['from_id'];
                $sessionsClient = new SessionsClient();
                try {
                    $session = $message['from_id'];
                    $queryInput = new QueryInput();
                    $response = $sessionsClient->detectIntent($session, $queryInput);
                    dump($response);
                } finally {
                    $sessionsClient->close();
                }
                $mes = 'test';

                //$wall->postMessageInWall($message['from_id'],$message['text']);
                $wall->himSelfSend('test');

                break;
        }

        return new Response('ok');

    }

}
