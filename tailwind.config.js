/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {},
  },
  plugins: [require('daisyui')],
  daisyui: {
    themes: ["dark", "light"], // Có thể chọn theme: dark phù hợp với web phim
    darkTheme: "dark", // Theme mặc định
  },
}
