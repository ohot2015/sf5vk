<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HookVkController extends AbstractController
{
    /**
     * @Route("/hook/vk", name="hook_vk")
     */
    public function index(Request $request): Response
    {


        $rqVkCode = 1;

        // приветствие
        switch ($rqVkCode) {
            case 1: {

                // проверить есть ли анкета
                    // да
                    // спросить не хочет ли обновить данные
                        // да
                        //  перейти к обновлению
                        // нет
                        // подобрать и показать
                            // понравилось не понравилось
                                // да
                //              // показать подробнее
                switch ($rqVkCode) {
                    case 1: {
                    }
                    case 2: {
                    }
                    case 3: {
                    }
                }
            }
            case 2: {
                //нет не беспокойить день
            }
            case 3: {
                // не беспокоить месяя
            }
        }
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/HookVkController.php',
        ]);
    }
}
