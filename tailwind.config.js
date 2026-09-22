/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
  content: [
    './app/Views/**/*.php',
    './public/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        navy: {
          50: '#F0F4F8',
          100: '#D9E2EC',
          200: '#BCCCDC',
          700: '#243B53',
          800: '#1B2A4A',
          900: '#101B30',
          950: '#0A1120',
        },
        terracotta: {
          50: '#FDF6F3',
          100: '#FCECE6',
          400: '#D9754C',
          500: '#C1613A',
          600: '#AB512D',
          700: '#8E3E1F',
        },
        mutedgreen: {
          50: '#F1F7F4',
          500: '#2F6E4E',
          600: '#26593F',
        },
        mutedamber: {
          50: '#FDF9F0',
          500: '#B8791F',
          600: '#9B6416',
        },
        parchment: {
          50: '#FDFBF7',
          100: '#FBF9F5',
          200: '#F4EFE6',
          300: '#E8E1D3',
        }
      },
      fontFamily: {
        sans: ['"IBM Plex Sans"', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
        serif: ['"IBM Plex Serif"', 'serif'],
        mono: ['"IBM Plex Mono"', 'monospace'],
      },
      boxShadow: {
        '2xs': '0 1px 2px 0 rgba(0, 0, 0, 0.04)',
        'xs': '0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05)',
        'soft': '0 4px 20px -2px rgba(27, 42, 74, 0.07)',
        'card': '0 10px 25px -5px rgba(27, 42, 74, 0.08), 0 8px 10px -6px rgba(27, 42, 74, 0.04)',
      },
      borderRadius: {
        'xs': '4px',
      }
    },
  },
  plugins: [],
}
