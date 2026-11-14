<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Repository\EmployeeRepository;
use App\Service\ImageStorage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

// Der Controller enthält die eigentliche Logik hinter den API-Endpunkten -
// er empfängt die Anfrage vom Frontend, verarbeitet sie und schickt eine Antwort zurück.

#[Route('/api')]
class EmployeeController
{
    // GET /api/employees → lädt alle Einträge aus der DB und gibt sie als JSON zurück
    #[Route('/employees', methods: ['GET'])]
    public function loadEmployees(EmployeeRepository $repo): JsonResponse
    {
        $employees = $repo->findBy([], ['id' => 'DESC']);

        return new JsonResponse(array_map(fn(Employee $e) => $this->employeeData($e), $employees));
    }

    // POST /api/employees → nimmt die Daten vom Frontend entgegen und legt einen neuen Eintrag in der DB an.
    #[Route('/employees', methods: ['POST'])]
    public function createEmployee(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }

        // required fields
        foreach (['firstName','lastName','street','zipCode','city','typeOfEmployment','department','emailAddress','dateOfEntry'] as $f) {
            if (!array_key_exists($f, $data) || $data[$f] === '') {
                return new JsonResponse(['error' => "Missing field: $f"], 400);
            }
        }

        $employee = new Employee();
        $employee->setFirstName((string)$data['firstName']);
        $employee->setLastName((string)$data['lastName']);
        $employee->setStreet((string)$data['street']);
        $employee->setZipCode((string)$data['zipCode']);
        $employee->setCity((string)$data['city']);
        $employee->setTypeOfEmployment((string)$data['typeOfEmployment']);
        $employee->setDepartment((string)$data['department']);
        $employee->setEmailAddress((string)$data['emailAddress']);

        try {
            $employee->setDateOfEntry(new \DateTime($data['dateOfEntry']));
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid dateOfEntry format'], 400);
        }

        if (!empty($data['dateOfLeaving'])) {
            try {
                $employee->setDateOfLeaving(new \DateTime($data['dateOfLeaving']));
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Invalid dateOfLeaving format'], 400);
            }
        }

        $employee->setNotes($data['notes'] ?? null);

        $em->persist($employee);
        $em->flush();

