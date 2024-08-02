<template>

    <Toast />

    <div class="container">
        <h1>Košík</h1>

        <p v-if="items.length === 0">Košík je prázdný</p>

        <CurrencyDropdown :exchangeRates="exchangeRates" :selectedCurrency="selectedCurrency"
            @update:currency="handleCurrencyChange"></CurrencyDropdown>

        <table v-if="items.length > 0" class="table">
            <thead>
                <tr>
                    <th>Produkt</th>
                    <th>Kategorie</th>
                    <th>Množství</th>
                    <th>Cena za kus</th>
                    <th>Celková cena</th>
                    <th>Smazat</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in items" :key="item.id">
                    <td>
                        <Link :href="`/product/${item.product.product_id}`">{{ item.product.name }}</Link>
                    </td>
                    <td>
                        <Button :label="item.product.category" severity="secondary" rounded />
                    </td>
                    <td>
                        <InputNumber v-model.number="item.quantity" @update:modelValue="updateItem(item.id, $event)"
                            showButtons buttonLayout="vertical" style="max-width:3rem" :min="0" :max="99">
                            <template #incrementbuttonicon>
                                <span class="pi pi-plus" />
                            </template>
                            <template #decrementbuttonicon>
                                <span class="pi pi-minus" />
                            </template>
                        </InputNumber>
                    </td>
                    <td>{{ formatCurrency(item.price) }}</td>
                    <td>{{ formatCurrency(item.quantity * item.price) }}</td>
                    <td>

                        <Button @click="removeItem(item.id)" icon="pi pi-times" severity="danger" rounded outlined
                            aria-label="Cancel" />
                    </td>
                </tr>
            </tbody>
        </table>

        <div>
            <p class="totalPrice">Celkem: {{ formatCurrency(countedPrice) }}</p>
        </div>

        <Button v-if="itemsCount" label="Zaplatit" @click="redirect(purchaseLink)">
        </Button>
        <Button v-else label="Zaplatit" disabled />
    </div>
</template>
<script>
import MainLayout from '../Layouts/MainLayout.vue'
export default {
    layout: MainLayout
}
</script>
<script setup>
import Button from "primevue/button"
import InputNumber from "primevue/inputnumber";
import SpeedDial from "primevue/speeddial";
import { ref, defineProps, computed, onMounted } from 'vue';
import axios from 'axios';
import { Link } from '@inertiajs/vue3';
import CurrencyDropdown from '@/Components/CurrencyDropdown/CurrencyDropdown.vue';
import { router } from "@inertiajs/vue3";
import Toast from 'primevue/toast';
import { useToast } from "primevue/usetoast";


const toast = useToast();
const props = defineProps({
    items: Array,
    auth: Object,
});

const items = ref(props.items);
const message = ref('');
const exchangeRates = ref({});
const selectedCurrency = ref('CZK');

let messageTimeout = null;


const itemsCount = computed(() => items.value.length > 0);


const redirect = (url) => {
    router.get(url);
}
const purchaseLink = computed(() => {
    let url = `/cart/purchase/${props.auth.user.id}`;
    url += `?price=${countedPrice.value}`;
    url += `&currency=${selectedCurrency.value}`;

    return url;
});

const countedPrice = computed(() => {
    let totalPrice = 0;

    items.value.forEach(item => {
        const itemTotalPrice = item.quantity * item.price;
        totalPrice += itemTotalPrice;
    });

    return totalPrice;
});


const handleCurrencyChange = (currency) => {
    selectedCurrency.value = currency;
};

const formatCurrency = (value) => {
    const rate = exchangeRates.value[selectedCurrency.value] || 1;
    const formattedValue = value * rate;


    return new Intl.NumberFormat('cs-CZ', {
        style: 'currency',
        currency: selectedCurrency.value,
    }).format(formattedValue);
};

const fetchExchangeRates = async () => {
    try {
        const response = await axios.get('/api/exchange-rates');
        exchangeRates.value = response.data.data;

    } catch (error) {
        message.value = 'Nepodarilo se nacist';
        hideMessageAfterTimeout();
    }
};

const updateItem = async (itemId, quantity) => {

    try {

        const response = await axios.post(`/cart/update/${itemId}`, { quantity });

        if (response.data.success) {
            showMultiple("update");
        } else {
            message.value = 'Došlo k chybě';
        }
        hideMessageAfterTimeout();
    } catch (error) {
        message.value = 'Došlo k chybě';
        hideMessageAfterTimeout();
    }
};
const showMultiple = (status) => {

    switch (status) {

        case "remove": toast.add({ severity: 'error', summary: 'Odstraněno', detail: 'Produkt byl odstraněn', life: 3150 }); break;
        case "error": toast.add({ severity: 'warn', summary: 'Chyba', detail: 'Nepodařilo se odstranit produkt', life: 3150 }); break
        case "update": toast.add({ severity: 'info', summary: 'Aktualizováno', detail: 'Produkt byl přidán', life: 3150 }); break

    }
};
const removeItem = async (itemId) => {

    try {
        const response = await axios.post(`/cart/remove/${itemId}`);
        if (response.data.success) {
            items.value = items.value.filter(item => item.id !== itemId);

            showMultiple("remove");
        } else {
            showMultiple("error");
        }
        hideMessageAfterTimeout();
    } catch (error) {
        message.value = 'Došlo k chybě';
        hideMessageAfterTimeout();
    }
};

const hideMessageAfterTimeout = () => {
    if (messageTimeout) clearTimeout(messageTimeout);
    messageTimeout = setTimeout(() => {
        message.value = '';
    }, 1000);
};

onMounted(fetchExchangeRates);
</script>

<style scoped>
.totalPrice {
    font-weight: 600;
    font-size: 1.5rem;
    margin-top: 2%;
}

.container {
    margin: 0 auto;
    max-width: 800px;
    padding: 1rem;
    display: flex;
    align-items: left;
    justify-content: center;
    flex-direction: column;
}

.table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 10px;
    background-color: #f9f9f9;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.table th,
.table td {
    padding: 0.75rem 1rem;
    text-align: left;
    border-bottom: none;
}

.table th {
    background-color: #f4f4f4;
    color: #333;
    border-radius: 10px 10px 0 0;
    text-align: center;
}

.table tr {
    transition: background-color 0.3s ease;
}

.table tr:hover {
    background-color: #e0e0e0;
}

select {
    margin-bottom: 1rem;
    padding: 0.5rem;
    font-size: 1rem;
    border-radius: 5px;
    border: 1px solid #ccc;
}

button {
    margin: 0.5rem 0;
}
</style>
