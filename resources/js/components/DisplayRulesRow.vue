<script setup>
const props = defineProps({
  formRow: {
    type: Object,
    required: true,
  },
});

const displayList = [
  { title: "Show", value: 1 },
  { title: "Hide", value: 0 },
];

const typeList = [
  { title: "Tag", value: "tag" },
  { title: "Article", value: "article" },
  { title: "URL", value: "url" },
  { title: "Home", value: "home" },
  { title: "Language", value: "language" },
];
</script>

<template>
  <VRow class="my-2">
    <VCol cols="12" md="2" class="flex">
      <v-select
        v-model="props.formRow.display"
        :items="displayList"
        item-title="title"
        item-value="value"
      ></v-select>
    </VCol>
    <VCol cols="12" md="2">
      <v-select
        v-model="props.formRow.type"
        :items="typeList"
        item-title="title"
        item-value="value"
        @input="() => console.log('changed')"
      ></v-select>
    </VCol>
  </VRow>
</template>
