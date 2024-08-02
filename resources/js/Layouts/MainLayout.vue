<template>

    <Menubar :model="items">
        <template #item="{ item, props, hasSubmenu }">
            <router-link v-if="item.route" :to="item.route" custom>
                <a v-ripple v-bind="props.action" @click="navigate">
                    <span :class="item.icon" />
                    <span class="ml-2">{{ item.label }}</span>
                </a>
            </router-link>
            <a v-else v-ripple :href="item.url" :target="item.target" v-bind="props.action">
                <span :class="item.icon" />
                <span class="ml-2">{{ item.label }}</span>
                <span v-if="hasSubmenu" class="pi pi-fw pi-angle-down ml-2" />
            </a>
        </template>



    </Menubar>




    <slot></slot>
</template>

<script setup>
import Menubar from 'primevue/menubar';

import { ref } from "vue";


const items = ref([

    {
        label: 'Produkty',
        icon: 'pi pi-link',
        url: '/products'

    },
    {
        label: 'Košík',
        icon: 'pi pi-link',
        url: '/cart'

    },
    {
        label: 'Profil',
        icon: 'pi pi-link',
        url: '/profile'

    },

]);
</script>





<style scoped></style>
