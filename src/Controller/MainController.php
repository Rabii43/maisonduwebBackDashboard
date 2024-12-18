<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class MainController extends AbstractController
{
    /**
     * @var EntityManagerInterface $em
     */
    public $em;
    /**
     * @var UserRepository $userRepository
     */
    public UserRepository $userRepository;
    public FileUploader $fileUploader;

    public function __construct(
        EntityManagerInterface $em,
        UserRepository         $userRepository,
        FileUploader           $fileUploader
    )
    {
        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->fileUploader = $fileUploader;
    }

    /**
     * Success Response
     * @param $object
     * @return Response
     */
    public function successResponse($object, $staus = null)
    {
        $serializer = SerializerBuilder::create()->build();
        $data = $serializer->serialize($object, 'json');
        if ($staus) {
            $response = new Response($data, $staus);
        } else {
            $response = new Response($data);
        }
        $response->headers->set('Content-type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    /**
     * Function to create random password
     * @param int $numberOfChars
     * @return string
     */
    public function randomPassword(int $numberOfChars)
    {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache

        for ($i = 0; $i < $numberOfChars; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }

        return implode($pass); // turn the array into a string
    }

    /**
     * Function to decode request content
     */
    public function jsonDecode($request)
    {
        return json_decode($request->getContent(), true);
    }

    public function insert(Request $request, $entityType, $param, $data)
    {
        $form = $this->createForm($entityType, $param);
        $form->handleRequest($request);
        $form->submit($data);
    }

    public function update(Request $request, $entityType, $param, $data)
    {
        $form = $this->createForm($entityType, $param);
        $form->handleRequest($request);
        $form->submit($data, false);
    }
}
