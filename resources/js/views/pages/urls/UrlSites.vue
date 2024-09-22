<script setup>
const options = ref({});

const dataList = ref([]);
const totalData = ref(0);
const loading = ref(false);
const perPage = ref(0);
const search = ref("");

const confirmAlert = ref({
  confirm: false,
  actionUrl: "",
  redirect: "",
});

// headers
const headers = [
  {
    title: "URL",
    key: "url",
    width: '500px'
  },
  {
    title: "Type",
    key: "type"
  },
  {
    title: "Hit",
    key: "hits",
  },
  {
    title: "Created At",
    key: "createdAt",
  },
  {
    title: "#",
    key: "actions",
    sortable: false,
  },
];

const fetchData = async () => {
  loading.value = true;

  const response = await $api("/admin/siteurls", {
    query: options.value,
    onResponseError({ response }) {
      console.log(response);
    },
  });

  // assign Response
  dataList.value = response.data;
  totalData.value = response.meta.total;
  perPage.value = response.meta.per_page;
  loading.value = false;
};

const deleteAction = (id) => {
  confirmAlert.value = {
    confirm: true,
    actionUrl: `/admin/siteurls/${id}`,
  };
};

watch(options, fetchData, { deep: true });
</script>

<template>
  <VCardText>
    <!-- 👉 Confirm Dialog -->
    <ConfirmDialog
      v-model:isDialogVisible="confirmAlert.confirm"
      :action-url="confirmAlert.actionUrl"
      @update-list="fetchData"
    />

    <VRow>
      <VCol cols="12" offset-md="8" md="4">
        <AppTextField
          v-model="search"
          density="compact"
          placeholder="Search ..."
          append-inner-icon="tabler-search"
          single-line
          hide-details
          dense
          outlined
        />
      </VCol>
    </VRow>
  </VCardText>

  <!-- 👉 Data Table  -->
  <VDataTableServer
    v-model:options="options"
    :search="search"
    :items-per-page="perPage"
    :headers="headers"
    :items="dataList"
    loading-text="Loading... Please wait"
    :loading="loading"
    :items-length="totalData"
    class="border-table"
  >
    <template #body.prepend>
      <tr>
        <td>
          <AppTextField
            v-model="options.url"
            append-inner-icon="tabler-search"
            density="compact"
            single-line
            hide-details
            dense
            outlined
          />
        </td>
        <td colspan="8" />
      </tr>
    </template>

    <!-- URL -->
    <template #item.url="{ item }">
      <div class="d-flex align-center">
        <a :href="item.url" target="__blank">{{ item.url }}</a>
      </div>
    </template>

    <!-- actions -->
    <template #item.actions="{ item }">
      <div class="d-flex align-center">
        <IconBtn>
          <VIcon icon="tabler-eye" />
        </IconBtn>
        <IconBtn @click="deleteAction(item.id)">
          <VIcon icon="tabler-square-x" />
        </IconBtn>
      </div>
    </template>
  </VDataTableServer>
</template>
