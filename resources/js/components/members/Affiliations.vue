<script setup>
const props = defineProps({
  affiliations: {
    type: Array,
    required: true,
  },
})
</script>

<template>
  <VTable
    v-if="props.affiliations.length != 0"
    class="border"
  >
    <thead>
      <tr>
        <th>room</th>
        <th>pseudo room</th>
        <th>status</th>
        <th>date</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="affiliation in props.affiliations">
        <td>{{ affiliation.room }}</td>
        <td>{{ affiliation.pseudo_room }}</td>
        <td>{{ affiliation.statut }}</td>
        <td>{{ affiliation.createdAt }}</td>
      </tr>
    </tbody>
  </VTable>

  <p v-else>
    Aucunes affiliations
  </p>
</template>
