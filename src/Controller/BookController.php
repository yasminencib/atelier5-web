<?php

namespace App\Controller;
use App\Entity\Author;
use App\Entity\Author11;
use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use PHPUnit\Framework\Constraint\Count;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
class BookController extends AbstractController
{
    #[Route('/deletebook/{id}', name:'app_deleteBook')]
    public function delete($id ,ManagerRegistry $Doctrine) : Response
    {  
        $delete = $Doctrine->getManager();
        if(!$id)
        {
            throw $this->createNotFoundException('No ID found');
        }
        $book = $delete->getRepository(Book::class)->find($id);
        if($book != null)
        {
            if ($book->getCategory() === 'Mystery') {
                $delete->remove($book);
                $delete->flush();
            } else {
                
               // return $this->redirectToRoute('app_AfficheBook', ['error' => 'Cannot delete non-ministry books.']);
               return $this->render('category is not mystery');
            }
        }
      
        return $this->redirectToRoute('app_AfficheBook');
    }
    #[Route('/editbook/{id}', name: 'app_editbook')]
    public function edit(BookRepository $repository, $id, Request $request , ManagerRegistry $Doctrine)
    {
        $book = $repository->find($id);
        $form = $this->createForm(BookType::class, $book);
        $form->add('published');
        $form->add('Edit', SubmitType::class);
         $form->handleRequest($request);
       
        
        if ($form->isSubmitted() && $form->isValid()) {
            if ($book->getCategory() === 'Mystery') {
            $em = $Doctrine->getManager();
            $em->flush(); // Correction : Utilisez la méthode flush() sur l'EntityManager pour enregistrer les modifications en base de données.
            return $this->redirectToRoute("app_AfficheBook");}
            else {return $this->render('category is not mystery');}
        }

        return $this->render('Book/edit.html.twig', [
            'f' => $form->createView(),
        ]);}

    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }
    #[Route('/AfficheBook', name: 'app_AfficheBook')]
    public function Affiche(BookRepository $repository , ManagerRegistry $Doctrine)
    {
        //récupérer les livres publiés
        $publishedBooks = $Doctrine->getRepository(Book::class)->findBy(['published' => true]);
        //compter le nombre de livres pubbliés et non publiés
        $numPublishedBooks = count($publishedBooks);
        
        $UnPublishedBook =$Doctrine->getRepository(Book::class)->findBy(['published' => false]);
        $numUnPublishedBooks = count($UnPublishedBook);

        if ($publishedBooks <0) {
            return $this->render('book/Affichee.html/.twig', ['publishedBooks' => $publishedBooks, 'numPublishedBooks' => $numPublishedBooks, 'numUnPublishedBooks' => $numUnPublishedBooks]);

        } else {
            //afficher un message si aucun livre n'a été trouvé$
          //  return $this->render('book/no_books_found.html.twig');
          return $this->render('book/Affichee.html.twig', ['publishedBooks' => $publishedBooks, 'numPublishedBooks' => $numPublishedBooks, 'numUnPublishedBooks' => $numUnPublishedBooks]);
        }


    }
    #[Route('/AddBook', name: 'app_AddBook')]
    public function Add(Request $request , ManagerRegistry $Doctrine)
    {
        $book = new Book();
        $form = $this->CreateForm(BookType::class, $book);
        $form->add('Ajouter', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
           
            $author = $book->getAuthor();
            

            if ($author instanceof Author11) {
                $author->setNbBooks($author->getNbBooks() + 1);
            }
            $em = $Doctrine->getManager();
            $em->persist($book);
            $em->flush();
            return $this->redirectToRoute('app_AfficheBook');
        }
        return $this->render('book/Add.html.twig', ['f' => $form->createView()]);

    }
    
  
    #[Route('/books-title/{ti}', name: 'app_booksByTitle')]
    public function booksByTitle(Request $request, BookRepository $bookRepository, string $ti)
    {
        // Rechercher les livres par titre
        $books = $bookRepository->findBy(['title' => $ti]);
    
        return $this->render('book/title.html.twig', [
            'title' => $ti,  // Passer le titre à la vue
            'books' => $books,
        ]);
    }
    ///////////////atelier5////////////////////////
    #[Route('/searchBookByRef', name: 'app_searchBookByRef')]
    public function searchBookByRef(Request $request, BookRepository $bookRepository): Response
    {
        $searchTerm = $request->query->get('search');
        $books = [];

        if ($searchTerm) {
            // Si un terme de recherche est soumis, recherchez les livres par "ref"
            $books = $bookRepository->searchBookByRef($searchTerm);
        }

        return $this->render('book/searchBookByRef.html.twig', [
            'books' => $books,
            'searchTerm' => $searchTerm,
        ]);
    }
    #[Route('/books-by-authors', name: 'app_booksByAuthors')]
    public function booksByAuthors(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->booksListByAuthors();

        return $this->render('book/books_by_authors.html.twig', [
            'books' => $books,
        ]);
    }
    #[Route('/books-published-before-2023', name: 'app_booksPublishedBefore2023')]
    public function booksPublishedBefore2023(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findBooksPublishedBefore2023WithAuthorMoreThan10Books();

        return $this->render('book/books_published_before_2023.html.twig', [
            'books' => $books,
        ]);
    }
    #[Route('/update-scifi-to-romance', name: 'app_updateScifiToRomance')]
    public function updateScifiToRomance(BookRepository $bookRepository, EntityManagerInterface $entityManager): Response
    {
        $bookRepository->updateCategoryToRomance();

        $entityManager->flush();

        return $this->redirectToRoute('app_AfficheBook');
    }
    #[Route('/count-romance-books', name: 'app_countRomanceBooks')]
    public function countRomanceBooks(BookRepository $bookRepository): Response
    {
        $count = $bookRepository->countBooksInCategory('Romance');

        return $this->render('book/count_romance_books.html.twig', [
            'count' => $count,
        ]);
    }
    #[Route('/books-published-between-dates', name: 'app_booksPublishedBetweenDates')]
    public function booksPublishedBetweenDates(BookRepository $bookRepository): Response
    {
        $startDate = new \DateTime('2014-01-01');
        $endDate = new \DateTime('2018-12-31');

        $books = $bookRepository->findBooksPublishedBetweenDates($startDate, $endDate);

        return $this->render('book/books_published_between_dates.html.twig', [
            'books' => $books,
        ]);
    }
  

    }
