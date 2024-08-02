<template>
    <div class="currency-selector">
      <label for="currency-select" class="sr-only">Select Currency</label>
      <select
        id="currency-select"
        v-model="selectedCurrency"
        @change="convertPrices"
        class="currency-dropdown"
      >
        <option v-for="(rate, currency) in exchangeRates" :key="currency" :value="currency">
          {{ currency  }} {{ getEmojiByCurrencyCode(currency) }}
          </option>
      </select>
    </div>
  </template>




  <script setup>
  import { ref, computed } from 'vue';
  import {
  getEmojiByCountryCode,
  getEmojiByCurrencyCode,
  countryData,
  currencyData,
} from "country-currency-emoji-flags";

  const props = defineProps({
    exchangeRates: Object,
    selectedCurrency: String,
  });

  const emit = defineEmits(['update:currency']);

  const convertPrices = () => {

    emit('update:currency', selectedCurrency.value);
  };

  const selectedCurrency = ref(props.selectedCurrency);

  </script>

  <style scoped>
  .currency-selector {
    margin: 1rem 0;
    position: relative;
    display: flex;
    justify-content: left;
  }

  .currency-dropdown {
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
    border-radius: 4px;
    padding: 0.5rem 1rem;
    font-size: 1rem;
    color: #495057;
    cursor: pointer;
    width: 120px;
  }

  .currency-dropdown:focus {
    outline: none;
    border-color: #007bff;
    background-color: #e9ecef;
    box-shadow: 0 0 0 0.2rem rgba(38, 143, 255, 0.25);
  }

  .currency-dropdown option {
    padding: 0.5rem 1rem;
  }
  </style>
