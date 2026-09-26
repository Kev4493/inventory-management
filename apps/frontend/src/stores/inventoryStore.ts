import { ref } from 'vue'
import type { Item } from '@/types/item'

// Hier werden alle Items gesammelt, die in der App hinzugefügt werden.
// Jede Komponente kann auf diese Liste zugreifen – zentraler Datenspeicher.


export const allItems = ref<Item[]>([])

// Diese drei Werte beschreiben den Zustand des API-Requests:
// loading = Request läuft, error = letzter Fehler, loaded = mindestens einmal erfolgreich geladen.
// loaded ist wichtig, weil auch eine leere Item-Liste ein gültiges Ergebnis sein kann.
export const itemsLoading = ref(false)
export const itemsError = ref<string | null>(null)
export const itemsLoaded = ref(false)

// Solange ein Request läuft, speichern wir hier seine Promise.
// Rufen mehrere Komponenten gleichzeitig loadAllItems() auf, erhalten alle
// dieselbe Promise und es wird nur ein Request an das Backend geschickt.
let itemsLoadPromise: Promise<void> | null = null


export function loadAllItems(): Promise<void> {
  // Einen bereits laufenden Request nicht noch einmal starten.
  if (itemsLoadPromise) return itemsLoadPromise

  itemsLoading.value = true
  itemsError.value = null

  itemsLoadPromise = (async () => {
    try {
      const res = await fetch('/api/items', {
        method: 'GET',
      })

      await handleFetchError(res)

      const data = (await res.json()) as Item[]
      allItems.value = data
      // Erst nach einem erfolgreichen Request gelten die Daten als geladen.
      itemsLoaded.value = true
    } catch (error) {
      itemsError.value = error instanceof Error ? error.message : 'Konnte Inventar nicht laden'
      throw error
    } finally {
      // finally läuft bei Erfolg und Fehler und räumt den Request-Zustand immer auf.
      itemsLoading.value = false
      itemsLoadPromise = null
    }
  })()

  return itemsLoadPromise
}

export function ensureItemsLoaded(): Promise<void> {
  // Für Komponenten, die nur sicherstellen möchten, dass Daten vorhanden sind:
  // Bereits geladene Daten werden wiederverwendet, ansonsten werden sie geladen.
  if (itemsLoaded.value) return Promise.resolve()
  return loadAllItems()
}


type ItemInput = Omit<Item, 'id' | 'imageUrl'>

export async function addItem(newItem: ItemInput, image: File | null = null) {
  let body: BodyInit
  const headers: Record<string, string> = {}

  if (image) {
    const formData = new FormData()
    appendItemFields(formData, newItem)
    formData.append('image', image)
    body = formData
  } else {
    headers['Content-Type'] = 'application/json'
    body = JSON.stringify(newItem)
  }

  const res = await fetch('/api/items', { method: 'POST', headers, body })

  await handleFetchError(res)

  // Nach einer Änderung neu laden, damit der Store dem Backend entspricht.
  await loadAllItems()
}

export async function updateItem(
  id: number,
  item: ItemInput,
  image: File | null = null,
  removeImage = false,
) {
  const res = await fetch(`/api/items/${id}`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(item),
  })

  await handleFetchError(res)

  if (image) {
    const formData = new FormData()
    formData.append('image', image)
    const imageRes = await fetch(`/api/items/${id}/image`, { method: 'POST', body: formData })
    await handleFetchError(imageRes)
  } else if (removeImage) {
    const imageRes = await fetch(`/api/items/${id}/image`, { method: 'DELETE' })
    await handleFetchError(imageRes)
  }

  await loadAllItems()
}

function appendItemFields(formData: FormData, item: ItemInput): void {
  formData.append('inventoryNumber', item.inventoryNumber)
  formData.append('name', item.name)
  formData.append('category', item.category)
  formData.append('location', item.location)
  if (item.personId !== null) formData.append('personId', String(item.personId))
  formData.append('purchaseDate', String(item.purchaseDate))
  if (item.notes !== null) formData.append('notes', item.notes)
}

export async function deleteItems(ids: number[]) {
  if (ids.length === 0) return

  await Promise.all(
    ids.map(async (id) => {
      const res = await fetch(`/api/items/${id}`, {
        method: 'DELETE',
      })

      await handleFetchError(res)
    }),
  )

  await loadAllItems()
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
