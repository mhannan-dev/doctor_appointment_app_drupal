/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './templates/**/*.{html,twig}',
    './templates/**/*.twig',
    './templates/**/*.html.twig',
    './*.module',
    './src/**/*.php'
  ],
  theme: {
    extend: {},
  },
  plugins: [],
};
