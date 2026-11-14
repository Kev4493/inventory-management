const de = {
  // ===== Navigation =====
  sidebar: {
    home: 'Home',
    inventory: 'Inventar',
    employee: 'Mitarbeiterliste',
  },

  // ===== Views ======
  inventory: {
    title: 'Inventarliste',
    noItems: 'Keine Items im Inventar.',
    loading: 'Inventar wird geladen...',
    imageColumn: 'Bild',
    imageAlt: 'Produktbild von {name}',
    noImage: 'Kein Produktbild',
  },

  employee: {
    title: 'Mitarbeiterliste',
    noEmployee: 'Keine Mitarbeiter gefunden',
    loading: 'Mitarbeiter werden geladen...',
  },

  employeeDetail: {
    photoAlt: 'Profilbild von {name}',
    photoPlaceholder: 'Kein Profilbild von {name} hinterlegt',
  },

  // ===== Forms =====
  form: {
    requiredHint: 'Pflichtfelder sind mit * markiert.',
    validation: {
      required: 'Bitte ausfüllen.',
      email: 'Bitte eine gültige E-Mail-Adresse eingeben.',
    },
    toast: {
      errorSummary: 'Speichern fehlgeschlagen',
      errorDetail: 'Die Änderungen konnten nicht gespeichert werden.',
    },
  },

  itemForm: {
    label: {
      inventoryNumber: 'Inventar-Nr.',
      productName: 'Produktname',
      category: 'Kategorie',
      location: 'Standort',
      assigned: 'Zugewiesen an',
      purchaseDate: 'Anschaffungsjahr',
      notes: 'Anmerkungen',
      image: 'Produktbild',
    },
    // {0} wird von PrimeVue FileUpload durch den Dateinamen ersetzt.
    image: {
      choose: 'Bild auswählen',
      empty: 'Bild hierher ziehen oder oben auswählen.',
      hint: 'JPEG, PNG oder WebP, maximal 5 MB.',
      invalidFileType: '{0}: Bitte JPEG, PNG oder WebP auswählen.',
      invalidFileSize: '{0}: Die Datei darf höchstens 5 MB groß sein.',
      existingAlt: 'Vorhandenes Produktbild',
      removeExisting: 'Vorhandenes Produktbild entfernen',
      discardSelected: 'Ausgewähltes Bild verwerfen',
    },
    placeholder: {
      chooseCategory: 'Kategorie auswählen',
      location: 'Standort auswählen',
      assigned: 'Person auswählen',
    },
    button: {
      addItem: 'Item hinzufügen',
      saveItem: 'Änderungen speichern',
    },
  },

  employeeForm: {
    headline: {
      personalInfo: 'Persönliche Informationen',
      jobInfo: 'Beschäftigungsinformationen',
    },
    label: {
      firstName: 'Vorname',
      lastName: 'Nachname',
      street: 'Straße + Hausnummer',
      zip: 'Postleitzahl',
      city: 'Stadt',
      employmentType: 'Beschäftigungsart',
      department: 'Abteilung',
      email: 'E-Mail',
      entryDate: 'Eintrittsdatum',
      exitDate: 'Austrittsdatum',
      notes: 'Anmerkungen',
      photo: 'Foto',
    },
    // {0} wird von PrimeVue FileUpload durch den Dateinamen ersetzt.
    photo: {
      choose: 'Foto auswählen',
      empty: 'Foto hierher ziehen oder oben auswählen.',
      hint: 'JPEG, PNG oder WebP, maximal 5 MB.',
      invalidFileType: '{0}: Bitte JPEG, PNG oder WebP auswählen.',
      invalidFileSize: '{0}: Die Datei darf höchstens 5 MB groß sein.',
      existingAlt: 'Vorhandenes Foto',
      removeExisting: 'Vorhandenes Foto entfernen',
      discardSelected: 'Ausgewähltes Foto verwerfen',
      uploadFailedSummary: 'Foto nicht gespeichert',
      uploadFailedDetail: 'Der Mitarbeiter wurde angelegt, das Foto konnte aber nicht hochgeladen werden: {0}',
      position: 'Bildausschnitt',
      positionTop: 'oben',
      positionBottom: 'unten',
      previewAlt: 'Vorschau des Bildausschnitts',
    },
    placeholder: {
      chooseEmploymentType: 'Beschäftigungsart auswählen',
      chooseDepartment: 'Abteilung auswählen',
    },
    employmentType: {
      fullTime: 'Vollzeit',
      partTime: 'Teilzeit',
      workingStudent: 'Werkstudent',
      intern: 'Praktikant',
      trainee: 'Trainee',
      freelancer: 'Freelancer',
    },
    department: {
      development: 'Entwicklung',
      marketing: 'Marketing',
      sales: 'Vertrieb',
      design: 'Design',
      management: 'Geschäftsführung',
      humanResources: 'Personalabteilung',
    },
    button: {
      addEmployee: 'Mitarbeiter hinzufügen',
      saveEmployee: 'Änderungen speichern',
    },
  },

  // ===== Data-Tables =====
  table: {
    globalFilter: 'Suche in allen Spalten',
  },
}

export default de
