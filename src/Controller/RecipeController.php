<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use PharIo\Manifest\Requirement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{
    #[Route('/recettes', name: 'recipe.index' ) ]
    public function index(Request $request, RecipeRepository $recipeRepository): Response
    {
        $recipes = $recipeRepository->findWithDurationLowerThan(30);
        // $recipes[0]->setText('pates alice');
        // $entityManagerInterface->flush();
        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }
    #[Route('/recettes/{slug}-{id}', name: 'recipe.show', requirements: ['id' => '\d+', 'slug' => '[a-z09-]+'])]
    public  function show(Request $request, string $slug, int $id, RecipeRepository  $recipeRepository ): Response
    {
        //return $this->json(['slug' => $slug]);
        //  return new Response('recette:' . $slug);
        $recipe = $recipeRepository->find($id);
        if ($recipe->getSlug() !== $slug) {
            return $this->redirectToRoute('recipe.show', ['slug' => $recipe->getSlug(), 'id' => $recipe->getId()]);
        }
        $recipe = $recipeRepository->findOneBy(['slug' => $slug]);
        return $this->render(
            'recipe/show.html.twig',
            [
                'recipe' => $recipe

            ]
        );
    }

    #[Route('/recettes/{id}/edit', name: 'recipe.edit',methods :['GET', 'POST'])]
    public function edit(Recipe $recipe,Request $request,EntityManagerInterface $entityManager ): Response
    {

        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        //  $recipe->setCreateAt(new \DateTimeImmutable());
         // $this->getDoctrine()->getManager()->flush();
         $entityManager->persist($recipe);
             $entityManager->flush();
            $this->addFlash('success','la recette a bien modifier ');
        return $this->redirectToRoute('recipe.index');
        }
       
        return $this->render('recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form
        ]);
    }



    #[Route('/recettes/create', name: 'recipe.create',methods :['GET', 'POST'])]
    public function create(Request $request,EntityManagerInterface $entityManager ): Response
    {   
        $recipe= new Recipe();

        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          //  $recipe->setCreateAt(new \DateTimeImmutable());
           // $recipe->setUpdateAt(new \DateTimeImmutable());
         // $this->getDoctrine()->getManager()->flush();
         $entityManager->persist($recipe);
             $entityManager->flush();
            $this->addFlash('success','la recette a bien cree ');
        return $this->redirectToRoute('recipe.index');
        }
       
        return $this->render('recipe/create.html.twig', [
            'recipe' => $recipe,
            'form' => $form
        ]);
    }




    #[Route('/recettes/{id}/edit', name: 'recipe.delete',methods :['DELETE'])]
    public function remove(Recipe $recipe,EntityManagerInterface $entityManager ): Response
    {

       $entityManager->remove($recipe);
       $entityManager->flush();
       $this->addFlash('success','la recette a bien supprimer ');
       return $this->redirectToRoute('recipe.index');
    }
}
