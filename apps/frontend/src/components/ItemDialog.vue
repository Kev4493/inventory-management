<template>
  <div class="card flex justify-center">
    <Dialog
      v-model:visible="isOpen"
      modal
      :header="item ? 'Produkt bearbeiten' : 'Neues Produkt'"
      :style="{ width: '50rem' }"
    >
      <ItemForm
        :item="item"
        @saved="handleSaved"
      />
    </Dialog>
  </div>
</template>

<script setup lang="ts">
import Dialog from 'primevue/dialog';
import ItemForm from '@/components/ItemForm.vue';
import type { Item } from '@/types/item.ts';

defineProps<{
  item?: Item | null;
}>();

// Verbindet isOpen mit v-model:isOpen aus der Parent-Komponente.
// Dadurch hat isOpen ein 'true' oder 'false' und der Dialog wird angezeigt oder nicht.
const isOpen = defineModel<boolean>('isOpen');

const emit = defineEmits<{
  saved: [];
}>();

function handleSaved() {
  isOpen.value = false;
  emit('saved');
}
</script>
