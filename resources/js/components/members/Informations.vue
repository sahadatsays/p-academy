<script setup>
import AppDateTimePicker from '@/@core/components/app-form-elements/AppDateTimePicker.vue'

const props = defineProps({
  member: {
    type: Object,
    required: true,
  },
  user: {
    type: Object,
    required: true,
  },
  status: {
    type: Object,
    required: true,
  },
})

const successMessage = ref('')
const hasSuccess = ref(false)
const isShow = ref(false)

const memberStatusForm = ref({
  level: '',
  ending_at: '',
  motifchange: '',
})

const levels = [
  {
    value: 'membre',
    title: 'Member',
  },
  {
    value: 'silver',
    title: 'Silver',
  },
  {
    value: 'gold',
    title: 'Gold',
  },
]

const statusUpdate = async userId => {
  const response = await $api(`/admin/members/status/${userId}`, {
    method: 'PUT',
    body: memberStatusForm.value,
  })

  successMessage.value = response.message
  hasSuccess.value = response.success
}

watchEffect(() => {
  memberStatusForm.value.level = props.status.level 
  memberStatusForm.value.ending_at = props.status.ending_at 
  memberStatusForm.value.motifchange = props.status.motifchange
})
</script>

<template>
  <VSnackbar
    v-model="hasSuccess"
    location="top end"
    color="success"
  >
    <VIcon icon="tabler-exclamation-circle" />
    {{ successMessage }}
  </VSnackbar>

  <VTable class="border">
    <thead>
      <tr>
        <th>Identifier</th>
        <th>Nom</th>
        <th>Email</th>
        <th>Solde PPA</th>
        <th>Solde Punibet</th>
        <th>Solde Pevbc</th>
        <th>Inscrit le</th>
        <th>Actif le</th>
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
  </VTable>
  <VDivider class="my-4" />
  <p>Statut du membre</p>
  <VTable class="border">
    <thead>
      <tr>
        <th>Level</th>
        <th>Ending At</th>
        <th>Period</th>
        <th>#</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>{{ status.level }}</td>
        <td>{{ status.ending_at }}</td>
        <td>{{ user.plan }}</td>
        <td>
          <VBtn
            size="small"
            @click="() => isShow = !isShow"
          >
            Modifier
          </VBtn>
        </td>
      </tr>
      <tr v-if="isShow">
        <td>
          <VSelect 
            v-model="memberStatusForm.level" 
            :items="levels"
            item-value="value"
            item-title="title"
          />
        </td>
        <td>
          <AppDateTimePicker
            v-model="memberStatusForm.ending_at"
            class="calendar-date-picker"
          />    
        </td>
        <td>
          <VTextField v-model="memberStatusForm.motifchange" />  
        </td>
        <td>
          <VBtn
            size="small"
            @click="statusUpdate(user.id)"
          >
            Enregistrer
          </VBtn>
        </td>
      </tr>
    </tbody>
  </VTable>
</template>
