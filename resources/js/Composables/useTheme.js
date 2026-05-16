import { ref, onMounted, watch } from 'vue';

export function useTheme() {
    const isDark = ref(false);

    const updateTheme = (dark) => {
        isDark.value = dark;
        if (dark) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    };

    const toggleTheme = () => {
        const newValue = !isDark.value;
        localStorage.setItem('theme', newValue ? 'dark' : 'light');
        updateTheme(newValue);
    };

    onMounted(() => {
        // Check local storage or system preference
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            updateTheme(savedTheme === 'dark');
        } else {
            const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            updateTheme(systemDark);
        }

        // Listen for system changes if no preference is saved
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('theme')) {
                updateTheme(e.matches);
            }
        });
    });

    return {
        isDark,
        toggleTheme
    };
}
