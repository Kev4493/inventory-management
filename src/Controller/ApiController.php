<?php
namespace App\Controller;

use App\Entity\Item;
use App\Repository\ItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class ApiController
{
    // GET /api/items  →Item Liste laden
    #[Route('/items', methods: ['GET'])]
    public function loadItems(ItemRepository $repo): JsonResponse
    {
        $items = $repo->findBy([], ['id' => 'DESC']);

        $data = array_map(fn(Item $i) => [
            'id' => $i->getId(),
            'name' => $i->getName(),
            'category' => $i->getCategory(),
            'location' => $i->getLocation(),
            'personId' => $i->getPersonId(),
            'purchaseDate' => $i->getPurchaseDate(),
            'notes' => $i->getNotes(),
        ], $items);

        return new JsonResponse($data);
    }

    // POST /api/items → neues Item anlegen
    #[Route('/items', methods: ['POST'])]
    public function createItem(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }

        foreach (['name','category','location','purchaseDate'] as $f) {
            if (!array_key_exists($f, $data) || $data[$f] === '') {
                return new JsonResponse(['error' => "Missing field: $f"], 400);
            }
        }

        $item = new Item();
        $item->setName((string)$data['name']);
        $item->setCategory((string)$data['category']);
        $item->setLocation((string)$data['location']);
        $item->setPersonId(isset($data['personId']) ? (int)$data['personId'] : null);
        $item->setPurchaseDate((int)$data['purchaseDate']);
        $item->setNotes($data['notes'] ?? null);

        $em->persist($item);
        $em->flush();

        return new JsonResponse([
            'id' => $item->getId(),
            'name' => $item->getName(),
            'category' => $item->getCategory(),
            'location' => $item->getLocation(),
            'personId' => $item->getPersonId(),
            'purchaseDate' => $item->getPurchaseDate(),
            'notes' => $item->getNotes(),
        ], 201);
    }
}
