<?php
namespace App\Controller;

use App\Entity\Item;
use App\Repository\ItemRepository;
use App\Service\ItemImageStorage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

// Der Controller enthält die eigentliche Logik hinter den API-Endpunkten -
// er empfängt die Anfrage vom Frontend, verarbeitet sie und schickt eine Antwort zurück.

#[Route('/api')]
class ItemController
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
            'imageUrl' => $this->imageUrl($i),
        ], $items);

        return new JsonResponse($data);
    }

    // POST /api/items → nimmt die Daten vom Frontend entgegen und legt einen neuen Eintrag in der DB an.
    #[Route('/items', methods: ['POST'])]
    public function createItem(Request $request, EntityManagerInterface $em, ItemImageStorage $images): JsonResponse
    {
        $data = $this->requestData($request);
        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Ungültige Produktdaten.'], 400);
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

        try {
            if (($upload = $request->files->get('image')) !== null) {
                $item->setImageFilename($images->store($upload));
            }
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 400);
        }

        $em->persist($item);
        try {
            $em->flush();
        } catch (\Throwable $exception) {
            $images->remove($item->getImageFilename());
            throw $exception;
        }

        return new JsonResponse($this->itemData($item), 201);
    }

    // PUT /api/items/{id} → aktualisiert ein vorhandenes Inventar-Item.
    #[Route('/items/{id}', methods: ['PUT'])]
    public function updateItem(int $id, Request $request, ItemRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $item = $repo->find($id);

        if (!$item) {
            return new JsonResponse(['error' => 'Item not found'], 404);
        }

        $data = $this->requestData($request);
        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Ungültige Produktdaten.'], 400);
        }

        foreach (['name', 'category', 'location', 'inventoryNumber', 'purchaseDate'] as $f) {
            if (!array_key_exists($f, $data) || $data[$f] === '') {
                return new JsonResponse(['error' => "Missing field: $f"], 400);
            }
        }

        $item->setName((string) $data['name']);
        $item->setCategory((string) $data['category']);
        $item->setLocation((string) $data['location']);
        $item->setInventoryNumber((string) $data['inventoryNumber']);
        $item->setPersonId(isset($data['personId']) ? (int) $data['personId'] : null);
        $item->setPurchaseDate((int) $data['purchaseDate']);
        $item->setNotes($data['notes'] ?? null);

        $em->flush();

        return new JsonResponse($this->itemData($item));
    }

    #[Route('/items/{id}/image', methods: ['POST'])]
    public function uploadItemImage(int $id, Request $request, ItemRepository $repo, EntityManagerInterface $em, ItemImageStorage $images): JsonResponse
    {
        $item = $repo->find($id);
        if (!$item) {
            return new JsonResponse(['error' => 'Produkt nicht gefunden.'], 404);
        }

        $upload = $request->files->get('image');
        if ($upload === null) {
            return new JsonResponse(['error' => 'Bitte ein Bild auswählen.'], 400);
        }

        try {
            $newFilename = $images->store($upload);
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 400);
        }

        $oldFilename = $item->getImageFilename();
        $item->setImageFilename($newFilename);
        try {
            $em->flush();
        } catch (\Throwable $exception) {
            $images->remove($newFilename);
            throw $exception;
        }
        $images->remove($oldFilename);

        return new JsonResponse($this->itemData($item));
    }

    #[Route('/items/{id}/image', methods: ['GET'])]
    public function loadItemImage(int $id, ItemRepository $repo, ItemImageStorage $images): BinaryFileResponse|JsonResponse
    {
        $item = $repo->find($id);
        if (!$item || $item->getImageFilename() === null) {
            return new JsonResponse(['error' => 'Produktbild nicht gefunden.'], 404);
        }

        $path = $images->path($item->getImageFilename());
        if (!is_file($path)) {
            return new JsonResponse(['error' => 'Produktbild nicht gefunden.'], 404);
        }

        $response = new BinaryFileResponse($path);
        $mimeType = match (pathinfo($path, PATHINFO_EXTENSION)) {
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            default => 'application/octet-stream',
        };
        $response->headers->set('Content-Type', $mimeType);
        $response->headers->set('Cache-Control', 'public, max-age=3600');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, basename($path));

        return $response;
    }

    #[Route('/items/{id}/image', methods: ['DELETE'])]
    public function deleteItemImage(int $id, ItemRepository $repo, EntityManagerInterface $em, ItemImageStorage $images): JsonResponse
    {
        $item = $repo->find($id);
        if (!$item) {
            return new JsonResponse(['error' => 'Produkt nicht gefunden.'], 404);
        }

        $oldFilename = $item->getImageFilename();
        $item->setImageFilename(null);
        $em->flush();
        $images->remove($oldFilename);

        return new JsonResponse($this->itemData($item));
    }

    // DELETE /api/items/{id} → entfernt ein Inventar-Item aus der DB anhand der ID
    #[Route('/items/{id}', methods: ['DELETE'])]
    public function deleteItem(int $id, ItemRepository $repo, EntityManagerInterface $em, ItemImageStorage $images): JsonResponse
    {
        $item = $repo->find($id);

        if (!$item) {
            return new JsonResponse(['error' => 'Item not found'], 404);
        }

        $imageFilename = $item->getImageFilename();
        $em->remove($item);
        $em->flush();
        $images->remove($imageFilename);

        return new JsonResponse(null, 204);
    }

    private function itemData(Item $item): array
    {
        return [
            'id' => $item->getId(),
            'name' => $item->getName(),
            'category' => $item->getCategory(),
            'location' => $item->getLocation(),
            'inventoryNumber' => $item->getInventoryNumber(),
            'personId' => $item->getPersonId(),
            'purchaseDate' => $item->getPurchaseDate(),
            'notes' => $item->getNotes(),
            'imageUrl' => $this->imageUrl($item),
        ];
    }

    private function imageUrl(Item $item): ?string
    {
        return $item->getImageFilename() === null
            ? null
            : '/api/items/'.$item->getId().'/image?v='.rawurlencode($item->getImageFilename());
    }

    private function requestData(Request $request): ?array
    {
        if (str_starts_with((string) $request->headers->get('Content-Type'), 'multipart/form-data')) {
            return $request->request->all();
        }

        $data = json_decode($request->getContent(), true);

        return is_array($data) ? $data : null;
    }
}
