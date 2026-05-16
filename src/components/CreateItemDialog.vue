<template>
  <div class="card flex justify-center">
    <Dialog v-model:visible="isOpen" modal header="Neuer Mitarbeiter" :style="{ width: '25rem' }">
      <h1>Das ist der Dialog</h1>
    </Dialog>
  </div>
</template>

<script setup lang="ts">
import Dialog from 'primevue/dialog'

// Verbindet isOpen mit v-model:isOpen aus der Parent-Komponente.
// Dadurch hat isOpen ein 'true' oder 'false' und der Dialog wird angezeigt oder nicht.
const isOpen = defineModel<boolean>('isOpen')
</script>
