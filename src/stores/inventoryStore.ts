import { ref } from 'vue'
import type { Item } from '@/types/item'

// Hier werden alle Items gesammelt, die in der App hinzugefügt werden.
// Jede Komponente kann auf diese Liste zugreifen – zentraler Datenspeicher.


export const allItems = ref<Item[]>([])


export async function loadAllItems() {
  const res = await fetch('/api/items', {
    method: 'GET',
  })

  await handleFetchError(res)

  const data = (await res.json()) as Item[]
  allItems.value = data
}


export async function addItem(newItem: Omit<Item, 'id'>) {
  const res = await fetch ('/api/items', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(newItem),
  });

  await handleFetchError(res)

  await loadAllItems()
}

export async function updateItem(id: number, item: Omit<Item, 'id'>) {
  const res = await fetch(`/api/items/${id}`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(item),
  })

  await handleFetchError(res)
  await loadAllItems()
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
