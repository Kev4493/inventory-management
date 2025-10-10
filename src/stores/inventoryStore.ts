import { ref } from 'vue'
import type { Item } from '@/types/item'

// Hier werden alle Items gesammelt, die in der App hinzugefügt werden.
// Jede Komponente kann auf diese Liste zugreifen – zentraler Datenspeicher.

export const allItems = ref<Item[]>([])
