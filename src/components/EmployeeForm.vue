<template>
  <form @submit.prevent="handleSubmit" class="addItemForm">
    <h2>Persönliche Informationen</h2>
    <div>
      <label for="firstName">Vorname:</label>
      <input v-model="newEmployee.firstName" type="text" id="firstName" name="firstName" required />
    </div>
    <div>
      <label for="lastName">Nachname:</label>
      <input v-model="newEmployee.lastName" type="text" id="lastName" name="lastName" required />
    </div>
    <div>
      <label for="street">Straße + Hausnummer:</label>
      <input v-model="newEmployee.street" type="text" id="street" name="street" required />
    </div>
    <div>
      <label for="zip">PLZ:</label>
      <input v-model="newEmployee.zip" type="text" id="zip" name="zip" required />
    </div>
    <div>
      <label for="city">Stadt:</label>
      <input v-model="newEmployee.city" type="text" id="city" name="city" required />
    </div>

    <h2>Geschäftliche Informationen</h2>

    <div>
      <label for="employmentType">Beschäftigungsart:</label>
      <select v-model="newEmployee.employmentType" id="employmentType" name="employmentType" required>
        <option value="" disabled>Beschäftigungsart auswählen</option>
        <option value="fullTime">Vollzeit</option>
        <option value="partTime">Teilzeit</option>
        <option value="workingStudent">Werkstudent</option>
        <option value="intern">Praktikant</option>
        <option value="trainee">Auszubildender</option>
        <option value="freelancer">Freelancer</option>
      </select>
    </div>

    <div>
      <label for="department">Abteilung:</label>
      <select v-model="newEmployee.department" id="department" name="department" required>
        <option value="" disabled>Abteilung auswählen</option>
        <option value="Messe Büro">Messe Büro</option>
        <option value="GEOX Büro">GEOX Büro</option>
        <option value="PM Büro">PM Büro</option>
        <option value="GF Büro">GF Büro</option>
        <option value="Kleines Büro">Kleines Büro</option>
        <option value="Besprechungsraum">Besprechungsraum</option>
      </select>
    </div>

    <div>
      <label for="email">Email:</label>
      <input v-model="newEmployee.email" type="text" id="email" name="email" required />
    </div>

    <div>
      <label for="entryDate">Eintrittsdatum:</label>
      <input v-model="newEmployee.entryDate" type="date" id="entryDate" name="entryDate" required />
    </div>
    <div>
      <label for="exitDate">Austrittsdatum:</label>
      <input v-model="newEmployee.exitDate" type="date" id="exitDate" name="exitDate" />
    </div>

    <div>
      <label for="notes">{{ $t('itemForm.label.notes') }}:</label>
      <textarea v-model="newEmployee.notes" id="notes" name="notes" required></textarea>
    </div>

    <button type="submit">Mitarbeiter hinzufügen</button>
  </form>
</template>

<script setup lang="ts">
import type { Employee } from '@/types/employee.ts'
import { reactive } from 'vue'
import { allEmployees } from '@/stores/employeeStore.ts'

// Objekt für ein neu hinzuzufügenden Mitarbeiter
const newEmployee = reactive<Omit<Employee, 'id'>>({
  firstName: '',
  lastName: '',
  street: '',
  zip: '',
  city: '',
  employmentType: '' as '' | 'fullTime' | 'partTime' | 'workingStudent' | 'intern' | 'trainee' | 'freelancer',
  email: '',
  department: '',
  entryDate: '',
  exitDate: '',
  notes: ''
})

let nextId = 1;


function handleSubmit() {
  // 1. Neues Item ins Array kopieren
  allEmployees.value.push({
    id: nextId++,
    ...newEmployee });

  // 2. Debug-Ausgabe
  console.log("📦 Alle Mitarbeiter:", allEmployees.value);

  // 3. Formular leeren
  Object.assign(newEmployee, {
    firstName: '',
    lastName: '',
    street: '',
    zip: '',
    city: '',
    employmentType: '' as '' | 'fullTime' | 'partTime' | 'workingStudent' | 'intern' | 'trainee' | 'freelancer',
    email: '',
    department: '',
    entryDate: '',
    exitDate: '',
    notes: ''
  });
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
}
</style>
