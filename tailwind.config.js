/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{js,jsx}'],
  theme: {
    extend: {
      colors: {
        green: '#2A9D8F',
        navy: '#0A2540',
        orange: '#F4A261',
        red: '#E63946',
        canvas: '#F5F8FB',
      },
      fontFamily: {
        sans: ['Nunito', 'sans-serif'],
      },
      boxShadow: {
        card: '0 8px 32px rgba(10, 37, 64, 0.055)',
      },
    },
  },
  plugins: [],
};
