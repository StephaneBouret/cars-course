<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentFormType;
use App\Service\SendMailService;
use App\Repository\CommentRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(CommentRepository $commentRepository, ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy([], [], 3);

        $comments = $commentRepository->findBy(['isValid' => true], ['createdAt' => 'DESC']);

        $averageRating = $commentRepository->averageRating();

        return $this->render('home/index.html.twig', [
            'comments' => $comments,
            'averageRating' => $averageRating,
            'products' => $products
        ]);
    }

    #[Route('/donner-avis', name: 'home_notice')]
    public function notice(Request $request, EntityManagerInterface $em, SendMailService $mail): Response
    {
        $comment = new Comment();

        $form = $this->createForm(CommentFormType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setFullname(ucwords($form->get('fullname')->getData()));
            $em->persist($comment);
            $em->flush();

            $mail->sendMail(
                'no-reply@monsite.net',
                'Demande de contact',
                'contact@monsite.net',
                'Nouveau commentaire sur le site',
                'comment',
                []
            );

            $this->addFlash('success', 'Votre avis a bien été envoyé, il sera publié après validation !');
            return $this->redirectToRoute('homepage');
        }

        return $this->render('home/notice.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
