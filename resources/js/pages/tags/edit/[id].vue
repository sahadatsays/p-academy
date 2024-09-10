<!-- eslint-disable camelcase -->
<script setup>
const form = ref({});
const langsTagsForm = ref([])
const langs = ref([]);
const route = useRoute("tags-edit-id");
const router = useRouter();

const parentTags = ref([]);
const errors = ref([]);
const hasError = ref(false);
const error = ref("");
const successMessage = ref("");
const hasSuccess = ref(false);
const currentTab = ref("properties");
const tagsTranslations = ref([]);

const submitForm = async () => {
  const response = await $api(`/admin/tags/${route.params.id}`, {
    method: "PUT",
    body: {...form.value, translations: langsTagsForm.value},
    onResponseError({ response }) {
      errors.value = response._data.errors;
      error.value = response._data.message;
      hasError.value = true;
    },
  });

  errors.value = {};
  successMessage.value = response.message;
  hasSuccess.value = response.success;
};

const getEditData = async (id) => {
  const response = await $api(`/admin/tags/${id}/edit`);
  const { tag, translations } = response.data;

  const data = {
    name: tag.name,
    in_url: tag.in_url,
    parent_id: tag.parent_id == 0 ? null : tag.parent_id,
    index_page: tag.index_page,
    order: tag.order,
    pa_menu_hide: tag.pa_menu_hide,
    per_page: tag.per_page,
    rules: tag.rules,
    sort_by: tag.sort_by,
    view_name: tag.view_name,
    // content1: translation.content1 == "" ? null : translation.content1,
    // content2: translation.content2 == "" ? null : translation.content2,
    // lang: translation.lang,
    // title: translation.title,
  };
  langsTagsForm.value = translations;
  form.value = data;
};

const fetchTags = async () => {
  const res = await $api("/admin/fetch/tags");
  parentTags.value = res;
};

const fetchLangs = async () => {
  const res = await $api("/admin/fetch/langs");

  langs.value = res;
};

onMounted(() => {
  fetchTags();
  fetchLangs();
  getEditData(route.params.id);
});
</script>

