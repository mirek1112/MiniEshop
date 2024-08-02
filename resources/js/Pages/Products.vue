<template>

    <Toast position="bottom-center" group="bc" @close="onClose">
        <template #message="slotProps">
            <div class="flex flex-col items-start flex-auto">
                <div class="flex items-center gap-2">
                    <span class="font-bold">{{ slotProps.message.summary }}</span>
                </div>
                <div class="font-medium text-lg my-4">Počet položek: {{ cartItems }}</div>
                <Button size="small" label="Přejít" severity="info" @click="onReply()"></Button>
            </div>
        </template>
    </Toast>

    <div class="card">
        <Toast />
        <DataView :value="products" :layout="layout">
            <template #header>
                <div class="flex justify-end">
                    <SelectButton v-model="layout" :options="options" :allowEmpty="false">
                        <template #option="{ option }">
                            <i :class="[option === 'list' ? 'pi pi-bars' : 'pi pi-table']" />
                        </template>
                    </SelectButton>
                </div>
            </template>



            <template #grid="slotProps">
                <div class="grid grid-cols-12 gap-4">
                    <div v-for="(item, index) in slotProps.items" :key="index"
                        class="col-span-12 sm:col-span-6 md:col-span-4 xl:col-span-6 p-2">
                        <div
                            class="p-6 border border-surface-200 dark:border-surface-700 bg-surface-0 dark:bg-surface-900 rounded flex flex-col">
                            <div class="bg-surface-50 flex justify-center rounded p-4">
                                <div class="relative mx-auto">
                                    <Link :href="`product/${item.id}`">
                                    <img class="rounded w-full" :src="`${item.image}`" :alt="item.name"
                                        style="max-width: 300px;max-height: 300px;" />
                                    </Link>
                                    <Tag :value="item.quantity" :severity="getSeverity(item)"
                                        class="absolute dark:!bg-surface-900" style="left: 4px; top: 4px"></Tag>
                                </div>
                            </div>
                            <div class="pt-6">
                                <div class="flex flex-row justify-between items-start gap-2">
                                    <div>
                                        <span class="font-medium text-surface-500 dark:text-surface-400 text-sm">{{
                                            item.category.name }}</span>
                                        <div class="text-lg font-medium mt-1">{{ item.name }}</div>
                                    </div>
                                    <div class="bg-surface-100 p-1" style="border-radius: 30px">
                                        <div class="bg-surface-0 flex items-center gap-2 justify-center py-1 px-2"
                                            style="border-radius: 30px; box-shadow: 0px 1px 2px 0px rgba(0, 0, 0, 0.04), 0px 1px 2px 0px rgba(0, 0, 0, 0.06)">
                                            <span v-if="!item.seen" class="text-surface-900 font-medium text-sm">{{
                                                item.average_rating
                                                }}</span>
                                            <p v-if="item.seen">Vaše hodnocení {{ item.user_rating }}</p>
                                            <Rating @change="newRating => changeRating(item.id, newRating.value)"
                                                v-if="item.seen" v-model="item.user_rating" />
                                            <Rating v-if="!item.seen" v-model="item.average_rating" readonly />
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-6 mt-6">
                                    <span class="text-2xl font-semibold">{{ item.price }} Kč</span>
                                    <div class="flex gap-2">

                                        <Button icon="pi pi-shopping-cart" @click="buyItem(item.id)" label="Koupit"
                                            :disabled="item.quantity === 'OUTOFSTOCK'"
                                            class="flex-auto whitespace-nowrap"></Button>

                                        <Button icon="pi pi-star" @click="changeCheckSeenStatus(item.id)"
                                            outlined></Button>

                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <template #list="slotProps">
                <div class="flex flex-col">
                    <div v-for="(item, index) in slotProps.items" :key="index">
                        <div class="flex flex-col sm:flex-row sm:items-center p-6 gap-4"
                            :class="{ 'border-t border-surface-200 dark:border-surface-700': index !== 0 }">
                            <div class="md:w-40 relative">
                                <Link :href="`product/${item.id}`">
                                <img class="rounded w-full" :src="`${item.image}`" :alt="item.name"
                                    style="max-width: 300px;max-height: 300px;" />
                                </Link>
                                <Tag :value="item.quantity" :severity="getSeverity(item)"
                                    class="absolute dark:!bg-surface-900" style="left: 4px; top: 4px"></Tag>
                            </div>
                            <div class="flex flex-col md:flex-row justify-between md:items-center flex-1 gap-6">
                                <div class="flex flex-row md:flex-col justify-between items-start gap-2">
                                    <div>
                                        <span class="font-medium text-surface-500 dark:text-surface-400 text-sm">{{
                                            item.category.name }}</span>
                                        <div class="text-lg font-medium mt-2">{{ item.name }}</div>
                                    </div>
                                    <div class="bg-surface-100 p-1" style="border-radius: 30px">
                                        <div class="bg-surface-0 flex items-center gap-2 justify-center py-1 px-2"
                                            style="border-radius: 30px; box-shadow: 0px 1px 2px 0px rgba(0, 0, 0, 0.04), 0px 1px 2px 0px rgba(0, 0, 0, 0.06)">
                                            <span v-if="!item.seen" class="text-surface-900 font-medium text-sm">{{
                                                item.average_rating
                                                }}</span>
                                            <p v-if="item.seen">Vaše hodnocení {{ item.user_rating }}</p>
                                            <Rating @change="newRating => changeRating(item.id, newRating.value)"
                                                v-if="item.seen" v-model="item.user_rating" />
                                            <Rating v-if="!item.seen" v-model="item.average_rating" readonly />
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col md:items-end gap-8">
                                    <span class="text-xl font-semibold">${{ item.price }}</span>
                                    <div class="flex flex-row-reverse md:flex-row gap-2">
                                        <Button icon="pi pi-star" @click="changeCheckSeenStatus(item.id)"
                                            outlined></Button>
                                        <Button icon="pi pi-shopping-cart" @click="buyItem(item.id)" label="Koupit"
                                            :disabled="item.quantity === 'OUTOFSTOCK'"
                                            class="flex-auto whitespace-nowrap"></Button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </DataView>

    </div>


