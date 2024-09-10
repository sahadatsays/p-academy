<script setup>
const form = ref({
  lang: "fr_FR",
  state: 0,
  content: "",
});

const route = useRoute("article-edit-id");

const langs = ref([]);
const errors = ref([]);
const hasError = ref(false);
const error = ref("");
const successMessage = ref("");
const hasSuccess = ref(false);
const mediaLoading = ref(false);
const categoryTree = ref([]);
const selectedCategories = ref([]);
const selectedFile = ref(null);
const articleMedias = ref([]);

const getEditData = async (id) => {
  const response = await $api(`/admin/articles/${id}/edit`);
  const data = response.data;
  const { article } = data;

  form.value.title = article.title;
  form.value.content = article.content;
  form.value.id = article.id;
  form.value.publish_down = article.publish_down;
  form.value.publish_up = article.publish_up;
  form.value.view_name = article.view_name;
  form.value.state = article.state;
  form.value.order = article.order;
  form.value.rules = article.rules;
  form.value.group_lang_id = article.group_lang_id;
  form.value.metakeywords = article.metakeywords;
  form.value.metadescription = article.metadescription;
  form.value.user_id = article.user_id;
  form.value.page_title = article.page_title;
  form.value.slug = article.slug;
  form.value.root_url = data.root_url;
  form.value.active_url = article.urlsite?.url;
  articleMedias.value = data.medias;

  //   other set
  selectedCategories.value = data.article_categories;
  categoryTree.value = data.categoriesTree;
  console.log(data.medias);
};

onMounted(() => {
  getEditData(route.params.id);
});

const submitForm = async () => {
  const response = await $api(`/admin/articles/${route.params.id}`, {
    method: "PUT",
    body: form.value,
    onResponseError({ response }) {
      errors.value = response._data.errors;
      error.value = response._data.message;
      hasError.value = true;
    },
  });

  errors.value = {};
  successMessage.value = response.message;
  hasSuccess.value = response.success;

  //   router.push('/articles')
};

const fetchLangs = async () => {
  const res = await $api("/admin/fetch/langs");

  langs.value = res;
};

onMounted(() => {
  fetchLangs();
});

const currentTab = ref("content");

const fileUpload = async (event) => {
  mediaLoading.value = true;
  const file = event.target.files[0];

  const formData = new FormData();
  formData.append("media", file);
  formData.append("article_id", route.params.id);

  const options = {
    method: "POST",
    body: formData,
    onResponseError({ response }) {
      errors.value = response._data.errors;
      error.value = response._data.message;
      hasError.value = true;
      mediaLoading.value = false;
    },
  };

  const res = await $api(`/admin/articles/set/media`, options);

  mediaLoading.value = false;
  fetchMedias();
};

const fetchMedias = async () => {
    const res = await $api(`/admin/articles/fetch/medias/${route.params.id}`, {
        onResponseError({ response }) {
            console.log(response);
        }
    })
    articleMedias.value = res.data
}

