<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController
{
    /**
     * Display all ingredients
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient', name: 'ingredient.index',methods: ['GET'])]
    public function index(IngredientRepository $repository,PaginatorInterface $paginator,Request $request): Response
    {
        $paginationIngredients = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );


        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $paginationIngredients
        ]);
    }

    /**Return a new ingredient in ingredient list
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/ingredient/new',name: 'ingredient.new',methods: ['GET','POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager ) :Response
    {

        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class,$ingredient);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $ingredient = $form->getData();
            $entityManager->persist($ingredient); // envoi des données mais pas envoyer en base
            $entityManager->flush(); // envoie en base

            $this->addFlash('success','Ingrédient ajouté');
            return $this->redirectToRoute('ingredient.index');
        }
        return $this->render('pages/ingredient/new.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /** Return an ingredient modified
     * @return Response
     */

    #[Route('ingredient/edit{id}','ingredient.edit', methods: ['GET','POST'])]
    public function edit(Ingredient $ingredient,Request $request,EntityManagerInterface $entityManager) : Response
    {
        $form = $this->createForm(IngredientType::class,$ingredient);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $ingredient = $form->getData();
            $entityManager->persist($ingredient); // envoi des données mais pas envoyer en base
            $entityManager->flush(); // envoie en base

            $this->addFlash('success','Ingrédient ajouté');
            return $this->redirectToRoute('ingredient.index');
        }
        return $this->render('pages/ingredient/edit.html.twig',[
            'form' => $form->createView()
        ]);
    }
    #[Route('ingredient/delete{id}','ingredient.delete',methods: ['GET'])]
    public function delete(EntityManagerInterface $entityManager,Ingredient $ingredient) :Response
    {
        if(!$ingredient){
            $this->addFlash('success', 'Ingredient non trouvé');
        }
        $entityManager->remove($ingredient);
        $entityManager->flush();

        $this->addFlash(
            'success','Ingrédient supprimé'
        );

        return $this->redirectToRoute('ingredient.index');
    }
}
