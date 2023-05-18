<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Psr\Log\LoggerInterface;

class ProjectController extends AbstractController
{   
    #[Route('/users/{uid}/projects', name: 'new_project', methods: ['POST'])]
    public function new_project(ManagerRegistry $doctrine, UploaderHelper $helper, int $uid): Response {
        $em = $doctrine->getManager();
        $user = $em->getRepository(User::class)->find($uid);
        if (!$user) {
            return new JsonResponse('', 404);
        }
        
        $proj = new Project();
        $proj->setUserId($uid);
        $proj->setName($_POST['name']);
        $proj->setDateSubmitted(new \DateTimeImmutable());
        $proj->setDateUpdated(new \DateTimeImmutable());
        
        $stl_tmp = $_FILES['stl']['tmp_name'];
        $stl_name = $_FILES['stl']['name'];
        $gcode_tmp = $_FILES['gcode']['tmp_name'];
        $gcode_name = $_FILES['gcode']['name'];
        
        $stl_file = new UploadedFile($stl_tmp, $stl_name);
        $gcode_file = new UploadedFile($gcode_tmp, $gcode_name);
        
        $proj->setStlFile($stl_file);
        $proj->setGcodeFile($gcode_file);
        
        $proj->setStlUri($helper->asset($proj,'stl_file'));
        $proj->setGcodeUri($helper->asset($proj, 'gcode_file'));
        
        $em->persist($proj);
        $em->flush();
        
        return new JsonResponse($proj);
    }
    
    #[Route('/users/{uid}/projects', name: 'user_proj_all', methods: ['GET'])]
    public function get_all_user_projects(ManagerRegistry $doctrine, int $uid): Response {
        $em = $doctrine->getManager();
        $user = $em->getRepository(User::class)->find($uid);
        if (!$user) {
            return new JsonResponse('', 404);
        }
        $query = $em->createQuery(
            'SELECT p 
            FROM App\Entity\Project p
            WHERE p.user_id = :uid'
        )->setParameter('uid', $uid);
        
        return new JsonResponse($query->getResult());
    }
    
    #[Route('/users/{uid}/projects/{id}', name: 'get_proj', methods: ['GET'])]
    public function get_proj(ManagerRegistry $doctrine, int $uid, int $id): Response {
        $em = $doctrine->getManager();
        
        $proj = $em->getRepository(Project::class)->find($id);
        $user = $em->getRepository(User::class)->find($uid);
        
        # Require match of project id and correct user.
        if (!$proj or !$user or $proj->getUserId() != $uid) {
            return new JsonResponse('', 404);
        }
        
        return new JsonResponse($proj, 200);
    }
    
    #[Route('/users/{uid}/projects/{id}', name: 'replace_proj', methods: ['PUT'])]
    public function replace_proj(ManagerRegistry $doctrine, int $uid, int $id): Response {
        $em = $doctrine->getManager();
        $proj = $em->getRepository(Project::class)->find($id);

        $user = $em->getRepository(User::class)->find($uid);
        
        if (!$proj or !$user or $proj->getUserId() != $uid) {
            return new JsonResponse([''], 404);
        }
        
        $data = json_decode(file_get_contents("php://input"), true);
        
        $proj->setName($data['name']);
        $proj->setDateModified(new \DateTimeImmutable());
        
        $em->flush();
        
        return new JsonResponse($proj, 200);
    }
    
    #[Route('/users/{uid}/projects/{id}', name: 'update_proj', methods: ['PATCH'])] 
    public function update_proj(ManagerRegistry $doctrine, int $uid, int $id): Response {
        $em = $doctrine->getManager();
        $proj = $em->getRepository(Project::class)->find($id);

        $user = $em->getRepository(User::class)->find($uid);
        
        if (!$proj or !$user or $proj->getUserId() != $uid) {
            return new JsonResponse('', 404);
        }
        
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (isset($data['name'])) $proj->setName($data['name']);
        $proj->setDateModified(new \DateTimeImmutable());
        
        $em->flush();
        
        return new JsonResponse($proj, 200);
    }
    
    #[Route('/users/{uid}/projects/{id}', name: 'delete_user', methods: ['DELETE'])]
    public function delete_user(ManagerRegistry $doctrine, int $uid, int $id): Response {
        $em = $doctrine->getManager();
        $proj = $em->getRepository(Project::class)->find($id);

        $user = $em->getRepository(User::class)->find($uid);
        
        if (!$proj or !$user or $proj->getUserId() != $uid) {
            return new JsonResponse([''], 404);
        }
        
        $em->remove($proj);
        $em->flush();
        
        return new JsonResponse($proj, 200);
    }
}
