<template>
  <div class="addItemWrapper">
    <div class="addItemHeader">
      <h1>{{ $t('addEmployee.title') }}</h1>
      <p>{{ $t('addEmployee.subtitle') }}</p>
    </div>
    <EmployeeForm></EmployeeForm>
  </div>
</template>

<script setup lang="ts">
import EmployeeForm from '@/components/EmployeeForm.vue'
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
