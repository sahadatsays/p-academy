<script setup>
const options = ref({})

const dataList = ref([])
const totalData = ref(0)
const loading = ref(false)
const perPage = ref(0)
const search = ref('')

const confirmAlert = ref({
  confirm: false,
  actionUrl: '',
  redirect: '',
})

// headers
const headers = [
  {
    title: 'ID',
    key: 'id',
  },
  {
    title: 'Name',
    key: 'name',
  },
  {
    title: 'Parent',
    key: 'parent_name',
  },
  {
    title: 'Type',
    key: 'type',
  },
  {
    title: 'Articles',
    key: 'article_counts',
  },
  {
    title: 'Status',
    key: 'status',
  },
  {
    title: 'Created At',
    key: 'createdAt',
  },
  {
    title: 'Updated At',
    key: 'updatedAt',
  },
  {
    title: '#',
    key: 'actions',
    sortable: false,
  },
]

const fetchData = async () => {
  loading.value = true

  const response = await $api('/admin/patags', {
    query: options.value,
    onResponseError({ response }) {
      console.log(response)
    },
  })

  console.log(response.data)

  // assign Response
  dataList.value = response.data 
  totalData.value = response.meta.total
  perPage.value = response.meta.per_page
  loading.value = false
}

const deleteAction =  id => {
  confirmAlert.value = {
    confirm: true,
    actionUrl: `/admin/patags/${id}`,
  }
}

watch(options, fetchData, { deep: true })
</script>

<template>
  <div>
    <!-- 👉 Confirm Dialog -->
    <ConfirmDialog
      v-model:isDialogVisible="confirmAlert.confirm"
      :action-url="confirmAlert.actionUrl"
      @update-list="fetchData"
    />
      
    <VCard title="PA Tags">
      <VCardText>
        <VRow>
          <VCol
            cols="12"
            md="2"
          >
            <div>
              <VBtn
                block
                :to="{ name: 'pa-tags-create' }"
              >
                <VIcon 
                  icon="tabler-plus"
                  start
                />
                New PA Tag 
              </VBtn>
            </div>
          </VCol>
          <VCol
            cols="12"
            offset-md="6"
            md="4"
          >
            <AppTextField
              v-model="search"
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
        class="text-no-wrap"
      >
        <template #body.prepend>
          <tr>
            <td>
              <AppTextField
                v-model="options.id"
                density="compact"
                append-inner-icon="tabler-search"
                single-line
                hide-details
                dense
                outlined
                style="width: 5rem;"
              /> 
            </td>
            <td>
              <AppTextField 
                v-model="options.name"
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

        <!-- actions -->
        <template #item.actions="{ item }">
          <div class="d-flex align-center">
            <IconBtn :to="{ name: 'pa-tags-edit-id', params: { id: item.id }}">
              <VIcon icon="tabler-pencil" />
            </IconBtn>
            <IconBtn @click="deleteAction(item.id)">
              <VIcon icon="tabler-square-x" />
            </IconBtn>
          </div>
        </template>
      </VDataTableServer>
    </VCard>
  </div>
</template>
