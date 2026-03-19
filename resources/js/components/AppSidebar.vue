<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    BriefcaseBusiness,
    CalendarDays,
    ExternalLink,
    FileText,
    FolderGit2,
    LayoutGrid,
    Library,
    Link2,
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
    {
        title: 'Link Health',
        href: '/admin/link-health',
        icon: Link2,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/makogai/makogai.me',
        icon: FolderGit2,
    },
    {
        title: 'Open website',
        href: '/',
        icon: ExternalLink,
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
