<?php

namespace App\Controller;

use App\Entity\MouvementStock;
use App\Form\MouvementStockType;
use App\Repository\MouvementStockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_EMPLOYE')]
#[Route('/mouvement/stock')]
final class MouvementStockController extends AbstractController
{
    #[Route(name: 'app_mouvement_stock_index', methods: ['GET'])]
    public function index(MouvementStockRepository $mouvementStockRepository): Response
    {
        return $this->render('mouvement_stock/index.html.twig', [
            'mouvement_stocks' => $mouvementStockRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_mouvement_stock_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $mouvementStock = new MouvementStock();
        $form = $this->createForm(MouvementStockType::class, $mouvementStock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $produit = $mouvementStock->getProduit();
            $quantite = $mouvementStock->getQuantite();
            $type = $mouvementStock->getType();

            if ($type === 'ENTREE') {
                $produit->setStock($produit->getStock() + $quantite);

            } elseif ($type === 'SORTIE') {

                if ($produit->getStock() < $quantite) {
                    $this->addFlash('danger', 'Stock insuffisant pour effectuer cette sortie.');
                    return $this->redirectToRoute('app_mouvement_stock_new');
                }

                $produit->setStock($produit->getStock() - $quantite);
            }

            $entityManager->persist($mouvementStock);
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('app_mouvement_stock_index');
        }

        return $this->render('mouvement_stock/new.html.twig', [
            'mouvement_stock' => $mouvementStock,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_mouvement_stock_show', methods: ['GET'])]
    public function show(MouvementStock $mouvementStock): Response
    {
        return $this->render('mouvement_stock/show.html.twig', [
            'mouvement_stock' => $mouvementStock,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_mouvement_stock_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MouvementStock $mouvementStock, EntityManagerInterface $entityManager): Response
    {
        // 🔹 Ancien état
        $oldQuantite = $mouvementStock->getQuantite();
        $oldType = $mouvementStock->getType();
        $produit = $mouvementStock->getProduit();

        $form = $this->createForm(MouvementStockType::class, $mouvementStock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // 🔁 Annuler l'ancien mouvement
            if ($oldType === 'ENTREE') {
                $produit->setStock($produit->getStock() - $oldQuantite);
            } elseif ($oldType === 'SORTIE') {
                $produit->setStock($produit->getStock() + $oldQuantite);
            }

            // ➕ Appliquer le nouveau mouvement
            $newQuantite = $mouvementStock->getQuantite();
            $newType = $mouvementStock->getType();

            if ($newType === 'ENTREE') {
                $produit->setStock($produit->getStock() + $newQuantite);

            } elseif ($newType === 'SORTIE') {

                if ($produit->getStock() < $newQuantite) {
                    $this->addFlash('danger', 'Stock insuffisant pour effectuer cette sortie.');
                    return $this->redirectToRoute('app_mouvement_stock_edit', [
                        'id' => $mouvementStock->getId()
                    ]);
                }

                $produit->setStock($produit->getStock() - $newQuantite);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_mouvement_stock_index');
        }

        return $this->render('mouvement_stock/edit.html.twig', [
            'mouvement_stock' => $mouvementStock,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_mouvement_stock_delete', methods: ['POST'])]
    public function delete(Request $request, MouvementStock $mouvementStock, EntityManagerInterface $entityManager): Response
    {
        $produit = $mouvementStock->getProduit();
        $quantite = $mouvementStock->getQuantite();
        $type = $mouvementStock->getType();

        // 🔁 Annuler l'impact du mouvement supprimé
        if ($type === 'ENTREE') {
            $produit->setStock($produit->getStock() - $quantite);
        } elseif ($type === 'SORTIE') {
            $produit->setStock($produit->getStock() + $quantite);
        }

        if ($this->isCsrfTokenValid('delete'.$mouvementStock->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($mouvementStock);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_mouvement_stock_index');
    }
}
