<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { 
    LayoutGrid, Package, Layers, RefreshCcw, 
    Tags, Truck, MapPin, Users, Building2, ShieldCheck 
} from 'lucide-vue-next';

import { useAuth } from '@/composables/useAuth';
import AppLogo from '@/components/AppLogo.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar, SidebarContent, SidebarFooter, SidebarHeader,
    SidebarMenu, SidebarMenuButton, SidebarMenuItem,
} from '@/components/ui/sidebar';

import type { NavItem } from '@/types';

import * as CategoriaController from '@/actions/App/Http/Controllers/Central/CategoriaController';
import * as LaboratorioController from '@/actions/App/Http/Controllers/Central/LaboratorioController';
import * as LoteController from '@/actions/App/Http/Controllers/Central/LoteController';
import * as MovimientoInventarioController from '@/actions/App/Http/Controllers/Central/MovimientoInventarioController';
import * as ProductoController from '@/actions/App/Http/Controllers/Central/ProductoMaestroController';
import * as ProveedorController from '@/actions/App/Http/Controllers/Central/ProveedorController';
import * as SedeController from '@/actions/App/Http/Controllers/Central/SedeController';
import * as TransferenciaController from '@/actions/App/Http/Controllers/Central/TransferenciaController';
import * as UserController from '@/actions/App/Http/Controllers/Central/UserController'; // Nuevo controlador
import { dashboard } from '@/routes';

const { can } = useAuth();

interface NavSection {
    label: string;
    items: NavItem[];
}

const navigationSections = computed<NavSection[]>(() => [
    {
        label: 'Resumen',
        items: [
            { title: 'Dashboard', href: dashboard(), icon: LayoutGrid },
        ],
    },
    {
        label: 'Gestión de Inventario',
        items: [
            { title: 'Productos', href: ProductoController.index.url(), icon: Package },
            { title: 'Lotes y Stock', href: LoteController.index.url(), icon: Layers },
            { title: 'Kardex (Movimientos)', href: MovimientoInventarioController.index.url(), icon: RefreshCcw },
            { title: 'Categorías', href: CategoriaController.index.url(), icon: Tags },
        ],
    },
    {
        label: 'Operaciones y Logística',
        items: [
            { title: 'Transferencias', href: TransferenciaController.index.url(), icon: Truck },
        ],
    },
    {
        label: 'Abastecimiento',
        items: [
            { title: 'Proveedores', href: ProveedorController.index.url(), icon: Users },
            { title: 'Laboratorios', href: LaboratorioController.index.url(), icon: Building2 },
        ],
    },

    {
        label: 'Configuración y Seguridad',
        items: [

            ...(can('usuarios.manage') ? [
                { title: 'Operadores', href: UserController.index.url(), icon: ShieldCheck }
            ] : []),

            ...(can('sedes.manage') ? [
                { title: 'Sedes', href: SedeController.index.url(), icon: MapPin }
            ] : []),
        ],
    },
]);
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :sections="navigationSections" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>