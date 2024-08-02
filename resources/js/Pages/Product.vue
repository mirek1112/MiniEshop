<template>
    <Toast />
    <div class="product-page">

        <Toast position="bottom-center" group="bc" @close="onClose">
            <template #message="slotProps">
                <div class="flex flex-col items-start flex-auto">
                    <div class="flex items-center gap-2">
                        <span class="font-bold">{{ slotProps.message.summary }}</span>
                    </div>
                    <div class="font-medium text-lg my-4">Počet položek: {{ cartItems }}</div>
                    <Button size="small" label="Přejít" severity="info" @click="onReply()" />
                </div>
            </template>
        </Toast>

        <div class="product-image-gallery">

            <img :src="selectedImage" alt="Product Image" class="main-image" />

        </div>


        <div class="product-info">
            <h1>{{ product.name }}</h1>

            <Tag severity="info" style="width: fit-content;">{{ product.category ? product.category.name : 'No Category'
                }}
            </Tag>
            <Tag :value="'Počet kusů ' + product.quantity" :severity="getSeverity(product)"
                class=" dark:!bg-surface-900" style="left: 4px; top: 4px;width: fit-content;"></Tag>


            <p class="product-description">{{ product.description }}</p>
            <p class="product-price">${{ product.price }}</p>

            <Rating v-if="!visibleRating" v-model="rating" readonly cancel="false" />

            <span v-if="visibleRating">Vaše hodnoceni

                <Rating v-if="visibleRating" v-model="product.user_rating"
                    @change="newRating => changeRating(product.id, newRating.value)" cancel="false" />
            </span>



            <div class="actionButtons">
                <Button style="  flex-grow: 20;" label="Koupit" @click="buyItem(product.id)" severity="success" />
                <Button style="  flex-grow: 1;" label="Ohodnotit" severity="secondary"
                    @click="changeCheckSeenStatus()" />
            </div>

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
import { computed, ref } from 'vue';
import Toast from 'primevue/toast';
import { useToast } from "primevue/usetoast";
import Rating from 'primevue/rating';
import Button from "primevue/button";
import axios from "axios";
import { router } from '@inertiajs/vue3'
import Tag from 'primevue/tag';


const props = defineProps({
    info: {
        type: Object,
        required: true
    }
});


const product = ref(props.info)

const selectedImage = ref(product.value.image);
const cartItems = ref(0);
const toast = useToast();
let messageTimeout = null;
const visible = ref(false);
const visibleRating = ref(false);

const rating = computed(() => {

    if (product.value.average_rating == 0)
        return 0;
    else
        return product.value.average_rating
});

const changeCheckSeenStatus = () => {

    visibleRating.value = !visibleRating.value;
};

const buyItem = async (itemId) => {

    try {
        const response = await axios.post(`/cart/add/${itemId}`);
        if (response.data.success == true) {
            cartItems.value = response.data.cartitems;
            showTemplate();
            product.value.quantity = response.data.quantity;


            showMultiple(response.data.success);
        } else if (response.data.success == "noquantity") {
            showMultiple(response.data.success);
        } else {
            showMultiple(response.data.success);
        }
    } catch (error) {
    }
};

const showTemplate = () => {
    if (!visible.value) {
        toast.add({ severity: 'info', summary: 'Přejít do košíku?', group: 'bc' });
        visible.value = true;
    }
};

const onReply = async () => {

    router.get('/cart');
};

const onClose = () => {
    visible.value = false;
};

const hideMessageAfterTimeout = () => {
    if (messageTimeout) clearTimeout(messageTimeout);
    messageTimeout = setTimeout(() => {
        message.value = '';
    }, 1000);
};

const showMultiple = (status) => {
    switch (status) {
        case true:
            toast.add({ severity: 'success', summary: 'Přidáno', detail: 'Položka byla přidána', life: 3000 });
            break;
        case "noquantity":
            toast.add({ severity: 'warn', summary: 'Chyba', detail: 'Položka není na skladu', life: 3000 });
            break;
        case "addedrating":
            toast.add({ severity: 'info', summary: 'Hodnocení', detail: 'Hodnocení bylo přidáno', life: 3000 });
            break;
        default:
            toast.add({ severity: 'error', summary: 'Chyba', detail: 'Došlo k chybě', life: 3150 });
    }
};

const changeRating = async (id, rating) => {
    try {
        const response = await axios.post(`/product/${id}/rate`, { rating });
        if (response.data.success) {

            product.value.average_rating = response.data.average_rating;
            showMultiple('addedrating');
            visibleRating.value = false;
        }
    } catch (error) {

    }
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

<style scoped>
html,
body {
    margin: 0;
    height: 100%;
    font-family: 'Roboto', sans-serif;
    background-color: #f0f2f5;
}

.actionButtons {

    display: flex;
    gap: 3%;
    align-items: center;
    justify-content: center;
    align-content: stretch;
}

.product-page {
    display: flex;
    flex-direction: row;
    gap: 30px;
    padding: 30px;
    max-width: 1200px;
    margin: 0 auto;
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    height: 90vh;
    align-items: center;
}

.product-image-gallery {
    height: 100%;
    width: 100%;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 15px;
    align-items: center;
    object-fit: contain;
}

.main-image {

    height: 100%;
    width: 100%;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.main-image:hover {
    transform: scale(1.05);
}

.thumbnail-gallery {
    display: flex;
    gap: 10px;
    margin-top: 10px;
    justify-content: center;
}

.thumbnail {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 5px;
    cursor: pointer;
    border: 2px solid transparent;
    transition: border-color 0.3s ease, transform 0.3s ease;
}

.thumbnail:hover {
    border-color: #e60023;
    transform: scale(1.1);
}

.product-info {
    flex: 1;
    padding: 30px;
    display: flex;
    flex-direction: column;
    gap: 15px;
    justify-content: center;
}

.product-info h1 {
    font-size: 2.5em;
    margin: 0 0 15px 0;
    color: #333333;
}

.product-description {
    font-size: 1.1em;
    color: #666666;
    line-height: 1.6;
}

.product-price {
    font-size: 2em;

    font-weight: bold;
}

.product-quantity,
.product-category {
    font-size: 1.1em;
    color: #555555;
}

.add-to-cart {
    padding: 15px 30px;
    background-color: #e60023;
    color: #ffffff;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1.2em;
    font-weight: bold;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
    align-self: flex-start;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.add-to-cart:hover {
    background-color: #d5001c;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

.rating-component {
    display: flex;
    gap: 5px;
    margin-top: 10px;
}

@media (max-width: 768px) {
    .product-page {
        flex-direction: column;
        align-items: center;
        padding: 20px;
        height: auto;
    }

    .product-image-gallery {
        width: 100%;
    }

    .product-info {
        width: 100%;
        padding: 20px;
    }

    .main-image {
        max-width: 100%;
    }

    .thumbnail-gallery {
        flex-direction: row;
        justify-content: center;
    }

    .thumbnail {
        width: 50px;
        height: 50px;
    }
}
</style>
