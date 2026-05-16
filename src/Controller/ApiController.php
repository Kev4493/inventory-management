<?php
namespace App\Controller;

use App\Entity\Item;
use App\Repository\ItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

// Der Controller enthält die eigentliche Logik hinter den API-Endpunkten -
// er empfängt die Anfrage vom Frontend, verarbeitet sie und schickt eine Antwort zurück.

#[Route('/api')]
class ApiController
{
    // GET /api/items → lädt alle Einträge aus der DB und gibt sie als JSON ans FE zurück
    #[Route('/items', methods: ['GET'])]
    public function loadItems(ItemRepository $repo): JsonResponse
    {
        $items = $repo->findBy([], ['id' => 'DESC']);

        $data = array_map(fn(Item $i) => [
            'id' => $i->getId(),
            'name' => $i->getName(),
            'category' => $i->getCategory(),
            'location' => $i->getLocation(),
            'inventoryNumber' => $i->getInventoryNumber(),
            'personId' => $i->getPersonId(),
            'purchaseDate' => $i->getPurchaseDate(),
            'notes' => $i->getNotes(),
        ], $items);

        return new JsonResponse($data);
    }

    // POST /api/items → nimmt die Daten vom Frontend entgegen und legt einen neuen Eintrag in der DB an.
    #[Route('/items', methods: ['POST'])]
    public function createItem(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }

        foreach (['name','category','location','inventoryNumber','purchaseDate'] as $f) {
            if (!array_key_exists($f, $data) || $data[$f] === '') {
                return new JsonResponse(['error' => "Missing field: $f"], 400);
            }
        }

        $item = new Item();
        $item->setName((string)$data['name']);
        $item->setCategory((string)$data['category']);
        $item->setLocation((string)$data['location']);
        $item->setInventoryNumber((string)$data['inventoryNumber']);
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
            'inventoryNumber' => $item->getInventoryNumber(),
            'personId' => $item->getPersonId(),
            'purchaseDate' => $item->getPurchaseDate(),
            'notes' => $item->getNotes(),
        ], 201);
    }
}
