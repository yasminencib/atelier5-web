<?php

namespace App\Controller;

use App\Form\AuthorType;
use App\Entity\Author11;

use App\Repository\Author11Repository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;


class AuthorController extends AbstractController
{ 
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    #[Route('/Affiche', name: 'app_Affiche')]


    public function Affiche (Author11Repository $repository)
        {
            $Author11=$repository->findAll() ; 
            return $this->render('author/Affiche.html.twig',['Author11'=>$Author11]);
        }

    #[Route('/Add', name: 'app_Add')]

public function  Add (Request  $request , ManagerRegistry $Doctrine)
{
    $author=new Author11();
    $form =$this->CreateForm(AuthorType::class,$author);
  $form->add('Ajouter',SubmitType::class);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid())
    {
        $em=$Doctrine->getManager();
        $em->persist($author);
        $em->flush();
        return $this->redirectToRoute('app_Affiche');
    }
    return $this->render('author/Add.html.twig',['f'=>$form->createView()]);

}
    #[Route('/edit/{id}', name: 'app_edit')]
    public function edit(Author11Repository $repository, $id, Request $request , ManagerRegistry $Doctrine)
    {
        $author = $repository->find($id);
        $form = $this->createForm(AuthorType::class, $author);
        $form->add('Edit', SubmitType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $Doctrine->getManager();
            $em->flush(); // Correction : Utilisez la méthode flush() sur l'EntityManager pour enregistrer les modifications en base de données.
            return $this->redirectToRoute("app_Affiche");
        }

        return $this->render('author/edit.html.twig', [
            'f' => $form->createView(),
        ]);}
    
    #[Route('/delete/{id}', name: 'app_delete')]
    public function delete($id, Author11Repository $repository , ManagerRegistry $Doctrine)
    {
        $author = $repository->find($id);

        if (!$author) {
            throw $this->createNotFoundException('Auteur non trouvé');
        }

        $em = $Doctrine->getManager();
        $em->remove($author);
        $em->flush();

        return $this->redirectToRoute('app_Affiche');
    }
    #[Route('/AddStatistique', name: 'app_AddStatistique')]

    public function addStatistique(EntityManagerInterface $entityManager): Response
    {
        // Créez une instance de l'entité Author
        $author1 = new Author11();
        $author1->setUsername("test"); // Utilisez "setUsername" pour définir le nom d'utilisateur
        $author1->setEmail("test@gmail.com"); // Utilisez "setEmail" pour définir l'email

        // Enregistrez l'entité dans la base de données
        $entityManager->persist($author1);
        $entityManager->flush();

        return $this->redirectToRoute('app_Affiche'); // Redirigez vers la route 'app_Affiche'
    }

    #[Route('/authors-by-email', name: 'app_authorsByEmail')]
    public function listAuthorsByEmail(Author11Repository $authorRepository): Response
    {
        $authors = $authorRepository->listAuthorByEmail();

        return $this->render('author/list_by_email.html.twig', [
            'authors' => $authors,
        ]);
    }
    #[Route('/searchAuthors', name: 'app_search_authors')]
    public function searchAuthors(Request $request, Author11Repository $authorRepository)
    {
        $form = $this->createForm(CustomAuthorSearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $minBooks = $form->get('minBooks')->getData();
            $maxBooks = $form->get('maxBooks')->getData();

            $authors = $authorRepository->findAuthorsByBookCountRange($minBooks, $maxBooks);

            return $this->render('author/search_results.html.twig', [
                'authors' => $authors,
            ]);
        }

        return $this->render('author/search.html.twig', [
            'searchForm' => $form->createView(),
        ]);
    }
    #[Route('/deleteAuthorsWithZeroBooks', name:'app_delete_authors_with_zero_books')]
  
   public function deleteAuthorsWithZeroBooks(Author11Repository $authorRepository)
   {
       $authorRepository->deleteAuthorsWithZeroBooks();
   
       return $this->redirectToRoute('app_Affiche'); // Redirigez vers la page souhaitée après la suppression.
   }
}
