<?php

namespace App\Service;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JwtCreatedSubscriber
{
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        // 1. Récupérer l'utilisateur (pour avoir son firstName et lastName)
        $user = $event->getUser();

        // 2. Enrichir les data pour qu'elles contiennent ces données
        $data = $event->getData();
        $data['id'] = $user->getId();
        $data['email'] = $user->getEmail();
        $data['phone'] = $user->getPhone();
        $data['firstName'] = $user->getFirstName();
        $data['lastName'] = $user->getLastName();
        $data['archived'] = $user->isArchived();
        $data['qrImageUrl'] = $user->getQrImageUrl();
        $data['imageUser'] = $user->getImageUser();
        $data['qrImageName'] = $user->getQrImageName();
        $data['roles'] = implode(",", $user->getRoles());

        $event->setData($data);
    }
}
