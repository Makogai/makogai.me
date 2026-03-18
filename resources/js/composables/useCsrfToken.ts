import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export function useCsrfToken() {
    const page = usePage();

    const token = computed(() => {
        const fromProps = (page.props as any).csrf_token as string | undefined;
        if (fromProps) {
            return fromProps;
        }

        if (typeof document === 'undefined') {
            return '';
        }

        return (
            (
                document.querySelector(
                    'meta[name="csrf-token"]',
                ) as HTMLMetaElement | null
            )?.content ?? ''
        );
    });

    return { token };
}
