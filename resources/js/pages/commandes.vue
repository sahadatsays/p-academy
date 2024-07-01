<script setup>
const options = ref({})

const orderList = ref([])
const totalOrders = ref(0)
const loading = ref(false)
const perPage = ref(0)
const search = ref('')

// headers
const headers = [
  {
    title: 'ID',
    key: 'id',
  },
  {
    title: 'Lib',
    key: 'lib',
  },
  {
    title: 'Price',
    key: 'prix',
  },
  {
    title: 'User ID',
    key: 'user_id',
  },
  {
    title: 'Username',
    key: 'username',
  },
  {
    title: 'Email',
    key: 'email',
  },
  {
    title: 'Payment',
    key: 'paiement',
  },
  {
    title: 'Date',
    key: 'date',
  },
]

const fetchMembers = async () => {
  loading.value = true

  const response = await $api('/admin/orders', {
    query: options.value,
    onResponseError({ response }) {
      console.log(response)
    },
  })

  // assign Response
  orderList.value = response.data 
  totalOrders.value = response.meta.total
  perPage.value = response.meta.per_page
  loading.value = false
}

watch(options, fetchMembers, { deep: true })
</script>

<template>
  <div>
    <VCard title="Commandes">
      <VCardText>
        <VRow>
          <VCol
            cols="12"
            offset-md="8"
            md="4"
          >
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
        :items="orderList"
        loading-text="Loading... Please wait"
        :loading="loading"
        :items-length="totalOrders"
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
                placeholder="ID"
                style="width: 5rem;"
              /> 
            </td>
            <td>
              <AppTextField 
                v-model="options.lib"
                append-inner-icon="tabler-search"
                density="compact"
                single-line
                hide-details
                dense
                outlined
                placeholder="Lib"
              />
            </td>
            <td />
            <td>
              <AppTextField 
                v-model="options.user_id"
                append-inner-icon="tabler-search"
                density="compact"
                single-line
                hide-details
                dense
                outlined
                placeholder="ID"
              />
            </td>
            <td>
              <AppTextField 
                v-model="options.username"
                append-inner-icon="tabler-search"
                density="compact"
                single-line
                hide-details
                dense
                outlined
                placeholder="Username"
              />
            </td>
            <td>
              <AppTextField 
                v-model="options.email"
                append-inner-icon="tabler-search"
                density="compact"
                single-line
                hide-details
                dense
                outlined
                placeholder="Email"
              />
            </td>
            <td colspan="3" />
          </tr>
        </template>

        <!-- User ID -->
        <template #item.user_id="{ item }">
          <div class="d-flex align-center">
            {{ item.user.id }}
          </div>
        </template>

        <!-- User ID -->
        <template #item.username="{ item }">
          <div class="d-flex align-center">
            {{ item.user.username }}
          </div>
        </template>

        <!-- User Email -->
        <template #item.email="{ item }">
          <div class="d-flex align-center">
            {{ item.user.email }}
          </div>
        </template>
      </VDataTableServer>
    </VCard>
  </div>
</template>
