<template>
  <div>
    <VCheckbox
      :label="category.name"
      :model-value="isSelected"
      @update:model-value="toggleSelection"
    />
    <div
      v-if="category.children && category.children.length > 0"
      class="pl-5"
    >
      <CategoryTreeView
        v-for="child in category.children"
        :key="child.id"
        :article-id="articleId"
        :category="child"
        :selected-categories="selectedCategories"
        @update:selected-categories="updateSelectedCategories"
      />
    </div>
  </div>
</template>
  
<script setup>
import { defineProps, defineEmits, computed } from 'vue'
  
const props = defineProps({
  category: Object,
  selectedCategories: Array,
  articleId: String,
})
  
const emit = defineEmits(['update:selectedCategories'])
  
const isSelected = computed(() => {
  return props.selectedCategories.includes(props.category.id)
})
  
const toggleSelection = async value => {
  if (value) {
    emit('update:selectedCategories', [...props.selectedCategories, props.category.id])
    await setCategoryId(true)
  } else {
    emit('update:selectedCategories', props.selectedCategories.filter(id => id !== props.category.id))
    await setCategoryId(false)
  }
}
  
const updateSelectedCategories = newSelectedCategories => {
  emit('update:selectedCategories', newSelectedCategories)
}

const setCategoryId = async isAdding => {
  const res = await $api('/admin/articles/set/tag', {
    method: 'POST',
    body: { tag_id: props.category.id, article_id: props.articleId },
  })
    
}
</script>
