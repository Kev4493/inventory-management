import { ref } from 'vue'
import type { Employee } from '@/types/employee.ts'

// Hier werden alle Employees gesammelt, die in der App hinzugefügt werden.
// Jede Komponente kann auf diese Liste zugreifen – zentraler Datenspeicher.


export const allEmployees = ref<Employee[]>([])


export async function loadAllEmployees() {
  const res = await fetch('/api/employees', {
    method: 'GET',
  })

  await handleFetchError(res)

  const data = (await res.json()) as Employee[]
  allEmployees.value = data
}


export async function addEmployee(newEmployee: Omit<Employee, 'id'>) {
  const res = await fetch ('/api/employees', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(newEmployee),
  });

  await handleFetchError(res)

  await loadAllEmployees()
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
