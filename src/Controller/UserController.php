<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Serializer;


class UserController extends AbstractController {
    /**
     * Fetch a user by id.
     */
    #[Route('api/users/{id}', name: 'get_user', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the specified user.',
        content: new Model(type: User::class)
    )]
    #[OA\Response(
        response: 404,
        description: 'No user found for the specified id.'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'The id of the user to fetch.',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    public function show(ManagerRegistry $doctrine, int $id): Response {
        $user = $doctrine->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse(['No user found for id ' . $id], 404);
        }

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($user, 'json');
        
        return new Response($jsonContent, 200);
    }
    
    /**
     * Show all users.
     */
    #[Route('api/users', name: 'get_all_users', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns all users.',
        content: new Model(type: User::class)
    )]
    public function fetch_all(ManagerRegistry $doctrine): Response {
        $repository = $doctrine->getRepository(User::class);
        $users = $repository->findAll();

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($users, 'json');
        
        return new Response($jsonContent);
    }

    /**
     * Create a new user.
     */
    #[Route('api/users', name: 'create_user', methods: ['POST'])]
    #[OA\Response(
        response: 201,
        description: 'Returns the newly created user.',
        content: new Model(type: User::class)
    )]
    #[OA\Response(
        response: 400,
        description: 'The request body is invalid.'
    )]
    #[OA\RequestBody(
        description: 'The user to create.',
        required: true,
        content: new Model(type: User::class)
    )]
    public function create_user(ManagerRegistry $doctrine): Response {
        $em = $doctrine->getManager();
        
        $user = new User();

        $data = json_decode(file_get_contents("php://input"), true);

        $user->setFirstName($data["firstName"]);
        $user->setLastName($data["lastName"]);
        $user->setEmail($data["email"]);
        $user->setPrintQuota($data["printQuota"]);
        
        $em->persist($user);
        $em->flush();

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($user, 'json');
        
        return new Response($jsonContent, 201);
    }
    
    /**
     * Replace a user of the specified id.
     */
    #[Route('api/users/{id}', name: 'replace_user', methods: ['PUT'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the updated user.',
        content: new Model(type: User::class)
    )]
    #[OA\Response(
        response: 400,
        description: 'The request body is invalid.'
    )]
    #[OA\Response(
        response: 404,
        description: 'No user found for the specified id.'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'The id of the user to update.',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    public function replace_user(ManagerRegistry $doctrine, int $id): Response {
        $em = $doctrine->getManager();
        $user = $em->getRepository(User::class)->find($id);
        
        if(!$user) {
            return new JsonResponse(['No user found for id ' . $id], 404);
        }
        
        $data = json_decode(file_get_contents("php://input"), true);
        
        $user->setFirstName($data["firstName"]);
        $user->setLastName($data["lastName"]);
        $user->setEmail($data["email"]);
        $user->setPrintQuota($data["printQuota"]);
        $user->setIsAdmin($data["admin"]);
        $user->setIsLabAssist($data["labAssist"]);
        
        $em->flush();

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($user, 'json');
        
        return new Response($jsonContent, 200);
        
    }

    /**
     * Modify a user of the specified id.
     */
    #[Route('api/users/{id}', name: 'modify_user', methods: ['PATCH'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the updated user.',
        content: new Model(type: User::class)
    )]
    #[OA\Response(
        response: 400,
        description: 'The request body is invalid.'
    )]
    #[OA\Response(
        response: 404,
        description: 'No user found for the specified id.'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'The id of the user to update.',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\RequestBody(
        description: 'The user to update.',
        required: true,
        content: new Model(type: User::class)
    )]
    public function modify_user(ManagerRegistry $doctrine, int $id): Response {
        $em = $doctrine->getManager();
        $user = $em->getRepository(User::class)->find($id);
        
        if (!$user) {
            return new JsonResponse(['No user found for id ' . $id], 404);
        }
        
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data["firstName"])) $user->setFirstName($data["firstName"]);
        if (isset($data["lastName"])) $user->setLastName($data["lastName"]);
        if (isset($data["email"])) $user->setEmail($data["email"]);
        if (isset($data["printQuota"])) $user->setPrintQuota($data["printQuota"]);
        if (isset($data["admin"])) $user->setIsAdmin($data["admin"]);
        if (isset($data["labAssist"])) $user->setIsLabAssist($data["labAssist"]);
        
        $em->flush();

        $user = $em->getRepository(User::class)->find($id);

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($user, 'json');
        
        return new Response($jsonContent, 200);
    }

    /**
     * Deletes the user with the specified id.
     */
    #[Route('api/users/{id}', name: 'delete_user', methods: ['DELETE'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the deleted user.',
        content: new Model(type: User::class)
    )]
    #[OA\Response(
        response: 404,
        description: 'No user found for the specified id.'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'The id of the user to delete.',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    public function delete_user(ManagerRegistry $doctrine, int $id): Response {
        $em = $doctrine->getManager();
        $user = $em->getRepository(User::class)->find($id);
        
        if (!$user) {
            return new JsonResponse(['No user found for id ' . $id], 404);
        }

        // Delete all projects and transactions associated with the user
        $query = $em->createQuery(
            'DELETE FROM App\Entity\Project p WHERE p.user_id = :user'
        )->setParameter('user', $user);
        $query->execute();

        $query = $em->createQuery(
            'DELETE FROM App\Entity\Transaction t WHERE t.account_id = :user'
        )->setParameter('user', $user);
        $query->execute();
        
        $em->remove($user);
        $em->flush();

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($user, 'json');
        
        return new Response($jsonContent, 200);
    }
}
