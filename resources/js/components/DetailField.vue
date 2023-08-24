<template>
    <PanelItem :index="index" :field="field">
        <template #value>
            <div v-if="lists.length > 0" class="flex flex-wrap gap-2">
                <button v-for="item in lists" class="appearance-none inline-flex items-center text-left rounded-lg !cursor-default">
                    <span class="inline-flex items-center whitespace-nowrap min-h-6 px-2 rounded-full uppercase text-xs font-bold bg-primary-50 dark:bg-primary-500 text-primary-600 dark:text-gray-900 space-x-1">
                        <span>{{ item.name }}</span>
                    </span>
                </button>
            </div>
        </template>
    </PanelItem>
</template>

<script>
import request from "../request";
export default {
    mixins: [request],

    mounted() {
        if (typeof this.field.options !== 'string') {
            this.handleLists(this.field.options);
        } else if (this.field.belongsToMany) {
            this.formatLists(this.field.value);
        } else {
            if (this.field.value.length === 0)
                return this.lists = [];
            this.loadLists();
        }
    }
}
</script>