<template>
  <div>
    <VSnackbar v-model="hasSuccess" location="top end" color="success">
      <VIcon icon="tabler-exclamation-circle" />
      {{ successMessage }}
    </VSnackbar>

    <VSnackbar v-model="hasError" location="top end" color="error">
      <VIcon icon="tabler-exclamation-circle" />
      {{ error }}
    </VSnackbar>

    <VForm @submit.prevent="submitForm">
      <VCard title="Edit Tag">
        <template #append>
          <div class="mt-n4 me-n2">
            <VBtn :to="{ name: 'tags' }">
              <VIcon variant="tonal" icon="tabler-list" start />
              Tags
            </VBtn>
          </div>
        </template>
        <VTabs v-model="currentTab">
          <VTab>Properties</VTab>
          <VTab v-for="translation in langsTagsForm">{{
            translation.lang
          }}</VTab>
        </VTabs>

        <VCardText>
          <VWindow v-model="currentTab">
            <VWindowItem value="properties">
              <VRow>
                <!-- 👉 Name -->
                <VCol cols="12" md="6">
                  <AppTextField
                    v-model="form.name"
                    label="Name"
                    placeholder="Tag Name"
                    :error-messages="errors.name"
                  />
                </VCol>

                <!-- 👉 Parent -->
                <VCol cols="12" md="6">
                  <AppSelect
                    v-model="form.parent_id"
                    label="Parent Tag"
                    :items="parentTags"
                    :error-messages="errors.parent_id"
                    item-value="id"
                    item-title="name"
                    placeholder="Select Parent"
                    name="parent_id"
                    required
                  />
                </VCol>

                <!-- 👉 Rules -->
                <VCol cols="12" md="3">
                  <AppSelect
                    v-model="form.rules"
                    label="Rules"
                    :items="[
                      { label: 'Public', value: '' },
                      { label: 'Admin', value: 'admin' },
                      { label: 'Member', value: 'member' },
                    ]"
                    item-title="label"
                    item-value="value"
                    :error-messages="errors.title"
                  />
                </VCol>
                <VCol cols="12" md="3">
                  <AppTextField
                    v-model="form.view_name"
                    label="View Name"
                    placeholder="View Name"
                    :error-messages="errors.view_name"
                  />
                </VCol>

                <VCol cols="12" md="6" class="d-flex pt-10">
                  <VCheckbox
                    v-model="form.in_url"
                    :value="form.in_url"
                    label="Show in URL"
                    class="me-10"
                  />
                  <VCheckbox
                    v-model="form.index_page"
                    :value="form.index_page"
                    label="Index Page"
                    class="me-10"
                  />
                  <VCheckbox
                    v-model="form.pa_menu_hide"
                    :value="form.pa_menu_hide"
                    label="Hide menu pa"
                  />
                </VCol>
                <VCol cols="12" md="4">
                  <AppTextField
                    v-model="form.order"
                    label="Order"
                    placeholder="Order"
                    :error-messages="errors.order"
                  />
                </VCol>
                <VCol cols="12" md="4">
                  <AppSelect
                    v-model="form.sort_by"
                    label="Sort By"
                    :items="[
                      { title: 'Order desc', value: 'order desc' },
                      { title: 'Order asc', value: 'order asc' },
                      { title: 'Date desc', value: 'publish_up desc' },
                      { title: 'Date asc', value: 'publish_up asc' },
                    ]"
                    :error-messages="errors.sort_by"
                  />
                </VCol>
                <VCol cols="12" md="4">
                  <AppTextField
                    v-model="form.per_page"
                    label="Per Page"
                    placeholder="Per page"
                    :error-messages="errors.per_page"
                  />
                </VCol>
              </VRow>
            </VWindowItem>
            <VWindowItem
              v-for="(translation, index) in langsTagsForm"
              :value="translation.lang"
            >
              <VRow>
                <!-- 👉 language -->
                <VCol cols="12" md="2">
                
                  <AppSelect
                    v-model="translation.lang"
                    label="Languages"
                    :items="langs"
                    :error-messages="errors.lang"
                    item-value="default_locale"
                    item-title="english_name"
                    placeholder="Select Language"
                    required
                  />
                </VCol>
                <VCol cols="12" md="5">
                  <AppTextField
                    v-model="translation.title"
                    label="Title"
                    placeholder="Title"
                    :error-messages="errors.title"
                  />
                </VCol>
                <VCol cols="12" md="5">
                  <AppTextField
                    v-model="translation.page_title"
                    label="Page Title"
                    placeholder="Page title"
                    :error-messages="errors.page_title"
                  />
                </VCol>
                <VCol cols="12" md="6">
                  <AppTextField
                    v-model="form.pa_menu_title"
                    label="Pa menu title"
                    placeholder="Pa menu title"
                    :error-messages="errors.pa_menu_title"
                  />
                </VCol>
                <VCol cols="12" md="6">
                  <AppTextField
                    v-model="form.old_url"
                    label="Old URL"
                    placeholder="Old Url"
                    :error-messages="errors.old_url"
                  />
                </VCol>
                <VCol cols="12">
                  <AppTextField
                    v-model="translation.slug"
                    label="Slug"
                    placeholder="Slug"
                    :error-messages="errors.slug"
                  />
                </VCol>
                <!-- Content 1 -->
                <VCol cols="12">
                  <VLabel class="mb-2"> Content 1 </VLabel>
                  <TiptapEditor
                    v-model="translation.content1"
                    :model-value="translation.content1"
                    label="Content 1"
                    placeholder="Write Content"
                    class="border rounded"
                  />
                </VCol>

                <!-- Content 2 -->
                <VCol cols="12">
                  <VLabel class="mb-2"> Content 2 </VLabel>
                  <TiptapEditor
                    v-model="translation.content2"
                    :model-value="translation.content2"
                    placeholder="Write Content"
                    class="border rounded"
                  />
                </VCol>

                <VCol cols="6">
                  <VTextarea
                    label="Meta Keywords"
                    v-model="translation.metakeywords"
                    placeholder="Write meta keywords"
                  />
                </VCol>
                <VCol cols="6">
                  <VTextarea
                    label="Meta Description"
                    v-model="translation.metadescription"
                    placeholder="Write meta description"
                  />
                </VCol>
              </VRow>
            </VWindowItem>
          </VWindow>
        </VCardText>
        <VCardActions>
          <VBtn type="submit" variant="flat" color="primary" class="px-5">
            <VIcon icon="tabler-device-floppy" class="mr-1" />
            Update
          </VBtn>
        </VCardActions>
      </VCard>
    </VForm>
  </div>
</template>
