<template>
  <form @submit.prevent="handleSubmit" class="addItemForm">
    <div>
      <label for="itemName">{{ $t('itemForm.label.productName') }}:</label>
      <input v-model="newItem.name" type="text" id="itemName" name="itemName" required />
    </div>

    <div>
      <label for="category">{{ $t('itemForm.label.category') }}:</label>
      <select v-model="newItem.category" id="category" name="category" required>
        <option value="" disabled>{{ $t('itemForm.placeholder.chooseCategory') }}</option>
        <option value="Laptop">Laptop</option>
        <option value="Bildschirm">Bildschirm</option>
        <option value="Tastatur">Tastatur</option>
        <option value="Maus">Maus</option>
        <option value="Docking station">Docking station</option>
        <option value="Headset">Headset</option>
        <option value="Fernseher">Fernseher</option>
        <option value="Kamera">Kamera</option>
        <option value="Laptop Ständer">Laptop Ständer</option>
        <option value="Beamer">Beamer</option>
      </select>
    </div>

    <div>
      <label for="location">{{ $t('itemForm.label.location') }}:</label>
      <select v-model="newItem.location" id="location" name="location" required>
        <option value="" disabled>{{ $t('itemForm.label.location') }}</option>
        <option value="Messe Büro">Messe Büro</option>
        <option value="GEOX Büro">GEOX Büro</option>
        <option value="PM Büro">PM Büro</option>
        <option value="GF Büro">GF Büro</option>
        <option value="Kleines Büro">Kleines Büro</option>
        <option value="Besprechungsraum">Besprechungsraum</option>
      </select>
    </div>

    <div>
      <label for="person">{{ $t('itemForm.label.assigned') }}:</label>
      <select v-model="newItem.person" id="person" name="person">
        <option :value="null" disabled>{{ $t('itemForm.label.assigned') }}</option>
        <option value="Kevin Wagner">Kevin Wagner</option>
        <option value="Itay Krämer">Itay Krämer</option>
        <option value="Camill Hauser">Camill Hauser</option>
        <option value="Johannes Sommer">Johannes Sommer</option>
        <option value="Benjamin Stiber">Benjamin Stiber</option>
      </select>
    </div>

    <div>
      <label for="purchaseDate">{{ $t('itemForm.label.purchaseDate') }}:</label>
      <input
        type="number"
        id="purchaseDate"
        name="purchaseDate"
        required
        v-model.number="newItem.purchaseDate"
      />
    </div>

    <div>
      <label for="notes">{{ $t('itemForm.label.notes') }}:</label>
      <textarea v-model="newItem.notes" id="notes" name="notes"></textarea>
    </div>

    <button type="submit" :disabled="isSaving" aria-busy="true">
      {{ $t('itemForm.button.addItem') }}
    </button>
  </form>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue'
import { allItems } from '@/stores/inventoryStore.ts'
import type { Item } from '@/types/item.ts'

// Objekt für ein neu hinzuzufügendes Item (wird direkt durch V-model befüllt)
const newItem = reactive<Omit<Item, 'id'>>({
  name: '',
  category: '',
  location: '',
  person: null,
  purchaseDate: new Date().getFullYear(),
  notes: null,
});

const isSaving = ref(false);

async function handleSubmit() {
  if (isSaving.value) return;
  isSaving.value = true;

  try {
    // 1) newItem an Backend (API) senden
    const res = await fetch('/api/items', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(newItem),
    });

    // 2) Antwort auslesen & Fehler prüfen
    const saved = await res.json().catch(() => null);
    console.log('Antwort vom Backend:', saved);   // ← zeigt das Objekt inkl. endgültiger id

    if (!res.ok) {
      throw new Error(saved?.error || 'Fehler beim Speichern');
    }

    // 3) in den Store pushen – jetzt mit echter DB-ID
    allItems.value.push(saved);

    // 4) Formular leeren (wie bisher)
    Object.assign(newItem, {
      name: '',
      category: '',
      location: '',
      person: null,
      purchaseDate: new Date().getFullYear(),
      notes: null,
    });

    console.log('📦 Gespeichert (aus DB):', saved);

  } catch (e: any) {
    console.error(e);
    alert(e?.message || 'Fehler beim Speichern');
  } finally {
    isSaving.value = false;
  }
}
</script>

<style scoped lang="scss">
.addItemForm {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  width: 100%;
  max-width: 600px;
  padding: 2rem;

  label {
    font-weight: bold;
  }

  input,
  textarea,
  select {
    padding: 0.5rem;
    border: 1px solid #ccc;
    border-radius: 4px;
    width: 100%;
    box-sizing: border-box;
  }

  button {
    padding: 0.5rem;
    background-color: $brandYellowBG;
    color: $mainBlackText;
    border: none;
    margin-left: auto;
    margin-right: auto;
    margin-top: 2rem;
    cursor: pointer;
    width: 200px;

    &:hover {
      background-color: #218838;
    }
  }

  button[disabled] {
    opacity: .6; cursor: not-allowed;
  }
}
</style>
