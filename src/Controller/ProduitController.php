<?php

namespace App\Controller;
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
#[Route('/produit')]
final class ProduitController extends AbstractController
{
    #[Route('', name: 'app_produit_index', methods: ['GET'])]
    #[IsGranted('ROLE_EMPLOYE')]
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    #[Route('/reapprovisionnement', name: 'app_produit_reapprovisionnement', methods: ['GET'])]
    public function reapprovisionnement(ProduitRepository $produitRepository): Response
    {
        $tousProduits = $produitRepository->findAll();
        $produitsAReapprovisionner = [];

        foreach ($tousProduits as $produit) {
            if ($produit->getStock() <= $produit->getSeuilAlerte()) {
                $produitsAReapprovisionner[] = $produit;
            }
        }

        return $this->render('produit/reapprovisionnement.html.twig', [
            'produits' => $produitsAReapprovisionner,
        ]);
    }

    #[Route('/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_index');
        }

        return $this->render('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_index');
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }
    #[Route('/{id}', name: 'app_produit_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
    if (!$this->isCsrfTokenValid('delete' . $produit->getId(), $request->getPayload()->getString('_token'))) {
        $this->addFlash('danger', 'Token CSRF invalide.');
        return $this->redirectToRoute('app_produit_index');
    }

    // Garde-fou: empêcher la suppression si des mouvements de stock sont liés
    if ($produit->getMouvementStocks()->count() > 0) {
        $this->addFlash('danger', 'Impossible de supprimer: des mouvements de stock sont liés à ce produit.');
        return $this->redirectToRoute('app_produit_index');
    }

    $entityManager->remove($produit);
    $entityManager->flush();

    $this->addFlash('success', 'Produit supprimé avec succès.');
    return $this->redirectToRoute('app_produit_index');    
    }
}
