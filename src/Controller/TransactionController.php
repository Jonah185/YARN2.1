<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Transaction;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use DateTimeImmutable;

class TransactionController extends AbstractController
{
    #[Route('/transactions', name: 'get_all_transactions', methods: ['GET'])]
    public function fetch_all(ManagerRegistry $doctrine): Response {
        $repository = $doctrine->getRepository(Transaction::class);
        $transactions = $repository->findAll();

        return new JsonResponse($transactions);
    }

    #[Route('/transactions', name: 'create_transaction', methods: ['POST'])]
    public function create_transaction(ManagerRegistry $doctrine): Response {
        $em = $doctrine->getManager();
        $user = $em->getRepository(User::class)->find($_POST["account_id"]);
        if(!$user) {
            return new JsonResponse('' ,400);
        }

        $transaction = new Transaction();
        /** @noinspection DuplicatedCode */
        $transaction->setType($_POST["type"]);
        $transaction->setAccountId($_POST["account_id"]);
        $transaction->setAuthorizedAdmin($_POST["authorized_admin"]);
        $transaction->setCreditsIssued($_POST["credits_issued"]);
        $transaction->setDebits($_POST["debits"]);
        $transaction->setAssociatedProject($_POST["project"]);
        $transaction->setComments($_POST["comments"]);
        $transaction->setTimestamp(new DateTimeImmutable());

        $user->setPrintQuota($user->getPrintQuota() + $_POST["credits_issued"] - $_POST["debits"]);

        $em->persist($transaction);
        $em->flush();

        return new JsonResponse($transaction, 201);
    }

    #[Route('/transactions/{id}', name: 'get_transaction', methods: ['GET'])]
    public function show(ManagerRegistry $doctrine, int $id): Response {
        $transaction = $doctrine->getRepository(Transaction::class)->find($id);

        if (!$transaction) {
            return new JsonResponse(['No transaction found for id ' . $id], 404);
        }

        return new JsonResponse($transaction);
    }

    #[Route('/transactions/{id}', name: 'replace_transaction', methods: ['PUT'])]
    public function replace_transaction(ManagerRegistry $doctrine, int $id): Response {
        $em = $doctrine->getManager();
        $transaction = $em->getRepository(Transaction::class)->find($id);
        if (!$transaction) {
            return new JsonResponse('' ,400);
        }
        $user = $em->getRepository(User::class)->find($transaction->getAccountId());

        if (!$user) {
            return new JsonResponse('' ,400);
        }

        $old_diff = $transaction->getCreditsIssued() - $transaction->getDebits();


        $data = json_decode(file_get_contents('php://input'), true);

        /** @noinspection DuplicatedCode */
        $transaction->setType($data["type"]);
        $transaction->setAccountId($data["account_id"]);
        $transaction->setAuthorizedAdmin($data["authorized_admin"]);
        $transaction->setCreditsIssued($data["credits_issued"]);
        $transaction->setDebits($data["debits"]);
        $transaction->setAssociatedProject($data["project"]);
        $transaction->setComments($data["comments"]);
        $transaction->setDate(new DateTimeImmutable());

        $new_diff = $transaction->getCreditsIssued() - $transaction->getDebits();

        // Update the user's print quota to reflect the new transaction.
        $user->setPrintQuota($user->getPrintQuota() + $new_diff - $old_diff);


        $em->flush();

        return new JsonResponse($transaction);
    }

    #[Route('/transactions/{id}', name: 'update_transaction', methods: ['PATCH'])]
    public function update_transaction(ManagerRegistry $doctrine, int $id): Response {
        $em = $doctrine->getManager();
        $transaction = $em->getRepository(Transaction::class)->find($id);

        if(!$transaction) {
            return new JsonResponse(['No transaction found for id ' . $id], 404);
        }

        $user = $em->getRepository(User::class)->find($transaction->getAccountId());
        $old_diff = $transaction->getCreditsIssued() - $transaction->getDebits();

        $data = json_decode(file_get_contents('php://input'), true);

        if(isset($data["type"])) {
            $transaction->setType($data["type"]);
        }
        if(isset($data["account_id"])) {
            $transaction->setAccountId($data["account_id"]);
        }
        if(isset($data["authorized_admin"])) {
            $transaction->setAuthorizedAdmin($data["authorized_admin"]);
        }
        if(isset($data["credits_issued"])) {
            $transaction->setCreditsIssued($data["credits_issued"]);
        }
        if(isset($data["debits"])) {
            $transaction->setDebits($data["debits"]);
        }
        if(isset($data["project"])) {
            $transaction->setAssociatedProject($data["project"]);
        }
        if(isset($data["comments"])) {
            $transaction->setComments($data["comments"]);
        }

        $transaction->setDate(new DateTimeImmutable());

        $new_diff = $transaction->getCreditsIssued() - $transaction->getDebits();

        // Update the user's print quota to reflect the new transaction.
        $user->setPrintQuota($user->getPrintQuota() + $new_diff - $old_diff);

        $em->flush();

        return new JsonResponse($transaction, 200);
    }

    #[Route('/transactions/{id}', name: 'delete_transaction', methods: ['DELETE'])]
    public function delete_transaction(ManagerRegistry $doctrine, int $id): Response {
        $em = $doctrine->getManager();
        $transaction = $em->getRepository(Transaction::class)->find($id);
        if (!$transaction) {
            return new JsonResponse('' ,400);
        }
        $diff = $transaction->getCreditsIssued() - $transaction->getDebits();
        $user = $em->getRepository(User::class)->find($transaction->getAccountId());

        //Restore the user's print quota.
        $user->setPrintQuota($user->getPrintQuota() - $diff);

        $em->remove($transaction);
        $em->flush();

        return new JsonResponse($transaction, 200);
    }
}
