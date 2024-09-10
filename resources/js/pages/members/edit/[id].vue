<!-- eslint-disable camelcase -->
<script setup>
import Affiliations from "@/components/members/Affiliations.vue"
import Commands from "@/components/members/Commands.vue"
import Informations from "@/components/members/Informations.vue"
import Payment from "@/components/members/Payment.vue"
import TransfertPPA from "@/components/members/TransfertPPA.vue"

const route = useRoute("members-edit-id")

const errors = ref([])
const hasError = ref(false)
const error = ref("")
const successMessage = ref("")
const hasSuccess = ref(false)
const member = ref({})
const commands = ref({})
const affiliations = ref({})
const payments = ref({})
const user = ref({})
const status = ref({})
const allTransfertPpa = ref({})

const getEditData = async id => {
  const response = await $api(`/admin/members/${id}`)

  user.value = response.user
  member.value = response.member
  status.value = response.status
  commands.value = response.commands
  affiliations.value = response.affiliations
  payments.value = response.payments
  allTransfertPpa.value = response.allTransfertPpa
}

onMounted(() => {
  getEditData(route.params.id)
})

const currentTab = ref("item-1")
</script>

<template>
  <div>
    <VSnackbar
      v-model="hasSuccess"
      location="top end"
      color="success"
    >
      <VIcon icon="tabler-exclamation-circle" />
      {{ successMessage }}
    </VSnackbar>

    <VSnackbar
      v-model="hasError"
      location="top end"
      color="error"
    >
      <VIcon icon="tabler-exclamation-circle" />
      {{ error }}
    </VSnackbar>

    <VCard title="Edit Member">
      <template #append>
        <div class="mt-n4 me-n2">
          <VBtn :to="{ name: 'members' }">
            <VIcon
              variant="tonal"
              icon="tabler-list"
              start
            />
            Members
          </VBtn>
        </div>
      </template>
      <VCardText>
        <VForm>
          <VTabs
            v-model="currentTab"
            grow
          >
            <VTab>information's</VTab>
            <VTab>Commands</VTab>
            <VTab>Paiements</VTab>
            <VTab>Affiliations</VTab>
            <VTab>Transfert PPA</VTab>
          </VTabs>

          <VCardText>
            <VWindow v-model="currentTab">
              <VWindowItem>
                <Informations
                  :member="member"
                  :user="user"
                  :status="status"
                />
              </VWindowItem>
              <VWindowItem>
                <Commands :commands="commands" />
              </VWindowItem>
              <VWindowItem>
                <Payment :payments="payments" />
              </VWindowItem>
              <VWindowItem>
                <Affiliations :affiliations="affiliations" />
              </VWindowItem>
              <VWindowItem>
                <TransfertPPA :all-transfert-ppa="allTransfertPpa" />
              </VWindowItem>
            </VWindow>
          </VCardText>
        </VForm>
      </VCardText>
    </VCard>
  </div>
</template>
