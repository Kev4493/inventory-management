<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
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

        $data = array_map(fn(Employee $e) => [
            'id' => $e->getId(),
            'firstName' => $e->getFirstName(),
            'lastName' => $e->getLastName(),
            'street' => $e->getStreet(),
            'zipCode' => $e->getZipCode(),
            'city' => $e->getCity(),
            'typeOfEmployment' => $e->getTypeOfEmployment(),
            'department' => $e->getDepartment(),
            'emailAddress' => $e->getEmailAddress(),
            'dateOfEntry' => $e->getDateOfEntry() ? $e->getDateOfEntry()->format('Y-m-d') : null,
            'dateOfLeaving' => $e->getDateOfLeaving() ? $e->getDateOfLeaving()->format('Y-m-d') : null,
            'notes' => $e->getNotes(),
        ], $employees);

        return new JsonResponse($data);
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

        return new JsonResponse([
            'id' => $employee->getId(),
            'firstName' => $employee->getFirstName(),
            'lastName' => $employee->getLastName(),
            'street' => $employee->getStreet(),
            'zipCode' => $employee->getZipCode(),
            'city' => $employee->getCity(),
            'typeOfEmployment' => $employee->getTypeOfEmployment(),
            'department' => $employee->getDepartment(),
            'emailAddress' => $employee->getEmailAddress(),
            'dateOfEntry' => $employee->getDateOfEntry() ? $employee->getDateOfEntry()->format('Y-m-d') : null,
            'dateOfLeaving' => $employee->getDateOfLeaving() ? $employee->getDateOfLeaving()->format('Y-m-d') : null,
            'notes' => $employee->getNotes(),
        ], 201);
    }
}



