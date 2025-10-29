<template>
  <div class="addItemWrapper">
    <div class="addItemHeader">
      <h1>{{ $t('addItem.title') }}</h1>
      <p>{{ $t('addItem.subtitle') }}</p>
    </div>

    <ItemForm></ItemForm>
  </div>
</template>

<script setup lang="ts">
import ItemForm from '@/components/ItemForm.vue'
</script>

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
