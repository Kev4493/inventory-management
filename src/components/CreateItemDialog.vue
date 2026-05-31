<template>
  <div class="card flex justify-center">
    <Dialog
      v-model:visible="isOpen"
      modal
      header="Neues Produkt"
      :style="{ width: '50rem' }"
    >
      <ItemForm></ItemForm>
    </Dialog>
  </div>
</template>

<script setup lang="ts">
import Dialog from 'primevue/dialog';
import ItemForm from '@/components/ItemForm.vue';

// Verbindet isOpen mit v-model:isOpen aus der Parent-Komponente.
// Dadurch hat isOpen ein 'true' oder 'false' und der Dialog wird angezeigt oder nicht.
const isOpen = defineModel<boolean>('isOpen');
</script>
