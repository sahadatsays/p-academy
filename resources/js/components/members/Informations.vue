<script setup>
const props = defineProps({
  member: {
    type: Object,
    required: true,
  },
  user: {
    type: Object,
    required: true,
  },
});

console.log(props.member);
</script>

<template>
  <v-table class="border" >
    <thead>
      <tr>
        <th>Identifier</th>
        <th>Nom</th>
        <th>Email</th>
        <th>solde ppa</th>
        <th>solde Punibet</th>
        <th>solde Pevbc</th>
        <th>inscrit le</th>
        <th>actif le</th>
      </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ user.username }}</td>
            <td>{{ user.name }}</td>
            <td>{{ user.email }}</td>
            <td>{{ member.ppa }}</td>
            <td>{{ member.punibet }}</td>
            <td>{{ member.pevbc }}</td>
            <td>{{ user.createdAt }}</td>
            <td>{{ user.lastActivity }}</td>
        </tr>
    </tbody>
  </v-table>
  <v-divider class="mt-4"/>
</template>
