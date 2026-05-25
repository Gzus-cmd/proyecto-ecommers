<script setup lang="ts">
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import { SidebarInset } from '@/components/ui/sidebar';
import { Toaster } from '@/components/ui/sonner';
import type { BreadcrumbItem } from '@/types';

defineProps<{ breadcrumbs?: BreadcrumbItem[] }>();
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <SidebarInset>
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            
            <!-- 
                Dejamos el slot libre. 
                Cada página (Dashboard, Productos) decidirá su propio tamaño.
            -->
            <slot />
            
        </SidebarInset>
        <Toaster />
    </AppShell>
</template>