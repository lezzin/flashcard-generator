<script setup lang="ts">
import { type HTMLAttributes } from 'vue'
import { cva, type VariantProps } from 'class-variance-authority'
import { cn } from '@/lib/utils'

const alertVariants = cva(
  'relative w-full rounded-lg border p-4 [&>svg~*]:pl-7 [&>svg+div]:translate-y-[-3px] [&>svg]:absolute [&>svg]:left-4 [&>svg]:top-4 [&>svg]:text-foreground',
  {
    variants: {
      variant: {
        default: 'bg-white text-gray-950',
        destructive:
          'border-red-500/50 text-red-600 dark:border-red-500 [&>svg]:text-red-600 bg-red-50',
        success: 'border-green-500/50 text-green-600 dark:border-green-500 [&>svg]:text-green-600 bg-green-50',
        info: 'border-blue-500/50 text-blue-600 dark:border-blue-500 [&>svg]:text-blue-600 bg-blue-50',
      },
    },
    defaultVariants: {
      variant: 'default',
    },
  },
)

const props = defineProps<{
  class?: HTMLAttributes['class']
  variant?: VariantProps<typeof alertVariants>['variant']
}>()
</script>

<template>
  <div :class="cn(alertVariants({ variant }), props.class)" role="alert">
    <slot />
  </div>
</template>
