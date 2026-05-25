<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavItem } from '@/types';

// Definimos la estructura de la sección
interface NavSection {
    label: string;
    items: NavItem[];
}

// Recibimos 'sections' como prop
defineProps<{
    sections: NavSection[];
}>();

const { isCurrentUrl } = useCurrentUrl();
</script>

<template>
    <div class="space-y-4 py-2">
        <SidebarGroup v-for="section in sections" :key="section.label" class="px-2 py-0">
            <!-- Etiqueta del grupo (Resumen, Inventario, etc.) -->
            <SidebarGroupLabel class="text-[10px] font-black uppercase tracking-[0.15em] text-muted-foreground/50 mb-2 px-2">
                {{ section.label }}
            </SidebarGroupLabel>
            
            <SidebarMenu>
                <SidebarMenuItem v-for="item in section.items" :key="item.title">
                    <SidebarMenuButton
                        as-child
                        :is-active="isCurrentUrl(item.href)"
                        :tooltip="item.title"
                        class="rounded-lg transition-all duration-200"
                    >
                        <Link :href="item.href" class="flex items-center gap-3">
                            <component v-if="item.icon" :is="item.icon" class="size-4" />
                            <span class="font-medium text-sm">{{ item.title }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarGroup>
    </div>
</template>