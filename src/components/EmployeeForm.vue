<template>
  <form @submit.prevent="handleSubmit" class="addItemForm">
    <h2>{{ $t('employeeForm.headline.personalInfo') }}</h2>
    <div>
      <label for="firstName">{{ $t('employeeForm.label.firstName') }}:</label>
      <input v-model="newEmployee.firstName" type="text" id="firstName" name="firstName" required />
    </div>
    <div>
      <label for="lastName">{{ $t('employeeForm.label.lastName') }}:</label>
      <input v-model="newEmployee.lastName" type="text" id="lastName" name="lastName" required />
    </div>
    <div>
      <label for="street">{{ $t('employeeForm.label.street') }}:</label>
      <input v-model="newEmployee.street" type="text" id="street" name="street" required />
    </div>
    <div>
      <label for="zip">{{ $t('employeeForm.label.zip') }}:</label>
      <input v-model="newEmployee.zipCode" type="text" id="zip" name="zip" required />
    </div>
    <div>
      <label for="city">{{ $t('employeeForm.label.city') }}:</label>
      <input v-model="newEmployee.city" type="text" id="city" name="city" required />
    </div>

    <h2>{{ $t('employeeForm.headline.jobInfo') }}</h2>

    <div>
      <label for="employmentType">{{ $t('employeeForm.label.employmentType') }}:</label>
      <select v-model="newEmployee.typeOfEmployment" id="employmentType" name="employmentType" required>
        <option value="" disabled>{{ $t('employeeForm.placeholder.chooseEmploymentType') }}</option>
        <option value="fullTime">{{ $t('employeeForm.employmentType.fullTime') }}</option>
        <option value="partTime">{{ $t('employeeForm.employmentType.partTime') }}</option>
        <option value="workingStudent">{{ $t('employeeForm.employmentType.workingStudent') }}</option>
        <option value="intern">{{ $t('employeeForm.employmentType.intern') }}</option>
        <option value="trainee">{{ $t('employeeForm.employmentType.trainee') }}</option>
        <option value="freelancer">{{ $t('employeeForm.employmentType.freelancer') }}</option>
      </select>
    </div>

    <div>
      <label for="department">{{ $t('employeeForm.label.department') }}:</label>
      <select v-model="newEmployee.department" id="department" name="department" required>
        <option value="" disabled>{{ $t('employeeForm.placeholder.chooseDepartment') }}</option>
        <option value="development">{{ $t('employeeForm.department.development') }}</option>
        <option value="marketing">{{ $t('employeeForm.department.marketing') }}</option>
        <option value="sales">{{ $t('employeeForm.department.sales') }}</option>
        <option value="design">{{ $t('employeeForm.department.design') }}</option>
        <option value="management">{{ $t('employeeForm.department.management') }}</option>
        <option value="humanResources">{{ $t('employeeForm.department.humanResources') }}</option>
      </select>
    </div>

    <div>
      <label for="email">{{ $t('employeeForm.label.email') }}:</label>
      <input v-model="newEmployee.emailAddress" type="text" id="email" name="email" required />
    </div>

    <div>
      <label for="entryDate">{{ $t('employeeForm.label.entryDate') }}:</label>
      <input v-model="newEmployee.dateOfEntry" type="date" id="entryDate" name="entryDate" required />
    </div>
    <div>
      <label for="exitDate">{{ $t('employeeForm.label.exitDate') }}:</label>
      <input v-model="newEmployee.dateOfLeaving" type="date" id="exitDate" name="exitDate" />
    </div>

    <div>
      <label for="notes">{{ $t('employeeForm.label.notes') }}:</label>
      <textarea v-model="newEmployee.notes" id="notes" name="notes"></textarea>
    </div>

    <button type="submit" :disabled="isSaving" aria-busy="true">
      {{ $t('employeeForm.button.addEmployee') }}
    </button>
  </form>
</template>

<script setup lang="ts">
import type { Employee } from '@/types/employee.ts'
import { reactive, ref } from 'vue'
import { addEmployee } from '@/stores/employeeStore.ts'

// Objekt für ein neu hinzuzufügenden Mitarbeiter
const newEmployee = reactive<Omit<Employee, 'id'>>({
  firstName: '',
  lastName: '',
  street: '',
  zipCode: '',
  city: '',
  typeOfEmployment: '' as '' | 'fullTime' | 'partTime' | 'workingStudent' | 'intern' | 'trainee' | 'freelancer',
  emailAddress: '',
  department: '',
  dateOfEntry: '',
  dateOfLeaving: null,
  notes: null
})

const isSaving = ref(false);

async function handleSubmit() {
  if (isSaving.value) return;
  isSaving.value = true;

  try {
    // 1) Funktion aufrufen, die den MA in der DB speichert
    await addEmployee(newEmployee);

    // 3. Formular leeren
    Object.assign(newEmployee, {
      firstName: '',
      lastName: '',
      street: '',
      zipCode: '',
      city: '',
      typeOfEmployment: '' as '' | 'fullTime' | 'partTime' | 'workingStudent' | 'intern' | 'trainee' | 'freelancer',
      emailAddress: '',
      department: '',
      dateOfEntry: '',
      dateOfLeaving: null,
      notes: null
    });

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
}
</style>
