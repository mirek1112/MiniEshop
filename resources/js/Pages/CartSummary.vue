<template>
    <div class="cart-summary">
        <h1>Přehled</h1>


        <div v-if="isLoading" class="loading">Načítání...</div>

        <div v-else>
            <div class="cart-items">
                <div v-for="item in items" :key="item.id" class="cart-item">
                    <h2>{{ item.product.name }}</h2>
                    <p>Počet: {{ item.quantity }}</p>
                    <p>Cena: {{ formatCurrency(item.price) }}</p>
                    <p>Celková cena: {{ formatCurrency(item.quantity * item.price) }}</p>
                </div>
            </div>

            <div class="summary">
                <p><strong>Celkem s DPH</strong> {{ formatCurrency(priceWithVat) }}</p>
                <p><strong>DPH: </strong> {{ formatCurrency(vat) }}</p>
                <p><strong>Celkem bez DPH</strong> {{ formatCurrency(totalPrice) }}</p>
                <p><strong>Měna:</strong> {{ currencyEmoji }} {{ currency }}</p>
            </div>


            <Button label="Zaplatit" severity="success" size="large" style="width: 100%;margin-top: 2%;" />


        </div>
    </div>
</template>

<script>
import MainLayout from '../Layouts/MainLayout.vue'
export default {
    layout: MainLayout
}
</script>
<script setup>
import { defineProps, computed, ref, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import { getEmojiByCurrencyCode } from 'country-currency-emoji-flags';
import axios from 'axios';
import Button from 'primevue/button';

const props = defineProps({
    items: Array,
    totalPrice: Number,
    currency: String,
    userId: String,
});

const vatRate = 0.21;
const isLoading = ref(true);
const exchangeRates = ref({});
const selectedCurrency = ref(props.currency);



const vat = computed(() => {
    return props.totalPrice * vatRate
})
const priceWithVat = computed(() => {
    return vat.value + parseFloat(props.totalPrice)
})

const fetchExchangeRates = async () => {
    try {
        const response = await axios.get('/api/exchange-rates');
        exchangeRates.value = response.data.data;
    } catch (error) {
    } finally {
        isLoading.value = false;
    }
};

const currencyEmoji = computed(() => {
    return getEmojiByCurrencyCode(props.currency) || '';
});

const exchangeRate = computed(() => {
    return exchangeRates.value[selectedCurrency.value] || 1;
});

const totalPriceIncludingVat = computed(() => {
    return props.totalPrice * (1 + vatRate) * exchangeRate.value;
});

const checkoutUrl = computed(() => {
    const currency = props.currency !== 'CZK' ? `&currency=${props.currency}` : '';
    return `/cart/purchase/${props.userId}?price=${totalPriceIncludingVat.value}${currency}`;
});

const formatCurrency = (value) => {
    const formattedValue = value * exchangeRate.value;
    return new Intl.NumberFormat('cs-CZ', {
        style: 'currency',
        currency: selectedCurrency.value,
    }).format(formattedValue);
};

onMounted(fetchExchangeRates);
</script>

<style scoped>
.cart-summary {
    max-width: 1000px;
    margin: 0 auto;
    padding: 1.5rem;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    font-family: 'Arial', sans-serif;
}

.cart-summary h1 {
    margin-bottom: 1rem;
    font-size: 2rem;
    color: #111111;
    font-weight: bold;
}

.cart-items {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}

.cart-item {
    flex: 1 1 calc(33.333% - 1rem);
    box-sizing: border-box;
    border: 1px solid #e0e0e0;
    padding: 0.5rem;
    background-color: #f9f9f9;
    border-radius: 8px;
    text-align: left;
}

.cart-item h2 {
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
    color: #333333;
}

.cart-item p {
    margin: 0.25rem 0;
    color: #666666;
}

.summary {
    margin-top: 1.5rem;
}

.summary p {
    font-size: 1rem;
    margin: 0.5rem 0;
    color: #333333;
}

.checkout-button {
    display: block;
    margin-top: 1.5rem;
    padding: 0.75rem 1.5rem;
    background-color: #000000;
    color: #ffffff;
    text-decoration: none;
    border-radius: 4px;
    font-weight: bold;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.checkout-button:hover {
    background-color: #333333;
    transform: translateY(-1px);
}

.loading {
    font-size: 1.25rem;
    text-align: center;
    margin: 2rem 0;
    color: #666666;
}
</style>
