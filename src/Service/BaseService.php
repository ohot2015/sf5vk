<?php
namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

class BaseService
{
    protected $container;
    protected $vk;
    protected $myGroupId;
    protected $appToken;
    protected $groupToken;
    protected $myPageId ;
    public function __construct(ContainerInterface $container, VK $vk) // <- Add this
    {
        $this->container = $container;
        $vk->setApiVersion(5.131);
        $this->myGroupId = $this->container->getParameter('myGroups');
        $this->groupToken = $this->container->getParameter('group_access_token');
        $vk->setAccessToken($this->groupToken);
        $this->vk = $vk;
        $this->appToken =  $this->container->getParameter('app_token');
        $this->myPageId = $this->container->getParameter('mypage');
    }
    public function himSelfSend($mes) {
        $this->vk->api('messages.send', [
            'message' => $mes,
            'peer_id' => $this->myPageId,
            'access_token' => $this->groupToken,
            'v' => '5.103',
            'random_id' => time().rand(1,10000)
        ], 'array', 'POST');
    }
}



