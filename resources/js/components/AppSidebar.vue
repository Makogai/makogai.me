<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    BookOpen,
    BriefcaseBusiness,
    CalendarDays,
    FileText,
    FolderGit2,
    LayoutGrid,
    Library,
    Settings,
} from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Admin',
        href: '/admin',
        icon: LayoutGrid,
    },
    {
        title: 'Posts',
        href: '/admin/posts',
        icon: FileText,
    },
    {
        title: 'Projects',
        href: '/admin/projects',
        icon: FolderGit2,
    },
    {
        title: 'Experience',
        href: '/admin/experience',
        icon: BriefcaseBusiness,
    },
    {
        title: 'Activity',
        href: '/admin/activities',
        icon: CalendarDays,
    },
    {
        title: 'Settings',
        href: '/admin/settings',
        icon: Settings,
    },
    {
        title: 'Media',
        href: '/admin/media',
        icon: Library,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link href="/admin">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
