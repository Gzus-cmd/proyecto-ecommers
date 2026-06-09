import { PageProps as InertiaPageProps } from '@inertiajs/core';
import { Auth } from './auth';

export * from './auth';
export * from './navigation';
export * from './ui';


export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string; 
    auth: Auth;
    flash: {
        success: string | null;
        error: string | null;
        warning: string | null;
        info: string | null;
    };
    sidebarOpen: boolean;
};


declare module '@inertiajs/vue3' {
    export function usePage<T>(): { props: PageProps & T };
}