const handleMediaKey = async (event, id) => {
  const option = {
    method: "POST",
    body: { key: event, media_id: id },
    onResponseError({ response }) {
      console.log(response);
    },
  };
  const res = await $api('/admin/articles/set/media/key', option);
  fetchMedias();
};
const deleteMedia = async (id) => {
    mediaLoading.value = true
    const res = await $api(`/admin/articles/delete/media/${id}`, {
        onResponseError({ response }) {
      console.log(response);
    },
    })
    mediaLoading.value = false
    fetchMedias();
}
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

    <VCard title="Edit Article">
      <template #append>
        <div class="mt-n4 me-n2">
          <VBtn :to="{ name: 'articles' }">
            <VIcon variant="tonal" icon="tabler-list" start />
            Articles
          </VBtn>
        </div>
      </template>

      <VTabs v-model="currentTab">
        <VTab>Content</VTab>
        <VTab>Properties</VTab>
        <VTab>Medias</VTab>
        <VTab>Categories</VTab>
        <VTab>Tags PA</VTab>
        <VTab>SEO & Status</VTab>
        <!-- <VTab>Custom Data</VTab> -->
      </VTabs>
      <VForm @submit.prevent="submitForm">
        <VCardText>
          <VWindow v-model="currentTab">
            <VWindowItem value="content">
              <VRow>
                <!-- 👉 Languages -->
                <VCol cols="12" md="2">
                  <AppSelect
                    v-model="form.lang"
                    label="Language"
                    :items="langs"
                    :error-messages="errors.lang"
                    item-value="default_locale"
                    item-title="english_name"
                    placeholder="Select Language"
                  />
                </VCol>

                <!-- 👉 Title -->
                <VCol cols="12" md="10">
                  <AppTextField
                    v-model="form.title"
                    label="Title"
                    placeholder="Write title"
                    :error-messages="errors.title"
                  />
                </VCol>

                <VCol cols="12" md="6">
                  <AppTextField
                    v-model="form.page_title"
                    label="Page Title"
                    placeholder="Write page title"
                    :error-messages="errors.page_title"
                  />
                </VCol>

                <!-- Content -->
                <VCol cols="12">
                  <VLabel class="mb-2"> Content </VLabel>
                  <TiptapEditor
                    v-model="form.content"
                    :model-value="form.content"
                    placeholder="Write Content"
                    class="border rounded"
                  />
                </VCol>
              </VRow>
              <VRow>
                <VCol cols="12" md="6">
                  <AppTextarea
                    v-model="form.metakeywords"
                    label="Meta Keywords"
                    placeholder="Write meta keywords"
                  />
                </VCol>
                <VCol cols="12" md="6">
                  <AppTextarea
                    v-model="form.metadescription"
                    label="Meta Description"
                    placeholder="Write meta description"
                  />
                </VCol>
              </VRow>
            </VWindowItem>
            <VWindowItem value="properties">
              <VRow>
                <VCol cols="12" md="3">
                  <AppSelect
                    v-model="form.state"
                    label="State"
                    :items="[
                      { label: 'Non publié', value: 0 },
                      { label: 'Publié', value: 1 },
                      { label: 'Corbeille', value: 2 },
                    ]"
                    :error-messages="errors.state"
                    item-value="value"
                    item-title="label"
                  />
                </VCol>

                <VCol cols="12" md="3">
                  <AppSelect
                    v-model="form.author"
                    label="Author"
                    :items="[{ name: 'Author', id: 1 }]"
                    :error-messages="errors.author"
                    item-value="id"
                    item-title="name"
                    placeholder="Choose Author"
                  />
                </VCol>

                <VCol cols="12" md="3">
                  <AppTextField
                    v-model="form.view_name"
                    label="View Name"
                    placeholder="Write view name"
                    :error-messages="errors.view_name"
                  />
                </VCol>

                <VCol cols="12" md="3">
                  <AppSelect
                    v-model="form.rules"
                    label="Rules"
                    :items="[
                      { label: 'Public', value: '' },
                      { label: 'Admin', value: 'admin' },
                      { label: 'Member', value: 'member' },
                      { label: 'DBR', value: 'DBR' },
                    ]"
                    :error-messages="errors.rules"
                    item-value="value"
                    item-title="label"
                    placeholder="Choose Rule"
                  />
                </VCol>

                <VCol cols="12" md="3">
                  <AppTextField
                    v-model="form.order"
                    type="number"
                    label="Order"
                    placeholder="Write order"
                    :error-messages="errors.order"
                  />
                </VCol>
                <VCol cols="12" md="3">
                  <AppTextField
                    v-model="form.group_lang_id"
                    type="number"
                    label="Language group"
                    placeholder="Write Language group"
                    :error-messages="errors.group_lang_id"
                  />
                </VCol>
                <VCol cols="12" md="2">
                  <AppDateTimePicker
                    v-model="form.publish_up"
                    label="Start Publication"
                    :model-value="form.publish_up"
                    placeholder="Start Publication"
                    :error-messages="errors.publish_up"
                    prepend-inner-icon="tabler-calendar"
                    :config="{
                      enableTime: true,
                      dateFormat: 'Y-m-d H:i',
                    }"
                  />
                </VCol>
                <VCol cols="12" md="2">
                  <AppDateTimePicker
                    v-model="form.publish_down"
                    label="Start Publication"
                    :model-value="form.publish_down"
                    placeholder="End Publication"
                    :error-messages="errors.publish_down"
                    prepend-inner-icon="tabler-calendar"
                    :config="{
                      enableTime: true,
                      dateFormat: 'Y-m-d H:i',
                    }"
                  />
                </VCol>
              </VRow>
            </VWindowItem>
            <VWindowItem value="medias">
              <VRow>
                <VCol cols="12" md="6">
                  <VFileInput
                    show-size
                    :loading="mediaLoading"
                    color="primary"
                    label="File input"
                    variant="outlined"
                    @change="fileUpload"
                  />
                </VCol>
              </VRow>
              <VRow>
                <VCol cols="12" md="3" v-for="(media, index) in articleMedias">
                  <VCard>
                    <div class="pa-1">
                      <VSelect
                        density="compact"
                        :items="[
                          { label: 'Index', key: 'index' },
                          { label: 'Gallery', key: 'gallery' },
                          { label: 'Module', key: 'module' },
                          { label: 'Title', key: 'title' },
                        ]"
                        item-title="label"
                        item-value="key"
                        :model-value="media.key"
                        @update:modelValue="handleMediaKey($event, media.id)"
                      />
                    </div>
                    <VImg :src="media.url" :alt="media.media_file_name" />
                    <IconBtn :disabled="mediaLoading" size="small" @click="deleteMedia(media.id)">
                        <VIcon size="24" icon="tabler-trash"/>
                    </IconBtn>
                  </VCard>
                </VCol>
              </VRow>
            </VWindowItem>
            <VWindowItem value="categories">
              <VRow>
                <VCol cols="12">
                  <CategoryTreeView
                    v-for="category in categoryTree"
                    :key="category.id"
                    :category="category"
                    :article-id="route.params.id"
                    :selected-categories="selectedCategories"
                    @update:selected-categories="selectedCategories = $event"
                  />
                </VCol>
              </VRow>
            </VWindowItem>
            <VWindowItem value="tagas-pa">
              <VRow>
                <VCol cols="12">
                  <!-- <p>Tags PA</p> -->
                </VCol>
              </VRow>
            </VWindowItem>
            <VWindowItem value="seo-status">
              <VRow>
                <VCol cols="12" md="6">
                  <AppTextField
                    v-model="form.slug"
                    label="Slug"
                    placeholder="Write Slug"
                    :error-messages="errors.slug"
                  />
                </VCol>
                <VCol cols="12" md="6">
                  <AppTextField
                    v-model="form.root_url"
                    label="Root"
                    placeholder="Root url"
                    :error-messages="errors.root_url"
                  />
                </VCol>
                <VCol cols="12" md="12">
                  <AppTextField
                    v-model="form.active_url"
                    label="Active URL"
                    placeholder="Active url"
                    :error-messages="errors.active_url"
                  />
                </VCol>
                <VCol cols="12" md="12">
                  <AppTextField
                    v-model="form.old_url"
                    label="Old URL"
                    placeholder="Old url"
                    :error-messages="errors.old_url"
                  />
                </VCol>
              </VRow>
            </VWindowItem>
            <VWindowItem value="custom-data">
              <VRow>
                <VCol cols="12">
                  <p>Custom Data</p>
                </VCol>
              </VRow>
            </VWindowItem>
          </VWindow>
        </VCardText>
        <VCardActions>
          <VBtn variant="flat" color="primary" type="submit">
            <VIcon icon="tabler-device-floppy" class="mr-1" />
            Save Change
          </VBtn>
        </VCardActions>
      </VForm>
    </VCard>
  </div>
</template>
