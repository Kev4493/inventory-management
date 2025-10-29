import { ref } from 'vue'
import type { Item } from '@/types/item'

// Hier werden alle Items gesammelt, die in der App hinzugefügt werden.
// Jede Komponente kann auf diese Liste zugreifen – zentraler Datenspeicher.

export const allItems = ref<Item[]>([])

export async function loadAllItems() {
  const res = await fetch('/api/items', {
    method: 'GET',
  })

  if (!res.ok) {
    let msg = res.statusText

    try {
      const err = await res.json()
      msg = err?.error || msg
    } catch {
      throw new Error(msg)
    }
  }

  const data = (await res.json()) as Item[]
  allItems.value = data
}
