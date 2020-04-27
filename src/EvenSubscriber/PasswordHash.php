<?php 
namespace App\EvenSubscriber;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordHash implements EventSubscriberInterface
{

    private $passEncode ; 
    
    public function __construct(UserPasswordEncoderInterface $passwordEnconder)
    {
        $this->passEncode = $passwordEnconder;
    }

    public static function getSubscribedEvents()
    {

        return [
            KernelEvents::VIEW => ['hasPassword', EventPriorities::PRE_WRITE] 
        ];


    }

    public function hasPassword(ViewEvent $getResponseForControllerResultEvent)
    {
        $user = $getResponseForControllerResultEvent->getControllerResult();
        $method = $getResponseForControllerResultEvent->getRequest()->getMethod();

        if(!$user instanceof User || Request::METHOD_POST !== $method ){
            return;
        }



        $encode = $this->passEncode->encodePassword($user,$user->getPassword());
        $user->setPassword($encode);
    }





}