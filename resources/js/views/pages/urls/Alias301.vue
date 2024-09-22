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
    title: "Alias",
    key: "alias",
    width: '500px'
  },
  {
    title: "URL",
    key: "url",
    width: '500px'
  },
  {
    title: "Reason",
    key: "reason",
  },
  {
    title: "Hits",
    key: "hits",
  },
  {
    title: "Created At",
    key: "created_at",
  },
  {
    title: "Bot Visit",
    key: "bot_visit",
  },
  {
    title: "#",
    key: "actions",
    sortable: false,
  },
];

const fetchData = async () => {
  loading.value = true;

  const response = await $api("/admin/url-301", {
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

watch(options, fetchData, { deep: true });

const deleteAction =  id => {
  confirmAlert.value = {
    confirm: true,
    actionUrl: `/admin/url-301/${id}`,
  }
}
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
        <VCol cols="12" md="2">
            <VBtn :to="{ name: 'urls-create'}">
                <VIcon icon="tabler-plus"></VIcon>
                Add Alias
            </VBtn>
        </VCol>
      <VCol cols="12" offset-md="6" md="4">
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
            v-model="options.alias"
            append-inner-icon="tabler-search"
            density="compact"
            single-line
            hide-details
            dense
            outlined
          />
        </td>
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
        <td colspan="7" />
      </tr>
    </template>

    <!-- Alias -->
    <template #item.alias="{ item }">
      <div class="d-flex align-center">
        <a :href="item.alias" target="__blank">{{ item.alias }}</a>
      </div>
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
        <IconBtn :to="{ name: 'urls-edit-id', params: { id: item.id } }">
          <VIcon icon="tabler-edit" />
        </IconBtn>
        <IconBtn @click="deleteAction(item.id)">
          <VIcon icon="tabler-square-x" />
        </IconBtn>
      </div>
    </template>
  </VDataTableServer>
</template>
