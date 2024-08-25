<script setup>
const props = defineProps({
  commands: {
    type: Array,
    required: true,
  },
});

console.log(props.commands);
</script>

<template>
  <v-table class="border" v-if="props.commands.length != 0">
    <thead>
      <tr>
        <th>Id</th>
        <th>Lib</th>
        <th>Prix</th>
        <th>Paiment</th>
        <th>date</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="command in props.commands">
        <td>{{ command.id }}</td>
        <td>{{ command.lib }}</td>
        <td>{{ command.prix }}</td>
        <td>{{ command.paiement }}</td>
        <td>{{ command.created_at }}</td>
      </tr>
    </tbody>
  </v-table>

  <p v-else>Aucunes commandes</p>
</template>
