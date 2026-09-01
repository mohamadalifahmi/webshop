import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './app/Livewire/**/*.php',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                serif: ['Playfair Display', ...defaultTheme.fontFamily.serif],
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                deep: '#0A0A0F',
                cosmic: {
                    DEFAULT: '#6B46C1',
                    50: '#F3EEFE',
                    100: '#E5DCFD',
                    200: '#CBB8FB',
                    300: '#B194F9',
                    400: '#8E6BF5',
                    500: '#6B46C1',
                    600: '#5538A3',
                    700: '#3F2A7E',
                    800: '#2A1C55',
                    900: '#150E2B',
                },
                nebula: {
                    DEFAULT: '#3B82F6',
                    50: '#EFF6FF',
                    100: '#DBEAFE',
                    200: '#BFDBFE',
                    300: '#93C5FD',
                    400: '#60A5FA',
                    500: '#3B82F6',
                    600: '#2563EB',
                    700: '#1D4ED8',
                    800: '#1E40AF',
                    900: '#1E3A8A',
                },
                stargold: {
                    DEFAULT: '#FBBF24',
                    50: '#FFFBEB',
                    100: '#FEF3C7',
                    200: '#FDE68A',
                    300: '#FCD34D',
                    400: '#FBBF24',
                    500: '#F59E0B',
                    600: '#D97706',
                    700: '#B45309',
                    800: '#92400E',
                    900: '#78350F',
                },
                space: {
                    700: '#1F2937',
                    800: '#111827',
                    900: '#0D1117',
                },
            },
            animation: {
                'shooting-star': 'shootingStar 3s ease-in-out infinite',
                'pulse-glow': 'pulseGlow 2s ease-in-out infinite',
                'float': 'float 6s ease-in-out infinite',
                'fade-in-up': 'fadeInUp 0.6s ease-out forwards',
            },
            keyframes: {
                shootingStar: {
                    '0%': { transform: 'translateX(-100%) translateY(-100%)', opacity: '0' },
                    '10%': { opacity: '1' },
                    '30%': { opacity: '1' },
                    '50%': { transform: 'translateX(100vw) translateY(100vh)', opacity: '0' },
                    '100%': { transform: 'translateX(100vw) translateY(100vh)', opacity: '0' },
                },
                pulseGlow: {
                    '0%, 100%': { boxShadow: '0 0 20px rgba(107,70,193,0.3)' },
                    '50%': { boxShadow: '0 0 40px rgba(107,70,193,0.6)' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-10px)' },
                },
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
        },
    },

    plugins: [forms],
};
