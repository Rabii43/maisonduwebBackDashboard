<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;


#[Route('api/user', name: 'app_user')]
class UserController extends MainController
{
    use QrCode\QrCodeGeneratorController;

    //get all users
    #[Route('/', name: 'app_getUser', methods: ['GET'])]
    public function index(): Response
    {
        $users = $this->em->getRepository(User::class)->findAll();
        return $this->successResponse($users);
    }

    //register

    /**
     * @throws \Exception
     */
    #[Route('/add', name: 'app_register', methods: ['POST'])]
    public function AddUser(Request $request, UserPasswordHasherInterface $encoder)
    {
        $data = $request->request->all();
        $data['roles'] = ['ROLE_USER'];
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if ($user !== null) {
            return $this->successResponse(['code' => 409, 'message' => 'Cet utilisateur existe déjà !'], 409);
        } else {
            $user = new User();
            $password = $encoder->hashPassword($user, $data['password']);
            $data['password'] = $password;
            $user->setRoles($data['roles']);
            if ($request->files->get('image')) {
                $image = $this->fileUploader->upload($request, 'image')['originalName'];
                $user->setImageUser($image);
                $this->insert($request, UserType::class, $user, $data);
                $this->em->persist($user);
                $dataImage = $this->generateQrCode($user->getId());
                if ($dataImage) {
                    $user->setQrImageUrl($dataImage['path']);
                    $user->setQrImageName($dataImage['imageName']);
                }
            }
        }
        $this->em->persist($user);
        $this->em->flush();
        return $this->successResponse(['code' => 201, 'message' => 'Utilisateur créé avec succès!'], 201);
    }

    //get user by id
    #[Route('/{id}', name: 'app_getUserById', methods: ['GET'])]
    public function show($id): Response
    {
        $user = $this->em->getRepository(User::class)->find($id);
        if ($user === null) {
            return $this->successResponse(['code' => 404, 'message' => 'Utilisateur non trouvé !'], 404);
        }
        return $this->successResponse($user);
    }

    //update user
    #[Route('/{id}', name: 'app_updateUser', methods: ['PUT'])]
    public function updateUser(Request $request, $id, UserPasswordHasherInterface $encoder)
    {
        $data = $this->jsonDecode($request);
        $user = $this->em->getRepository(User::class)->find($id);
        if ($user === null) {
            return $this->successResponse(['code' => 404, 'message' => 'Utilisateur non trouvé !'], 404);
        }
        $this->update($request, UserType::class, $user, $data);
        if (isset($data['password'])) {
            $password = $encoder->hashPassword($user, $data['password']);
            $user->setPassword($password);
        }
        $this->em->flush();
        return $this->successResponse(['code' => 200, 'message' => 'Utilisateur modifié avec succès !'], 200);
    }

    //delete user
    #[Route('/{id}', name: 'app_deleteUser', methods: ['DELETE'])]
    public function deleteUser($id)
    {
        $user = $this->em->getRepository(User::class)->find($id);
        if ($user === null) {
            return $this->successResponse(['code' => 404, 'message' => 'Utilisateur non trouvé !'], 404);
        }
        $this->em->remove($user);
        $this->em->flush();
        return $this->successResponse(['code' => 200, 'message' => 'Utilisateur supprimé avec succès !'], 200);
    }

    //archive user
    #[Route('/archive/{id}', name: 'app_archiveUser', methods: ['PUT'])]
    public function archiveUser($id)
    {
        $user = $this->em->getRepository(User::class)->find($id);
        if ($user === null) {
            return $this->successResponse(['code' => 404, 'message' => 'Utilisateur non trouvé !'], 404);
        }
        $user->setArchived(true);
        $this->em->flush();
        return $this->successResponse(['code' => 200, 'message' => 'Utilisateur archivé avec succès !'], 200);
    }
}