</template>

<script>
import MainLayout from '../Layouts/MainLayout.vue'
export default {
    layout: MainLayout
}
</script>
<script setup>
import { ref } from "vue";
import Tag from "primevue/tag";
import SelectButton from "primevue/selectbutton";
import Button from "primevue/button";
import DataView from "primevue/dataview";
import { computed } from "vue";
import axios from "axios";
import Toast from 'primevue/toast';
import { useToast } from "primevue/usetoast";
import ToastService from 'primevue/toastservice';
import Rating from 'primevue/rating';
import { Link } from "@inertiajs/vue3";
import { router } from '@inertiajs/vue3'

import { onBeforeMount } from "vue";


const props = defineProps({
    products: Array

});





const toast = useToast();
const products = ref(props.products.map(product => ({ ...product, seen: false })));
const layout = ref('grid');
const options = ref(['list', 'grid']);
let messageTimeout = null;
const selectedCategory = ref('');
const message = ref('');
const visible = ref(false);
const cartItems = ref(0);


onBeforeMount(async () => {

try {
  const response = await axios.post('products/dataFetch');
  products.value = response.data.products.map(product => ({ ...product, seen: false }));
} catch (error) {


}
});

const showTemplate = () => {
    if (!visible.value) {
        toast.add({ severity: 'info', summary: 'Přejít do košíku?', group: 'bc' });
        visible.value = true;
    }
};

const onReply = async () => {

    router.get('/cart')
}

const onClose = () => {
    visible.value = false;
}

const findProductById = (id) => {
    return products.value.find(product => product.id === id);
};

const changeRating = async (id, rating) => {

    const response = await axios.post(`/product/${id}/rate`, { rating })
    if (response.data.success) {
        let product = findProductById(id);

        product.average_rating = response.data.average_rating
        showMultiple('addedrating')
        product.seen = false;

    }

}





const changeCheckSeenStatus = (id) => {
    products.value = products.value.map(product => {
        if (product.id === id) {
            return { ...product, seen: !product.seen };
        }
        return product;
    });
};



const Categories = computed(() => {
    const categories = props.products.map(product => product.category);
    const unique = new Map();
    categories.forEach(category => unique.set(category.id, category));

    return Array.from(unique.values());
});

const showMultiple = (status) => {

    switch (status) {

        case true: toast.add({ severity: 'success', summary: 'Přidáno', detail: 'Položka byla přidána', life: 3000 }); break;
        case "noquantity": toast.add({ severity: 'warn', summary: 'Chyba', detail: 'Položka není na skladu', life: 3000 }); break;
        case "addedrating": toast.add({ severity: 'info', summary: 'Hodnocení', detail: 'Hodnocení bylo přidáno', life: 3000 }); break;
        default: toast.add({ severity: 'error', summary: 'Chyba', detail: 'Došlo k chybě', life: 3150 });

    }
};



const buyItem = async (itemId) => {
    try {
        const response = await axios.post(`/cart/add/${itemId}`);

        if (response.data.success == true) {
            cartItems.value = response.data.cartitems;
            showTemplate();

            products.value = products.value.map(product => {

                if (product.id == response.data.product_id) {

                    return { ...product, quantity: response.data.quantity };
                }

                return product;
            });
            hideMessageAfterTimeout();
            showMultiple(response.data.success);
        }
        else if (response.data.success == "noquantity")
            showMultiple(response.data.success)
        else
            showMultiple(response.data.success)

    } catch (error) {

    }
};

const hideMessageAfterTimeout = () => {
    if (messageTimeout) clearTimeout(messageTimeout);
    messageTimeout = setTimeout(() => {
        message.value = '';
    }, 1000);
};




const getSeverity = (product) => {

    if (product.quantity > 5) {
        return 'success';
    } else if (product.quantity < 5 && product.quantity > 0) {
        return 'warn';
    } else if (product.quantity === 0) {
        return 'danger';
    } else {
        return null;
    }
}

</script>
