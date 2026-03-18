<script setup lang="ts">
import { onClickOutside, useVModel } from '@vueuse/core'
import { computed, ref } from 'vue'
import { cn } from '@/lib/utils'
import Input from './input.vue'

interface Option {
  raw: string
  formatted: string
}

const props = defineProps<{
  modelValue?: string | null
  options: Option[]
  placeholder?: string
  disabled?: boolean
  class?: string
}>()

const emits = defineEmits<{
  (e: 'update:modelValue', payload: string): void
}>()

const modelValue = useVModel(props, 'modelValue', emits)

const containerRef = ref<HTMLElement | null>(null)
const isOpen = ref(false)
const search = ref('')

const filteredOptions = computed(() => {
  if (!search.value) return props.options
  return props.options.filter((option) =>
    option.formatted.toLowerCase().includes(search.value.toLowerCase()),
  )
})

const selectedLabel = computed(() => {
  const found = props.options.find((o) => o.raw === props.modelValue)
  return found?.formatted || ''
})

const handleSelect = (option: Option) => {
  modelValue.value = option.raw
  isOpen.value = false
  search.value = ''
}

onClickOutside(containerRef, () => {
  isOpen.value = false
})
</script>

<template>
  <div ref="containerRef" :class="cn('relative w-full', props.class)">
    <button type="button" :class="cn(
      'flex h-10 w-full items-center justify-between rounded-md border border-gray-200 bg-white px-3 py-2 text-sm ring-offset-white placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
      isOpen ? 'ring-2 ring-blue-600 ring-offset-2' : ''
    )" @click="!disabled && (isOpen = !isOpen)" :disabled="disabled">
      <span class="truncate block text-left w-full pr-6">{{ selectedLabel || placeholder || 'Selecione uma opção'
      }}</span>
      <svg class="absolute right-3 top-3 h-4 w-4 opacity-50 transition-transform duration-200"
        :class="{ 'rotate-180': isOpen }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="m6 9 6 6 6-6" />
      </svg>
    </button>

    <div v-if="isOpen && !disabled"
      class="absolute z-50 mt-2 max-h-60 w-full overflow-auto rounded-md border border-gray-300 bg-white py-1 text-base shadow-lg focus:outline-none sm:text-sm">
      <div class="sticky top-0 bg-white p-2 border-b border-gray-100 z-10">
        <Input v-model="search" placeholder="Pesquisar..."
          class="h-8 text-xs border-gray-200 focus-visible:ring-1 focus-visible:ring-offset-0" @click.stop />
      </div>

      <ul class="py-1">
        <li v-for="option in filteredOptions" :key="option.raw" @click="handleSelect(option)"
          class="relative cursor-pointer select-none py-2 pl-3 pr-9 hover:bg-blue-50 text-gray-900 transition-colors"
          :class="{ 'font-semibold bg-blue-50': modelValue === option.raw }">
          <span class="block truncate">{{ option.formatted }}</span>
          <span v-if="modelValue === option.raw"
            class="absolute inset-y-0 right-0 flex items-center pr-4 text-blue-600">
            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd"
                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                clip-rule="evenodd" />
            </svg>
          </span>
        </li>
        <li v-if="filteredOptions.length === 0"
          class="relative cursor-default select-none py-4 px-3 text-gray-500 italic text-xs text-center">
          Nenhuma opção encontrada.
        </li>
      </ul>
    </div>
  </div>
</template>
