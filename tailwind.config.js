import forms from '@tailwindcss/forms'
import preline from 'preline/plugin'

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
    './node_modules/preline/dist/*.js',
  ],
  darkMode: 'class',
  theme: {
    fontFamily: {
      sans: ['Rubik', 'sans-serif'],
      serif: ['Hedvig Letters Serif', 'serif'],
      mono: ['Inconsolata'],
    },
  },
  plugins: [forms, preline],
}
