// Definition eines Objekttyps namens "Employee"
export type Employee = {
  id: number
  firstName: string
  lastName: string
  street: string
  zip: string
  city: string
  employmentType: '' | 'fullTime' | 'partTime' | 'workingStudent' | 'intern' | 'trainee' | 'freelancer'
  department: '' | 'development' | 'marketing' | 'sales' | 'design' | 'management' | 'humanResources'
  email: string
  entryDate: string
  exitDate?: string
  notes: string
};