        return new JsonResponse($this->employeeData($employee), 201);
    }

    // PUT /api/employees/{id} → aktualisiert einen vorhandenen Mitarbeiter.
    #[Route('/employees/{id}', methods: ['PUT'])]
    public function updateEmployee(int $id, Request $request, EmployeeRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $employee = $repo->find($id);

        if (!$employee) {
            return new JsonResponse(['error' => 'Employee not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }

        foreach (['firstName', 'lastName', 'street', 'zipCode', 'city', 'typeOfEmployment', 'department', 'emailAddress', 'dateOfEntry'] as $f) {
            if (!array_key_exists($f, $data) || $data[$f] === '') {
                return new JsonResponse(['error' => "Missing field: $f"], 400);
            }
        }

        try {
            $dateOfEntry = new \DateTime($data['dateOfEntry']);
            $dateOfLeaving = !empty($data['dateOfLeaving']) ? new \DateTime($data['dateOfLeaving']) : null;
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid date format'], 400);
        }

        $employee->setFirstName((string) $data['firstName']);
        $employee->setLastName((string) $data['lastName']);
        $employee->setStreet((string) $data['street']);
        $employee->setZipCode((string) $data['zipCode']);
        $employee->setCity((string) $data['city']);
        $employee->setTypeOfEmployment((string) $data['typeOfEmployment']);
        $employee->setDepartment((string) $data['department']);
        $employee->setEmailAddress((string) $data['emailAddress']);
        $employee->setDateOfEntry($dateOfEntry);
        $employee->setDateOfLeaving($dateOfLeaving);
        $employee->setNotes($data['notes'] ?? null);

        $em->flush();

        return new JsonResponse($this->employeeData($employee));
    }

    // POST /api/employees/{id}/photo → speichert ein neues Foto und ersetzt ein vorhandenes.
    #[Route('/employees/{id}/photo', methods: ['POST'])]
    public function uploadEmployeePhoto(int $id, Request $request, EmployeeRepository $repo, EntityManagerInterface $em, ImageStorage $employeePhotoStorage): JsonResponse
    {
        $employee = $repo->find($id);
        if (!$employee) {
            return new JsonResponse(['error' => 'Mitarbeiter nicht gefunden.'], 404);
        }

        $upload = $request->files->get('photo');
        if ($upload === null) {
            return new JsonResponse(['error' => 'Bitte ein Foto auswählen.'], 400);
        }

        try {
            $newFilename = $employeePhotoStorage->store($upload);
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 400);
        }

        $oldFilename = $employee->getPhotoFilename();
        $employee->setPhotoFilename($newFilename);
        try {
            $em->flush();
        } catch (\Throwable $exception) {
            $employeePhotoStorage->remove($newFilename);
            throw $exception;
        }
        $employeePhotoStorage->remove($oldFilename);

        return new JsonResponse($this->employeeData($employee));
    }

    // GET /api/employees/{id}/photo → liefert das Foto aus.
    #[Route('/employees/{id}/photo', methods: ['GET'])]
    public function loadEmployeePhoto(int $id, EmployeeRepository $repo, ImageStorage $employeePhotoStorage): BinaryFileResponse|JsonResponse
    {
        $employee = $repo->find($id);
        $response = $employee?->getPhotoFilename() !== null
            ? $employeePhotoStorage->response($employee->getPhotoFilename())
            : null;

        return $response ?? new JsonResponse(['error' => 'Mitarbeiterfoto nicht gefunden.'], 404);
    }

    // DELETE /api/employees/{id}/photo → entfernt das Foto, der Mitarbeiter bleibt erhalten.
    #[Route('/employees/{id}/photo', methods: ['DELETE'])]
    public function deleteEmployeePhoto(int $id, EmployeeRepository $repo, EntityManagerInterface $em, ImageStorage $employeePhotoStorage): JsonResponse
    {
        $employee = $repo->find($id);
        if (!$employee) {
            return new JsonResponse(['error' => 'Mitarbeiter nicht gefunden.'], 404);
        }

        $oldFilename = $employee->getPhotoFilename();
        $employee->setPhotoFilename(null);
        $em->flush();
        $employeePhotoStorage->remove($oldFilename);

        return new JsonResponse($this->employeeData($employee));
    }

    // DELETE /api/employees/{id} → entfernt einen Employee aus der DB anhand der ID
    #[Route('/employees/{id}', methods: ['DELETE'])]
    public function deleteEmployee(int $id, EmployeeRepository $repo, EntityManagerInterface $em, ImageStorage $employeePhotoStorage): JsonResponse
    {
        $employee = $repo->find($id);

        if (!$employee) {
            return new JsonResponse(['error' => 'Employee not found'], 404);
        }

        $photoFilename = $employee->getPhotoFilename();
        $em->remove($employee);
        $em->flush();
        $employeePhotoStorage->remove($photoFilename);

        return new JsonResponse(null, 204);
    }

    private function employeeData(Employee $employee): array
    {
        return [
            'id' => $employee->getId(),
            'firstName' => $employee->getFirstName(),
            'lastName' => $employee->getLastName(),
            'street' => $employee->getStreet(),
            'zipCode' => $employee->getZipCode(),
            'city' => $employee->getCity(),
            'typeOfEmployment' => $employee->getTypeOfEmployment(),
            'department' => $employee->getDepartment(),
            'emailAddress' => $employee->getEmailAddress(),
            'dateOfEntry' => $employee->getDateOfEntry()?->format('Y-m-d'),
            'dateOfLeaving' => $employee->getDateOfLeaving()?->format('Y-m-d'),
            'notes' => $employee->getNotes(),
            'photoUrl' => $this->photoUrl($employee),
        ];
    }

    // Der Dateiname im Parameter v ändert sich bei jedem neuen Foto, damit der Browser-Cache nicht greift.
    private function photoUrl(Employee $employee): ?string
    {
        return $employee->getPhotoFilename() === null
            ? null
            : '/api/employees/'.$employee->getId().'/photo?v='.rawurlencode($employee->getPhotoFilename());
    }
}
