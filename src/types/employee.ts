// Definition eines Objekttyps namens "Employee"
export type Employee = {
  id: number
  firstName: string
  lastName: string
  street: string
  zip: string
  city: string
  employmentType: '' | 'fullTime' | 'partTime' | 'workingStudent' | 'intern' | 'trainee' | 'freelancer'
  department: string
  email: string
  entryDate: string
  exitDate?: string
  notes: string
};
