/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './templates/**/*.{html,twig}',
    './templates/**/*.twig',
    './templates/**/*.html.twig',
    './*.module',
    './doctor_appointment.module',
    './src/**/*.{php,inc}',
    './**/*.{module,theme,inc,php}'
  ],
  theme: {
    extend: {},
  },
  plugins: [],
};
