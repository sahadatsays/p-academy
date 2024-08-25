<script setup>
const props = defineProps({
  payments: {
    type: Array,
    required: true,
  },
});
</script>

<template>
  <v-table class="border" v-if="props.payments.length != 0">
    <thead>
      <tr>
        <th>Id</th>
        <th>bank </th>
        <th>Item </th>
        <th>Item Id</th>
        <th>Price</th>
        <th>State</th>
        <th>date</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="payment in props.payments">
        <td>{{ payment.id }}</td>
        <td>{{ payment.bank }}</td>
        <td>{{ payment.item }}</td>
        <td>{{ payment.item_id }}</td>
        <td>{{ payment.prix }}</td>
        <td>
            <span v-if="payment.state == 1">payé</span>
            <span v-else>échoué</span>
        </td>
        <td>{{ payment.created_at }}</td>
      </tr>
    </tbody>
  </v-table>

  <p v-else>Aucunes commandes</p>
</template>
