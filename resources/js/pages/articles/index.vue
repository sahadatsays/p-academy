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
    title: 'Title',
    key: 'title',
  },
  {
    title: 'Tags',
    key: 'tags',
  },
  {
    title: 'Created At',
    key: 'createdAt',
  },
  {
    title: 'Updated',
    key: 'updated_at',
  },
  {
    title: 'Created By',
    key: 'createdBy',
  },
  {
    title: 'State',
    key: 'state',
  },
  {
    title: 'Order',
    key: 'order',
  },
  {
    title: 'Rules',
    key: 'rules',
  },
  {
    title: 'Views',
    key: 'hits',
  },
  {
    title: '#',
    key: 'actions',
    sortable: false,
  },
]

const fetchData = async () => {
  loading.value = true

  const response = await $api('/admin/articles', {
    query: options.value,
    onResponseError({ response }) {
      console.log(response)
    },
  })

  // assign Response
  dataList.value = response.data 
  totalData.value = response.meta.total
  perPage.value = response.meta.per_page
  loading.value = false
}

const deleteAction =  id => {
  confirmAlert.value = {
    confirm: true,
    actionUrl: `/admin/articles/${id}`,
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

    <VCard title="Articles">
      <VCardText>
        <VRow>
          <VCol
            cols="12"
            md="2"
          >
            <div>
              <VBtn
                block
                :to="{ name: 'articles-create' }"
              >
                <VIcon 
                  icon="tabler-plus"
                  start
                />
                New Article  
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
                v-model="options.title"
                append-inner-icon="tabler-search"
                density="compact"
                single-line
                hide-details
                dense
                outlined
              />
            </td>
            <td colspan="4" />
            <td>
              <AppSelect
                v-model="options.activated"
                :items="['None', 'Active', 'Inactive']"
                density="compact"
                placeholder="Select"
                item-title="title"
                item-value="value"
                style="width: 5rem;"
              />
            </td>
            <td colspan="4" />
          </tr>
        </template>
        <template #item.tags="{ item }">
          <div class="d-flex align-center">
            <div 
              v-for="tag in item.tags" 
              :key="tag.id" 
              class="d-flex flex-column ms-3"
            >
              <VChip label>
                {{ tag.name }}
              </VChip>
            </div>
          </div>
        </template>
        <!-- activated -->
        <template #item.activated="{ item }">
          <div class="d-flex align-center">
            <VChip v-if="item.activated">
              <VIcon icon="tabler-circle-check text-2xl" />
            </VChip>
          </div>
        </template>
        <!-- actions -->
        <template #item.actions="{ item }">
          <div class="d-flex align-center">
            <IconBtn>
              <VIcon icon="tabler-eye" />
            </IconBtn>
            <IconBtn :to="{name: 'articles-edit-id', params: {id: item.id }}">
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
