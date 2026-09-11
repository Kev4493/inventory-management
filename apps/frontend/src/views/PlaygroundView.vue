<template>
  <div class="playgroundWrapper">
    <h1>Spielwiese</h1>
    <div class="playgroundWrapperContent">
      <p>Wie lautet dein Vorname?</p>
      <input v-model="firstName" type="text" />

      <p>Wie lautet dein Nachname?</p>
      <input v-model="lastName" type="text" />

      <p>Wie alt bist du?</p>
      <input v-model="alter" type="number" />
    </div>

    <div v-if="firstName && lastName && alter">
      <h3>Willkommen {{ firstName }} {{ lastName }}!</h3>
      <p>Du bist {{ alter }} alt</p>
      <span v-if="volljaehrig">und somit Volljährig.</span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'

const firstName = ref()
const lastName = ref()
const alter = ref()

const kursName = ref('Vue 3')
kursName.value = 'Vue JS Onlinekurs'

const volljaehrig = computed(() => alter.value >= 18)

console.log(kursName.value)
</script>

<style scoped lang="scss">
.playgroundWrapper {
  display: flex;
  align-items: center;
  flex-direction: column;
}

.playgroundWrapperContent {
  margin-top: 100px;
}
</style>
