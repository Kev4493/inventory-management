import { ref } from 'vue'
import type { Employee } from '@/types/employee.ts'

export const allEmployees = ref<Employee[]>([])
