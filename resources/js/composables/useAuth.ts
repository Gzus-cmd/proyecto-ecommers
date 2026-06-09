import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { PageProps } from '@/types';

export function useAuth() {
    const page = usePage<PageProps>();

    const user = computed(() => page.props.auth.user);
    const permissions = computed(() => user.value?.permissions ?? []);
    const roles = computed(() => user.value?.roles ?? []);


    const can = (permission: string): boolean => {
        return permissions.value.includes(permission);
    };


    const hasRole = (role: string): boolean => {
        return roles.value.includes(role);
    };

    return {
        user,
        can,
        hasRole,
        permissions,
        roles
    };
}