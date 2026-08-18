import { ref } from 'vue'
import type { Employee } from '@/types/employee.ts'

// Hier werden alle Employees gesammelt, die in der App hinzugefügt werden.
// Jede Komponente kann auf diese Liste zugreifen – zentraler Datenspeicher.


export const allEmployees = ref<Employee[]>([])

// Diese drei Werte beschreiben den Zustand des API-Requests:
// loading = Request läuft, error = letzter Fehler, loaded = mindestens einmal erfolgreich geladen.
// loaded ist wichtig, weil auch eine leere Mitarbeiterliste ein gültiges Ergebnis sein kann.
export const employeesLoading = ref(false)
export const employeesError = ref<string | null>(null)
export const employeesLoaded = ref(false)

// Solange ein Request läuft, speichern wir hier seine Promise.
// Rufen mehrere Komponenten gleichzeitig loadAllEmployees() auf, erhalten alle
// dieselbe Promise und es wird nur ein Request an das Backend geschickt.
let employeesLoadPromise: Promise<void> | null = null


export function loadAllEmployees(): Promise<void> {
  // Einen bereits laufenden Request nicht noch einmal starten.
  if (employeesLoadPromise) return employeesLoadPromise

  employeesLoading.value = true
  employeesError.value = null

  employeesLoadPromise = (async () => {
    try {
      const res = await fetch('/api/employees', {
        method: 'GET',
      })

      await handleFetchError(res)

      const data = (await res.json()) as Employee[]
      allEmployees.value = data
      // Erst nach einem erfolgreichen Request gelten die Daten als geladen.
      employeesLoaded.value = true
    } catch (error) {
      employeesError.value =
        error instanceof Error ? error.message : 'Konnte Mitarbeiter nicht laden'
      throw error
    } finally {
      // finally läuft bei Erfolg und Fehler und räumt den Request-Zustand immer auf.
      employeesLoading.value = false
      employeesLoadPromise = null
    }
  })()

  return employeesLoadPromise
}

export function ensureEmployeesLoaded(): Promise<void> {
  // Für Komponenten, die nur sicherstellen möchten, dass Daten vorhanden sind:
  // Bereits geladene Daten werden wiederverwendet, ansonsten werden sie geladen.
  if (employeesLoaded.value) return Promise.resolve()
  return loadAllEmployees()
}


export async function addEmployee(newEmployee: Omit<Employee, 'id'>) {
  const res = await fetch ('/api/employees', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(newEmployee),
  });

  await handleFetchError(res)

  // Nach einer Änderung neu laden, damit der Store dem Backend entspricht.
  await loadAllEmployees()
}

export async function updateEmployee(id: number, employee: Omit<Employee, 'id'>) {
  const res = await fetch(`/api/employees/${id}`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(employee),
  })

  await handleFetchError(res)
  await loadAllEmployees()
}


export async function deleteEmployees(ids: number[]) {
  if (ids.length === 0) return

  await Promise.all(
    ids.map(async (id) => {
      const res = await fetch(`/api/employees/${id}`, {
        method: 'DELETE',
      })

      await handleFetchError(res)
    }),
  )

  await loadAllEmployees()
}

export function getEmployeeNameById(id: number | null): string {
  if (id === null) return '—'
  const employee = allEmployees.value.find((e) => e.id === id)
  return employee ? `${employee.firstName} ${employee.lastName}` : '—'
}

// Fehlerbehandlung bei Fetch-Antworten
async function handleFetchError(res: Response): Promise<void> {
  if (res.ok) return

  let msg = res.statusText || 'Unknown error'
  try {
    const err = await res.json()
    msg = (err && (err.error || err.message)) || msg
  } catch {
    throw new Error(msg)
  }

  throw new Error(msg)
}
