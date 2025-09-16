<script setup lang="ts">
import ItemForm from '@/components/ItemForm.vue'
</script>

<template>
  <div class="addItemWrapper">
    <div class="addItemHeader">
      <h1>Neues Item hinzufügen</h1>
      <p>Füge das neue Produkt einfach über das Formular hinzu.</p>
    </div>

    <ItemForm></ItemForm>
  </div>
</template>

<style scoped lang="scss">
.addItemWrapper {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.addItemHeader {
  text-align: center;
  padding: 2rem;
  width: 100%;
  margin-bottom: 3rem;
}
</style>
