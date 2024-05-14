<?php
namespace App\Controller;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
Use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use App\Entity\Article;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\ArticleType;
class IndexController extends AbstractController
{
   #[Route('/',name:'article_list')]
   public function home(EntityManagerInterface  $entityManager): Response
   {
       $articles = $entityManager->getRepository(Article::class)->findAll();
       return $this->render('article/index.html.twig',['article'=> $articles]);
   }

   #[Route('/new', name: 'new_article', methods:['GET','POST'])]
    public function new(PersistenceManagerRegistry $managerRegistry,Request $request)  {
      $article = new Article();
      $form = $this->createForm(ArticleType::class,$article);
      $form->handleRequest($request);
      if($form->isSubmitted() && $form->isValid()) 
      { 
        $article = $form->getData();
        $entityManager =$managerRegistry->getManager();
        $entityManager->persist($article);
        $entityManager->flush();
        return $this->redirectToRoute('article_list');
    }
    return $this->render('article/new.html.twig',['form' => $form->createView()]);
  }
     
 

     #[Route('/save', name: 'save-article')]
 public function save(PersistenceManagerRegistry $doctrine){
    $entityManager = $doctrine->getManager();
    $article = new Article();
    $article->setNom('Article 3');
    $article->setPrix(2080);
   
    $entityManager->persist($article);
    $entityManager->flush();
    return new Response('Article enregisté avec id '.$article->getId());
 }


 #[Route('/article/{id}', name:"article_show")]
 public function show(PersistenceManagerRegistry $managerRegistry,$id)  {
   $article=$managerRegistry->getRepository(Article::class)->find($id);
   return $this->render('article/show.html.twig', array('article' => $article)); 
 }

  //Modifier un article
  #[Route('/article/edit/{id}',name:"edit_article",methods:['GET','POST'])]
  public function edit(PersistenceManagerRegistry $managerRegistry,Request $request,$id)  {
    $article = new Article();
    $article=$managerRegistry->getRepository(Article::class)->find($id);
    $form = $this->createForm(ArticleType::class,$article);
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()) 
    { 
      $entityManager = $managerRegistry->getManager(); 
      $entityManager->flush(); 
      return $this->redirectToRoute('article_list');
  }
  return $this->render('article/edit.html.twig', ['form' => $form->createView()]);
}

#[Route('/article/delete/{id}', name: "delete_article")]
public function delete(PersistenceManagerRegistry $managerRegistry, Request $request, int $id): Response
{
    // Récupérer l'article avec l'identifiant spécifié
    $article = $managerRegistry->getRepository(Article::class)->find($id);

    // Vérifier si l'article existe
    if (!$article) {
        // Si l'article n'existe pas, vous pouvez gérer cela de différentes manières, par exemple, rediriger vers une page d'erreur 404.
        return new Response('Article not found', Response::HTTP_NOT_FOUND);
    }

    // Récupérer l'EntityManager
    $entityManager = $managerRegistry->getManager();

    // Supprimer l'article
    $entityManager->remove($article);

    // Appliquer les modifications à la base de données
    $entityManager->flush();

    // Rediriger vers la liste des articles après la suppression
    return $this->redirectToRoute('article_list');
}



   

 
}