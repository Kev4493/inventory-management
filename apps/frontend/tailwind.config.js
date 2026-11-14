/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{vue,js,ts,jsx,tsx}'],
  theme: {
    extend: {
      // Gemeinsames Seitenverhältnis für Mitarbeiterfotos in Formular-Vorschau und Drawer,
      // damit der eingestellte Bildausschnitt an beiden Stellen gleich aussieht.
      aspectRatio: {
        photo: '3 / 2',
      },
    },
  },
  plugins: [],
}
