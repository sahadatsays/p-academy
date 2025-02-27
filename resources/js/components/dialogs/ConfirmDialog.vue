<script setup>
const props = defineProps({
  confirmationQuestion: {
    type: String,
    default: 'Are you sure to delete ?',
  },
  isDialogVisible: {
    type: Boolean,
    required: true,
  },
  confirmTitle: {
    type: String,
    default: 'Deleted',
  },
  confirmMsg: {
    type: String,
    default: 'Your data deleted successfully.',
  },
  cancelTitle: {
    type: String,
    default: 'Cancelled',
  },
  cancelMsg: {
    type: String,
    default: 'Delete Cancelled!!',
  },
  actionUrl: {
    type: String,
    required: true,
  },
})

const emit = defineEmits([
  'update:isDialogVisible',
  'confirm',
  'updateList',
])

const confirmMessage = ref(props.confirmMsg)
const deleted = ref(false)
const cancelled = ref(false)

const updateModelValue = val => {
  emit('update:isDialogVisible', val)
}

const deleteAction = async () => {
  const res = await $api(props.actionUrl, {
    method: 'DELETE',
  })

  emit('updateList')
  confirmMessage.value = res.message
}

const onConfirmation = () => {
  emit('confirm', true)
  updateModelValue(false)
  deleted.value = true
  deleteAction()
}

const onCancel = () => {
  emit('confirm', false)
  emit('update:isDialogVisible', false)
  cancelled.value = true
}
</script>

<template>
  <!-- 👉 Confirm Dialog -->
  <VDialog
    max-width="500"
    :model-value="props.isDialogVisible"
    @update:model-value="updateModelValue"
  >
    <VCard class="text-center px-10 py-6">
      <VCardText>
        <VBtn
          icon
          variant="outlined"
          color="warning"
          class="my-4"
          style=" block-size: 88px;inline-size: 88px; pointer-events: none;"
        >
          <span class="text-5xl">!</span>
        </VBtn>

        <h6 class="text-lg font-weight-medium">
          {{ props.confirmationQuestion }}
        </h6>
      </VCardText>

      <VCardText class="d-flex align-center justify-center gap-2">
        <VBtn
          variant="elevated"
          @click="onConfirmation"
        >
          Confirm
        </VBtn>

        <VBtn
          color="secondary"
          variant="tonal"
          @click="onCancel"
        >
          Cancel
        </VBtn>
      </VCardText>
    </VCard>
  </VDialog>

  <!-- deleted -->
  <VDialog
    v-model="deleted"
    max-width="500"
  >
    <VCard>
      <VCardText class="text-center px-10 py-6">
        <VBtn
          icon
          variant="outlined"
          color="success"
          class="my-4"
          style=" block-size: 88px;inline-size: 88px; pointer-events: none;"
        >
          <VIcon
            icon="tabler-check"
            size="38"
          />
        </VBtn>

        <h1 class="text-h4 mb-4">
          {{ props.confirmTitle }}
        </h1>

        <p>{{ confirmMessage }}</p>

        <VBtn
          color="success"
          @click="deleted = false"
        >
          Ok
        </VBtn>
      </VCardText>
    </VCard>
  </VDialog>

  <!-- Cancelled -->
  <VDialog
    v-model="cancelled"
    max-width="500"
  >
    <VCard>
      <VCardText class="text-center px-10 py-6">
        <VBtn
          icon
          variant="outlined"
          color="error"
          class="my-4"
          style=" block-size: 88px;inline-size: 88px; pointer-events: none;"
        >
          <span class="text-5xl font-weight-light">X</span>
        </VBtn>

        <h1 class="text-h4 mb-4">
          {{ props.cancelTitle }}
        </h1>

        <p>{{ props.cancelMsg }}</p>

        <VBtn
          color="success"
          @click="cancelled = false"
        >
          Ok
        </VBtn>
      </VCardText>
    </VCard>
  </VDialog>
</template>
