<script setup>
const props = defineProps({
  allTransfertPpa: {
    type: Array,
    required: true,
  },
})

// headers
const headers = [
  {
    title: "Id",
    key: "id",
  },
  {
    title: "Expediteur",
    key: "expediteur",
  },
  {
    title: "Destinataire",
    key: "destinataire",
  },
  {
    title: "PPA",
    key: "ppa",
  },
  {
    title: "Motif",
    key: "motif",
  },
  {
    title: "Categ",
    key: "categ",
  },
  {
    title: "crée le",
    key: "createdAt",
  },
]
</script>

<template>
  <VCard>
    <VDataTable
      :headers="headers"
      :items="props.allTransfertPpa"
    />
  </VCard>
</template>
