<template>
    <PanelItem :index="index" :field="field">
        <template #value>
            <div class="flex flex-wrap gap-2">
                <button v-for="item in field.value" class="appearance-none inline-flex items-center text-left rounded-lg !cursor-default">
                    <span class="inline-flex items-center whitespace-nowrap min-h-6 px-2 rounded-full uppercase text-xs font-bold bg-primary-50 dark:bg-primary-500 text-primary-600 dark:text-gray-900 space-x-1">
                        <span>{{ item.name }}</span>
                    </span>
                </button>
            </div>
        </template>
    </PanelItem>
</template>

<script>
export default {
    props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],
}
</script>
